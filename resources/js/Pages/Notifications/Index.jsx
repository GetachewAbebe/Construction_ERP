import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Bell,
    CheckCircle2,
    Clock,
    AlertCircle,
    ArrowRight,
    Check,
    Banknote,
    Package,
    Calendar,
    ExternalLink
} from 'lucide-react';

export default function Index({ notifications = { data: [] } }) {
    const list = notifications?.data || [];

    const handleMarkAsRead = (id) => {
        router.post(`/notifications/${id}/mark-as-read`, {}, {
            preserveScroll: true,
        });
    };

    const handleMarkAllAsRead = (e) => {
        e.preventDefault();
        router.post('/notifications/mark-all-as-read', {}, {
            preserveScroll: true,
        });
    };

    const getNotificationDetails = (item) => {
        const d = item.data || {};
        const type = (d.type || '').toLowerCase();
        const title = (d.title || '').toLowerCase();
        const message = (d.message || '').toLowerCase();

        // 1. Expense Requests / Approvals
        if (
            type === 'expense_request' ||
            title.includes('expense request') ||
            message.includes('requires your approval') ||
            (!type && title.includes('expense') && message.includes('approval'))
        ) {
            return {
                icon: Banknote,
                iconClass: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
                badge: 'Expense Approval',
                badgeClass: 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60',
                actionLabel: 'Review & Approve',
                actionUrl: `/notifications/${item.id}/open`,
                isApproval: true,
                btnClass: 'bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-sm shadow-amber-500/20',
            };
        }

        // 2. Inventory Loan / Item Requests
        if (
            type === 'inventory_request' ||
            title.includes('item request') ||
            title.includes('loan request') ||
            (!type && title.includes('requested') && message.includes('item'))
        ) {
            return {
                icon: Package,
                iconClass: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20',
                badge: 'Item Request',
                badgeClass: 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60',
                actionLabel: 'Review & Approve',
                actionUrl: `/notifications/${item.id}/open`,
                isApproval: true,
                btnClass: 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-600/20',
            };
        }

        // 3. Leave Requests
        if (type === 'leave_request' || title.includes('leave request')) {
            return {
                icon: Calendar,
                iconClass: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20',
                badge: 'Leave Request',
                badgeClass: 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60',
                actionLabel: 'Review & Approve',
                actionUrl: `/notifications/${item.id}/open`,
                isApproval: true,
                btnClass: 'bg-purple-600 hover:bg-purple-700 text-white shadow-sm shadow-purple-600/20',
            };
        }

        // 4. Low stock
        if (type === 'inventory_low_stock' || title.includes('stock')) {
            return {
                icon: AlertCircle,
                iconClass: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
                badge: 'Stock Alert',
                badgeClass: 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60',
                actionLabel: 'Restock Inventory',
                actionUrl: `/notifications/${item.id}/open`,
                isApproval: false,
                btnClass: 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm shadow-rose-600/20',
            };
        }

        // 5. General status changes or alerts
        const hasUrl = Boolean(d.url);
        return {
            icon: Bell,
            iconClass: 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20',
            badge: 'Notice',
            badgeClass: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
            actionLabel: hasUrl ? 'View Details' : null,
            actionUrl: hasUrl ? `/notifications/${item.id}/open` : null,
            isApproval: false,
            btnClass: 'bg-slate-800 hover:bg-slate-900 text-white dark:bg-slate-700 dark:hover:bg-slate-600',
        };
    };

    return (
        <AuthenticatedLayout title="Notifications">
            <div className="space-y-6">
                {/* Header Title Bar */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                            <Bell className="w-6 h-6 text-blue-600" />
                            <span>Notifications &amp; Activity</span>
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            System alerts, workflow requests, and status dispatches.
                        </p>
                    </div>

                    <div className="flex items-center gap-3">
                        <button
                            onClick={handleMarkAllAsRead}
                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-800 shadow-sm transition-all cursor-pointer"
                        >
                            <Check className="w-4 h-4 text-emerald-500" />
                            <span>Mark all as read</span>
                        </button>
                    </div>
                </div>

                {/* Notifications Card */}
                <div className="rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 shadow-sm overflow-hidden">
                    {list.length > 0 ? (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800/80">
                            {list.map((item) => {
                                const d = item.data || {};
                                const isUnread = !item.read_at;
                                const details = getNotificationDetails(item);
                                const IconComponent = details.icon;

                                return (
                                    <div
                                        key={item.id}
                                        className={`group p-5 sm:p-6 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 ${
                                            isUnread
                                                ? 'bg-blue-50/40 dark:bg-blue-950/20'
                                                : 'hover:bg-slate-50/60 dark:hover:bg-slate-800/30'
                                        }`}
                                    >
                                        <div className="flex items-start gap-4 min-w-0 flex-1">
                                            {/* Icon */}
                                            <div className={`w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 ${details.iconClass}`}>
                                                <IconComponent className="w-5 h-5" />
                                            </div>

                                            {/* Content */}
                                            <div className="min-w-0 flex-1">
                                                <div className="flex flex-wrap items-center gap-2">
                                                    {details.actionUrl ? (
                                                        <Link
                                                            href={details.actionUrl}
                                                            className="font-bold text-sm text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-1.5"
                                                        >
                                                            <span>{d.title || 'System Alert'}</span>
                                                            <ExternalLink className="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500 transition-colors" />
                                                        </Link>
                                                    ) : (
                                                        <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                            {d.title || 'System Alert'}
                                                        </h3>
                                                    )}

                                                    {isUnread && (
                                                        <span className="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold text-[10px]">
                                                            New
                                                        </span>
                                                    )}

                                                    {details.badge && (
                                                        <span className={`px-2 py-0.5 rounded-full text-[10px] font-semibold ${details.badgeClass}`}>
                                                            {details.badge}
                                                        </span>
                                                    )}
                                                </div>

                                                <p className="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                                    {d.message || 'Workflow notification alert.'}
                                                </p>

                                                <div className="flex items-center gap-2 mt-2.5 text-[11px] text-slate-400">
                                                    <Clock className="w-3.5 h-3.5" />
                                                    <span>{item.created_at ? new Date(item.created_at).toLocaleString() : 'Recent'}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Actions */}
                                        <div className="flex items-center gap-2.5 shrink-0 self-end sm:self-center">
                                            {details.actionUrl && (
                                                <Link
                                                    href={details.actionUrl}
                                                    className={`inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer ${details.btnClass}`}
                                                >
                                                    <span>{details.actionLabel}</span>
                                                    <ArrowRight className="w-3.5 h-3.5" />
                                                </Link>
                                            )}

                                            {isUnread && (
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        handleMarkAsRead(item.id);
                                                    }}
                                                    className="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors cursor-pointer"
                                                    title="Mark as read without leaving"
                                                >
                                                    Mark read
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="py-16 px-6 text-center">
                            <div className="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3.5">
                                <Bell className="w-7 h-7" />
                            </div>
                            <h3 className="font-bold text-slate-900 dark:text-white text-base">
                                All caught up!
                            </h3>
                            <p className="text-xs text-slate-400 dark:text-slate-500 max-w-sm mx-auto mt-1">
                                You have no new unread alerts or pending workflow notifications.
                            </p>
                        </div>
                    )}

                    {/* Pagination */}
                    {notifications?.links && notifications.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>
                                Showing {notifications.from || 0} to {notifications.to || 0} of {notifications.total || 0} notifications
                            </div>
                            <div className="flex items-center gap-1">
                                {notifications.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        preserveScroll
                                        className={`px-2.5 py-1 rounded-lg text-xs font-semibold ${
                                            link.active
                                                ? 'bg-blue-900 text-white'
                                                : link.url
                                                ? 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                : 'text-slate-300 dark:text-slate-700 cursor-not-allowed'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
