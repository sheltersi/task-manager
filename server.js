import path from 'node:path';
import { fileURLToPath } from 'node:url';
import express from 'express';
import session from 'express-session';
import {
    createTask,
    createUser,
    createWorklog,
    deleteTask,
    deleteUser,
    deleteWorklog,
    findTaskById,
    findUserByEmail,
    findWorklogById,
    getTaskLoggedHoursMap,
    getTaskWorklogs,
    getTimesheetWeekData,
    queryUserTasks,
    serializeUser,
    updateTask,
    updateUserPassword,
    updateUserProfile,
    updateWorklog,
    verifyUserPassword,
} from './src/store.js';
import {
    attachViewLocals,
    methodOverrideMiddleware,
    redirectIfAuthenticated,
    requireAuth,
    setFlash,
} from './src/middleware.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = 3000;
const HOST = '0.0.0.0';

app.set('trust proxy', 1);
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use(express.static(path.join(__dirname, 'public')));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(methodOverrideMiddleware);

app.use(
    session({
        name: 'task_manager_session',
        secret: process.env.SESSION_SECRET || 'laravel-task-manager-node-session-secret',
        resave: false,
        saveUninitialized: false,
        cookie: {
            httpOnly: true,
            secure: 'auto',
            sameSite: 'lax',
            maxAge: 1000 * 60 * 60 * 24 * 7,
        },
    })
);

app.use(attachViewLocals);

// Public Landing Page
app.get('/', (req, res) => {
    res.render('welcome');
});

// Authentication Routes
app.get('/login', redirectIfAuthenticated, (req, res) => {
    res.render('auth/login');
});

app.post('/login', redirectIfAuthenticated, (req, res) => {
    const email = String(req.body.email || '').trim().toLowerCase();
    const password = String(req.body.password || '');
    const remember = Boolean(req.body.remember);

    const errors = {};
    if (!email) errors.email = 'The email field is required.';
    if (!password) errors.password = 'The password field is required.';

    if (Object.keys(errors).length > 0) {
        setFlash(req, { errors, oldInput: { email } });
        return res.redirect('/login');
    }

    const user = findUserByEmail(email);
    if (!user || !verifyUserPassword(user, password)) {
        setFlash(req, {
            errors: { email: 'These credentials do not match our records.' },
            oldInput: { email },
        });
        return res.redirect('/login');
    }

    req.session.userId = user.id;
    if (remember) {
        req.session.cookie.maxAge = 1000 * 60 * 60 * 24 * 30;
    }

    return res.redirect('/dashboard');
});

app.get('/register', redirectIfAuthenticated, (req, res) => {
    res.render('auth/register');
});

app.post('/register', redirectIfAuthenticated, (req, res) => {
    const name = String(req.body.name || '').trim();
    const email = String(req.body.email || '').trim().toLowerCase();
    const position = String(req.body.position || '').trim();
    const password = String(req.body.password || '');
    const passwordConfirmation = String(req.body.password_confirmation || '');

    const errors = {};
    if (!name) errors.name = 'The name field is required.';
    if (!email) {
        errors.email = 'The email field is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.email = 'Please enter a valid email address.';
    } else if (findUserByEmail(email)) {
        errors.email = 'The email has already been taken.';
    }

    if (!password) {
        errors.password = 'The password field is required.';
    } else if (password.length < 8) {
        errors.password = 'The password must be at least 8 characters.';
    } else if (password !== passwordConfirmation) {
        errors.password = 'The password confirmation does not match.';
    }

    if (Object.keys(errors).length > 0) {
        setFlash(req, { errors, oldInput: { name, email, position } });
        return res.redirect('/register');
    }

    const user = createUser({ name, email, password, position });
    req.session.userId = user.id;
    return res.redirect('/dashboard');
});

app.post('/logout', (req, res) => {
    req.session.destroy(() => {
        res.clearCookie('task_manager_session');
        res.redirect('/');
    });
});

app.get('/forgot-password', redirectIfAuthenticated, (req, res) => {
    res.render('auth/forgot-password');
});

