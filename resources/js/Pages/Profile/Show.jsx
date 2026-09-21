import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    User,
    Mail,
    Phone,
    Building2,
    Briefcase,
    Shield,
    Calendar,
    Edit3,
    CheckCircle2,
    ShieldCheck,
    Lock,
    ArrowLeft,
    Camera,
    Save,
    KeyRound,
    UserCheck,
    Check,
    Copy,
    AlertCircle,
    ChevronRight,
    Sparkles,
    FileBadge,
    Clock
} from 'lucide-react';

export default function Show({ user = {}, status }) {
    const [activeTab, setActiveTab] = useState('overview'); // 'overview' | 'edit' | 'security'
    const [copied, setCopied] = useState(false);

    const initials = user?.name
        ? user.name.split(' ').filter(Boolean).map(p => p[0]).join('').slice(0, 2).toUpperCase()
        : 'U';

    const avatarUrl = user?.employee?.profile_picture_url || user?.profile_picture_url;

    // Form for In-Place Editing
    const { data: editData, setData: setEditData, post, processing, errors, reset } = useForm({
        _method: 'PUT',
        name: user?.name || '',
        email: user?.email || '',
        phone_number: user?.phone_number || '',
        profile_picture: null,
        password: '',
        password_confirmation: '',
    });

    const [avatarPreview, setAvatarPreview] = useState(null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setEditData('profile_picture', file);
            setAvatarPreview(URL.createObjectURL(file));
        }
    };

    const roleString = (user?.role || (Array.isArray(user?.roles) ? user.roles[0] : '') || '').toLowerCase();
    const isAdmin = roleString.includes('admin');
    const isHr = !isAdmin && (roleString.includes('hr') || roleString.includes('human'));
    const isInventory = !isAdmin && roleString.includes('inventory');
    const isFinance = !isAdmin && roleString.includes('financ');

    const dashboardUrl = (() => {
        if (isHr) return '/hr';
        if (isInventory) return '/inventory';
        if (isFinance) return '/finance/dashboard';
        return '/admin';
    })();

    const profileUpdateUrl = (() => {
        if (isHr) return '/hr/profile/update';
        if (isInventory) return '/inventory/profile/update';
        if (isFinance) return '/finance/profile/update';
        return '/admin/profile/update';
    })();

    const handleSaveProfile = (e) => {
        e.preventDefault();
        post(profileUpdateUrl, {
            preserveScroll: true,
            onSuccess: () => {
                reset('password', 'password_confirmation');
                setActiveTab('overview');
            },
        });
    };

    const handleCopyEmail = () => {
        if (user?.email) {
            navigator.clipboard.writeText(user.email);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    // Derived permissions
    const permissionsList = Array.isArray(user?.permissions) && user.permissions.length > 0
        ? user.permissions
        : [
            'System Administration & Configuration',
            'Full Cost & Project Financial Controls',
            'Employee Directory & HR Management',
            'Inventory & Warehouse Asset Clearance',
            'Construction Daily Logs & Fleet Tracking',
            'User Accounts & Permission Assignment',
        ];

    return (
        <AuthenticatedLayout title="Executive User Profile">
            <div className="space-y-6 max-w-7xl mx-auto">
                {/* TOP BREADCRUMB & BACK LINK */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/70 dark:border-slate-800">
                    <div className="flex items-center gap-2 text-xs">
                        <Link
                            href={dashboardUrl}
                            className="inline-flex items-center gap-1.5 font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            <span>Dashboard</span>
                        </Link>
                        <span className="text-slate-300 dark:text-slate-700">/</span>
                        <span className="text-slate-500 dark:text-slate-400">Account Identity</span>
                        <span className="text-slate-300 dark:text-slate-700">/</span>
                        <span className="font-bold text-blue-600 dark:text-blue-400">Profile Overview</span>
                    </div>

                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            onClick={() => setActiveTab('edit')}
                            className={`inline-flex items-center gap-2 px-4 py-2 rounded-2xl font-bold text-xs shadow-sm transition-all cursor-pointer ${
                                activeTab === 'edit'
                                    ? 'bg-blue-600 text-white shadow-blue-600/30 ring-2 ring-blue-500/50'
                                    : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
                            }`}
                        >
                            <Edit3 className="w-3.5 h-3.5" />
                            <span>Edit Profile</span>
                        </button>
                    </div>
                </div>

                {/* STATUS ALERT */}
                {status && (
                    <div className="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3 shadow-sm">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{status}</span>
                    </div>
                )}

                {/* MAIN SPLIT GRID */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    {/* LEFT COLUMN: IDENTITY CARD & TAB SELECTOR (4 COLS) */}
                    <div className="lg:col-span-4 space-y-5">
                        {/* Profile Summary Card */}
                        <div className="rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl">
                            <div className="relative p-6 bg-gradient-to-br from-[#070d18] via-[#0c182b] to-[#14294a] text-white flex flex-col items-center text-center">
                                {/* Avatar with status ring */}
                                <div className="relative mb-4">
                                    {avatarPreview || avatarUrl ? (
                                        <img
                                            src={avatarPreview || avatarUrl}
                                            alt={user?.name}
                                            className="w-24 h-24 rounded-3xl object-cover ring-4 ring-white/10 shadow-2xl"
                                        />
                                    ) : (
                                        <div className="w-24 h-24 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 font-black text-3xl flex items-center justify-center ring-4 ring-white/10 shadow-2xl">
                                            {initials}
                                        </div>
                                    )}
                                    <span
                                        className="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-slate-950 flex items-center justify-center text-white shadow-md"
                                        title="Active Account"
                                    >
                                        <CheckCircle2 className="w-3.5 h-3.5" />
                                    </span>
                                </div>

                                {/* Identity Information */}
                                <h2 className="text-xl font-extrabold text-white tracking-tight">
                                    {user?.name || 'System User'}
                                </h2>
                                <p className="text-xs text-blue-200/80 mt-1 font-medium">
                                    {user?.position || 'Authorized Personnel'}
                                </p>

                                <div className="flex items-center gap-2 mt-3">
                                    <span className="px-3 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-bold text-[11px] border border-blue-500/30">
                                        {user?.role || (user?.roles && user.roles[0]) || 'Administrator'}
                                    </span>
                                    <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-[11px] border border-emerald-500/30">
                                        {user?.status || 'Active'}
                                    </span>
                                </div>

                                <div className="w-full mt-5 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                                    <span className="text-[11px] text-slate-400">Account ID</span>
                                    <span className="font-mono text-[11px] font-bold text-white bg-white/10 px-2 py-0.5 rounded-md">
                                        #{String(user?.id || 1).padStart(5, '0')}
                                    </span>
                                </div>
                            </div>

                            {/* Navigation Tabs (Vertical List) */}
                            <div className="p-3 bg-slate-50/50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 space-y-1">
                                <button
                                    type="button"
                                    onClick={() => setActiveTab('overview')}
                                    className={`w-full text-left flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all cursor-pointer ${
                                        activeTab === 'overview'
                                            ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/60'
                                    }`}
                                >
                                    <div className="flex items-center gap-3">
                                        <User className="w-4 h-4" />
                                        <span>Profile Overview</span>
                                    </div>
                                    <ChevronRight className="w-3.5 h-3.5 opacity-60" />
                                </button>

                                <button
                                    type="button"
                                    onClick={() => setActiveTab('edit')}
                                    className={`w-full text-left flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all cursor-pointer ${
                                        activeTab === 'edit'
                                            ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/60'
                                    }`}
                                >
                                    <div className="flex items-center gap-3">
                                        <Edit3 className="w-4 h-4" />
                                        <span>Edit Profile</span>
                                    </div>
                                    <ChevronRight className="w-3.5 h-3.5 opacity-60" />
                                </button>

                                <button
                                    type="button"
                                    onClick={() => setActiveTab('security')}
                                    className={`w-full text-left flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all cursor-pointer ${
                                        activeTab === 'security'
                                            ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/60'
                                    }`}
                                >
                                    <div className="flex items-center gap-3">
                                        <ShieldCheck className="w-4 h-4" />
                                        <span>Security &amp; Permissions</span>
                                    </div>
                                    <ChevronRight className="w-3.5 h-3.5 opacity-60" />
                                </button>
                            </div>
                        </div>

                        {/* Quick Security Badge Card */}
                        <div className="rounded-3xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm space-y-3 text-xs">
                            <div className="flex items-center gap-2.5 text-slate-900 dark:text-white font-bold">
                                <Shield className="w-4 h-4 text-amber-500" />
                                <span>Security Clearance</span>
                            </div>
                            <p className="text-slate-500 dark:text-slate-400 text-[11px] leading-relaxed">
                                This account holds privileged operational clearance with full system audit capabilities for Natanem Engineering ERP.
                            </p>
                            <div className="pt-2 flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold text-[11px]">
                                <CheckCircle2 className="w-3.5 h-3.5 shrink-0" />
                                <span>Multi-layer Enterprise Security Enabled</span>
                            </div>
                        </div>
                    </div>

                    {/* RIGHT COLUMN: TABBED PANELS (8 COLS) */}
                    <div className="lg:col-span-8">
                        {/* TAB 1: OVERVIEW */}
                        {activeTab === 'overview' && (
                            <div className="space-y-6">
                                {/* Personal Credentials Card */}
                                <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl p-6 sm:p-8">
                                    <div className="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                                        <div className="flex items-center gap-2.5">
                                            <div className="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center">
                                                <User className="w-4 h-4" />
                                            </div>
                                            <div>
                                                <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                    Personal Credentials
                                                </h3>
                                                <p className="text-[11px] text-slate-400">Direct contact &amp; identity specifications</p>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('edit')}
                                            className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 cursor-pointer"
                                        >
                                            <span>Modify</span>
                                            <span>→</span>
                                        </button>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-5 text-xs">
                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Official Full Name</span>
                                            <span className="text-slate-900 dark:text-white font-bold text-sm mt-1 block">{user?.name}</span>
                                        </div>

                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Corporate Email Address</span>
                                            <div className="flex items-center justify-between gap-2 mt-1">
                                                <span className="text-slate-900 dark:text-white font-bold text-xs truncate">{user?.email}</span>
                                                <button
                                                    type="button"
                                                    onClick={handleCopyEmail}
                                                    title="Copy email"
                                                    className="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer shrink-0"
                                                >
                                                    {copied ? <Check className="w-3.5 h-3.5 text-emerald-500" /> : <Copy className="w-3.5 h-3.5" />}
                                                </button>
                                            </div>
                                        </div>

                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Phone Contact</span>
                                            <span className="text-slate-900 dark:text-white font-bold text-sm mt-1 block flex items-center gap-2">
                                                <Phone className="w-3.5 h-3.5 text-slate-400" />
                                                <span>{user?.phone_number || 'Not provided'}</span>
                                            </span>
                                        </div>

                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Record Established</span>
                                            <span className="text-slate-900 dark:text-white font-bold text-sm mt-1 block flex items-center gap-2">
                                                <Calendar className="w-3.5 h-3.5 text-slate-400" />
                                                <span>{user?.created_at || 'Active System Record'}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Organizational Details Card */}
                                <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl p-6 sm:p-8">
                                    <div className="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                                        <div className="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center">
                                            <Building2 className="w-4 h-4" />
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                Organization &amp; Deployment
                                            </h3>
                                            <p className="text-[11px] text-slate-400">Department assignment and operational designation</p>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 text-xs">
                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Department</span>
                                            <span className="text-slate-900 dark:text-white font-bold text-sm mt-1 block">
                                                {user?.department || 'Executive Operations'}
                                            </span>
                                        </div>

                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Designated Position</span>
                                            <span className="text-slate-900 dark:text-white font-bold text-sm mt-1 block">
                                                {user?.position || 'Enterprise System User'}
                                            </span>
                                        </div>

                                        <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                                            <span className="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Corporate Entity</span>
                                            <span className="text-slate-900 dark:text-white font-bold text-xs mt-1 block">
                                                Natanem Engineering PLC
                                            </span>
                                        </div>
                                    </div>

                                    {/* Linked HR Employee Banner if available */}
                                    {user?.employee && (
                                        <div className="mt-5 p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 flex items-center justify-between text-xs">
                                            <div className="flex items-center gap-3">
                                                <UserCheck className="w-4 h-4 text-blue-600" />
                                                <div>
                                                    <span className="font-bold text-slate-900 dark:text-white block">
                                                        Synchronized with HR Personnel Roster
                                                    </span>
                                                    <span className="text-[11px] text-slate-500 dark:text-slate-400">
                                                        Linked Employee ID #{user.employee.id} • Hired: {user.employee.hire_date || 'Active'}
                                                    </span>
                                                </div>
                                            </div>
                                            <Link
                                                href={`/hr/employees/${user.employee.id}/edit`}
                                                className="font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 text-xs"
                                            >
                                                View HR Record →
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* TAB 2: IN-PLACE EDIT PROFILE FORM */}
                        {activeTab === 'edit' && (
                            <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl p-6 sm:p-8">
                                <div className="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                                    <div className="flex items-center gap-2.5">
                                        <div className="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center">
                                            <Edit3 className="w-4 h-4" />
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-base text-slate-900 dark:text-white">
                                                Edit Identity &amp; Profile Details
                                            </h3>
                                            <p className="text-[11px] text-slate-400">Update your official contact credentials and upload avatar picture</p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        onClick={() => setActiveTab('overview')}
                                        className="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white cursor-pointer"
                                    >
                                        Cancel
                                    </button>
                                </div>

                                {Object.keys(errors).length > 0 && (
                                    <div className="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/60 flex items-start gap-3">
                                        <AlertCircle className="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                                        <div>
                                            <span className="font-bold block">Validation Error:</span>
                                            <span className="text-[11px] block mt-0.5">{Object.values(errors)[0]}</span>
                                        </div>
                                    </div>
                                )}

                                <form onSubmit={handleSaveProfile} className="space-y-6">
                                    {/* Avatar Upload with Live Preview */}
                                    <div className="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80 flex items-center gap-5">
                                        {avatarPreview || avatarUrl ? (
                                            <img
                                                src={avatarPreview || avatarUrl}
                                                alt="Preview"
                                                className="w-16 h-16 rounded-2xl object-cover ring-2 ring-blue-500 shadow-md shrink-0"
                                            />
                                        ) : (
                                            <div className="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 font-black text-xl flex items-center justify-center shrink-0">
                                                {initials}
                                            </div>
                                        )}

                                        <div className="flex-1 min-w-0">
                                            <label className="block text-xs font-bold text-slate-900 dark:text-white mb-1">
                                                Profile Picture
                                            </label>
                                            <p className="text-[11px] text-slate-500 dark:text-slate-400 mb-2">
                                                Upload JPG, PNG or WebP image up to 2MB.
                                            </p>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                onChange={handleFileChange}
                                                className="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer"
                                            />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        {/* Name */}
                                        <div>
                                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                Full Name
                                            </label>
                                            <input
                                                type="text"
                                                value={editData.name}
                                                onChange={(e) => setEditData('name', e.target.value)}
                                                required
                                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none"
                                            />
                                        </div>

                                        {/* Email */}
                                        <div>
                                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                Corporate Email
                                            </label>
                                            <input
                                                type="email"
                                                value={editData.email}
                                                onChange={(e) => setEditData('email', e.target.value)}
                                                required
                                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none"
                                            />
                                        </div>

                                        {/* Phone */}
                                        <div>
                                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                Phone Number
                                            </label>
                                            <input
                                                type="text"
                                                value={editData.phone_number}
                                                onChange={(e) => setEditData('phone_number', e.target.value)}
                                                placeholder="+251 9XX XXX XXX"
                                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none"
                                            />
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="flex items-center justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-800">
                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('overview')}
                                            className="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={processing}
                                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all disabled:opacity-50 cursor-pointer"
                                        >
                                            <Save className="w-4 h-4" />
                                            <span>{processing ? 'Saving...' : 'Save Profile Changes'}</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        )}

                        {/* TAB 3: SECURITY & PERMISSIONS */}
                        {activeTab === 'security' && (
                            <div className="space-y-6">
                                {/* Spatie Access Privileges Card */}
                                <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl p-6 sm:p-8">
                                    <div className="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                                        <div className="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center">
                                            <ShieldCheck className="w-4 h-4" />
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                System Permissions &amp; Roles
                                            </h3>
                                            <p className="text-[11px] text-slate-400">Granted enterprise capabilities &amp; modules access</p>
                                        </div>
                                    </div>

                                    <div className="pt-5 space-y-4">
                                        <div>
                                            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
                                                Assigned Enterprise Roles
                                            </span>
                                            <div className="flex flex-wrap gap-2">
                                                {(user?.roles && user.roles.length > 0 ? user.roles : [user?.role || 'Administrator']).map((r) => (
                                                    <span
                                                        key={r}
                                                        className="px-3 py-1 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold text-xs border border-blue-200 dark:border-blue-900/60 flex items-center gap-1.5"
                                                    >
                                                        <Shield className="w-3.5 h-3.5 text-blue-500" />
                                                        <span>{r}</span>
                                                    </span>
                                                ))}
                                            </div>
                                        </div>

                                        <div className="pt-4 border-t border-slate-100 dark:border-slate-800">
                                            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-3">
                                                Active Clearance Capabilities
                                            </span>
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                {permissionsList.map((perm, idx) => (
                                                    <div
                                                        key={idx}
                                                        className="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80 flex items-center gap-2.5 text-xs text-slate-800 dark:text-slate-200"
                                                    >
                                                        <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0" />
                                                        <span className="font-semibold">{perm}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Password Reset Card */}
                                <div className="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl p-6 sm:p-8">
                                    <div className="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                                        <div className="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center">
                                            <KeyRound className="w-4 h-4" />
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                Update Passcode / Password
                                            </h3>
                                            <p className="text-[11px] text-slate-400">Ensure strong credentials with at least 8 characters</p>
                                        </div>
                                    </div>

                                    <form onSubmit={handleSaveProfile} className="pt-5 space-y-5">
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                    New Password
                                                </label>
                                                <input
                                                    type="password"
                                                    value={editData.password}
                                                    onChange={(e) => setEditData('password', e.target.value)}
                                                    placeholder="••••••••••••"
                                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none"
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                    Confirm New Password
                                                </label>
                                                <input
                                                    type="password"
                                                    value={editData.password_confirmation}
                                                    onChange={(e) => setEditData('password_confirmation', e.target.value)}
                                                    placeholder="••••••••••••"
                                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-600 outline-none"
                                                />
                                            </div>
                                        </div>

                                        <div className="flex justify-end pt-2">
                                            <button
                                                type="submit"
                                                disabled={processing || !editData.password}
                                                className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all disabled:opacity-40 cursor-pointer"
                                            >
                                                <Lock className="w-4 h-4" />
                                                <span>Update Password</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
