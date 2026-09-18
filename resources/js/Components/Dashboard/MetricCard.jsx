import { ArrowUpRight } from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function MetricCard({
    title,
    value,
    trend = '2.3%',
    trendPositive = true,
    comparisonText = 'vs last month',
    type = 'bars', // 'bars' | 'stepped' | 'progress' | 'arc'
    progressPercent = 65,
    href = '#',
}) {
    const formattedTrend = `${trendPositive ? '+' : '-'}${String(trend).replace(/^[+-]/, '')}`;

    return (
        <div className="group relative rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 h-full flex flex-col justify-between">
            {/* Top Bar: Title & Link */}
            <div className="flex items-center justify-between gap-2">
                <span className="text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-400">
                    {title}
                </span>
                {href ? (
                    <Link
                        href={href}
                        className="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        title="View details"
                    >
                        <ArrowUpRight className="w-4 h-4" />
                    </Link>
                ) : (
                    <span className="w-7 h-7 rounded-full flex items-center justify-center text-slate-300 dark:text-slate-700">
                        <ArrowUpRight className="w-4 h-4" />
                    </span>
                )}
            </div>

            {/* Middle: Value & Visualizer */}
            <div className="my-3 flex items-end justify-between gap-3">
                <div className="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white truncate">
                    {value}
                </div>

                {/* Micro-visualizer variants */}
                <div className="shrink-0 pb-1">
                    {type === 'bars' && (
                        /* Vertical equalizer sparkline */
                        <div className="flex items-end gap-1 h-8">
                            {[35, 60, 45, 80, 55, 95, 70, 85, 65, 90, 80].map((h, i) => (
                                <span
                                    key={i}
                                    style={{ height: `${h}%` }}
                                    className={`w-1 rounded-full transition-all duration-300 ${
                                        i >= 8
                                            ? 'bg-blue-600 dark:bg-blue-500'
                                            : 'bg-blue-900/25 dark:bg-blue-400/20'
                                    }`}
                                />
                            ))}
                        </div>
                    )}

                    {type === 'stepped' && (
                        /* Stepped amber bars */
                        <div className="flex items-end gap-1 h-8">
                            {[25, 40, 35, 60, 75, 95, 60].map((h, i) => (
                                <span
                                    key={i}
                                    style={{ height: `${h}%` }}
                                    className={`w-1.5 rounded-sm transition-all duration-300 ${
                                        i >= 4
                                            ? 'bg-amber-500 shadow-sm'
                                            : 'bg-amber-500/25 dark:bg-amber-400/20'
                                    }`}
                                />
                            ))}
                        </div>
                    )}

                    {type === 'progress' && (
                        /* Horizontal pill progress gauge */
                        <div className="w-24 sm:w-28 h-3.5 rounded-full bg-slate-100 dark:bg-slate-800 p-0.5 overflow-hidden flex items-center border border-slate-200/50 dark:border-slate-700/50">
                            <div
                                style={{ width: `${Math.min(100, Math.max(12, progressPercent))}%` }}
                                className="h-full rounded-full bg-emerald-500 shadow-sm transition-all duration-500"
                            />
                        </div>
                    )}

                    {type === 'arc' && (
                        /* Mini Arc gauge */
                        <div className="relative w-14 h-7 overflow-hidden flex items-end justify-center">
                            <svg viewBox="0 0 44 22" className="w-14 h-7">
                                <path
                                    d="M 4 20 A 18 18 0 0 1 40 20"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="4.5"
                                    className="text-slate-100 dark:text-slate-800"
                                />
                                <path
                                    d="M 4 20 A 18 18 0 0 1 40 20"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="4.5"
                                    strokeDasharray="56.5"
                                    strokeDashoffset="22"
                                    strokeLinecap="round"
                                    className="text-rose-500 drop-shadow-sm"
                                />
                            </svg>
                        </div>
                    )}
                </div>
            </div>

            {/* Bottom: Trend Indicator */}
            <div className="flex items-center gap-1.5 text-xs font-semibold">
                <span
                    className={`inline-flex items-center ${
                        trendPositive
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : 'text-rose-600 dark:text-rose-400'
                    }`}
                >
                    {formattedTrend}
                </span>
                <span className="text-slate-400 dark:text-slate-500 font-normal text-[11px]">
                    {comparisonText}
                </span>
            </div>
        </div>
    );
}
