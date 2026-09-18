import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Mail, Bell, MessageSquare } from 'lucide-react';

export default function Edit({ notificationTemplate }) {
    const rawVars = Array.isArray(notificationTemplate.variables)
        ? notificationTemplate.variables.join(', ')
        : (notificationTemplate.variables || '');

    const { data, setData, put, processing, errors } = useForm({
        key: notificationTemplate.key || '',
        name: notificationTemplate.name || '',
        subject: notificationTemplate.subject || '',
        body: notificationTemplate.body || '',
        type: notificationTemplate.type || 'email',
        variables: rawVars,
        is_active: Boolean(notificationTemplate.is_active),
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/admin/notification-templates/${notificationTemplate.id}`);
    };

    return (
        <AuthenticatedLayout title={`Edit Template • ${notificationTemplate.name}`} header="System Administration">
            <Head title={`Edit Template: ${notificationTemplate.name}`} />

            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/admin/notification-templates"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Edit Template: {notificationTemplate.name}
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                System key: <span className="font-mono font-semibold">{notificationTemplate.key}</span>
                            </p>
                        </div>
                    </div>

                    <Link
                        href={`/admin/notification-templates/${notificationTemplate.id}/preview`}
                        className="px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50"
                    >
                        Preview Output
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    System Key <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={data.key}
                                    onChange={e => setData('key', e.target.value)}
                                    className={`w-full font-mono text-xs rounded-xl border ${
                                        errors.key ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                    } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                                />
                                {errors.key && <p className="text-rose-500 text-[11px] mt-1">{errors.key}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Template Title <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className={`w-full text-xs rounded-xl border ${
                                        errors.name ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                    } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                                />
                                {errors.name && <p className="text-rose-500 text-[11px] mt-1">{errors.name}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Delivery Channel <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    value={data.type}
                                    onChange={e => setData('type', e.target.value)}
                                    className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="email">Email Dispatch</option>
                                    <option value="notification">In-App Notification Bell</option>
                                    <option value="sms">SMS Text Broadcast</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Subject Header (For Emails)
                                </label>
                                <input
                                    type="text"
                                    value={data.subject}
                                    onChange={e => setData('subject', e.target.value)}
                                    className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Allowed Variables (Comma-separated)
                            </label>
                            <input
                                type="text"
                                value={data.variables}
                                onChange={e => setData('variables', e.target.value)}
                                className="w-full text-xs font-mono rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Message Body <span className="text-rose-500">*</span>
                            </label>
                            <textarea
                                rows={7}
                                required
                                value={data.body}
                                onChange={e => setData('body', e.target.value)}
                                className={`w-full text-xs font-mono rounded-xl border ${
                                    errors.body ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                } bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                            />
                            {errors.body && <p className="text-rose-500 text-[11px] mt-1">{errors.body}</p>}
                        </div>

                        <label className="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={e => setData('is_active', e.target.checked)}
                                className="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span>Active template (operational for immediate delivery)</span>
                        </label>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href="/admin/notification-templates"
                            className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Saving...' : 'Save Template Changes'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
