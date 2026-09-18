import { ArrowUpRight, ShieldCheck } from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function RiskGauge({
    score = 80,
    status = 'Medium',
    title = 'Risk Distribution',
    healthPercent = 94,
    href = '#',
}) {
    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm h-full flex flex-col justify-between">
            {/* Header with Title & Link */}
            <div className="flex items-center justify-between gap-2 mb-2">
                <div>
                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        {title}
                    </h3>
                    <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Operational &amp; project safety telemetry.
                    </p>
                </div>
                {href && (
                    <Link
                        href={href}
                        className="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        title="View details"
                    >
                        <ArrowUpRight className="w-4 h-4" />
                    </Link>
                )}
            </div>

            {/* Circular Arc Meter Display */}
            <div className="relative flex flex-col items-center justify-center my-auto py-4">
                <div className="relative w-64 h-48 flex items-center justify-center">
                    <svg viewBox="0 0 240 180" className="w-full h-full select-none">
                        {/* Segment 1: Low (Green) */}
                        <path
                            d="M 38 152 A 82 82 0 0 1 85 42"
                            fill="none"
                            stroke="#10b981"
                            strokeWidth="15"
                            strokeLinecap="round"
                        />
                        {/* Segment 2: Medium (Yellow/Amber) */}
                        <path
                            d="M 96 35 A 82 82 0 0 1 144 35"
                            fill="none"
                            stroke="#f59e0b"
                            strokeWidth="15"
                            strokeLinecap="round"
                        />
                        {/* Segment 3: High (Orange) */}
                        <path
                            d="M 155 42 A 82 82 0 0 1 202 100"
                            fill="none"
                            stroke="#f97316"
                            strokeWidth="15"
                            strokeLinecap="round"
                        />
                        {/* Segment 4: Critical (Red) */}
                        <path
                            d="M 205 112 A 82 82 0 0 1 202 152"
                            fill="none"
                            stroke="#ef4444"
                            strokeWidth="15"
                            strokeLinecap="round"
                        />
                    </svg>

                    {/* Centered Score & Level */}
                    <div className="absolute inset-0 flex flex-col items-center justify-center pt-8 pointer-events-none">
                        <span className="text-4xl sm:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                            {score}
                        </span>
                        <span className="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">
                            {status}
                        </span>
                    </div>
                </div>

                {/* Status Indicator Pill */}
                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-[11px] font-bold text-emerald-700 dark:text-emerald-300">
                    <ShieldCheck className="w-3.5 h-3.5" />
                    <span>Safe Operational Index ({healthPercent}%)</span>
                </div>
            </div>

            {/* Legend Pills at Bottom */}
            <div className="flex items-center justify-center gap-3 sm:gap-5 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-semibold">
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-emerald-500" />
                    <span className="text-slate-600 dark:text-slate-400">Low</span>
                </div>
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-amber-500" />
                    <span className="text-slate-600 dark:text-slate-400">Medium</span>
                </div>
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-orange-500" />
                    <span className="text-slate-600 dark:text-slate-400">High</span>
                </div>
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-rose-500" />
                    <span className="text-slate-600 dark:text-slate-400">Critical</span>
                </div>
            </div>
        </div>
    );
}
