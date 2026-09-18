import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Edit3, Mail, MessageSquare, Bell, Code } from 'lucide-react';

export default function Preview({ notificationTemplate, renderedBody, sampleData = {} }) {
    return (
        <AuthenticatedLayout title={`Preview • ${notificationTemplate.name}`} header="System Administration">
            <Head title={`Template Preview: ${notificationTemplate.name}`} />

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
                                Live Output Preview: {notificationTemplate.name}
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Simulated message dispatch with interpolated sample values.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={`/admin/notification-templates/${notificationTemplate.id}/edit`}
                        className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm"
                    >
                        <Edit3 className="w-3.5 h-3.5" />
                        <span>Edit Template</span>
                    </Link>
                </div>

                {/* Rendered Message Card */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div className="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-wrap items-center gap-3 text-xs">
                        <span className="font-mono text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">
                            {notificationTemplate.key}
                        </span>
                        <span className="uppercase font-bold px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-800 dark:text-blue-300">
                            {notificationTemplate.type}
                        </span>
                        {notificationTemplate.subject && (
                            <div className="text-slate-700 dark:text-slate-300 font-medium">
                                Subject: <span className="font-bold">{notificationTemplate.subject}</span>
                            </div>
                        )}
                    </div>

                    <div className="p-6">
                        <div className="whitespace-pre-wrap rounded-xl bg-slate-50 dark:bg-slate-800/50 p-5 font-mono text-xs sm:text-sm text-slate-900 dark:text-slate-100 leading-relaxed border border-slate-100 dark:border-slate-800">
                            {renderedBody}
                        </div>
                    </div>
                </div>

                {/* Interpolated Sample Variables */}
                {Object.keys(sampleData).length > 0 && (
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-3">
                        <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <Code className="w-3.5 h-3.5 text-blue-600" />
                            <span>Interpolated Sample Variables</span>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            {Object.entries(sampleData).map(([k, v]) => (
                                <div key={k} className="p-2 rounded-lg bg-slate-50 dark:bg-slate-800/40 font-mono text-xs flex items-center justify-between">
                                    <span className="text-slate-400">{`{{ ${k} }}`}</span>
                                    <span className="text-blue-600 dark:text-blue-400 font-bold">{v}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
