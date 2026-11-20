import bcrypt from 'bcryptjs';

const ALLOWED_STATUSES = ['to_do', 'in_progress', 'in_review', 'completed'];

const users = new Map();
const tasks = new Map();
const worklogs = new Map();

let nextUserId = 1;
let nextTaskId = 1;
let nextWorklogId = 1;

export function getProfilePhotoUrl(user) {
    const initials = (user?.name || 'U')
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || '')
        .join('') || 'U';

    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">
        <rect width="96" height="96" rx="48" fill="#E0E7FF"/>
        <text x="50%" y="53%" dominant-baseline="middle" text-anchor="middle" fill="#4338CA" font-family="Inter, Figtree, system-ui, sans-serif" font-size="34" font-weight="600">${initials}</text>
    </svg>`;

    return `data:image/svg+xml;utf8,${encodeURIComponent(svg)}`;
}

export function serializeUser(user) {
    if (!user) return null;
    return {
        id: user.id,
        name: user.name,
        email: user.email,
        position: user.position || '',
        profile_photo_url: getProfilePhotoUrl(user),
        created_at: user.created_at,
        updated_at: user.updated_at,
    };
}

export function findUserByEmail(email) {
    const normalized = String(email || '').trim().toLowerCase();
    for (const user of users.values()) {
        if (user.email.toLowerCase() === normalized) {
            return user;
        }
    }
    return null;
}

export function findUserById(id) {
    return users.get(Number(id)) || null;
}

export function createUser({ name, email, password, position = '' }) {
    const now = new Date();
    const user = {
        id: nextUserId++,
        name: String(name).trim(),
        email: String(email).trim().toLowerCase(),
        position: position ? String(position).trim() : '',
        password_hash: bcrypt.hashSync(String(password), 10),
        created_at: now,
        updated_at: now,
    };
    users.set(user.id, user);
    return user;
}

export function verifyUserPassword(user, plainPassword) {
    if (!user) return false;
    return bcrypt.compareSync(String(plainPassword || ''), user.password_hash);
}

export function updateUserProfile(userId, { name, email, position }) {
    const user = findUserById(userId);
    if (!user) return null;
    user.name = String(name).trim();
    user.email = String(email).trim().toLowerCase();
    user.position = position ? String(position).trim() : '';
    user.updated_at = new Date();
    return user;
}

export function updateUserPassword(userId, newPassword) {
    const user = findUserById(userId);
    if (!user) return null;
    user.password_hash = bcrypt.hashSync(String(newPassword), 10);
    user.updated_at = new Date();
    return user;
}

export function deleteUser(userId) {
    const id = Number(userId);
    for (const [taskId, task] of tasks.entries()) {
        if (task.user_id === id) {
            tasks.delete(taskId);
        }
    }
    for (const [logId, log] of worklogs.entries()) {
        if (log.user_id === id) {
            worklogs.delete(logId);
        }
    }
    return users.delete(id);
}

export function createTask(userId, { title, description = null, status = 'to_do', priority = 3, due_date = null, created_at = null }) {
    const now = created_at instanceof Date ? created_at : new Date();
    const parsedDueDate = due_date ? parseDateOnly(due_date) : null;
    const task = {
        id: nextTaskId++,
        user_id: Number(userId),
        title: String(title).trim(),
        description: description && String(description).trim() !== '' ? String(description).trim() : null,
        status: ALLOWED_STATUSES.includes(status) ? status : 'to_do',
        priority: Math.min(5, Math.max(1, Number(priority) || 1)),
        due_date: parsedDueDate,
        created_at: now,
        updated_at: now,
    };
    tasks.set(task.id, task);
    return task;
}

export function findTaskById(id) {
    return tasks.get(Number(id)) || null;
}

export function updateTask(taskId, updates) {
    const task = findTaskById(taskId);
    if (!task) return null;

    if (updates.title !== undefined) {
        task.title = String(updates.title).trim();
    }
    if (updates.description !== undefined) {
        task.description = updates.description && String(updates.description).trim() !== '' ? String(updates.description).trim() : null;
    }
    if (updates.status !== undefined && ALLOWED_STATUSES.includes(updates.status)) {
        task.status = updates.status;
    }
    if (updates.priority !== undefined) {
        task.priority = Math.min(5, Math.max(1, Number(updates.priority) || 1));
    }
    if (updates.due_date !== undefined) {
        task.due_date = updates.due_date ? parseDateOnly(updates.due_date) : null;
    }

    task.updated_at = new Date();
    return task;
}

export function deleteTask(taskId) {
    const id = Number(taskId);
    for (const [logId, log] of worklogs.entries()) {
        if (log.task_id === id) {
            worklogs.delete(logId);
        }
    }
    return tasks.delete(id);
}

export function getUserTasksAll(userId) {
    const uid = Number(userId);
    return Array.from(tasks.values())
        .filter((t) => t.user_id === uid)
        .sort((a, b) => b.created_at.getTime() - a.created_at.getTime());
}

export function getTaskLoggedHoursMap(userId) {
    const uid = Number(userId);
    const totals = new Map();
    for (const log of worklogs.values()) {
        if (log.user_id !== uid) continue;
        totals.set(log.task_id, Math.round(((totals.get(log.task_id) || 0) + log.hours) * 100) / 100);
    }
    return totals;
}

export function getTaskWorklogs(userId, taskId) {
    const uid = Number(userId);
    const tid = Number(taskId);
    return Array.from(worklogs.values())
        .filter((w) => w.user_id === uid && w.task_id === tid)
        .sort((a, b) => {
            if (b.date !== a.date) return b.date.localeCompare(a.date);
            return b.created_at.getTime() - a.created_at.getTime();
        });
}

export function createWorklog(userId, { task_id, date, hours, description = '', billable = true, started_at = '09:00' }) {
    const now = new Date();
    const numericHours = Math.round(Math.min(24, Math.max(0.25, Number(hours) || 0.25)) * 100) / 100;
    const entry = {
        id: nextWorklogId++,
        user_id: Number(userId),
        task_id: Number(task_id),
        date: normalizeDateStr(date),
        hours: numericHours,
        description: String(description || '').trim(),
        billable: Boolean(billable),
        started_at: String(started_at || '09:00').trim() || '09:00',
        created_at: now,
        updated_at: now,
    };
    worklogs.set(entry.id, entry);
    return entry;
}

export function findWorklogById(id) {
    return worklogs.get(Number(id)) || null;
}

export function updateWorklog(id, updates) {
    const entry = findWorklogById(id);
    if (!entry) return null;

    if (updates.task_id !== undefined) {
        entry.task_id = Number(updates.task_id);
    }
    if (updates.date !== undefined) {
        entry.date = normalizeDateStr(updates.date);
    }
    if (updates.hours !== undefined) {
        entry.hours = Math.round(Math.min(24, Math.max(0.25, Number(updates.hours) || 0.25)) * 100) / 100;
    }
    if (updates.description !== undefined) {
        entry.description = String(updates.description || '').trim();
    }
    if (updates.billable !== undefined) {
        entry.billable = Boolean(updates.billable);
    }
    if (updates.started_at !== undefined) {
        entry.started_at = String(updates.started_at || '09:00').trim() || '09:00';
    }
    entry.updated_at = new Date();
    return entry;
}

export function deleteWorklog(id) {
    return worklogs.delete(Number(id));
}

function normalizeDateStr(value) {
    if (!value) {
        const now = new Date();
        return `${now.getUTCFullYear()}-${String(now.getUTCMonth() + 1).padStart(2, '0')}-${String(now.getUTCDate()).padStart(2, '0')}`;
    }
    if (value instanceof Date) {
        return `${value.getUTCFullYear()}-${String(value.getUTCMonth() + 1).padStart(2, '0')}-${String(value.getUTCDate()).padStart(2, '0')}`;
    }
    const match = /^(\d{4}-\d{2}-\d{2})/.exec(String(value).trim());
    if (match) return match[1];
    return normalizeDateStr(null);
}

function addDaysToDateStr(dateStr, deltaDays) {
    const [y, m, d] = dateStr.split('-').map(Number);
    const dt = new Date(Date.UTC(y, m - 1, d + deltaDays));
    return `${dt.getUTCFullYear()}-${String(dt.getUTCMonth() + 1).padStart(2, '0')}-${String(dt.getUTCDate()).padStart(2, '0')}`;
}

export function getMondayOfWeek(dateInput) {
    const baseStr = normalizeDateStr(dateInput);
    const [y, m, d] = baseStr.split('-').map(Number);
    const dt = new Date(Date.UTC(y, m - 1, d));
    const dayOfWeek = dt.getUTCDay(); // 0 = Sun, 1 = Mon ... 6 = Sat
    const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
    return addDaysToDateStr(baseStr, diffToMonday);
}

export function getWeekDays(weekStartStr) {
    const monday = getMondayOfWeek(weekStartStr);
    const todayStr = normalizeDateStr(new Date());
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const fullDayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const days = [];
    for (let i = 0; i < 7; i++) {
        const dateStr = addDaysToDateStr(monday, i);
        const [y, m, d] = dateStr.split('-').map(Number);
        days.push({
            date: dateStr,
            dayShort: dayNames[i],
            dayFull: fullDayNames[i],
            dayNum: String(d).padStart(2, '0'),
            monthShort: months[m - 1],
            year: y,
            isToday: dateStr === todayStr,
            isWeekend: i >= 5,
            targetHours: i >= 5 ? 0 : 8,
        });
    }
    return days;
}

export function getTimesheetWeekData(userId, { week = '', search = '', billable = 'all' } = {}) {
    const uid = Number(userId);
    const thisWeek = getMondayOfWeek(new Date());
    const weekStart = week ? getMondayOfWeek(week) : thisWeek;
    const prevWeek = addDaysToDateStr(weekStart, -7);
    const nextWeek = addDaysToDateStr(weekStart, 7);
    const weekDays = getWeekDays(weekStart);
    const weekDateSet = new Set(weekDays.map((d) => d.date));

    const firstDay = weekDays[0];
    const lastDay = weekDays[6];
    const weekLabel = firstDay.monthShort === lastDay.monthShort
        ? `${firstDay.monthShort} ${Number(firstDay.dayNum)} – ${Number(lastDay.dayNum)}, ${lastDay.year}`
        : `${firstDay.monthShort} ${Number(firstDay.dayNum)} – ${lastDay.monthShort} ${Number(lastDay.dayNum)}, ${lastDay.year}`;

    const userTasks = getUserTasksAll(uid);
    const taskMap = new Map(userTasks.map((t) => [t.id, t]));
    const trimmedSearch = String(search || '').trim().toLowerCase();

    const weekEntries = Array.from(worklogs.values())
        .filter((log) => {
            if (log.user_id !== uid) return false;
            if (!weekDateSet.has(log.date)) return false;
            if (billable === 'billable' && !log.billable) return false;
            if (billable === 'non_billable' && log.billable) return false;
            const task = taskMap.get(log.task_id);
            if (trimmedSearch) {
                const inTitle = task?.title.toLowerCase().includes(trimmedSearch);
                const inKey = `tm-${log.task_id}`.includes(trimmedSearch);
                const inDesc = log.description.toLowerCase().includes(trimmedSearch);
                if (!inTitle && !inKey && !inDesc) return false;
            }
            return true;
        })
        .sort((a, b) => {
            if (a.date !== b.date) return b.date.localeCompare(a.date);
            return b.started_at.localeCompare(a.started_at);
        });

    // Build task matrix rows for all tasks with worklogs in this week
    const rowMap = new Map();
    for (const entry of weekEntries) {
        const task = taskMap.get(entry.task_id);
        if (!task) continue;
        if (!rowMap.has(task.id)) {
            const daysObj = {};
            for (const wd of weekDays) {
                daysObj[wd.date] = { totalHours: 0, billableHours: 0, entries: [] };
            }
            rowMap.set(task.id, {
                task,
                taskKey: `TM-${task.id}`,
                days: daysObj,
                rowTotal: 0,
                rowBillable: 0,
                entryCount: 0,
            });
        }
        const row = rowMap.get(task.id);
        const cell = row.days[entry.date];
        if (cell) {
            cell.totalHours = Math.round((cell.totalHours + entry.hours) * 100) / 100;
            if (entry.billable) {
                cell.billableHours = Math.round((cell.billableHours + entry.hours) * 100) / 100;
            }
            cell.entries.push(entry);
        }
        row.rowTotal = Math.round((row.rowTotal + entry.hours) * 100) / 100;
        if (entry.billable) {
            row.rowBillable = Math.round((row.rowBillable + entry.hours) * 100) / 100;
        }
        row.entryCount += 1;
    }

    const matrixRows = Array.from(rowMap.values()).sort((a, b) => b.rowTotal - a.rowTotal);

    const dailyTotals = weekDays.map((wd) => {
        let totalHours = 0;
        let billableHours = 0;
        let entriesCount = 0;
        for (const entry of weekEntries) {
            if (entry.date === wd.date) {
                totalHours = Math.round((totalHours + entry.hours) * 100) / 100;
                if (entry.billable) {
                    billableHours = Math.round((billableHours + entry.hours) * 100) / 100;
                }
                entriesCount += 1;
            }
        }
        const pctOfTarget = wd.targetHours > 0 ? Math.min(100, Math.round((totalHours / wd.targetHours) * 100)) : (totalHours > 0 ? 100 : 0);
        return {
            ...wd,
            totalHours,
            billableHours,
            entriesCount,
            pctOfTarget,
        };
    });

    const totalHours = Math.round(weekEntries.reduce((sum, e) => sum + e.hours, 0) * 100) / 100;
    const billableHours = Math.round(weekEntries.filter((e) => e.billable).reduce((sum, e) => sum + e.hours, 0) * 100) / 100;
    const nonBillableHours = Math.round((totalHours - billableHours) * 100) / 100;
    const weeklyCapacity = 40;
    const capacityPct = Math.min(100, Math.round((totalHours / weeklyCapacity) * 100));
    const remainingHours = Math.max(0, Math.round((weeklyCapacity - totalHours) * 100) / 100);
    const billablePct = totalHours > 0 ? Math.round((billableHours / totalHours) * 100) : 0;

    // Entries grouped by day (Mon..Sun or reverse for timeline feed)
    const entriesByDay = weekDays
        .map((wd) => {
            const dayEntries = weekEntries
                .filter((e) => e.date === wd.date)
                .map((e) => ({
                    ...e,
                    task: taskMap.get(e.task_id),
                    taskKey: `TM-${e.task_id}`,
                }));
            const dayTotal = Math.round(dayEntries.reduce((s, e) => s + e.hours, 0) * 100) / 100;
            return {
                ...wd,
                totalHours: dayTotal,
                entries: dayEntries,
            };
        })
        .reverse();

    return {
        weekStart,
        thisWeek,
        prevWeek,
        nextWeek,
        isCurrentWeek: weekStart === thisWeek,
        weekLabel,
        weekDays,
        matrixRows,
        dailyTotals,
        entriesByDay,
        weekEntries: weekEntries.map((e) => ({
            ...e,
            task: taskMap.get(e.task_id),
            taskKey: `TM-${e.task_id}`,
        })),
        allTasks: userTasks.map((t) => ({
            ...t,
            taskKey: `TM-${t.id}`,
        })),
        summary: {
            totalHours,
            billableHours,
            nonBillableHours,
            billablePct,
            weeklyCapacity,
            capacityPct,
            remainingHours,
            entryCount: weekEntries.length,
        },
    };
}

export function queryUserTasks(userId, { search = '', status = '', sort = 'latest', page = 1, perPage = 10 } = {}) {
    const uid = Number(userId);
    let results = Array.from(tasks.values()).filter((t) => t.user_id === uid);

    const trimmedSearch = String(search || '').trim().toLowerCase();
    if (trimmedSearch) {
        results = results.filter((t) =>
            t.title.toLowerCase().includes(trimmedSearch) ||
            (t.description && t.description.toLowerCase().includes(trimmedSearch))
        );
    }

    if (ALLOWED_STATUSES.includes(status)) {
        results = results.filter((t) => t.status === status);
    }

    if (sort === 'due_date') {
        results.sort((a, b) => {
            if (!a.due_date && !b.due_date) return b.created_at - a.created_at;
            if (!a.due_date) return 1;
            if (!b.due_date) return -1;
            const diff = a.due_date.getTime() - b.due_date.getTime();
            return diff !== 0 ? diff : b.created_at - a.created_at;
        });
    } else if (sort === 'priority') {
        results.sort((a, b) => {
            const diff = b.priority - a.priority;
            return diff !== 0 ? diff : b.created_at - a.created_at;
        });
    } else {
        results.sort((a, b) => {
            const diff = b.created_at.getTime() - a.created_at.getTime();
            return diff !== 0 ? diff : b.id - a.id;
        });
    }

    const total = results.length;
    const lastPage = Math.max(1, Math.ceil(total / perPage));
    const currentPage = Math.min(lastPage, Math.max(1, Number(page) || 1));
    const offset = (currentPage - 1) * perPage;
    const data = results.slice(offset, offset + perPage);

    return {
        data,
        total,
        perPage,
        currentPage,
        lastPage,
        from: total === 0 ? 0 : offset + 1,
        to: Math.min(total, offset + perPage),
        hasPrev: currentPage > 1,
        hasNext: currentPage < lastPage,
    };
}

export function parseDateOnly(value) {
    if (!value) return null;
    if (value instanceof Date) {
        return new Date(Date.UTC(value.getUTCFullYear(), value.getUTCMonth(), value.getUTCDate()));
    }
    const str = String(value).trim();
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(str);
    if (match) {
        return new Date(Date.UTC(Number(match[1]), Number(match[2]) - 1, Number(match[3])));
    }
    const parsed = new Date(str);
    if (Number.isNaN(parsed.getTime())) return null;
    return new Date(Date.UTC(parsed.getUTCFullYear(), parsed.getUTCMonth(), parsed.getUTCDate()));
}

export const viewHelpers = {
    truncate(text, limit = 70) {
        if (!text) return '';
        return text.length > limit ? text.slice(0, limit) + '...' : text;
    },
    formatDate(date) {
        if (!date) return '—';
        const d = date instanceof Date ? date : new Date(date);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const day = String(d.getUTCDate()).padStart(2, '0');
        const month = months[d.getUTCMonth()];
        const year = d.getUTCFullYear();
        return `${day} ${month} ${year}`;
    },
    formatInputDate(date) {
        if (!date) return '';
        const d = date instanceof Date ? date : new Date(date);
        const year = d.getUTCFullYear();
        const month = String(d.getUTCMonth() + 1).padStart(2, '0');
        const day = String(d.getUTCDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    },
    isOverdue(task) {
        if (!task?.due_date || task.status === 'completed') return false;
        const now = new Date();
        const todayUtc = Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate());
        return task.due_date.getTime() < todayUtc;
    },
    diffForHumans(date) {
        if (!date) return '';
        const d = date instanceof Date ? date : new Date(date);
        const diffMs = d.getTime() - Date.now();
        const absSec = Math.round(Math.abs(diffMs) / 1000);
        const isFuture = diffMs > 0;

        const formatUnit = (val, unit) => {
            const label = val === 1 ? unit : `${unit}s`;
            return isFuture ? `in ${val} ${label}` : `${val} ${label} ago`;
        };

        if (absSec < 60) return isFuture ? 'in a few seconds' : 'just now';
        const mins = Math.round(absSec / 60);
        if (mins < 60) return formatUnit(mins, 'minute');
        const hours = Math.round(mins / 60);
        if (hours < 24) return formatUnit(hours, 'hour');
        const days = Math.round(hours / 24);
        if (days < 30) return formatUnit(days, 'day');
        const months = Math.round(days / 30);
        if (months < 12) return formatUnit(months, 'month');
        const years = Math.round(months / 12);
        return formatUnit(years, 'year');
    },
    statusLabel(status) {
        return {
            to_do: 'To Do',
            in_progress: 'In Progress',
            in_review: 'In Review',
            completed: 'Completed',
        }[status] || status;
    },
    statusBadgeClass(status) {
        return {
            to_do: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-600',
            in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200 border-blue-200 dark:border-blue-800',
            in_review: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200 border-yellow-200 dark:border-yellow-800',
            completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200 border-green-200 dark:border-green-800',
        }[status] || 'bg-gray-100 text-gray-800 border-gray-200';
    },
    priorityLabel(priority) {
        return {
            1: 'Lowest',
            2: 'Low',
            3: 'Medium',
            4: 'High',
            5: 'Highest',
        }[Number(priority)] || 'N/A';
    },
};

function offsetDays(days) {
    const d = new Date();
    d.setUTCDate(d.getUTCDate() + days);
    return `${d.getUTCFullYear()}-${String(d.getUTCMonth() + 1).padStart(2, '0')}-${String(d.getUTCDate()).padStart(2, '0')}`;
}

function hoursAgo(hours) {
    return new Date(Date.now() - hours * 3600 * 1000);
}

function seedInitialData() {
    if (users.size > 0) return;

    // Seeded test user matching DatabaseSeeder.php
    const testUser = createUser({
        name: 'Test User',
        email: 'sheltersibanda001@gmail.com',
        password: '@Secret123',
        position: 'Lead Developer',
    });

    const sherUser = createUser({
        name: 'sher',
        email: 'sher@gmail.com',
        password: 'Secret123',
        position: 'Software Engineer',
    });

    const sampleTasks = [
        {
            title: 'Finalize Q4 sprint roadmap and backlog grooming',
            description: 'Review open engineering tickets with product leads, assign story points, and lock sprint priorities before Monday standup.',
            status: 'in_progress',
            priority: 5,
            due_date: offsetDays(2),
            created_at: hoursAgo(2),
        },
        {
            title: 'Implement role-based task authorization checks',
            description: 'Ensure every task detail, update, status patch, and deletion endpoint verifies task ownership against the authenticated user.',
            status: 'completed',
            priority: 5,
            due_date: offsetDays(-1),
            created_at: hoursAgo(6),
        },
        {
            title: 'Review pull request for dashboard pagination',
            description: 'Verify query string filters (search, status, sort) persist smoothly across page navigation links.',
            status: 'in_review',
            priority: 4,
            due_date: offsetDays(1),
            created_at: hoursAgo(12),
        },
        {
            title: 'Audit database indexes on tasks table',
            description: 'Benchmark composite index performance for (user_id, status, due_date) filtering and priority sorting.',
            status: 'to_do',
            priority: 4,
            due_date: offsetDays(-2),
            created_at: hoursAgo(18),
        },
        {
            title: 'Prepare onboarding documentation for new interns',
            description: 'Update README setup instructions, environment variable descriptions, and local testing walkthrough.',
            status: 'to_do',
            priority: 3,
            due_date: offsetDays(5),
            created_at: hoursAgo(24),
        },
        {
            title: 'Add SweetAlert2 confirmation dialogs to delete flows',
            description: 'Replace browser native confirm prompts with styled SweetAlert2 modals matching dark and light themes.',
            status: 'completed',
            priority: 3,
            due_date: offsetDays(-3),
            created_at: hoursAgo(30),
        },
        {
            title: 'Configure automated CI test workflow',
            description: 'Set up feature test suite checks for authentication, task validation, and policy enforcement on every push.',
            status: 'in_progress',
            priority: 4,
            due_date: offsetDays(4),
            created_at: hoursAgo(36),
        },
        {
            title: 'Refine responsive mobile navigation drawer',
            description: 'Check hamburger menu toggle behavior and profile dropdown alignment on small viewports.',
            status: 'in_review',
            priority: 2,
            due_date: offsetDays(6),
            created_at: hoursAgo(42),
        },
        {
            title: 'Clean up unused starter CSS classes',
            description: 'Remove redundant utility overrides and verify Tailwind purge paths cover all view templates.',
            status: 'to_do',
            priority: 1,
            due_date: null,
            created_at: hoursAgo(48),
        },
        {
            title: 'Optimize task search query matching',
            description: 'Test partial keyword matching across both task titles and multi-line descriptions.',
            status: 'completed',
            priority: 3,
            due_date: offsetDays(-4),
            created_at: hoursAgo(54),
        },
        {
            title: 'Update user profile position field validation',
            description: 'Allow optional job title updates from both registration and profile settings forms.',
            status: 'completed',
            priority: 2,
            due_date: null,
            created_at: hoursAgo(60),
        },
        {
            title: 'Schedule weekly engineering sync notes',
            description: 'Compile completed milestones, blockers, and upcoming release dates for Friday review.',
            status: 'to_do',
            priority: 2,
            due_date: offsetDays(9),
            created_at: hoursAgo(72),
        },
        {
            title: 'Verify date input formatting in edit task view',
            description: 'Ensure existing due dates pre-populate cleanly in YYYY-MM-DD format across browsers.',
            status: 'in_review',
            priority: 3,
            due_date: offsetDays(3),
            created_at: hoursAgo(84),
        },
        {
            title: 'Draft API token permissions specification',
            description: 'Document read/write scopes for external integrations accessing /api/user and task endpoints.',
            status: 'to_do',
            priority: 1,
            due_date: offsetDays(14),
            created_at: hoursAgo(96),
        },
        {
            title: 'Verify empty state UI when filters match zero tasks',
            description: 'Check table colspan alignment and reset filter button behavior when search yields no results.',
            status: 'completed',
            priority: 2,
            due_date: offsetDays(-5),
            created_at: hoursAgo(108),
        },
    ];

    const createdTestTasks = [];
    for (const item of sampleTasks) {
        createdTestTasks.push(createTask(testUser.id, item));
    }

    const sherTasks = [
        {
            title: 'Set up local development workspace',
            description: 'Configure dependencies and verify local server startup on port 3000.',
            status: 'completed',
            priority: 4,
            due_date: offsetDays(-1),
            created_at: hoursAgo(4),
        },
        {
            title: 'Create initial project task board',
            description: 'Organize deliverables into To Do, In Progress, In Review, and Completed stages.',
            status: 'in_progress',
            priority: 5,
            due_date: offsetDays(2),
            created_at: hoursAgo(10),
        },
        {
            title: 'Write unit tests for task priority sorting',
            description: 'Confirm highest priority (5) appears before lowest priority (1).',
            status: 'to_do',
            priority: 3,
            due_date: offsetDays(5),
            created_at: hoursAgo(20),
        },
        {
            title: 'Review UI accessibility contrast',
            description: 'Check badge colors and form labels for clear readability.',
            status: 'in_review',
            priority: 2,
            due_date: offsetDays(7),
            created_at: hoursAgo(32),
        },
        {
            title: 'Archive completed sprint notes',
            description: null,
            status: 'to_do',
            priority: 1,
            due_date: null,
            created_at: hoursAgo(44),
        },
    ];

    const createdSherTasks = [];
    for (const item of sherTasks) {
        createdSherTasks.push(createTask(sherUser.id, item));
    }

    // Seed realistic Tempo-style weekly worklogs for Test User across current and previous week
    const thisMonday = getMondayOfWeek(new Date());
    const lastMonday = addDaysToDateStr(thisMonday, -7);

    const testWorklogs = [
        // Monday
        { task: createdTestTasks[0], dayOffset: 0, hours: 3.5, started_at: '09:00', billable: true, description: 'Facilitated Q4 backlog grooming with product & engineering leads' },
        { task: createdTestTasks[1], dayOffset: 0, hours: 2.5, started_at: '13:30', billable: true, description: 'Implemented task ownership policy guards across CRUD routes' },
        { task: createdTestTasks[4], dayOffset: 0, hours: 1.5, started_at: '16:15', billable: false, description: 'Updated intern onboarding setup checklist and walkthrough' },

        // Tuesday
        { task: createdTestTasks[0], dayOffset: 1, hours: 2.0, started_at: '09:30', billable: true, description: 'Estimated story points and finalized sprint milestone dates' },
        { task: createdTestTasks[2], dayOffset: 1, hours: 3.0, started_at: '12:00', billable: true, description: 'Reviewed pagination query-string persistence PR & edge cases' },
        { task: createdTestTasks[6], dayOffset: 1, hours: 2.5, started_at: '15:30', billable: true, description: 'Configured automated test pipeline and lint stages' },

        // Wednesday
        { task: createdTestTasks[3], dayOffset: 2, hours: 3.5, started_at: '09:00', billable: true, description: 'Profiled slow task filter queries and benchmarked composite indexes' },
        { task: createdTestTasks[5], dayOffset: 2, hours: 2.0, started_at: '13:30', billable: true, description: 'Integrated SweetAlert2 modal confirmations for task delete actions' },
        { task: createdTestTasks[11], dayOffset: 2, hours: 1.0, started_at: '16:00', billable: false, description: 'Compiled weekly engineering sync notes and blocker summary' },

        // Thursday
        { task: createdTestTasks[1], dayOffset: 3, hours: 2.0, started_at: '09:15', billable: true, description: 'Added regression tests for unauthorized task access (403 checks)' },
        { task: createdTestTasks[6], dayOffset: 3, hours: 3.0, started_at: '11:30', billable: true, description: 'Validated CI matrix runs and artifact caching' },
        { task: createdTestTasks[7], dayOffset: 3, hours: 2.5, started_at: '15:00', billable: true, description: 'Refined mobile drawer breakpoints and dropdown keyboard focus' },

        // Friday
        { task: createdTestTasks[0], dayOffset: 4, hours: 2.0, started_at: '09:00', billable: true, description: 'Published sprint kickoff brief to engineering workspace' },
        { task: createdTestTasks[12], dayOffset: 4, hours: 1.5, started_at: '11:30', billable: false, description: 'Verified YYYY-MM-DD date formatting on edit task views' },

        // Previous week logs for week navigation verification
        { task: createdTestTasks[9], dayOffset: 0, base: lastMonday, hours: 4.0, started_at: '09:00', billable: true, description: 'Optimized multi-column search filter logic' },
        { task: createdTestTasks[10], dayOffset: 1, base: lastMonday, hours: 3.5, started_at: '10:00', billable: true, description: 'Added position validation to registration & profile forms' },
        { task: createdTestTasks[14], dayOffset: 3, base: lastMonday, hours: 3.0, started_at: '14:00', billable: false, description: 'Tested empty state table alignment across viewports' },
    ];

    for (const wl of testWorklogs) {
        if (!wl.task) continue;
        createWorklog(testUser.id, {
            task_id: wl.task.id,
            date: addDaysToDateStr(wl.base || thisMonday, wl.dayOffset),
            hours: wl.hours,
            started_at: wl.started_at,
            billable: wl.billable,
            description: wl.description,
        });
    }

    // Seed worklogs for sherUser
    const sherWorklogs = [
        { task: createdSherTasks[0], dayOffset: 0, hours: 3.0, started_at: '09:00', billable: true, description: 'Configured local Node.js runtime and verified port 3000 binding' },
        { task: createdSherTasks[1], dayOffset: 1, hours: 4.5, started_at: '10:00', billable: true, description: 'Structured initial sprint task board and priority tiers' },
        { task: createdSherTasks[2], dayOffset: 2, hours: 2.5, started_at: '14:00', billable: false, description: 'Authored test cases for task priority sorting order' },
    ];

    for (const wl of sherWorklogs) {
        if (!wl.task) continue;
        createWorklog(sherUser.id, {
            task_id: wl.task.id,
            date: addDaysToDateStr(thisMonday, wl.dayOffset),
            hours: wl.hours,
            started_at: wl.started_at,
            billable: wl.billable,
            description: wl.description,
        });
    }
}

seedInitialData();
