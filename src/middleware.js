import { findUserById, serializeUser, viewHelpers } from './store.js';

export function methodOverrideMiddleware(req, res, next) {
    if (req.method === 'POST' && req.body && typeof req.body._method === 'string') {
        const method = req.body._method.toUpperCase();
        if (['PUT', 'PATCH', 'DELETE'].includes(method)) {
            req.method = method;
        }
    }
    next();
}

export function attachViewLocals(req, res, next) {
    if (req.session && req.secure) {
        req.session.cookie.sameSite = 'none';
    }
    const rawUser = req.session?.userId ? findUserById(req.session.userId) : null;
    req.user = rawUser;

    const flash = req.session?.flash || {};
    if (req.session) {
        delete req.session.flash;
    }

    res.locals.appName = process.env.APP_NAME || 'Task Manager';
    res.locals.user = serializeUser(rawUser);
    res.locals.currentPath = req.path;
    res.locals.query = req.query || {};
    res.locals.success = flash.success || null;
    res.locals.statusMessage = flash.status || null;
    res.locals.errors = flash.errors || {};
    res.locals.oldInput = flash.oldInput || {};
    res.locals.old = (field, fallback = '') => {
        if (flash.oldInput && Object.prototype.hasOwnProperty.call(flash.oldInput, field)) {
            return flash.oldInput[field];
        }
        return fallback ?? '';
    };
    res.locals.h = viewHelpers;
    res.locals.currentYear = new Date().getFullYear();

    next();
}

export function setFlash(req, data) {
    if (!req.session) return;
    req.session.flash = {
        ...(req.session.flash || {}),
        ...data,
    };
}

export function requireAuth(req, res, next) {
    if (req.user) {
        return next();
    }
    if (req.path.startsWith('/api/')) {
        return res.status(401).json({ message: 'Unauthenticated.' });
    }
    return res.redirect('/login');
}

export function redirectIfAuthenticated(req, res, next) {
    if (req.user) {
        return res.redirect('/dashboard');
    }
    return next();
}
