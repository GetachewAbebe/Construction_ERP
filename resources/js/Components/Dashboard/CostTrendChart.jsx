import { useState } from 'react';

// Monthly enterprise trajectory data matching real budget scale
const DEFAULT_DATA = [
    { month: 'Jan', actual: 420000, baseline: 380000 },
    { month: 'Feb', actual: 580000, baseline: 520000 },
    { month: 'Mar', actual: 720000, baseline: 690000 },
    { month: 'Apr', actual: 640000, baseline: 750000 },
    { month: 'May', actual: 890000, baseline: 840000 },
    { month: 'Jun', actual: 810000, baseline: 920000 },
    { month: 'Jul', actual: 1100000, baseline: 990000 },
    { month: 'Aug', actual: 780000, baseline: 950000 },
    { month: 'Sep', actual: 720000, baseline: 880000 },
    { month: 'Oct', actual: 950000, baseline: 980000 },
    { month: 'Nov', actual: 1080000, baseline: 1020000 },
    { month: 'Dec', actual: 940000, baseline: 1050000 },
];

export default function CostTrendChart({
    data = DEFAULT_DATA,
    title = 'Cost Trend Analysis',
    currency = 'ETB',
}) {
    const [hoverIndex, setHoverIndex] = useState(null);

    const width = 760;
    const height = 260;
    const padding = { top: 25, right: 25, bottom: 40, left: 65 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const maxY = 1200000;
    const yTicks = [1200000, 900000, 600000, 300000, 0];

    const getX = (index) => padding.left + (index / (data.length - 1)) * chartWidth;
    const getY = (val) => padding.top + chartHeight - (val / maxY) * chartHeight;

    const formatYValue = (val) => {
        if (val >= 1000000) return `${(val / 1000000).toFixed(1)}M`;
        if (val >= 1000) return `${(val / 1000).toFixed(0)}k`;
        return '0';
    };

    // Cubic spline path
    const buildPath = (key) => {
        if (!data.length) return '';
        let path = `M ${getX(0)} ${getY(data[0][key])}`;
        for (let i = 0; i < data.length - 1; i++) {
            const x0 = getX(i);
            const y0 = getY(data[i][key]);
            const x1 = getX(i + 1);
            const y1 = getY(data[i + 1][key]);
            const cx = (x0 + x1) / 2;
            path += ` C ${cx} ${y0}, ${cx} ${y1}, ${x1} ${y1}`;
        }
        return path;
    };

    const actualLine = buildPath('actual');
    const actualArea = `${actualLine} L ${getX(data.length - 1)} ${padding.top + chartHeight} L ${getX(0)} ${padding.top + chartHeight} Z`;

    const baselineLine = buildPath('baseline');
    const baselineArea = `${baselineLine} L ${getX(data.length - 1)} ${padding.top + chartHeight} L ${getX(0)} ${padding.top + chartHeight} Z`;

    const activeItem = hoverIndex !== null ? data[hoverIndex] : null;
    const activeX = hoverIndex !== null ? getX(hoverIndex) : 0;
    const activeY = hoverIndex !== null ? getY(activeItem.actual) : 0;

    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm h-full flex flex-col justify-between">
            {/* Header with Title & Legend */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        {title}
                    </h3>
                    <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Actual expenditure tracking against planned engineering baseline.
                    </p>
                </div>

                <div className="flex items-center gap-5 text-xs font-semibold">
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-500 shadow-sm" />
                        <span className="text-slate-700 dark:text-slate-300">Actual Cost</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700" />
                        <span className="text-slate-400 dark:text-slate-500">Baseline</span>
                    </div>
                </div>
            </div>

            {/* SVG Chart Area */}
            <div className="relative w-full overflow-x-auto my-auto py-2">
                <svg
                    viewBox={`0 0 ${width} ${height}`}
                    className="w-full h-auto min-w-[620px] select-none"
                    onMouseLeave={() => setHoverIndex(null)}
                >
                    <defs>
                        {/* Actual Cost Gradient Fill */}
                        <linearGradient id="actualGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor="#2563eb" stopOpacity="0.25" />
                            <stop offset="100%" stopColor="#2563eb" stopOpacity="0.01" />
                        </linearGradient>

                        {/* Baseline Gradient Fill */}
                        <linearGradient id="baselineGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor="#94a3b8" stopOpacity="0.12" />
                            <stop offset="100%" stopColor="#94a3b8" stopOpacity="0.00" />
                        </linearGradient>

                        {/* Hover Column Glow */}
                        <linearGradient id="highlightBand" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor="#3b82f6" stopOpacity="0.10" />
                            <stop offset="100%" stopColor="#3b82f6" stopOpacity="0.01" />
                        </linearGradient>
                    </defs>

                    {/* Horizontal Gridlines & Y-Axis Labels */}
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
                                    x={padding.left - 12}
                                    y={yPos + 4}
                                    textAnchor="end"
                                    className="text-[11px] font-medium fill-slate-400 dark:fill-slate-500"
                                >
                                    {formatYValue(val)}
                                </text>
                            </g>
                        );
                    })}

                    {/* Baseline Area & Line */}
                    <path d={baselineArea} fill="url(#baselineGradient)" />
                    <path
                        d={baselineLine}
                        fill="none"
                        stroke="#94a3b8"
                        strokeWidth="1.75"
                        strokeDasharray="4 4"
                        className="opacity-70"
                    />

                    {/* Actual Cost Area & Line */}
                    <path d={actualArea} fill="url(#actualGradient)" />
                    <path
                        d={actualLine}
                        fill="none"
                        stroke="#2563eb"
                        strokeWidth="2.75"
                        strokeLinecap="round"
                    />

                    {/* Interactive Hover Indicator */}
                    {hoverIndex !== null && (
                        <g>
                            <rect
                                x={activeX - 22}
                                y={padding.top}
                                width="44"
                                height={chartHeight}
                                rx="8"
                                fill="url(#highlightBand)"
                            />
                            <line
                                x1={activeX}
                                y1={padding.top}
                                x2={activeX}
                                y2={padding.top + chartHeight}
                                stroke="#3b82f6"
                                strokeWidth="1.5"
                                strokeDasharray="3 3"
                                className="opacity-80"
                            />
                            <circle
                                cx={activeX}
                                cy={activeY}
                                r="5.5"
                                fill="#2563eb"
                                stroke="#ffffff"
                                strokeWidth="2.5"
                                className="shadow-md"
                            />
                        </g>
                    )}

                    {/* X-Axis Month Labels & Triggers */}
                    {data.map((item, idx) => {
                        const xPos = getX(idx);
                        const isSelected = hoverIndex === idx;
                        return (
                            <g
                                key={idx}
                                className="cursor-pointer"
                                onMouseEnter={() => setHoverIndex(idx)}
                            >
                                <text
                                    x={xPos}
                                    y={height - 12}
                                    textAnchor="middle"
                                    className={`text-[11px] transition-colors ${
                                        isSelected
                                            ? 'font-bold fill-blue-600 dark:fill-blue-400'
                                            : 'font-medium fill-slate-400 dark:fill-slate-500'
                                    }`}
                                >
                                    {item.month}
                                </text>
                                <rect
                                    x={xPos - chartWidth / (data.length * 2)}
                                    y={0}
                                    width={chartWidth / data.length}
                                    height={height}
                                    fill="transparent"
                                />
                            </g>
                        );
                    })}
                </svg>

                {/* Hover Tooltip (Only visible on hover) */}
                {hoverIndex !== null && activeItem && (
                    <div
                        style={{
                            left: `${(activeX / width) * 100}%`,
                            top: '20%',
                            transform: 'translate(-50%, -100%)',
                        }}
                        className="pointer-events-none absolute hidden sm:block z-20 bg-slate-900/95 text-white rounded-2xl p-3 border border-slate-700 shadow-xl backdrop-blur-md min-w-[150px]"
                    >
                        <div className="text-[11px] font-bold text-slate-200 pb-1.5 border-b border-slate-800">
                            {activeItem.month}, 2026
                        </div>
                        <div className="mt-1.5 space-y-1 text-[11px]">
                            <div className="flex items-center justify-between gap-3">
                                <span className="flex items-center gap-1.5 text-slate-400">
                                    <span className="w-2 h-2 rounded-full bg-blue-500" />
                                    Actual:
                                </span>
                                <span className="font-extrabold text-white">{currency} {Number(activeItem.actual).toLocaleString()}</span>
                            </div>
                            <div className="flex items-center justify-between gap-3 text-slate-400">
                                <span className="flex items-center gap-1.5">
                                    <span className="w-2 h-2 rounded-full bg-slate-500" />
                                    Baseline:
                                </span>
                                <span className="font-semibold text-slate-300">{currency} {Number(activeItem.baseline).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