app.post('/forgot-password', redirectIfAuthenticated, (req, res) => {
    const email = String(req.body.email || '').trim();
    if (!email) {
        setFlash(req, { errors: { email: 'The email field is required.' }, oldInput: { email } });
        return res.redirect('/forgot-password');
    }
    setFlash(req, {
        status: 'If an account matches that email, password reset instructions have been queued.',
    });
    return res.redirect('/forgot-password');
});

// Protected Dashboard & Task Routes
app.get('/dashboard', requireAuth, (req, res) => {
    const search = String(req.query.search || '');
    const status = String(req.query.status || '');
    const sort = String(req.query.sort || 'latest');
    const page = Number(req.query.page) || 1;

    const pagination = queryUserTasks(req.user.id, { search, status, sort, page, perPage: 10 });
    const loggedHoursMap = getTaskLoggedHoursMap(req.user.id);

    res.render('dashboard', {
        tasks: pagination.data,
        pagination,
        loggedHoursMap,
        search,
        status,
        sort,
    });
});

app.get('/tasks/create', requireAuth, (req, res) => {
    res.render('tasks/create');
});

app.post('/tasks', requireAuth, (req, res) => {
    const { data, errors } = validateTaskInput(req.body);
    if (Object.keys(errors).length > 0) {
        setFlash(req, { errors, oldInput: req.body });
        return res.redirect('/tasks/create');
    }

    createTask(req.user.id, data);
    setFlash(req, { success: 'Task created successfully.' });
    return res.redirect('/dashboard');
});

