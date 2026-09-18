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
    ShieldAlert
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

                                return (
                                    <div
                                        key={item.id}
                                        className={`p-5 sm:p-6 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4 ${
                                            isUnread
                                                ? 'bg-blue-50/40 dark:bg-blue-950/20'
                                                : 'hover:bg-slate-50/60 dark:hover:bg-slate-800/30'
                                        }`}
                                    >
                                        <div className="flex items-start gap-4">
                                            <div className={`w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 ${
                                                isUnread
                                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
                                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-400'
                                            }`}>
                                                <Bell className="w-5 h-5" />
                                            </div>

                                            <div>
                                                <div className="flex items-center gap-2.5">
                                                    <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                        {d.title || 'System Alert'}
                                                    </h3>
                                                    {isUnread && (
                                                        <span className="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold text-[10px]">
                                                            New
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                                    {d.message || 'Workflow notification alert.'}
                                                </p>
                                                <div className="flex items-center gap-2 mt-2 text-[11px] text-slate-400">
                                                    <Clock className="w-3.5 h-3.5" />
                                                    <span>{item.created_at ? new Date(item.created_at).toLocaleString() : 'Recent'}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-2.5 shrink-0 self-end sm:self-center">
                                            {isUnread && (
                                                <button
                                                    onClick={() => handleMarkAsRead(item.id)}
                                                    className="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors cursor-pointer"
                                                >
                                                    Mark as read
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
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
