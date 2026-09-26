import { useState, useMemo } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileSpreadsheet,
    ArrowLeft,
    Plus,
    Trash2,
    Users,
    Building2,
    Calendar,
    Clock,
    DollarSign,
    CheckCircle2,
    AlertCircle,
    UserCheck,
    HardHat,
} from 'lucide-react';

export default function Create({
    projects = [],
    supervisors = [],
    activeWorkers = [],
    selectedProjectId = null,
    today = '',
}) {
    const { data, setData, post, processing, errors } = useForm({
        project_id: selectedProjectId || (projects[0]?.id ? String(projects[0].id) : ''),
        date: today || new Date().toISOString().split('T')[0],
        title: '',
        supervisor_id: supervisors[0]?.id ? String(supervisors[0].id) : '',
        notes: '',
        status: 'submitted',
        items: [],
    });

    // Populate all project workers in one click
    const handleAddAllWorkers = () => {
        const newItems = activeWorkers.map((worker) => {
            const dailyRate = Number(worker.daily_wage_rate) || 0;
            const otRate = Number(worker.overtime_hourly_rate) || Number((dailyRate / 8 * 1.25).toFixed(2));
            return {
                casual_laborer_id: worker.id,
                worker_code: worker.worker_code,
                full_name: worker.full_name,
                trade: worker.trade,
                attendance_status: 'full_day',
                days_worked: 1.0,
                daily_rate: dailyRate,
                regular_amount: dailyRate,
                overtime_hours: 0,
                overtime_rate: otRate,
                overtime_amount: 0,
                deduction_amount: 0,
                net_amount: dailyRate,
                task_assigned: '',
                remarks: '',
            };
        });

        setData('items', newItems);
    };

    // Add individual worker
    const handleAddWorker = (workerId) => {
        if (!workerId) return;
        const worker = activeWorkers.find((w) => String(w.id) === String(workerId));
        if (!worker) return;

        // Check if already added
        if (data.items.some((item) => String(item.casual_laborer_id) === String(worker.id))) {
            alert(`${worker.full_name} is already added to this muster roll sheet.`);
            return;
        }

        const dailyRate = Number(worker.daily_wage_rate) || 0;
        const otRate = Number(worker.overtime_hourly_rate) || Number((dailyRate / 8 * 1.25).toFixed(2));

        const newItem = {
            casual_laborer_id: worker.id,
            worker_code: worker.worker_code,
            full_name: worker.full_name,
            trade: worker.trade,
            attendance_status: 'full_day',
            days_worked: 1.0,
            daily_rate: dailyRate,
            regular_amount: dailyRate,
            overtime_hours: 0,
            overtime_rate: otRate,
            overtime_amount: 0,
            deduction_amount: 0,
            net_amount: dailyRate,
            task_assigned: '',
            remarks: '',
        };

        setData('items', [...data.items, newItem]);
    };

    // Remove row
    const handleRemoveItem = (index) => {
        const updated = [...data.items];
        updated.splice(index, 1);
        setData('items', updated);
    };

    // Update item field and recalculate row amounts
    const handleItemChange = (index, field, value) => {
        const updated = [...data.items];
        const item = { ...updated[index] };

        item[field] = value;

        // When attendance status toggles, adjust days_worked automatically
        if (field === 'attendance_status') {
            if (value === 'full_day') item.days_worked = 1.0;
            else if (value === 'half_day') item.days_worked = 0.5;
            else if (value === 'absent' || value === 'overtime_only') item.days_worked = 0.0;
        }

        const days = Number(item.days_worked) || 0;
        const rate = Number(item.daily_rate) || 0;
        const otHours = Number(item.overtime_hours) || 0;
        const otRate = Number(item.overtime_rate) || 0;
        const deduction = Number(item.deduction_amount) || 0;

        item.regular_amount = Number((days * rate).toFixed(2));
        item.overtime_amount = Number((otHours * otRate).toFixed(2));
        item.net_amount = Math.max(0, Number((item.regular_amount + item.overtime_amount - deduction).toFixed(2)));

        updated[index] = item;
        setData('items', updated);
    };

    // Quick set all attendance status
    const setAllAttendance = (status) => {
        const updated = data.items.map((item) => {
            let days = 1.0;
            if (status === 'half_day') days = 0.5;
            if (status === 'absent') days = 0.0;

            const rate = Number(item.daily_rate) || 0;
            const regular = Number((days * rate).toFixed(2));
            const ot = Number(item.overtime_amount) || 0;
            const ded = Number(item.deduction_amount) || 0;
            const net = Math.max(0, Number((regular + ot - ded).toFixed(2)));

            return {
                ...item,
                attendance_status: status,
                days_worked: days,
                regular_amount: regular,
                net_amount: net,
            };
        });

        setData('items', updated);
    };

    // Grand Totals Computation
    const totals = useMemo(() => {
        const workersPresent = data.items.filter(
            (it) => Number(it.days_worked) > 0 || Number(it.overtime_hours) > 0
        ).length;
        const totalRegular = data.items.reduce((acc, it) => acc + (Number(it.regular_amount) || 0), 0);
        const totalOt = data.items.reduce((acc, it) => acc + (Number(it.overtime_amount) || 0), 0);
        const totalDeductions = data.items.reduce((acc, it) => acc + (Number(it.deduction_amount) || 0), 0);
        const grandNet = Math.max(0, totalRegular + totalOt - totalDeductions);

        return {
            workersPresent,
            totalRegular,
            totalOt,
            totalDeductions,
            grandNet,
        };
    }, [data.items]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (data.items.length === 0) {
            alert('Please add at least one casual worker to the muster roll sheet.');
            return;
        }
        post('/labor/muster-rolls');
    };

    return (
        <AuthenticatedLayout title="New Daily Muster Roll" header="Site Operations">
            <form onSubmit={handleSubmit} className="space-y-6 pb-24">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/labor/muster-rolls"
                            className="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors"
                        >
                            <ArrowLeft className="w-5 h-5" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                                <FileSpreadsheet className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
                                Record Daily Site Muster Roll
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                Rapid daily attendance check-in, overtime logs, and casual labor wage calculation sheet.
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            type="button"
                            onClick={() => {
                                setData('status', 'draft');
                                handleSubmit(new Event('submit'));
                            }}
                            disabled={processing}
                            className="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all cursor-pointer disabled:opacity-50"
                        >
                            Save Draft
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer disabled:opacity-50"
                        >
                            <CheckCircle2 className="w-4 h-4" />
                            <span>{processing ? 'Processing...' : 'Submit Muster Roll'}</span>
                        </button>
                    </div>
                </div>

                {/* Form Fields: Project & Work Details */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <Building2 className="w-4 h-4 text-emerald-600" />
                        Work Site & General Information
                    </h3>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Project Work Site *
                            </label>
                            <select
                                required
                                value={data.project_id}
                                onChange={(e) => setData('project_id', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">Select Project Site</option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>{p.name} ({p.location || 'Site'})</option>
                                ))}
                            </select>
                            {errors.project_id && <p className="text-rose-500 text-[10px] mt-1">{errors.project_id}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Work Date *
                            </label>
                            <input
                                type="date"
                                required
                                value={data.date}
                                onChange={(e) => setData('date', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                            />
                            {errors.date && <p className="text-rose-500 text-[10px] mt-1">{errors.date}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Site Foreman / Supervisor
                            </label>
                            <select
                                value={data.supervisor_id}
                                onChange={(e) => setData('supervisor_id', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">Select Supervisor</option>
                                {supervisors.map((s) => (
                                    <option key={s.id} value={s.id}>{s.name}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Activity / Work Description
                            </label>
                            <input
                                type="text"
                                placeholder="e.g. Concrete casting, formwork, steel fixing"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                className="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>
                    </div>
                </div>

                {/* Worker Roster Section */}
                <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <Users className="w-4 h-4 text-emerald-600" />
                                Site Labor Attendance & Wage Roster ({data.items.length} Registered)
                            </h3>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Set attendance for the day, review rates, log overtime hours, and record tasks.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-2">
                            {/* Fast Fill All Active Workers */}
                            <button
                                type="button"
                                onClick={handleAddAllWorkers}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition-all cursor-pointer"
                            >
                                <UserCheck className="w-3.5 h-3.5" />
                                <span>Add All Active Workers ({activeWorkers.length})</span>
                            </button>

                            {/* Dropdown to add specific worker */}
                            <select
                                onChange={(e) => {
                                    handleAddWorker(e.target.value);
                                    e.target.value = '';
                                }}
                                className="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">+ Add Individual Worker...</option>
                                {activeWorkers.map((w) => (
                                    <option key={w.id} value={w.id}>
                                        {w.worker_code} - {w.full_name} ({w.trade})
                                    </option>
                                ))}
                            </select>

                            {/* Quick Attendance Mass Actions */}
                            {data.items.length > 0 && (
                                <div className="flex items-center gap-1 ml-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                                    <span className="text-[10px] font-semibold text-slate-400">Set All:</span>
                                    <button
                                        type="button"
                                        onClick={() => setAllAttendance('full_day')}
                                        className="px-2 py-1 text-[10px] font-semibold rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 hover:bg-emerald-100 transition-colors"
                                    >
                                        Full Day
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setAllAttendance('half_day')}
                                        className="px-2 py-1 text-[10px] font-semibold rounded bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 hover:bg-blue-100 transition-colors"
                                    >
                                        Half Day
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>

                    {errors.items && (
                        <div className="mb-4 p-3 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-600 dark:text-rose-400 text-xs flex items-center gap-2">
                            <AlertCircle className="w-4 h-4 shrink-0" />
                            <span>{errors.items}</span>
                        </div>
                    )}

                    {/* Workers Attendance Table */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs min-w-[850px]">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th className="py-2.5 px-3">#</th>
                                    <th className="py-2.5 px-3">Worker & Trade</th>
                                    <th className="py-2.5 px-3">Attendance</th>
                                    <th className="py-2.5 px-3">Daily Rate (ETB)</th>
                                    <th className="py-2.5 px-3">Overtime</th>
                                    <th className="py-2.5 px-3">Deduction</th>
                                    <th className="py-2.5 px-3 text-right">Net Wage</th>
                                    <th className="py-2.5 px-3">Task Assigned</th>
                                    <th className="py-2.5 px-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                                {data.items.length === 0 ? (
                                    <tr>
                                        <td colSpan="9" className="py-8 text-center text-slate-400">
                                            No workers added yet. Click <strong>"Add All Active Workers"</strong> or pick individual artisans from the dropdown.
                                        </td>
                                    </tr>
                                ) : (
                                    data.items.map((item, index) => (
                                        <tr key={item.casual_laborer_id} className="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                            <td className="py-2.5 px-3 font-mono text-slate-400">
                                                {index + 1}
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <div className="font-bold text-slate-900 dark:text-white">
                                                    {item.full_name}
                                                </div>
                                                <div className="flex items-center gap-1.5 mt-0.5">
                                                    <span className="font-mono text-[10px] text-amber-600 dark:text-amber-400 font-semibold">
                                                        {item.worker_code}
                                                    </span>
                                                    <span className="text-[10px] text-slate-500">• {item.trade}</span>
                                                </div>
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <select
                                                    value={item.attendance_status}
                                                    onChange={(e) => handleItemChange(index, 'attendance_status', e.target.value)}
                                                    className={`px-2 py-1 rounded-lg text-xs font-semibold border ${
                                                        item.attendance_status === 'full_day'
                                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200'
                                                            : item.attendance_status === 'half_day'
                                                            ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200'
                                                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300'
                                                    }`}
                                                >
                                                    <option value="full_day">Full Day (1.0)</option>
                                                    <option value="half_day">Half Day (0.5)</option>
                                                    <option value="absent">Absent (0.0)</option>
                                                    <option value="overtime_only">OT Only (0.0)</option>
                                                </select>
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    value={item.daily_rate}
                                                    onChange={(e) => handleItemChange(index, 'daily_rate', e.target.value)}
                                                    className="w-24 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-white"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <div className="flex items-center gap-1.5">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="0"
                                                        placeholder="0 hrs"
                                                        value={item.overtime_hours}
                                                        onChange={(e) => handleItemChange(index, 'overtime_hours', e.target.value)}
                                                        className="w-16 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-white"
                                                    />
                                                    <span className="text-[10px] text-slate-400">hrs</span>
                                                    {Number(item.overtime_amount) > 0 && (
                                                        <span className="text-[10px] font-bold text-blue-600 dark:text-blue-400">
                                                            +{Number(item.overtime_amount).toLocaleString()} ETB
                                                        </span>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00"
                                                    value={item.deduction_amount}
                                                    onChange={(e) => handleItemChange(index, 'deduction_amount', e.target.value)}
                                                    className="w-20 px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-rose-600 dark:text-rose-400"
                                                />
                                            </td>
                                            <td className="py-2.5 px-3 text-right font-extrabold text-slate-900 dark:text-white">
                                                {Number(item.net_amount).toLocaleString()} ETB
                                            </td>
                                            <td className="py-2.5 px-3">
                                                <input
                                                    type="text"
                                                    placeholder="e.g. Slab casting, steel"
                                                    value={item.task_assigned}
                                                    onChange={(e) => handleItemChange(index, 'task_assigned', e.target.value)}
                                                    className="w-full px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white"
                                                />
                                            </td>
                                            <td className="py-2.5 px-2 text-center">
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemoveItem(index)}
                                                    className="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors"
                                                    title="Remove worker"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Sticky Grand Totals Bar */}
                <div className="fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 px-6 py-3.5 shadow-xl">
                    <div className="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div className="flex flex-wrap items-center gap-6 text-xs">
                            <div>
                                <span className="text-slate-500 block">Workers Present:</span>
                                <span className="font-extrabold text-slate-900 dark:text-white text-base">
                                    {totals.workersPresent} <span className="text-xs font-normal text-slate-400">/ {data.items.length}</span>
                                </span>
                            </div>
                            <div className="border-l border-slate-200 dark:border-slate-800 pl-4">
                                <span className="text-slate-500 block">Regular Gross:</span>
                                <span className="font-bold text-slate-800 dark:text-slate-200">
                                    {totals.totalRegular.toLocaleString()} ETB
                                </span>
                            </div>
                            <div className="border-l border-slate-200 dark:border-slate-800 pl-4">
                                <span className="text-slate-500 block">Overtime Gross:</span>
                                <span className="font-bold text-blue-600 dark:text-blue-400">
                                    +{totals.totalOt.toLocaleString()} ETB
                                </span>
                            </div>
                            {totals.totalDeductions > 0 && (
                                <div className="border-l border-slate-200 dark:border-slate-800 pl-4">
                                    <span className="text-slate-500 block">Deductions:</span>
                                    <span className="font-bold text-rose-600">
                                        -{totals.totalDeductions.toLocaleString()} ETB
                                    </span>
                                </div>
                            )}
                            <div className="border-l-2 border-emerald-500 pl-4">
                                <span className="text-emerald-600 dark:text-emerald-400 font-bold block">Grand Net Payable:</span>
                                <span className="text-lg font-black text-emerald-600 dark:text-emerald-400">
                                    {totals.grandNet.toLocaleString()} ETB
                                </span>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <button
                                type="submit"
                                disabled={processing || data.items.length === 0}
                                className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all cursor-pointer disabled:opacity-50"
                            >
                                <CheckCircle2 className="w-4 h-4" />
                                <span>{processing ? 'Saving...' : 'Submit Muster Roll'}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
