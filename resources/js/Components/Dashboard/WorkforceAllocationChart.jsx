import { useState } from 'react';

const DEFAULT_CATEGORIES = [
    { name: 'Site Workers', allocated: 38, available: 52 },
    { name: 'Engineers', allocated: 28, available: 42 },
    { name: 'Supervisors', allocated: 14, available: 20 },
    { name: 'Admin', allocated: 24, available: 32 },
    { name: 'Safety', allocated: 16, available: 22 },
];

export default function WorkforceAllocationChart({
    data = DEFAULT_CATEGORIES,
    title = 'Workforce Allocation',
}) {
    const width = 560;
    const height = 260;
    const padding = { top: 25, right: 25, bottom: 45, left: 45 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const maxY = 60;
    const yTicks = [60, 45, 30, 15, 0];

    const getY = (val) => padding.top + chartHeight - (val / maxY) * chartHeight;
    const groupWidth = chartWidth / data.length;
    const barWidth = 20;
    const barGap = 6;

    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm h-full flex flex-col justify-between">
            {/* Header with Title & Legend */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        {title}
                    </h3>
                    <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Active personnel deployment across project divisions.
                    </p>
                </div>

                <div className="flex items-center gap-4 text-xs font-semibold">
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-500 shadow-sm" />
                        <span className="text-slate-700 dark:text-slate-300">Allocated</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-sm border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800" />
                        <span className="text-slate-400 dark:text-slate-500">Available</span>
                    </div>
                </div>
            </div>

            {/* SVG Chart */}
            <div className="relative w-full overflow-x-auto my-auto py-2">
                <svg viewBox={`0 0 ${width} ${height}`} className="w-full h-auto min-w-[480px] select-none">
                    <defs>
                        {/* Diagonal Striped Pattern for Available Bars */}
                        <pattern id="wfDiagonalStripes" width="6" height="6" patternTransform="rotate(45 0 0)" patternUnits="userSpaceOnUse">
                            <line x1="0" y1="0" x2="0" y2="6" stroke="#94a3b8" strokeWidth="1.8" className="opacity-40" />
                        </pattern>
                    </defs>

                    {/* Horizontal Gridlines */}
                    {yTicks.map((val, idx) => {
                        const yPos = getY(val);
                        return (
                            <g key={idx}>
                                <line
                                    x1={padding.left}
                                    y1={yPos}
                                    x2={width - padding.right}
                                    y2={yPos}
                                    stroke="currentColor"
                                    strokeDasharray="3 3"
                                    className="text-slate-100 dark:text-slate-800/60"
                                />
                                <text
                                    x={padding.left - 10}
                                    y={yPos + 4}
                                    textAnchor="end"
                                    className="text-[11px] font-medium fill-slate-400 dark:fill-slate-500"
                                >
                                    {val}
                                </text>
                            </g>
                        );
                    })}

                    {/* Grouped Bars */}
                    {data.map((cat, idx) => {
                        const groupCenter = padding.left + idx * groupWidth + groupWidth / 2;
                        const xAllocated = groupCenter - barWidth - barGap / 2;
                        const xAvailable = groupCenter + barGap / 2;

                        const yAllocated = getY(cat.allocated);
                        const hAllocated = Math.max(4, padding.top + chartHeight - yAllocated);

                        const yAvailable = getY(cat.available);
                        const hAvailable = Math.max(4, padding.top + chartHeight - yAvailable);

                        return (
                            <g key={idx} className="group cursor-pointer">
                                {/* Allocated Bar (Solid Blue) */}
                                <rect
                                    x={xAllocated}
                                    y={yAllocated}
                                    width={barWidth}
                                    height={hAllocated}
                                    rx="6"
                                    fill="#2563eb"
                                    className="transition-all duration-200 group-hover:fill-blue-700"
                                />

                                {/* Available Bar (Patterned / Striped) */}
                                <rect
                                    x={xAvailable}
                                    y={yAvailable}
                                    width={barWidth}
                                    height={hAvailable}
                                    rx="6"
                                    fill="url(#wfDiagonalStripes)"
                                    stroke="#cbd5e1"
                                    strokeWidth="1.2"
                                    className="transition-all duration-200 group-hover:opacity-85"
                                />

                                {/* Category Label */}
                                <text
                                    x={groupCenter}
                                    y={height - 12}
                                    textAnchor="middle"
                                    className="text-[11px] font-semibold fill-slate-500 dark:fill-slate-400"
                                >
                                    {cat.name}
                                </text>
                            </g>
                        );
                    })}
                </svg>
            </div>
        </div>
    );
}
