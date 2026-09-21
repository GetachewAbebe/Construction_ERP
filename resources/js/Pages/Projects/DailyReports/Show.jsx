import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileText,
    ArrowLeft,
    Calendar,
    Users,
    CloudSun,
    Building2,
    ShieldAlert,
    Wrench,
    Package,
    UserCheck,
    Clock,
    Printer,
    Camera,
    ExternalLink,
} from 'lucide-react';

export default function Show({ report }) {
    if (!report) {
        return null;
    }

    const reportDate = report.report_date ? new Date(report.report_date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }) : '—';

    return (
        <AuthenticatedLayout title={`Daily Report • ${report.project?.name || 'Site Progress'}`} header="Operations & Plant">
            <Head title={`DPR: ${report.project?.name || 'Site Report'}`} />

            <div className="max-w-5xl mx-auto space-y-6">
                {/* Back Link & Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/projects/daily-reports"
                            className="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-xs"
                            title="Back to Reports"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                                <Building2 className="w-3.5 h-3.5" />
                                <span>{report.project?.name || 'Construction Project'}</span>
                            </div>
                            <h1 className="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white mt-0.5">
                                Site Daily Progress Report
                            </h1>
                        </div>
                    </div>

                    <div className="flex items-center gap-2.5">
                        <div className="flex items-center gap-2 text-xs text-slate-500 font-mono bg-white dark:bg-slate-900 px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800">
                            <Calendar className="w-3.5 h-3.5 text-slate-400" />
                            <span>{reportDate}</span>
                        </div>
                        <a
                            href={`/projects/daily-reports/${report.id}/print`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition-all cursor-pointer"
                        >
                            <Printer className="w-3.5 h-3.5" />
                            <span>Print DPR Voucher</span>
                        </a>
                    </div>
                </div>

                {/* Key Metrics Grid */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold mb-1">
                            <CloudSun className="w-4 h-4 text-amber-500" />
                            <span>Weather</span>
                        </div>
                        <p className="font-extrabold text-sm text-slate-900 dark:text-white">
                            {report.weather || 'Clear / Sunny'}
                        </p>
                    </div>

                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold mb-1">
                            <Users className="w-4 h-4 text-blue-500" />
                            <span>Site Manpower</span>
                        </div>
                        <p className="font-extrabold text-sm text-slate-900 dark:text-white font-mono">
                            {report.manpower_count || 0} Trades
                        </p>
                    </div>

                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold mb-1">
                            <UserCheck className="w-4 h-4 text-emerald-500" />
                            <span>Recorded By</span>
                        </div>
                        <p className="font-extrabold text-sm text-slate-900 dark:text-white truncate">
                            {report.author?.name || 'Site Engineer'}
                        </p>
                    </div>

                    <div className="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                        <div className="flex items-center gap-2 text-slate-400 text-xs font-semibold mb-1">
                            <Clock className="w-4 h-4 text-purple-500" />
                            <span>Report ID</span>
                        </div>
                        <p className="font-extrabold text-sm text-slate-900 dark:text-white font-mono">
                            DPR-#{report.id}
                        </p>
                    </div>
                </div>

                {/* Main Content Sections */}
                <div className="space-y-4">
                    {/* Work Performed */}
                    <div className="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                        <div className="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <FileText className="w-4 h-4 text-blue-600" />
                            <span>Work Execution & Activities Performed</span>
                        </div>
                        <p className="text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-wrap pt-1">
                            {report.work_performed || 'No work activities recorded for this date.'}
                        </p>
                    </div>

                    {/* Materials & Plant Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {/* Materials Received */}
                        <div className="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                            <div className="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <Package className="w-4 h-4 text-amber-500" />
                                <span>Materials Received On Site</span>
                            </div>
                            <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed pt-1">
                                {report.materials_received || 'No materials received today.'}
                            </p>
                        </div>

                        {/* Machinery Deployed */}
                        <div className="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                            <div className="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <Wrench className="w-4 h-4 text-indigo-500" />
                                <span>Machinery & Fleet Operating</span>
                            </div>
                            <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed pt-1">
                                {report.machinery_deployed || 'Standard tooling and plant deployed.'}
                            </p>
                        </div>
                    </div>

                    {/* Safety / HSE */}
                    <div className="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                        <div className="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <ShieldAlert className="w-4 h-4 text-emerald-500" />
                            <span>Health, Safety & Environmental (HSE) Compliance</span>
                        </div>
                        <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed pt-1">
                            {report.safety_incidents || 'Zero safety incidents or hazards reported.'}
                        </p>
                    </div>

                    {/* Visual Progress & Site Photos */}
                    {Array.isArray(report.photos) && report.photos.length > 0 && (
                        <div className="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <Camera className="w-4 h-4 text-teal-500" />
                                    <span>Field Evidence & Site Photos ({report.photos.length})</span>
                                </div>
                                <span className="text-[11px] text-slate-400">Click photo to view full size</span>
                            </div>
                            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                {report.photos.map((photo, idx) => (
                                    <a
                                        key={idx}
                                        href={photo}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="group relative block aspect-video rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 hover:ring-2 hover:ring-teal-500 transition-all bg-slate-100 dark:bg-slate-800"
                                    >
                                        <img
                                            src={photo}
                                            alt={`Site evidence ${idx + 1}`}
                                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                        <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2 text-white text-[10px] font-bold">
                                            <span>Photo #{idx + 1}</span>
                                        </div>
                                    </a>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* Footer Navigation */}
                <div className="pt-2 flex items-center justify-between">
                    <Link
                        href="/projects/daily-reports"
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs border border-slate-200 dark:border-slate-800 shadow-xs transition-colors"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        <span>Return to DPR Registry</span>
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
