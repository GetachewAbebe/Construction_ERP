import { Link } from '@inertiajs/react';
import { ArrowUpRight, Building2, HardHat, Wrench, ShieldCheck, MapPin } from 'lucide-react';

export default function ProjectVelocityCard({
    projects = [],
    fleetStats = { total: 5, operational: 4, maintenance: 1, standby: 0 },
    title = 'Site Execution & Project Health',
    href = '/finance/projects',
}) {
    // Default fallback projects if none loaded
    const displayProjects = projects.length > 0 ? projects.slice(0, 3) : [
        {
            id: 1,
            name: 'Bole Road Rejuvenation',
            location: 'Addis Ababa',
            spent: 24.2,
            budget: 50.0,
            usage_pct: 48.4,
            status_label: 'On Track',
        },
        {
            id: 2,
            name: 'Derba Site Expansion',
            location: 'Derba',
            spent: 9.8,
            budget: 15.0,
            usage_pct: 65.3,
            status_label: 'On Track',
        },
        {
            id: 3,
            name: 'Waserbi Warehouse',
            location: 'Waserbi',
            spent: 19.5,
            budget: 25.0,
            usage_pct: 78.0,
            status_label: 'Caution',
        },
    ];

    const totalFleet = fleetStats.total || 5;
    const operationalFleet = fleetStats.operational || 4;
    const maintenanceFleet = fleetStats.maintenance || 1;
    const fleetReadyPct = totalFleet > 0 ? Math.round((operationalFleet / totalFleet) * 100) : 100;

    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm h-full flex flex-col justify-between gap-5">
            {/* Header */}
            <div>
                <div className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <Building2 className="w-4 h-4" />
                        </div>
                        <div>
                            <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                {title}
                            </h3>
                            <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                Real-time site budget execution &amp; fleet readiness.
                            </p>
                        </div>
                    </div>
                    {href && (
                        <Link
                            href={href}
                            className="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0"
                            title="View all construction projects"
                        >
                            <ArrowUpRight className="w-4 h-4" />
                        </Link>
                    )}
                </div>
            </div>

            {/* Active Projects Execution Bars */}
            <div className="space-y-4 my-auto">
                {displayProjects.map((project) => {
                    const pct = Number(project.usage_pct || 0);
                    const isCaution = pct >= 75 && pct < 90;
                    const isCritical = pct >= 90;

                    return (
                        <div key={project.id || project.name} className="space-y-1.5 group">
                            {/* Title & Location Tag */}
                            <div className="flex items-center justify-between text-xs">
                                <div className="flex items-center gap-1.5 min-w-0 pr-2">
                                    <span className="font-bold text-slate-800 dark:text-slate-200 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {project.name}
                                    </span>
                                </div>
                                <span className="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full shrink-0">
                                    <MapPin className="w-2.5 h-2.5" />
                                    <span>{project.location}</span>
                                </span>
                            </div>

                            {/* Two-Tone Execution Progress Bar */}
                            <div className="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <div
                                    className={`h-full rounded-full transition-all duration-500 ${
                                        isCritical
                                            ? 'bg-rose-500'
                                            : isCaution
                                            ? 'bg-amber-500'
                                            : 'bg-emerald-500'
                                    }`}
                                    style={{ width: `${Math.min(100, Math.max(8, pct))}%` }}
                                />
                            </div>

                            {/* Financial Spent vs Budget & Percentage Badge */}
                            <div className="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                <span>
                                    ETB {project.spent}M <span className="text-slate-400">/ {project.budget}M spent</span>
                                </span>
                                <span
                                    className={`font-mono font-bold text-[10px] px-1.5 py-0.2 rounded ${
                                        isCritical
                                            ? 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40'
                                            : isCaution
                                            ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40'
                                            : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40'
                                    }`}
                                >
                                    {pct}% · {project.status_label || (isCritical ? 'Critical' : isCaution ? 'Caution' : 'On Track')}
                                </span>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Heavy Plant Machinery Fleet Availability Footer */}
            <div className="pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <Link
                    href="/operations/equipment"
                    className="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-blue-50/50 dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-700/60 transition-colors flex items-center justify-between text-xs group"
                    title="Inspect fleet preventive maintenance & status"
                >
                    <div className="flex items-center gap-2">
                        <div className="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <HardHat className="w-3.5 h-3.5" />
                        </div>
                        <div>
                            <span className="font-bold text-slate-800 dark:text-slate-200 block text-[11px] group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                Plant Fleet: {operationalFleet}/{totalFleet} Active ({fleetReadyPct}%)
                            </span>
                        </div>
                    </div>

                    <div className="flex items-center gap-1.5 text-[10px]">
                        {maintenanceFleet > 0 ? (
                            <span className="px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800/60">
                                {maintenanceFleet} in Service
                            </span>
                        ) : (
                            <span className="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800/60">
                                100% Operational
                            </span>
                        )}
                        <ArrowUpRight className="w-3 h-3 text-slate-400 group-hover:text-blue-600 transition-colors" />
                    </div>
                </Link>
            </div>
        </div>
    );
}
