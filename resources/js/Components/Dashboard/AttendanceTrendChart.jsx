import { useState } from 'react';

export default function AttendanceTrendChart({
    title = '7-Day Shift Attendance & Punctuality',
    subtitle = 'Daily site clock-in trend (On-Time vs Late arrivals)',
    labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    onTimeData = [0, 0, 0, 0, 0, 0, 0],
    lateData = [0, 0, 0, 0, 0, 0, 0],
    dailyTotals = [],
}) {
    const [hoverIdx, setHoverIdx] = useState(null);

    const safeLabels = labels.length ? labels : ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];
    const safeOnTime = onTimeData.length ? onTimeData : safeLabels.map(() => 0);
    const safeLate = lateData.length ? lateData : safeLabels.map(() => 0);

    const maxVal = Math.max(
        ...safeLabels.map((_, i) => (Number(safeOnTime[i] || 0) + Number(safeLate[i] || 0))),
        10
    );
    const maxY = Math.ceil(maxVal * 1.25);

    const totalOnTime = safeOnTime.reduce((a, b) => a + Number(b || 0), 0);
    const totalLate = safeLate.reduce((a, b) => a + Number(b || 0), 0);
    const totalAttend = totalOnTime + totalLate;
    const punctuality = totalAttend > 0 ? Math.round((totalOnTime / totalAttend) * 100) : 100;

    const width = 580;
    const height = 240;
    const padding = { top: 20, right: 20, bottom: 35, left: 35 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;
    const barWidth = 24;
    const step = chartWidth / safeLabels.length;

    const getY = (val) => padding.top + chartHeight - (val / maxY) * chartHeight;

    const yTicks = [maxY, Math.round(maxY * 0.66), Math.round(maxY * 0.33), 0];

    return (
        <div className="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 p-5 sm:p-6 shadow-xs flex flex-col justify-between h-full">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 className="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight">
                        {title}
                    </h3>
                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {subtitle}
                    </p>
                </div>

                <div className="flex items-center gap-3 text-xs font-semibold shrink-0">
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm" />
                        <span className="text-slate-700 dark:text-slate-300">On-Time</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm" />
                        <span className="text-slate-700 dark:text-slate-300">Late</span>
                    </div>
                    <div className="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800/40">
                        {punctuality}% Punctual
                    </div>
                </div>
            </div>

            {/* SVG Chart */}
            <div className="relative w-full overflow-x-auto my-auto select-none">
                <svg viewBox={`0 0 ${width} ${height}`} className="w-full h-auto min-w-[440px]">
                    {/* Y-axis grid lines */}
                    {yTicks.map((tick, i) => {
                        const y = getY(tick);
                        return (
                            <g key={i}>
                                <line
                                    x1={padding.left}
                                    y1={y}
                                    x2={width - padding.right}
                                    y2={y}
                                    stroke="currentColor"
                                    className="text-slate-100 dark:text-slate-800/80"
                                    strokeDasharray="4 4"
                                />
                                <text
                                    x={padding.left - 8}
                                    y={y + 4}
                                    textAnchor="end"
                                    className="text-[10px] fill-slate-400 dark:fill-slate-500 font-mono font-medium"
                                >
                                    {tick}
                                </text>
                            </g>
                        );
                    })}

                    {/* Bars */}
                    {safeLabels.map((label, idx) => {
                        const onTime = Number(safeOnTime[idx] || 0);
                        const late = Number(safeLate[idx] || 0);
                        const total = onTime + late;

                        const cx = padding.left + idx * step + step / 2;
                        const barX = cx - barWidth / 2;

                        const onTimeHeight = (onTime / maxY) * chartHeight;
                        const lateHeight = (late / maxY) * chartHeight;

                        const onTimeY = padding.top + chartHeight - onTimeHeight;
                        const lateY = onTimeY - lateHeight;

                        const isHovered = hoverIdx === idx;

                        return (
                            <g
                                key={idx}
                                className="cursor-pointer"
                                onMouseEnter={() => setHoverIdx(idx)}
                                onMouseLeave={() => setHoverIdx(null)}
                            >
                                {/* Transparent hover catcher */}
                                <rect
                                    x={cx - step / 2}
                                    y={padding.top}
                                    width={step}
                                    height={chartHeight}
                                    fill={isHovered ? 'currentColor' : 'transparent'}
                                    className="text-slate-50/70 dark:text-slate-800/40 transition-colors"
                                />

                                {/* On-Time Bar (bottom) */}
                                {onTime > 0 && (
                                    <rect
                                        x={barX}
                                        y={onTimeY}
                                        width={barWidth}
                                        height={onTimeHeight}
                                        rx={late > 0 ? 0 : 5}
                                        className="fill-emerald-500 hover:fill-emerald-400 transition-colors"
                                    />
                                )}

                                {/* Late Bar (top) */}
                                {late > 0 && (
                                    <rect
                                        x={barX}
                                        y={lateY}
                                        width={barWidth}
                                        height={lateHeight}
                                        rx={5}
                                        className="fill-amber-500 hover:fill-amber-400 transition-colors"
                                    />
                                )}

                                {/* Zero count subtle marker */}
                                {total === 0 && (
                                    <circle
                                        cx={cx}
                                        cy={padding.top + chartHeight - 4}
                                        r={2}
                                        className="fill-slate-300 dark:fill-slate-700"
                                    />
                                )}

                                {/* X-axis Label */}
                                <text
                                    x={cx}
                                    y={height - 12}
                                    textAnchor="middle"
                                    className={`text-[11px] font-medium transition-colors ${
                                        isHovered
                                            ? 'fill-slate-900 dark:fill-white font-bold'
                                            : 'fill-slate-400 dark:fill-slate-500'
                                    }`}
                                >
                                    {label}
                                </text>

                                {/* Tooltip or Value Label on hover */}
                                {isHovered && total > 0 && (
                                    <g>
                                        <rect
                                            x={cx - 36}
                                            y={Math.max(5, lateY - 26)}
                                            width={72}
                                            height={20}
                                            rx={6}
                                            className="fill-slate-900 dark:fill-slate-100 shadow-md"
                                        />
                                        <text
                                            x={cx}
                                            y={Math.max(19, lateY - 12)}
                                            textAnchor="middle"
                                            className="text-[10px] font-mono font-bold fill-white dark:fill-slate-900"
                                        >
                                            {total} staff ({onTime}✓ {late}⚠️)
                                        </text>
                                    </g>
                                )}
                            </g>
                        );
                    })}
                </svg>
            </div>

            {/* Footer Summary */}
            <div className="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>Total 7-Day Shift Check-ins: <strong className="text-slate-900 dark:text-white font-mono">{totalAttend}</strong></span>
                <span>On-Time: <strong className="text-emerald-600 dark:text-emerald-400 font-mono">{totalOnTime}</strong> · Late: <strong className="text-amber-600 dark:text-amber-400 font-mono">{totalLate}</strong></span>
            </div>
        </div>
    );
}
