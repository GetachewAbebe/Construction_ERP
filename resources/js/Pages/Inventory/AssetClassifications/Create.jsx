import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Boxes } from 'lucide-react';

export default function Create({ parents = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        code: '',
        parent_id: '',
        icon_identifier: '',
        description: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/inventory/asset-classifications');
    };

    return (
        <AuthenticatedLayout title="New Category" header="Inventory & Store">
            <Head title="Create Asset Category" />

            <div className="max-w-2xl mx-auto space-y-6">
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-2">
                        <Link
                            href="/inventory/asset-classifications"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                Add New Category Node
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Expand taxonomy for raw materials, machinery, or consumables.
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Category Name <span className="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                required
                                placeholder="e.g. Structural Steel or Earthmoving Equipment"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                className={`w-full text-xs rounded-xl border ${
                                    errors.name ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                            />
                            {errors.name && <p className="text-rose-500 text-[11px] mt-1">{errors.name}</p>}
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Nomenclature Code (Optional)
                                </label>
                                <input
                                    type="text"
                                    placeholder="e.g. MTRL-ST"
                                    value={data.code}
                                    onChange={e => setData('code', e.target.value.toUpperCase())}
                                    className={`w-full font-mono text-xs uppercase rounded-xl border ${
                                        errors.code ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700'
                                    } bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                                />
                                {errors.code && <p className="text-rose-500 text-[11px] mt-1">{errors.code}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Parent Category
                                </label>
                                <select
                                    value={data.parent_id}
                                    onChange={e => setData('parent_id', e.target.value)}
                                    className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">None (Top-Level Root Category)</option>
                                    {parents.map(p => (
                                        <option key={p.id} value={p.id}>
                                            {'— '.repeat(p.depth || 0)}{p.name} {p.code ? `(${p.code})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Description & Allocation Scope
                            </label>
                            <textarea
                                rows={3}
                                placeholder="Describe items grouped under this classification node..."
                                value={data.description}
                                onChange={e => setData('description', e.target.value)}
                                className="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href="/inventory/asset-classifications"
                            className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            <span>{processing ? 'Saving...' : 'Save Category Node'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