app.get('/tasks/:id', requireAuth, (req, res) => {
    const task = findTaskById(req.params.id);
    if (!task) {
        return res.status(404).render('error', { statusCode: 404, message: 'Task not found.' });
    }
    if (task.user_id !== req.user.id) {
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    const taskWorklogs = getTaskWorklogs(req.user.id, task.id);
    const totalTaskHours = Math.round(taskWorklogs.reduce((sum, w) => sum + w.hours, 0) * 100) / 100;
    const now = new Date();
    const todayStr = `${now.getUTCFullYear()}-${String(now.getUTCMonth() + 1).padStart(2, '0')}-${String(now.getUTCDate()).padStart(2, '0')}`;

    return res.render('tasks/show', { task, taskWorklogs, totalTaskHours, todayStr });
});

app.get('/tasks/:id/edit', requireAuth, (req, res) => {
    const task = findTaskById(req.params.id);
    if (!task) {
        return res.status(404).render('error', { statusCode: 404, message: 'Task not found.' });
    }
    if (task.user_id !== req.user.id) {
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    return res.render('tasks/edit', { task });
});

app.put('/tasks/:id', requireAuth, (req, res) => {
    const task = findTaskById(req.params.id);
    if (!task) {
        return res.status(404).render('error', { statusCode: 404, message: 'Task not found.' });
    }
    if (task.user_id !== req.user.id) {
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    const { data, errors } = validateTaskInput(req.body);
    if (Object.keys(errors).length > 0) {
        setFlash(req, { errors, oldInput: req.body });
        return res.redirect(`/tasks/${task.id}/edit`);
    }

    updateTask(task.id, data);
    setFlash(req, { success: 'Task updated successfully.' });
    return res.redirect('/dashboard');
});

app.delete('/tasks/:id', requireAuth, (req, res) => {
    const task = findTaskById(req.params.id);
    if (!task) {
        return res.status(404).render('error', { statusCode: 404, message: 'Task not found.' });
    }
    if (task.user_id !== req.user.id) {
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    deleteTask(task.id);
    setFlash(req, { success: 'Task deleted successfully.' });
    return res.redirect('/dashboard');
});

app.patch('/tasks/:id/status', requireAuth, (req, res) => {
    const task = findTaskById(req.params.id);
    if (!task) {
        return res.status(404).render('error', { statusCode: 404, message: 'Task not found.' });
    }
    if (task.user_id !== req.user.id) {
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    const status = String(req.body.status || '');
    if (!['to_do', 'in_progress', 'in_review', 'completed'].includes(status)) {
        setFlash(req, { errors: { status: 'Invalid task status selected.' } });
        return res.redirect(req.get('referer') || '/dashboard');
    }

    updateTask(task.id, { status });
    setFlash(req, { success: 'Status updated successfully!' });
    return res.redirect(req.get('referer') || '/dashboard');
});

app.patch('/tasks/:id/priority', requireAuth, (req, res) => {
    const task = findTaskById(req.params.id);
    if (!task) {
        return res.status(404).render('error', { statusCode: 404, message: 'Task not found.' });
    }
    if (task.user_id !== req.user.id) {
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    const priority = Number(req.body.priority);
    if (!Number.isInteger(priority) || priority < 1 || priority > 5) {
        setFlash(req, { errors: { priority: 'Priority must be between 1 and 5.' } });
        return res.redirect(req.get('referer') || '/dashboard');
    }

    updateTask(task.id, { priority });
    setFlash(req, { success: 'Priority updated successfully!' });
    return res.redirect(req.get('referer') || '/dashboard');
});

// Timesheets Routes (Tempo-style Weekly Matrix, Timeline, Live Timer & CSV Export)
app.get('/timesheets', requireAuth, (req, res) => {
    const week = String(req.query.week || '');
    const view = req.query.view === 'list' ? 'list' : 'grid';
    const search = String(req.query.search || '');
    const billable = ['billable', 'non_billable'].includes(req.query.billable) ? req.query.billable : 'all';
    const openTaskId = req.query.taskId ? Number(req.query.taskId) : null;
    const openDate = req.query.date ? String(req.query.date) : null;

    const timesheet = getTimesheetWeekData(req.user.id, { week, search, billable });
    const now = new Date();
    const todayStr = `${now.getUTCFullYear()}-${String(now.getUTCMonth() + 1).padStart(2, '0')}-${String(now.getUTCDate()).padStart(2, '0')}`;

    res.render('timesheets/index', {
        timesheet,
        view,
        search,
        billable,
        todayStr,
        openTaskId,
        openDate,
    });
});

app.post('/timesheets/worklogs', requireAuth, (req, res) => {
    const wantsJson = req.headers.accept?.includes('application/json') || req.xhr;
    const taskId = Number(req.body.task_id);
    const task = findTaskById(taskId);
    if (!task || task.user_id !== req.user.id) {
        if (wantsJson) return res.status(403).json({ error: 'Please select a valid task you own.' });
        setFlash(req, { errors: { task_id: 'Please select a valid task.' } });
        return res.redirect(req.get('referer') || '/timesheets');
    }

    const hours = Number(req.body.hours);
    const date = String(req.body.date || '').trim();
    const startedAt = String(req.body.started_at || '09:00').trim();
    const description = String(req.body.description || '').trim();
    const billableRaw = req.body.billable;
    const billable = billableRaw === true || billableRaw === 'true' || billableRaw === '1' || billableRaw === 'on';

    if (!Number.isFinite(hours) || hours < 0.25 || hours > 24) {
        if (wantsJson) return res.status(422).json({ error: 'Hours must be between 0.25 and 24.' });
        setFlash(req, { errors: { hours: 'Hours must be between 0.25 and 24.' } });
        return res.redirect(req.get('referer') || '/timesheets');
    }

    if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        if (wantsJson) return res.status(422).json({ error: 'Please enter a valid date (YYYY-MM-DD).' });
        setFlash(req, { errors: { date: 'Please enter a valid date.' } });
        return res.redirect(req.get('referer') || '/timesheets');
    }

    const entry = createWorklog(req.user.id, {
        task_id: task.id,
        date,
        hours,
        started_at: /^\d{2}:\d{2}$/.test(startedAt) ? startedAt : '09:00',
        description,
        billable,
    });

    if (wantsJson) {
        return res.status(201).json({ entry });
    }

    setFlash(req, { success: `Logged ${entry.hours.toFixed(2).replace(/\.00$/, '.0')}h on TM-${task.id}.` });
    const redirectWeek = req.body.week ? `?week=${encodeURIComponent(req.body.week)}&view=${encodeURIComponent(req.body.view || 'grid')}` : '';
    return res.redirect(req.body.return_to || `/timesheets${redirectWeek}`);
});

app.put('/timesheets/worklogs/:id', requireAuth, (req, res) => {
    const wantsJson = req.headers.accept?.includes('application/json') || req.xhr;
    const entry = findWorklogById(req.params.id);
    if (!entry) {
        if (wantsJson) return res.status(404).json({ error: 'Worklog entry not found.' });
        return res.status(404).render('error', { statusCode: 404, message: 'Worklog entry not found.' });
    }
    if (entry.user_id !== req.user.id) {
        if (wantsJson) return res.status(403).json({ error: 'This action is unauthorized.' });
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    const taskId = Number(req.body.task_id || entry.task_id);
    const task = findTaskById(taskId);
    if (!task || task.user_id !== req.user.id) {
        if (wantsJson) return res.status(403).json({ error: 'Invalid task selected.' });
        setFlash(req, { errors: { task_id: 'Invalid task selected.' } });
        return res.redirect(req.get('referer') || '/timesheets');
    }

    const hours = Number(req.body.hours);
    const date = String(req.body.date || entry.date).trim();
    const startedAt = String(req.body.started_at || entry.started_at).trim();
    const description = String(req.body.description ?? entry.description).trim();
    const billableRaw = req.body.billable;
    const billable = billableRaw === true || billableRaw === 'true' || billableRaw === '1' || billableRaw === 'on';

    if (!Number.isFinite(hours) || hours < 0.25 || hours > 24) {
        if (wantsJson) return res.status(422).json({ error: 'Hours must be between 0.25 and 24.' });
        setFlash(req, { errors: { hours: 'Hours must be between 0.25 and 24.' } });
        return res.redirect(req.get('referer') || '/timesheets');
    }

    const updated = updateWorklog(entry.id, {
        task_id: task.id,
        date,
        hours,
        started_at: /^\d{2}:\d{2}$/.test(startedAt) ? startedAt : '09:00',
        description,
        billable,
    });

    if (wantsJson) {
        return res.json({ entry: updated });
    }

    setFlash(req, { success: 'Worklog updated successfully.' });
    return res.redirect(req.get('referer') || '/timesheets');
});

app.delete('/timesheets/worklogs/:id', requireAuth, (req, res) => {
    const wantsJson = req.headers.accept?.includes('application/json') || req.xhr;
    const entry = findWorklogById(req.params.id);
    if (!entry) {
        if (wantsJson) return res.status(404).json({ error: 'Worklog entry not found.' });
        return res.status(404).render('error', { statusCode: 404, message: 'Worklog entry not found.' });
    }
    if (entry.user_id !== req.user.id) {
        if (wantsJson) return res.status(403).json({ error: 'This action is unauthorized.' });
        return res.status(403).render('error', { statusCode: 403, message: 'This action is unauthorized.' });
    }

    deleteWorklog(entry.id);
    if (wantsJson) {
        return res.json({ deleted: true });
    }
    setFlash(req, { success: 'Worklog removed.' });
    return res.redirect(req.get('referer') || '/timesheets');
});

app.get('/timesheets/export', requireAuth, (req, res) => {
    const week = String(req.query.week || '');
    const search = String(req.query.search || '');
    const billable = ['billable', 'non_billable'].includes(req.query.billable) ? req.query.billable : 'all';
    const timesheet = getTimesheetWeekData(req.user.id, { week, search, billable });

    const escapeCsv = (val) => `"${String(val ?? '').replace(/"/g, '""')}"`;
    const rows = [
        ['Date', 'Started At', 'Task Key', 'Task Title', 'Description', 'Hours', 'Billable'],
        ...timesheet.weekEntries.map((entry) => [
            entry.date,
            entry.started_at,
            entry.taskKey,
            entry.task?.title || '',
            entry.description,
            entry.hours.toFixed(2),
            entry.billable ? 'Yes' : 'No',
        ]),
    ];

    const csvContent = rows.map((r) => r.map(escapeCsv).join(',')).join('\n');
    res.setHeader('Content-Type', 'text/csv; charset=utf-8');
    res.setHeader('Content-Disposition', `attachment; filename="timesheet-${timesheet.weekStart}.csv"`);
    res.send(csvContent);
});

// User Profile Routes (Jetstream Parity)
app.get('/user/profile', requireAuth, (req, res) => {
    res.render('profile/show');
});

app.put('/user/profile-information', requireAuth, (req, res) => {
    const name = String(req.body.name || '').trim();
    const email = String(req.body.email || '').trim().toLowerCase();
    const position = String(req.body.position || '').trim();

    const errors = {};
    if (!name) errors.name = 'The name field is required.';
    if (!email) {
        errors.email = 'The email field is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.email = 'Please enter a valid email address.';
    } else {
        const existing = findUserByEmail(email);
        if (existing && existing.id !== req.user.id) {
            errors.email = 'The email has already been taken.';
        }
    }

    if (Object.keys(errors).length > 0) {
        setFlash(req, { errors, oldInput: { name, email, position } });
        return res.redirect('/user/profile');
    }

    updateUserProfile(req.user.id, { name, email, position });
    setFlash(req, { success: 'Profile information updated successfully.' });
    return res.redirect('/user/profile');
});

app.put('/user/password', requireAuth, (req, res) => {
    const currentPassword = String(req.body.current_password || '');
    const password = String(req.body.password || '');
    const passwordConfirmation = String(req.body.password_confirmation || '');

    const errors = {};
    if (!verifyUserPassword(req.user, currentPassword)) {
        errors.current_password = 'The provided password does not match your current password.';
    }
    if (!password) {
        errors.password = 'The new password field is required.';
    } else if (password.length < 8) {
        errors.password = 'The password must be at least 8 characters.';
    } else if (password !== passwordConfirmation) {
        errors.password = 'The password confirmation does not match.';
    }

    if (Object.keys(errors).length > 0) {
        setFlash(req, { errors });
        return res.redirect('/user/profile');
    }

    updateUserPassword(req.user.id, password);
    setFlash(req, { success: 'Password updated successfully.' });
    return res.redirect('/user/profile');
});

app.delete('/user', requireAuth, (req, res) => {
    const password = String(req.body.password || '');
    if (!verifyUserPassword(req.user, password)) {
        setFlash(req, {
            errors: { delete_password: 'This password does not match our records.' },
        });
        return res.redirect('/user/profile');
    }

    deleteUser(req.user.id);
    req.session.destroy(() => {
        res.clearCookie('task_manager_session');
        res.redirect('/');
    });
});

// API Route (Sanctum Parity)
app.get('/api/user', requireAuth, (req, res) => {
    res.json(serializeUser(req.user));
});

// 404 Handler
app.use((req, res) => {
    res.status(404).render('error', {
        statusCode: 404,
        message: 'Page not found.',
    });
});

// Error Handler
app.use((err, req, res, _next) => {
    console.error('[Server Error]', err);
    res.status(500).render('error', {
        statusCode: 500,
        message: 'An unexpected error occurred.',
    });
});

function validateTaskInput(body) {
    const title = String(body.title || '').trim();
    const description = body.description !== undefined ? String(body.description).trim() : '';
    const status = String(body.status || 'to_do');
    const priority = Number(body.priority);
    const dueDate = String(body.due_date || '').trim();

    const errors = {};
    if (!title) {
        errors.title = 'The title field is required.';
    } else if (title.length > 255) {
        errors.title = 'The title must not be greater than 255 characters.';
    }

    if (!['to_do', 'in_progress', 'in_review', 'completed'].includes(status)) {
        errors.status = 'The selected status is invalid.';
    }

    if (!Number.isInteger(priority) || priority < 1 || priority > 5) {
        errors.priority = 'Priority must be between 1 and 5.';
    }

    if (dueDate && !/^\d{4}-\d{2}-\d{2}$/.test(dueDate)) {
        errors.due_date = 'Please provide a valid date (YYYY-MM-DD).';
    }

    return {
        data: {
            title,
            description: description || null,
            status,
            priority,
            due_date: dueDate || null,
        },
        errors,
    };
}

app.listen(PORT, HOST, () => {
    console.log(`Task Manager server running at http://${HOST}:${PORT}`);
});
