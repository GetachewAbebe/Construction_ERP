import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Settings, Save, Building2, Mail, Phone, MapPin, Globe, DollarSign } from 'lucide-react';

export default function Index({ settings = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        company_name: settings.company_name || 'Natanem Engineering',
        company_email: settings.company_email || 'info@natanem.com',
        company_phone: settings.company_phone || '+251 11 XXX XXXX',
        company_address: settings.company_address || 'Addis Ababa, Ethiopia',
        timezone: settings.timezone || 'Africa/Addis_Ababa',
        date_format: settings.date_format || 'Y-m-d',
        currency: settings.currency || 'ETB',
        items_per_page: settings.items_per_page || '15',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/system-settings');
    };

    return (
        <AuthenticatedLayout title="System Configuration" header="Administration">
            <div className="max-w-4xl mx-auto space-y-6">
                <div className="flex items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Global Enterprise Parameters
                        </h1>
                        <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Manage organization branding, base accounting currency, timezone, and portal configurations.
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-6">
                        <h2 className="text-xs font-bold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 dark:border-slate-800">
                            1. Corporate Identity & Contact Details
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Entity Legal Name
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={data.company_name}
                                    onChange={(e) => setData('company_name', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                                {errors.company_name && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.company_name}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Primary Corporate Email
                                </label>
                                <input
                                    type="email"
                                    required
                                    value={data.company_email}
                                    onChange={(e) => setData('company_email', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                                {errors.company_email && (
                                    <p className="mt-1 text-[11px] text-rose-500">{errors.company_email}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Telephone Number
                                </label>
                                <input
                                    type="text"
                                    value={data.company_phone}
                                    onChange={(e) => setData('company_phone', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Headquarters Physical Address
                                </label>
                                <input
                                    type="text"
                                    value={data.company_address}
                                    onChange={(e) => setData('company_address', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                            </div>
                        </div>

                        <h2 className="text-xs font-bold text-slate-400 uppercase tracking-wider pt-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                            2. Regional & Accounting Locale
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Base Accounting Currency
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={data.currency}
                                    onChange={(e) => setData('currency', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    System Timezone
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={data.timezone}
                                    onChange={(e) => setData('timezone', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Date Format Specification
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={data.date_format}
                                    onChange={(e) => setData('date_format', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20 font-mono"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Default Pagination Size
                                </label>
                                <input
                                    type="number"
                                    required
                                    min={5}
                                    max={100}
                                    value={data.items_per_page}
                                    onChange={(e) => setData('items_per_page', e.target.value)}
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-900/20 font-mono"
                                />
                            </div>
                        </div>

                        <div className="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer disabled:opacity-50"
                            >
                                <Save className="w-4 h-4" />
                                <span>{processing ? 'Saving...' : 'Save Configuration Matrix'}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
