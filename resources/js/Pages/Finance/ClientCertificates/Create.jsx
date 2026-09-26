import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    FileText,
    ArrowLeft,
    Save,
    AlertTriangle,
    Building2,
    Calendar,
    DollarSign,
    Percent,
} from 'lucide-react';

export default function Create({
    projects = [],
    selectedProject = null,
    previousIpc = null,
    suggestedCertificateNo = '',
    suggestedSequence = 1,
    previousCumulativeGross = 0,
}) {
    const { data, setData, post, processing, errors } = useForm({
        project_id: selectedProject?.id || '',
        period_start: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        period_end: new Date().toISOString().split('T')[0],
        submission_date: new Date().toISOString().split('T')[0],
        client_name: selectedProject ? `${selectedProject.name} Employer` : '',
        consultant_name: 'Supervising Consultant Engineer',
        work_description: '',
        cumulative_gross_amount: previousCumulativeGross,
        previous_gross_amount: previousCumulativeGross,
        materials_on_site: 0,
        retention_rate: 5,
        advance_recoupment_rate: 0,
        other_deductions: 0,
        tax_rate: 15,
        notes: '',
    });

    const handleProjectChange = (projId) => {
        if (!projId) {
            router.get('/finance/client-certificates/create');
            return;
        }
        router.get('/finance/client-certificates/create', { project_id: projId }, {
            preserveState: false,
        });
    };

    // Calculate real-time figures
    const cumulative = parseFloat(data.cumulative_gross_amount) || 0;
    const previous = parseFloat(data.previous_gross_amount) || 0;
    const currentGross = Math.max(0, cumulative - previous);
    const mos = parseFloat(data.materials_on_site) || 0;
    const retRate = parseFloat(data.retention_rate) || 0;
    const advRate = parseFloat(data.advance_recoupment_rate) || 0;
    const otherDed = parseFloat(data.other_deductions) || 0;
    const taxRate = parseFloat(data.tax_rate) || 0;

    const retentionDeduction = currentGross * (retRate / 100);
    const advanceDeduction = currentGross * (advRate / 100);
    const subtotalNet = Math.max(0, currentGross + mos - retentionDeduction - advanceDeduction - otherDed);
    const taxAmount = subtotalNet * (taxRate / 100);
    const totalClaimAmount = subtotalNet + taxAmount;

    const submit = (e) => {
        e.preventDefault();
        post('/finance/client-certificates');
    };

    return (
        <AuthenticatedLayout title="Submit Progress Billing Claim" header="Finance & Commercial">
            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <FileText className="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                            Submit Client Interim Payment Certificate (IPC)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Auto-numbered as <strong className="text-indigo-600 dark:text-indigo-400">{suggestedCertificateNo || 'IPC-PRJ-01'}</strong> • Progress Valuation Claim (FIDIC Standard)
                        </p>
                    </div>

                    <Link
                        href="/finance/client-certificates"
                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors cursor-pointer"
                    >
                        <ArrowLeft className="w-3.5 h-3.5" />
                        <span>Cancel</span>
                    </Link>
                </div>

                {Object.keys(errors).length > 0 && (
                    <div className="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/50 flex items-start gap-2.5">
                        <AlertTriangle className="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                        <div>
                            <span className="block font-bold">Please correct the claim errors:</span>
                            <ul className="list-disc list-inside text-[11px] font-normal text-rose-700 dark:text-rose-300/90 mt-1 space-y-0.5">
                                {Object.values(errors).map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-6">
                    {/* Project & Client Selection */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Contract & Project Information
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Project Contract <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    required
                                    value={data.project_id}
                                    onChange={(e) => handleProjectChange(e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold"
                                >
                                    <option value="">-- Select Project --</option>
                                    {projects.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.name} (Budget: {Number(p.budget).toLocaleString()} ETB)
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Client / Employer Name
                                </label>
                                <input
                                    type="text"
                                    value={data.client_name}
                                    onChange={(e) => setData('client_name', e.target.value)}
                                    placeholder="e.g. Ethiopian Roads Authority / Commercial Bank"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Supervising Consultant Engineer
                                </label>
                                <input
                                    type="text"
                                    value={data.consultant_name}
                                    onChange={(e) => setData('consultant_name', e.target.value)}
                                    placeholder="e.g. MH Engineering Consultants"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Billing Period Start <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={data.period_start}
                                    onChange={(e) => setData('period_start', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Billing Period End <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={data.period_end}
                                    onChange={(e) => setData('period_end', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Submission Date <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={data.submission_date}
                                    onChange={(e) => setData('submission_date', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                Scope of Work Executed This Month <span className="text-rose-500">*</span>
                            </label>
                            <textarea
                                required
                                rows={2}
                                value={data.work_description}
                                onChange={(e) => setData('work_description', e.target.value)}
                                placeholder="e.g. Substructure excavation 1200m3, foundation concrete casting 350m3, basement retaining wall reinforcement"
                                className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    {/* Progress Valuation Financial Form */}
                    <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                        <h2 className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Statement of Claim & Valuation (FIDIC / Civil Format)
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Cumulative Gross Value of Work to Date (ETB) <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    value={data.cumulative_gross_amount}
                                    onChange={(e) => setData('cumulative_gross_amount', e.target.value)}
                                    className="w-full px-3 py-2 text-sm font-black rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100"
                                />
                                <span className="text-[10px] text-slate-400">Total permanent work completed up to this billing cut-off.</span>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Less: Previous Certified Gross Value (ETB) <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    value={data.previous_gross_amount}
                                    onChange={(e) => setData('previous_gross_amount', e.target.value)}
                                    className="w-full px-3 py-2 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300"
                                />
                                <span className="text-[10px] text-slate-400">Auto-filled from previous certificate ({previousIpc ? previousIpc.certificate_no : 'Initial claim'}).</span>
                            </div>
                        </div>

                        {/* Deductions & Additions Grid */}
                        <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Materials on Site (MOS)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={data.materials_on_site}
                                    onChange={(e) => setData('materials_on_site', e.target.value)}
                                    placeholder="0.00"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-amber-600 dark:text-amber-400 mb-1.5 uppercase tracking-wider">
                                    Retention Rate (%)
                                </label>
                                <input
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    max="100"
                                    value={data.retention_rate}
                                    onChange={(e) => setData('retention_rate', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-amber-300 dark:border-amber-800 bg-amber-50/30 dark:bg-amber-950/20 font-bold"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Advance Recoupment (%)
                                </label>
                                <input
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    max="100"
                                    value={data.advance_recoupment_rate}
                                    onChange={(e) => setData('advance_recoupment_rate', e.target.value)}
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                                    Other Deductions (ETB)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={data.other_deductions}
                                    onChange={(e) => setData('other_deductions', e.target.value)}
                                    placeholder="0.00"
                                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                                />
                            </div>
                        </div>

                        {/* Breakdown Calculation Table */}
                        <div className="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-xs space-y-2">
                            <div className="flex justify-between">
                                <span className="text-slate-600 dark:text-slate-400">Current Period Gross Valuation:</span>
                                <span className="font-bold text-slate-900 dark:text-white">{currentGross.toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                            </div>
                            <div className="flex justify-between text-amber-600 dark:text-amber-400">
                                <span>Less: Retention Money ({data.retention_rate}%):</span>
                                <span className="font-semibold">({retentionDeduction.toLocaleString(undefined, { minimumFractionDigits: 2 })}) ETB</span>
                            </div>
                            {advanceDeduction > 0 && (
                                <div className="flex justify-between text-rose-600 dark:text-rose-400">
                                    <span>Less: Mobilization Advance Recoupment ({data.advance_recoupment_rate}%):</span>
                                    <span className="font-semibold">({advanceDeduction.toLocaleString(undefined, { minimumFractionDigits: 2 })}) ETB</span>
                                </div>
                            )}
                            <div className="flex justify-between pt-1 border-t border-slate-200 dark:border-slate-700">
                                <span className="font-bold">Subtotal Net Valuation:</span>
                                <span className="font-bold">{subtotalNet.toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                            </div>
                            <div className="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Value Added Tax (VAT {data.tax_rate}%):</span>
                                <span className="font-semibold">{taxAmount.toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                            </div>
                            <div className="flex justify-between text-sm font-black text-indigo-600 dark:text-indigo-400 pt-2 border-t border-slate-300 dark:border-slate-700">
                                <span>TOTAL CERTIFIED CLAIM PAYABLE BY CLIENT:</span>
                                <span>{totalClaimAmount.toLocaleString(undefined, { minimumFractionDigits: 2 })} ETB</span>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/finance/client-certificates"
                            className="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-xs transition-colors cursor-pointer"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/25 transition-all cursor-pointer disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Submitting Claim...' : 'Submit Progress Certificate'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
