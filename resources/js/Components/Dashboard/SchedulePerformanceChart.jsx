const DEFAULT_TIMELINE = [
    { label: 'W1', actual: 16, planned: 12 },
    { label: 'W2', actual: 28, planned: 24 },
    { label: 'W3', actual: 22, planned: 34 },
    { label: 'W4', actual: 35, planned: 30 },
    { label: 'W5', actual: 30, planned: 42 },
    { label: 'W6', actual: 48, planned: 38 },
];

export default function SchedulePerformanceChart({
    data = DEFAULT_TIMELINE,
    title = 'Schedule Performance',
}) {
    const width = 560;
    const height = 260;
    const padding = { top: 25, right: 25, bottom: 45, left: 45 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const maxY = 60;
    const yTicks = [60, 45, 30, 15, 0];

    const getX = (index) => padding.left + (index / (data.length - 1)) * chartWidth;
    const getY = (val) => padding.top + chartHeight - (val / maxY) * chartHeight;

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
    const plannedLine = buildPath('planned');
    const plannedArea = `${plannedLine} L ${getX(data.length - 1)} ${padding.top + chartHeight} L ${getX(0)} ${padding.top + chartHeight} Z`;

    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-7 shadow-sm h-full flex flex-col justify-between">
            {/* Header with Title & Legend */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        {title}
                    </h3>
                    <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Construction milestones tracking across execution weeks.
                    </p>
                </div>

                <div className="flex items-center gap-4 text-xs font-semibold">
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-500 shadow-sm" />
                        <span className="text-slate-700 dark:text-slate-300">Actual Progress</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-slate-700" />
                        <span className="text-slate-400 dark:text-slate-500">Planned Progress</span>
                    </div>
                </div>
            </div>

            {/* SVG Chart */}
            <div className="relative w-full overflow-x-auto my-auto py-2">
                <svg viewBox={`0 0 ${width} ${height}`} className="w-full h-auto min-w-[480px] select-none">
                    <defs>
                        <linearGradient id="schedPlannedWave" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor="#cbd5e1" stopOpacity="0.45" />
                            <stop offset="100%" stopColor="#cbd5e1" stopOpacity="0.05" />
                        </linearGradient>
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

                    {/* Planned Progress Soft Filled Wave */}
                    <path d={plannedArea} fill="url(#schedPlannedWave)" className="dark:opacity-25" />

                    {/* Actual Progress Spline Curve */}
                    <path
                        d={actualLine}
                        fill="none"
                        stroke="#2563eb"
                        strokeWidth="2.75"
                        strokeLinecap="round"
                    />

                    {/* X-Axis Timeline Labels */}
                    {data.map((item, idx) => {
                        const xPos = getX(idx);
                        return (
                            <text
                                key={idx}
                                x={xPos}
                                y={height - 12}
                                textAnchor="middle"
                                className="text-[11px] font-semibold fill-slate-500 dark:fill-slate-400"
                            >
                                {item.label}
                            </text>
                        );
                    })}
                </svg>
            </div>
        </div>
    );
}
