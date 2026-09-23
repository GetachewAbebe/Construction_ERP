import React, { useState, useMemo, useRef } from 'react';
import {
    Calendar,
    Clock,
    CheckCircle2,
    AlertTriangle,
    Sliders,
    Edit,
    PlusCircle,
    ChevronLeft,
    ChevronRight,
    Flag,
    Activity,
    Info,
} from 'lucide-react';

export default function ProjectGanttChart({
    milestones = [],
    projectStartDate = null,
    projectEndDate = null,
    onEditMilestone,
    onAdjustProgress,
    onAddMilestone,
}) {
    const [hoveredMilestone, setHoveredMilestone] = useState(null);
    const [statusFilter, setStatusFilter] = useState('all'); // 'all' | 'in_progress' | 'delayed' | 'completed' | 'pending'
    const chartScrollRef = useRef(null);

    // Filter milestones based on status
    const filteredMilestones = useMemo(() => {
        if (statusFilter === 'all') return milestones;
        return milestones.filter((m) => {
            if (statusFilter === 'delayed') return m.status === 'delayed' || m.is_overdue;
            return m.status === statusFilter;
        });
    }, [milestones, statusFilter]);

    // Compute Timeline Boundary (Start Date to End Date)
    const { timelineStart, timelineEnd, totalDays, months } = useMemo(() => {
        let minDate = projectStartDate ? new Date(projectStartDate) : null;
        let maxDate = projectEndDate ? new Date(projectEndDate) : null;

        milestones.forEach((m) => {
            if (m.start_date) {
                const s = new Date(m.start_date);
                if (!minDate || s < minDate) minDate = s;
            }
            if (m.due_date) {
                const d = new Date(m.due_date);
                if (!maxDate || d > maxDate) maxDate = d;
            }
        });

        const today = new Date();
        if (!minDate) minDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        if (!maxDate) maxDate = new Date(today.getFullYear(), today.getMonth() + 3, 1);

        // Add 7 days padding at both ends for visual buffer
        const start = new Date(minDate);
        start.setDate(start.getDate() - 7);
        start.setHours(0, 0, 0, 0);

        const end = new Date(maxDate);
        end.setDate(end.getDate() + 14);
        end.setHours(23, 59, 59, 999);

        const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));

        // Generate Month blocks for timescale header
        const monthBlocks = [];
        let curr = new Date(start.getFullYear(), start.getMonth(), 1);

        while (curr <= end) {
            const monthStart = new Date(Math.max(curr.getTime(), start.getTime()));
            const nextMonth = new Date(curr.getFullYear(), curr.getMonth() + 1, 1);
            const monthEnd = new Date(Math.min(nextMonth.getTime() - 1, end.getTime()));

            const monthDays = Math.max(1, (monthEnd - monthStart) / (1000 * 60 * 60 * 24));
            const leftPct = ((monthStart - start) / (end - start)) * 100;
            const widthPct = (monthDays / days) * 100;

            monthBlocks.push({
                label: curr.toLocaleDateString(undefined, { month: 'short', year: 'numeric' }),
                leftPct,
                widthPct,
            });

            curr = nextMonth;
        }

        return {
            timelineStart: start,
            timelineEnd: end,
            totalDays: days,
            months: monthBlocks,
        };
    }, [milestones, projectStartDate, projectEndDate]);

    // Calculate "Today" indicator position
    const todayPct = useMemo(() => {
        const now = new Date();
        if (now < timelineStart || now > timelineEnd) return null;
        return ((now - timelineStart) / (timelineEnd - timelineStart)) * 100;
    }, [timelineStart, timelineEnd]);

    // Compute coordinate positioning for a milestone
    const getMilestonePosition = (m) => {
        if (!m.start_date && !m.due_date) return null;

        const mStart = m.start_date ? new Date(m.start_date) : new Date(timelineStart);
        const mEnd = m.due_date ? new Date(m.due_date) : new Date(mStart.getTime() + 14 * 24 * 60 * 60 * 1000);

        const left = Math.max(0, Math.min(99, ((mStart - timelineStart) / (timelineEnd - timelineStart)) * 100));
        const rawWidth = ((mEnd - mStart) / (timelineEnd - timelineStart)) * 100;
        const width = Math.max(2.5, Math.min(100 - left, rawWidth));

        const durationDays = Math.max(1, Math.ceil((mEnd - mStart) / (1000 * 60 * 60 * 24)));

        return { left, width, durationDays };
    };

    const handleJumpToToday = () => {
        if (chartScrollRef.current && todayPct !== null) {
            const container = chartScrollRef.current;
            const scrollTarget = (todayPct / 100) * container.scrollWidth - container.clientWidth / 2;
            container.scrollTo({ left: Math.max(0, scrollTarget), behavior: 'smooth' });
        }
    };

    return (
        <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
            {/* Gantt Control Toolbar */}
            <div className="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                <div className="flex items-center gap-2">
                    <span className="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                        <Calendar className="w-4 h-4" />
                    </span>
                    <div>
                        <h3 className="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            Interactive Gantt Schedule Timeline
                        </h3>
                        <p className="text-[11px] text-slate-400">
                            Span: {timelineStart.toLocaleDateString()} – {timelineEnd.toLocaleDateString()} ({totalDays} calendar days)
                        </p>
                    </div>
                </div>

                <div className="flex flex-wrap items-center gap-2">
                    {/* Filter Pills */}
                    <div className="flex items-center gap-1 p-0.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-[11px]">
                        {[
                            { id: 'all', label: 'All' },
                            { id: 'in_progress', label: 'Active' },
                            { id: 'delayed', label: 'Delayed' },
                            { id: 'completed', label: 'Done' },
                        ].map((btn) => (
                            <button
                                key={btn.id}
                                type="button"
                                onClick={() => setStatusFilter(btn.id)}
                                className={`px-2.5 py-1 rounded-lg font-semibold transition-all cursor-pointer ${
                                    statusFilter === btn.id
                                        ? 'bg-white dark:bg-slate-700 text-blue-900 dark:text-white shadow-xs'
                                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                                }`}
                            >
                                {btn.label}
                            </button>
                        ))}
                    </div>

                    {todayPct !== null && (
                        <button
                            type="button"
                            onClick={handleJumpToToday}
                            className="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                        >
                            <Clock className="w-3.5 h-3.5 text-blue-600" />
                            <span>Jump to Today</span>
                        </button>
                    )}

                    {onAddMilestone && (
                        <button
                            type="button"
                            onClick={onAddMilestone}
                            className="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm cursor-pointer"
                        >
                            <PlusCircle className="w-3.5 h-3.5" />
                            <span>Add Phase</span>
                        </button>
                    )}
                </div>
            </div>

            {/* Gantt Chart Container */}
            <div className="flex flex-col overflow-hidden">
                {/* Scrollable Timeline Grid */}
                <div
                    ref={chartScrollRef}
                    className="overflow-x-auto relative"
                    style={{ minHeight: '380px' }}
                >
                    <div style={{ minWidth: '850px' }} className="w-full">
                        {/* 1. Timescale Months Header */}
                        <div className="flex border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 sticky top-0 z-20">
                            {/* Sticky Left Sidebar Header */}
                            <div className="w-64 shrink-0 px-4 py-2.5 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-r border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800">
                                WBS Phase & Deliverables
                            </div>

                            {/* Timeline Scale Area */}
                            <div className="flex-1 relative h-9">
                                {months.map((m, i) => (
                                    <div
                                        key={i}
                                        className="absolute top-0 bottom-0 border-r border-slate-200 dark:border-slate-800/80 px-2 flex items-center text-[10px] font-mono font-bold text-slate-500 uppercase truncate"
                                        style={{
                                            left: `${m.leftPct}%`,
                                            width: `${m.widthPct}%`,
                                        }}
                                    >
                                        {m.label}
                                    </div>
                                ))}

                                {/* Today Header Marker */}
                                {todayPct !== null && (
                                    <div
                                        className="absolute top-0 bottom-0 z-30 flex flex-col items-center pointer-events-none"
                                        style={{ left: `${todayPct}%` }}
                                    >
                                        <span className="px-1.5 py-0.5 rounded-full bg-rose-600 text-white text-[9px] font-bold shadow-xs -translate-x-1/2">
                                            Today
                                        </span>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* 2. Timeline Rows */}
                        <div className="relative divide-y divide-slate-100 dark:divide-slate-800/70">
                            {/* Vertical Today Marker Line extending through all rows */}
                            {todayPct !== null && (
                                <div
                                    className="absolute top-0 bottom-0 z-10 border-l-2 border-dashed border-rose-500/80 pointer-events-none"
                                    style={{ left: `calc(16rem + (100% - 16rem) * ${todayPct / 100})` }}
                                />
                            )}

                            {filteredMilestones.length === 0 ? (
                                <div className="py-16 text-center text-slate-400 text-xs">
                                    No scheduled milestones match the current filter.
                                </div>
                            ) : (
                                filteredMilestones.map((m) => {
                                    const pos = getMilestonePosition(m);
                                    const isHovered = hoveredMilestone?.id === m.id;

                                    return (
                                        <div
                                            key={m.id}
                                            className={`flex items-center hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors ${
                                                isHovered ? 'bg-blue-50/30 dark:bg-blue-950/20' : ''
                                            }`}
                                            onMouseEnter={() => setHoveredMilestone(m)}
                                            onMouseLeave={() => setHoveredMilestone(null)}
                                        >
                                            {/* Left Column: Milestone Details */}
                                            <div className="w-64 shrink-0 px-4 py-3 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between gap-2 z-10">
                                                <div className="min-w-0 flex-1">
                                                    <div className="flex items-center gap-1.5">
                                                        {m.wbs_code && (
                                                            <span className="px-1 py-0.2 rounded-md bg-slate-100 dark:bg-slate-800 font-mono text-[9px] font-bold text-slate-600 dark:text-slate-300">
                                                                {m.wbs_code}
                                                            </span>
                                                        )}
                                                        <span className="text-xs font-bold text-slate-900 dark:text-white truncate block" title={m.title}>
                                                            {m.title}
                                                        </span>
                                                    </div>
                                                    <div className="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                                        <span>{m.progress}% completed</span>
                                                        {pos && <span>• {pos.durationDays}d</span>}
                                                    </div>
                                                </div>

                                                {/* Action Buttons */}
                                                <div className="flex items-center gap-1 opacity-70 hover:opacity-100 transition-opacity">
                                                    {onAdjustProgress && (
                                                        <button
                                                            type="button"
                                                            onClick={() => onAdjustProgress(m)}
                                                            className="p-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-blue-600 cursor-pointer"
                                                            title="Adjust Progress"
                                                        >
                                                            <Sliders className="w-3 h-3" />
                                                        </button>
                                                    )}
                                                    {onEditMilestone && (
                                                        <button
                                                            type="button"
                                                            onClick={() => onEditMilestone(m)}
                                                            className="p-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white cursor-pointer"
                                                            title="Edit Details"
                                                        >
                                                            <Edit className="w-3 h-3" />
                                                        </button>
                                                    )}
                                                </div>
                                            </div>

                                            {/* Right Timeline Canvas: Milestone Gantt Bar */}
                                            <div className="flex-1 relative py-2.5 px-1 min-h-[46px] flex items-center">
                                                {pos ? (
                                                    <div
                                                        onClick={() => onAdjustProgress && onAdjustProgress(m)}
                                                        className={`absolute h-7 rounded-lg border transition-all cursor-pointer shadow-xs flex items-center overflow-hidden group ${
                                                            m.status === 'completed'
                                                                ? 'bg-emerald-100/70 dark:bg-emerald-950/60 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-100'
                                                                : m.status === 'delayed' || m.is_overdue
                                                                ? 'bg-rose-100/70 dark:bg-rose-950/60 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-100'
                                                                : m.status === 'in_progress'
                                                                ? 'bg-blue-100/80 dark:bg-blue-950/60 border-blue-300 dark:border-blue-700 text-blue-950 dark:text-blue-100'
                                                                : 'bg-slate-100 dark:bg-slate-800/80 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200'
                                                        }`}
                                                        style={{
                                                            left: `${pos.left}%`,
                                                            width: `${pos.width}%`,
                                                        }}
                                                    >
                                                        {/* Progress Fill Bar */}
                                                        <div
                                                            className={`h-full transition-all duration-300 ${
                                                                m.status === 'completed'
                                                                    ? 'bg-emerald-500'
                                                                    : m.status === 'delayed' || m.is_overdue
                                                                    ? 'bg-rose-500'
                                                                    : m.status === 'in_progress'
                                                                    ? 'bg-blue-600'
                                                                    : 'bg-slate-400'
                                                            }`}
                                                            style={{ width: `${m.progress}%` }}
                                                        />

                                                        {/* Bar Label Overlay */}
                                                        <div className="absolute inset-0 px-2 flex items-center justify-between text-[10px] font-bold pointer-events-none drop-shadow-xs">
                                                            <span className="truncate pr-1">
                                                                {m.title}
                                                            </span>
                                                            <span className="font-mono text-[9px] shrink-0">
                                                                {m.progress}%
                                                            </span>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <div className="px-3 py-1 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[10px] font-medium border border-amber-200 dark:border-amber-900 inline-flex items-center gap-1">
                                                        <AlertTriangle className="w-3 h-3" />
                                                        <span>No start or due date scheduled — Click edit to assign timeline</span>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </div>
                </div>

                {/* Footer Legend */}
                <div className="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex flex-wrap items-center justify-between text-[11px] text-slate-500 gap-3">
                    <div className="flex flex-wrap items-center gap-4">
                        <span className="font-semibold text-slate-700 dark:text-slate-300">Legend:</span>
                        <div className="flex items-center gap-1.5">
                            <span className="w-3 h-3 rounded bg-emerald-500 inline-block" />
                            <span>Completed</span>
                        </div>
                        <div className="flex items-center gap-1.5">
                            <span className="w-3 h-3 rounded bg-blue-600 inline-block" />
                            <span>In Progress</span>
                        </div>
                        <div className="flex items-center gap-1.5">
                            <span className="w-3 h-3 rounded bg-rose-500 inline-block" />
                            <span>Delayed / Overdue</span>
                        </div>
                        <div className="flex items-center gap-1.5">
                            <span className="w-3 h-3 rounded bg-slate-400 inline-block" />
                            <span>Pending</span>
                        </div>
                        <div className="flex items-center gap-1.5">
                            <span className="w-3 h-0.5 border-b-2 border-dashed border-rose-500 inline-block" />
                            <span>Today Marker</span>
                        </div>
                    </div>

                    <div className="flex items-center gap-1 text-[10px] text-slate-400">
                        <Info className="w-3.5 h-3.5" />
                        <span>Click any milestone bar to adjust completion percentage or update schedule</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
