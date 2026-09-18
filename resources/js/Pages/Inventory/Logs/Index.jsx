import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { History, ArrowUpRight, ArrowDownRight, Package } from 'lucide-react';

export default function Index({ logs }) {
    const logList = logs?.data || [];

    return (
        <AuthenticatedLayout title="Inventory Movement Logs" header="Inventory & Store">
            <div className="space-y-6">
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Warehouse Movement Logs
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Audit trail of item check-ins, loan check-outs, transfers, and inventory adjustments.
                        </p>
                    </div>
                </div>

                <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th className="py-3.5 px-4">Timestamp</th>
                                    <th className="py-3.5 px-4">Item</th>
                                    <th className="py-3.5 px-4">Action / Reason</th>
                                    <th className="py-3.5 px-4">Quantity Change</th>
                                    <th className="py-3.5 px-4">Balance After</th>
                                    <th className="py-3.5 px-4">Authorized By</th>
                                    <th className="py-3.5 px-4">Remarks</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800/80">
                                {logList.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="py-8 text-center text-slate-400 text-xs">
                                            No warehouse movement logs recorded.
                                        </td>
                                    </tr>
                                ) : (
                                    logList.map((log) => {
                                        const isPositive = Number(log.change_amount || 0) >= 0;

                                        return (
                                            <tr key={log.id} className="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                <td className="py-3 px-4 font-mono text-slate-500 text-[11px]">
                                                    {log.created_at ? new Date(log.created_at).toLocaleString() : '—'}
                                                </td>
                                                <td className="py-3 px-4">
                                                    <div className="font-bold text-slate-900 dark:text-white">
                                                        {log.item?.name || 'Stock Item'}
                                                    </div>
                                                    <div className="text-[11px] text-slate-400 font-mono">
                                                        SKU: #{log.item?.item_no || log.item?.id}
                                                    </div>
                                                </td>
                                                <td className="py-3 px-4">
                                                    <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                        {log.reason || 'Movement'}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 font-mono font-bold">
                                                    <span className={`inline-flex items-center gap-1 ${
                                                        isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'
                                                    }`}>
                                                        {isPositive ? '+' : ''}{log.change_amount}
                                                    </span>
                                                </td>
                                                <td className="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                                    {log.new_quantity}
                                                </td>
                                                <td className="py-3 px-4 text-slate-600 dark:text-slate-300">
                                                    {log.user?.name || 'System Operator'}
                                                </td>
                                                <td className="py-3 px-4 text-slate-400 text-[11px]">
                                                    {log.remarks || '—'}
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>

                    {logs?.links && logs.links.length > 3 && (
                        <div className="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <div>Showing {logs.from || 0} to {logs.to || 0} of {logs.total || 0} logs</div>
                            <div className="flex items-center gap-1">
                                {logs.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        preserveScroll
                                        className={`px-2.5 py-1 rounded-lg text-xs font-semibold ${
                                            link.active
                                                ? 'bg-blue-900 text-white'
                                                : link.url
                                                ? 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                                                : 'text-slate-300 dark:text-slate-700 cursor-not-allowed'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
