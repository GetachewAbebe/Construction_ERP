import { useState } from 'react';

const DEFAULT_PALETTE = [
    '#3b82f6', // blue-500
    '#10b981', // emerald-500
    '#f59e0b', // amber-500
    '#8b5cf6', // purple-500
    '#ec4899', // pink-500
    '#06b6d4', // cyan-500
    '#f97316', // orange-500
    '#64748b', // slate-500
];

export default function DonutBreakdownChart({
    title = 'Distribution Breakdown',
    subtitle = 'Category allocation summary',
    data = [],
    valueKey = 'total',
    labelKey = 'category',
    centerLabel = 'Total',
    currency = '',
    colors = DEFAULT_PALETTE,
    emptyMessage = 'No data records to display.',
}) {
    const [hoveredIdx, setHoveredIdx] = useState(null);

    const safeData = Array.isArray(data) ? data.filter(d => Number(d[valueKey] || 0) > 0) : [];
    const totalVal = safeData.reduce((acc, curr) => acc + Number(curr[valueKey] || 0), 0);

    const size = 220;
    const strokeWidth = 28;
    const radius = (size - strokeWidth) / 2;
    const circumference = 2 * Math.PI * radius;

    let cumulativeAngle = 0;
    const segments = safeData.map((item, idx) => {
        const val = Number(item[valueKey] || 0);
        const fraction = totalVal > 0 ? val / totalVal : 0;
        const dashLength = fraction * circumference;
        const dashOffset = circumference - dashLength;
        const rotation = (cumulativeAngle / totalVal) * 360 - 90;
        cumulativeAngle += val;

        return {
            ...item,
            index: idx,
            val,
            fraction,
            pct: (fraction * 100).toFixed(1),
            color: colors[idx % colors.length],
            dashLength,
            dashOffset,
            rotation,
        };
    });

    const activeItem = hoveredIdx !== null ? segments[hoveredIdx] : null;

    const formatVal = (val) => {
        if (currency) {
            return `${currency} ${Number(val).toLocaleString(undefined, { maximumFractionDigits: 1 })}`;
        }
        return Number(val).toLocaleString();
    };

    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between h-full">
            {/* Header */}
            <div className="mb-4">
                <h3 className="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight">
                    {title}
                </h3>
                {subtitle && (
                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {subtitle}
                    </p>
                )}
            </div>

            {safeData.length === 0 ? (
                <div className="py-12 text-center text-xs text-slate-400 dark:text-slate-500">
                    {emptyMessage}
                </div>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center my-auto">
                    {/* SVG Donut Circle */}
                    <div className="sm:col-span-6 flex items-center justify-center relative">
                        <svg
                            viewBox={`0 0 ${size} ${size}`}
                            className="w-44 h-44 sm:w-48 sm:h-48 transform -rotate-90 select-none"
                        >
                            {/* Background Track */}
                            <circle
                                cx={size / 2}
                                cy={size / 2}
                                r={radius}
                                fill="transparent"
                                stroke="currentColor"
                                strokeWidth={strokeWidth}
                                className="text-slate-100 dark:text-slate-800/60"
                            />

                            {/* Donut Segments */}
                            {segments.map((seg) => {
                                const isHovered = hoveredIdx === seg.index;
                                return (
                                    <circle
                                        key={seg.index}
                                        cx={size / 2}
                                        cy={size / 2}
                                        r={radius}
                                        fill="transparent"
                                        stroke={seg.color}
                                        strokeWidth={isHovered ? strokeWidth + 4 : strokeWidth}
                                        strokeDasharray={`${seg.dashLength} ${circumference}`}
                                        strokeDashoffset={-((seg.rotation + 90) / 360) * circumference}
                                        className="transition-all duration-300 cursor-pointer"
                                        onMouseEnter={() => setHoveredIdx(seg.index)}
                                        onMouseLeave={() => setHoveredIdx(null)}
                                        style={{
                                            filter: isHovered ? 'drop-shadow(0 0 6px rgba(0,0,0,0.25))' : 'none',
                                            transformOrigin: `${size / 2}px ${size / 2}px`,
                                        }}
                                    />
                                );
                            })}
                        </svg>

                        {/* Center Metric */}
                        <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-4">
                            <span className="text-[11px] uppercase tracking-wider font-bold text-slate-400">
                                {activeItem ? (activeItem[labelKey] || 'Selected') : centerLabel}
                            </span>
                            <span className="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white font-mono leading-tight mt-0.5 truncate max-w-[120px]">
                                {activeItem ? formatVal(activeItem.val) : formatVal(totalVal)}
                            </span>
                            <span className="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                {activeItem ? `${activeItem.pct}% share` : `${safeData.length} categories`}
                            </span>
                        </div>
                    </div>

                    {/* Legend Items */}
                    <div className="sm:col-span-6 space-y-2 max-h-56 overflow-y-auto pr-1">
                        {segments.map((seg) => {
                            const isHovered = hoveredIdx === seg.index;
                            return (
                                <div
                                    key={seg.index}
                                    onMouseEnter={() => setHoveredIdx(seg.index)}
                                    onMouseLeave={() => setHoveredIdx(null)}
                                    className={`flex items-center justify-between p-2 rounded-xl text-xs transition-colors cursor-pointer ${
                                        isHovered
                                            ? 'bg-slate-100 dark:bg-slate-800 font-semibold'
                                            : 'hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-700 dark:text-slate-300'
                                    }`}
                                >
                                    <div className="flex items-center gap-2 min-w-0 pr-2">
                                        <span
                                            className="w-2.5 h-2.5 rounded-full shrink-0"
                                            style={{ backgroundColor: seg.color }}
                                        />
                                        <span className="truncate font-medium text-slate-800 dark:text-slate-200">
                                            {seg[labelKey] || 'Other'}
                                        </span>
                                    </div>
                                    <div className="text-right shrink-0">
                                        <div className="font-mono font-bold text-slate-900 dark:text-white">
                                            {seg.pct}%
                                        </div>
                                        <div className="text-[10px] text-slate-400 font-mono">
                                            {formatVal(seg.val)}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            )}
        </div>
    );
}
