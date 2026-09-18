import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    ArrowLeft,
    Building2,
    Mail,
    Phone,
    MapPin,
    Tag,
    Edit3,
    CheckCircle2,
    XCircle,
} from 'lucide-react';

export default function Show({ vendor }) {
    return (
        <AuthenticatedLayout title={`Vendor • ${vendor.name}`} header="Inventory & Store">
            <Head title={`Vendor Details: ${vendor.name}`} />

            <div className="max-w-3xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/inventory/vendors"
                            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    {vendor.name}
                                </h1>
                                {vendor.is_active ? (
                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                                        <CheckCircle2 className="w-3 h-3" /> Active Vendor
                                    </span>
                                ) : (
                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                                        <XCircle className="w-3 h-3" /> Inactive
                                    </span>
                                )}
                            </div>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                Supplier Code: <span className="font-mono font-semibold">{vendor.code || `#${vendor.id}`}</span>
                            </p>
                        </div>
                    </div>

                    <Link
                        href={`/inventory/vendors/${vendor.id}/edit`}
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm"
                    >
                        <Edit3 className="w-3.5 h-3.5" />
                        <span>Edit Supplier</span>
                    </Link>
                </div>

                {/* Details Card */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Building2 className="w-3.5 h-3.5 text-blue-600" />
                                <span>Contact Person</span>
                            </div>
                            <div className="text-sm font-semibold text-slate-900 dark:text-white">
                                {vendor.contact_person || 'Unspecified'}
                            </div>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Tag className="w-3.5 h-3.5 text-indigo-600" />
                                <span>Supply Category</span>
                            </div>
                            <div className="text-sm font-semibold text-slate-900 dark:text-white">
                                {vendor.category || 'General Supplies'}
                            </div>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Mail className="w-3.5 h-3.5 text-emerald-600" />
                                <span>Email Address</span>
                            </div>
                            <div className="text-sm font-mono text-slate-900 dark:text-white">
                                {vendor.email || 'No email provided'}
                            </div>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <Phone className="w-3.5 h-3.5 text-amber-600" />
                                <span>Direct Phone</span>
                            </div>
                            <div className="text-sm font-mono text-slate-900 dark:text-white">
                                {vendor.phone || 'No phone registered'}
                            </div>
                        </div>

                        <div className="sm:col-span-2 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                                <MapPin className="w-3.5 h-3.5 text-rose-600" />
                                <span>Headquarters / Physical Address</span>
                            </div>
                            <div className="text-sm text-slate-900 dark:text-white">
                                {vendor.address || 'Address on file not specified.'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
