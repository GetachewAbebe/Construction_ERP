import { useState, useEffect, useRef } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import {
    Building2,
    LayoutDashboard,
    Users,
    Banknote,
    Truck,
    Package,
    Settings,
    LogOut,
    ChevronDown,
    Menu,
    X,
    Sun,
    Moon,
    Bell,
    Sparkles,
    User as UserIcon,
    Shield,
    FileText,
    CalendarCheck,
    Briefcase,
    HardHat,
    History,
    CheckCircle2,
    AlertCircle,
    UserPlus,
    PlusCircle,
    Clock,
} from 'lucide-react';

export default function AuthenticatedLayout({ title, header, children }) {
    const page = usePage();
    const currentUrl = page.url || (typeof window !== 'undefined' ? window.location.pathname : '') || '';
    const { auth, counts, flash } = page.props || {};
    const user = auth?.user;

    const [mobileOpen, setMobileOpen] = useState(false);
    const [userDropdownOpen, setUserDropdownOpen] = useState(false);
    const [quickActionsOpen, setQuickActionsOpen] = useState(false);
    const dropdownRef = useRef(null);
    const quickActionRef = useRef(null);

    // Theme Switcher
    const [darkMode, setDarkMode] = useState(() => {
        if (typeof window === 'undefined') return false;
        const saved = localStorage.getItem('erp-theme');
        return saved === 'natanem-dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
    });

    useEffect(() => {
        const theme = darkMode ? 'natanem-dark' : 'natanem';
        const htmlRoot = document.getElementById('html-root');
        if (htmlRoot) htmlRoot.setAttribute('data-theme', theme);
        localStorage.setItem('erp-theme', theme);
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }, [darkMode]);

    // Close dropdowns on outside click or route change
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setUserDropdownOpen(false);
            }
            if (quickActionRef.current && !quickActionRef.current.contains(e.target)) {
                setQuickActionsOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Auto-close on page navigation
    useEffect(() => {
        setUserDropdownOpen(false);
        setQuickActionsOpen(false);
        setMobileOpen(false);
        const unbind = router.on('navigate', () => {
            setUserDropdownOpen(false);
            setQuickActionsOpen(false);
            setMobileOpen(false);
        });
        return () => unbind();
    }, [currentUrl]);

    const toggleTheme = () => setDarkMode(prev => !prev);

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    // Determine user role flags
    const getNormalizedRole = () => {
        const directRole = (user?.role || '').toLowerCase();
        if (directRole) return directRole;
        if (Array.isArray(user?.roles)) {
            return user.roles.map(r => (typeof r === 'string' ? r : r?.name || '')).join(' ').toLowerCase();
        }
        if (typeof user?.roles === 'string') {
            return user.roles.toLowerCase();
        }
        return '';
    };

    const roleString = getNormalizedRole();
    const isAdmin = roleString.includes('admin');
    const isHr = !isAdmin && (roleString.includes('hr') || roleString.includes('human'));
    const isInventory = !isAdmin && roleString.includes('inventory');
    const isFinance = !isAdmin && roleString.includes('financ');

    const displayRole = (() => {
        if (isAdmin) return 'Administrator';
        if (isHr) return 'HR Manager';
        if (isInventory) return 'Inventory Manager';
        if (isFinance) return 'Financial Manager';
        return user?.role || (Array.isArray(user?.roles) ? user.roles[0] : null) || 'Authorized User';
    })();

    // Role-based dashboard URL
    const dashboardUrl = (() => {
        if (isHr) return '/hr';
        if (isInventory) return '/inventory';
        if (isFinance) return '/finance/dashboard';
        return '/admin';
    })();

    // Role-based profile URL
    const profileUrl = (() => {
        if (isHr) return '/hr/profile';
        if (isInventory) return '/inventory/profile';
        if (isFinance) return '/finance/profile';
        return '/admin/profile';
    })();

    // Role-specific Navigation Tabs
    const getNavItems = () => {
        if (isHr) {
            return [
                {
                    label: 'Dashboard',
                    href: '/hr',
                    active: Boolean(currentUrl === '/hr' || currentUrl.startsWith('/hr/dashboard')),
                },
                {
                    label: 'Staff Directory',
                    href: '/hr/employees',
                    active: Boolean(currentUrl.startsWith('/hr/employees')),
                },
                {
                    label: 'Attendance',
                    href: '/hr/attendance',
                    active: Boolean(currentUrl.startsWith('/hr/attendance')),
                },
                {
                    label: 'Leave Requests',
                    href: '/hr/leaves',
                    active: Boolean(currentUrl.startsWith('/hr/leaves')),
                },
            ];
        }

        if (isInventory) {
            return [
                {
                    label: 'Dashboard',
                    href: '/inventory',
                    active: Boolean(currentUrl === '/inventory' || currentUrl.startsWith('/inventory/dashboard')),
                },
                {
                    label: 'Warehouse Stock',
                    href: '/inventory/items',
                    active: Boolean(currentUrl.startsWith('/inventory/items')),
                },
                {
                    label: 'Gate Passes & Loans',
                    href: '/inventory/loans',
                    active: Boolean(currentUrl.startsWith('/inventory/loans')),
                },
                {
                    label: 'Equipment Fleet',
                    href: '/inventory/equipment',
                    active: Boolean(currentUrl.startsWith('/inventory/equipment') || currentUrl.startsWith('/equipment')),
                },
                {
                    label: 'Suppliers & Vendors',
                    href: '/inventory/vendors',
                    active: Boolean(currentUrl.startsWith('/inventory/vendors')),
                },
            ];
        }

        if (isFinance) {
            return [
                {
                    label: 'Dashboard',
                    href: '/finance/dashboard',
                    active: Boolean(currentUrl === '/finance' || currentUrl.startsWith('/finance/dashboard')),
                },
                {
                    label: 'Projects & Budgets',
                    href: '/finance/projects',
                    active: Boolean(currentUrl.startsWith('/finance/projects')),
                },
                {
                    label: 'Expense Vouchers',
                    href: '/finance/expenses',
                    active: Boolean(currentUrl.startsWith('/finance/expenses')),
                },
                {
                    label: 'Site Daily Logs',
                    href: '/projects/daily-reports',
                    active: Boolean(currentUrl.startsWith('/projects/daily-reports')),
                },
            ];
        }

        // Administrator (Executive Oversight of Core Business Domains)
        return [
            {
                label: 'Dashboard',
                href: '/admin',
                active: Boolean(currentUrl === '/admin' || currentUrl.startsWith('/admin/dashboard') || currentUrl.includes('/admin/home')),
            },
            {
                label: 'Finance',
                href: '/finance/projects',
                active: Boolean(currentUrl.startsWith('/finance') || currentUrl.startsWith('/projects/daily-reports')),
            },
            {
                label: 'Inventory',
                href: '/inventory/items',
                active: Boolean(currentUrl.startsWith('/inventory') || currentUrl.startsWith('/equipment')),
            },
            {
                label: 'Human Resource',
                href: '/hr/employees',
                active: Boolean(currentUrl.startsWith('/hr')),
            },
            {
                label: 'Users',
                href: '/admin/users',
                active: Boolean(
                    currentUrl.startsWith('/admin/users') ||
                    currentUrl.startsWith('/admin/roles') ||
                    currentUrl.startsWith('/admin/activity-logs') ||
                    currentUrl.startsWith('/admin/system-settings') ||
                    currentUrl.startsWith('/admin/attendance-settings') ||
                    currentUrl.startsWith('/admin/maintenance') ||
                    currentUrl.startsWith('/admin/notification-templates') ||
                    currentUrl.startsWith('/admin/trash')
                ),
            },
        ];
    };

    const navItems = getNavItems();

    // Role-specific Quick Actions
    const getQuickActions = () => {
        if (isHr) {
            return [
                {
                    label: 'Onboard New Employee',
                    href: '/hr/employees/create',
                    icon: UserPlus,
                    color: 'text-blue-400',
                },
                {
                    label: 'Submit Leave Request',
                    href: '/hr/leaves/create',
                    icon: CalendarCheck,
                    color: 'text-emerald-400',
                },
                {
                    label: 'Daily Attendance Sheet',
                    href: '/hr/attendance/daily-sheet',
                    icon: Clock,
                    color: 'text-amber-400',
                },
                {
                    label: 'Approved Leaves Register',
                    href: '/hr/leaves/approved',
                    icon: CheckCircle2,
                    color: 'text-purple-400',
                },
            ];
        }

        if (isInventory) {
            return [
                {
                    label: 'Register Machinery',
                    href: '/inventory/equipment?action=register',
                    icon: HardHat,
                    color: 'text-indigo-400',
                },
                {
                    label: 'Issue Asset Loan',
                    href: '/inventory/loans/create',
                    icon: Package,
                    color: 'text-amber-400',
                },
                {
                    label: 'Add Stock Item',
                    href: '/inventory/items/create',
                    icon: PlusCircle,
                    color: 'text-blue-400',
                },
                {
                    label: 'Add New Vendor',
                    href: '/inventory/vendors/create',
                    icon: Truck,
                    color: 'text-emerald-400',
                },
            ];
        }

        if (isFinance) {
            return [
                {
                    label: 'New Expense Voucher',
                    href: '/finance/expenses/create',
                    icon: Banknote,
                    color: 'text-blue-400',
                },
                {
                    label: 'New Project Budget',
                    href: '/finance/projects/create',
                    icon: Building2,
                    color: 'text-amber-400',
                },
                {
                    label: 'Daily Construction Report',
                    href: '/projects/daily-reports',
                    icon: FileText,
                    color: 'text-emerald-400',
                },
            ];
        }

        // Administrator (Approval & Governance Queues - No Creation Actions)
        return [
            {
                label: 'Expense Approvals',
                href: '/admin/requests/finance',
                icon: Banknote,
                color: 'text-emerald-400',
                badgeCount: counts?.pendingExpenses || 0,
            },
            {
                label: 'Asset Loan Approvals',
                href: '/admin/requests/items',
                icon: Package,
                color: 'text-amber-400',
                badgeCount: counts?.pendingLoans || 0,
            },
            {
                label: 'Leave Approvals',
                href: '/admin/requests/leave-approvals',
                icon: CalendarCheck,
                color: 'text-blue-400',
                badgeCount: counts?.pendingLeaves || 0,
            },
            {
                label: 'User Directory & Roles',
                href: '/admin/users',
                icon: Shield,
                color: 'text-indigo-400',
            },
            {
                label: 'System Activity Logs',
                href: '/admin/activity-logs',
                icon: History,
                color: 'text-purple-400',
            },
        ];
    };

    const quickActions = getQuickActions();
    const pendingApprovalsCount = (counts?.pendingExpenses || 0) + (counts?.pendingLoans || 0) + (counts?.pendingLeaves || 0);

    const totalBadgeCount = (counts?.pendingExpenses || 0) + (counts?.pendingLoans || 0) + (counts?.pendingLeaves || 0) + (counts?.unreadNotifications || 0);

    return (
        <div className="min-h-screen bg-[#f4f6fb] dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col font-sans antialiased transition-colors duration-200">
            <Head title={title ? `${title} — Natanem Engineering` : 'Natanem Engineering ERP'} />

            {/* TOP NAVIGATION BAR (BuildIQ Style) */}
            <div className="relative z-40 w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 print:hidden no-print">
                <header className="relative z-40 bg-[#0b0f19] dark:bg-slate-900/95 text-white rounded-2xl md:rounded-3xl px-4 sm:px-6 py-3 shadow-xl border border-slate-800/80 backdrop-blur-xl flex items-center justify-between gap-3 sm:gap-6">
                    {/* Left: Brand Identity */}
                    <div className="flex items-center gap-3 shrink-0">
                        <Link href={dashboardUrl} className="flex items-center gap-2.5 group">
                            <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform">
                                <Building2 className="w-5 h-5" />
                            </div>
                            <span className="font-extrabold text-sm sm:text-base tracking-tight text-white group-hover:text-blue-200 transition-colors">
                                Natanem Engineering
                            </span>
                        </Link>
                    </div>

                    {/* Center: Navigation Pill Tabs (Desktop) */}
                    <nav className="hidden lg:flex items-center gap-1.5 bg-slate-900/70 p-1 rounded-full border border-slate-800">
                        {navItems.map((item) => (
                            <Link
                                key={item.label}
                                href={item.href}
                                className={`text-xs font-semibold px-3 xl:px-4 py-1.5 rounded-full whitespace-nowrap transition-all duration-200 ${
                                    item.active
                                        ? 'bg-white text-slate-950 shadow-sm font-bold scale-[1.02]'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800/80'
                                }`}
                            >
                                {item.label}
                            </Link>
                        ))}
                    </nav>

                    {/* Right: Actions & Profile */}
                    <div className="flex items-center gap-2 sm:gap-3 shrink-0">
                        {/* Quick Actions Dropdown */}
                        <div className="relative" ref={quickActionRef}>
                            <button
                                onClick={() => setQuickActionsOpen(!quickActionsOpen)}
                                className="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all cursor-pointer"
                            >
                                <Sparkles className="w-3.5 h-3.5 text-amber-300" />
                                <span>{isAdmin ? 'Approval Center' : 'Quick Actions'}</span>
                                {isAdmin && pendingApprovalsCount > 0 && (
                                    <span className="ml-1 px-1.5 py-0.5 rounded-full bg-rose-500 text-white font-extrabold text-[10px] shadow-sm animate-pulse">
                                        {pendingApprovalsCount}
                                    </span>
                                )}
                            </button>

                            {/* Quick Action Dropdown */}
                            {quickActionsOpen && (
                                <div className="absolute right-0 top-full mt-2.5 w-64 rounded-2xl bg-[#0b0f19] border border-slate-700/80 shadow-2xl py-2 z-50 text-xs text-slate-200 ring-1 ring-white/10 animate-in fade-in slide-in-from-top-2 duration-150">
                                    <div className="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 flex items-center justify-between">
                                        <span>{isAdmin ? 'Pending Approvals & Audit' : 'Fast Navigation'}</span>
                                        <span className="text-[9px] font-medium text-slate-400">({displayRole})</span>
                                    </div>
                                    {quickActions.map((action) => {
                                        const ActionIcon = action.icon;
                                        return (
                                            <Link
                                                key={action.label}
                                                href={action.href}
                                                onClick={() => setQuickActionsOpen(false)}
                                                className="flex items-center justify-between gap-2.5 px-4 py-2 hover:bg-slate-800 transition-colors"
                                            >
                                                <div className="flex items-center gap-2.5 min-w-0">
                                                    <ActionIcon className={`w-3.5 h-3.5 ${action.color} shrink-0`} />
                                                    <span className="truncate">{action.label}</span>
                                                </div>
                                                {Boolean(action.badgeCount && action.badgeCount > 0) && (
                                                    <span className="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 shrink-0">
                                                        {action.badgeCount} pending
                                                    </span>
                                                )}
                                            </Link>
                                        );
                                    })}
                                </div>
                            )}
                        </div>

                        {/* Notifications Bell */}
                        <Link
                            href="/notifications"
                            className="relative w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                            title="Notifications"
                        >
                            <Bell className="w-4 h-4" />
                            {Boolean(counts?.unreadNotifications > 0) && (
                                <span className="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500 animate-pulse" />
                            )}
                        </Link>

                        {/* Theme Toggle */}
                        <button
                            onClick={toggleTheme}
                            type="button"
                            className="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                            title={darkMode ? 'Switch to light mode' : 'Switch to dark mode'}
                        >
                            {darkMode ? <Sun className="w-4 h-4 text-amber-400" /> : <Moon className="w-4 h-4" />}
                        </button>

                        {/* User Profile Avatar with Dropdown */}
                        <div className="relative" ref={dropdownRef}>
                            <button
                                onClick={() => setUserDropdownOpen(!userDropdownOpen)}
                                className="flex items-center gap-2 pl-1.5 pr-2.5 py-1 rounded-full bg-slate-900/80 hover:bg-slate-800 border border-slate-800 transition-colors cursor-pointer"
                                title="Open Profile Menu"
                            >
                                {user?.employee?.profile_picture_url || user?.profile_picture_url ? (
                                    <img
                                        src={user.employee?.profile_picture_url || user.profile_picture_url}
                                        alt={user?.name || 'Profile'}
                                        className="w-7 h-7 rounded-full object-cover ring-1 ring-blue-500/40 shadow-sm"
                                    />
                                ) : (
                                    <div className="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 font-black text-xs flex items-center justify-center shadow-sm">
                                        {user?.first_name ? user.first_name.charAt(0).toUpperCase() : (user?.name ? user.name.charAt(0).toUpperCase() : 'U')}
                                    </div>
                                )}
                                <span className="hidden md:inline text-xs font-semibold text-slate-200">
                                    {user?.first_name || user?.name || 'Account'}
                                </span>
                                <ChevronDown className="w-3 h-3 text-slate-400" />
                            </button>

                            {userDropdownOpen && (
                                <div className="absolute right-0 top-full mt-2.5 w-64 rounded-2xl bg-[#0b0f19] border border-slate-700/90 shadow-2xl py-2 z-50 text-xs ring-1 ring-white/10 animate-in fade-in slide-in-from-top-2 duration-150">
                                    {/* User Identity Header */}
                                    <div className="p-3 mx-2 rounded-xl border border-slate-800/80 bg-slate-900/60 flex items-center gap-3">
                                        {user?.employee?.profile_picture_url || user?.profile_picture_url ? (
                                            <img
                                                src={user.employee?.profile_picture_url || user.profile_picture_url}
                                                alt={user?.name}
                                                className="w-9 h-9 rounded-xl object-cover ring-1 ring-blue-500/30 shrink-0"
                                            />
                                        ) : (
                                            <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 font-black text-sm flex items-center justify-center shadow-sm shrink-0">
                                                {user?.first_name ? user.first_name.charAt(0).toUpperCase() : (user?.name ? user.name.charAt(0).toUpperCase() : 'U')}
                                            </div>
                                        )}
                                        <div className="min-w-0 flex-1">
                                            <div className="font-bold text-white truncate text-sm">
                                                {user?.name || `${user?.first_name || ''} ${user?.last_name || ''}`.trim() || 'System Administrator'}
                                            </div>
                                            <div className="mt-1">
                                                <span className="inline-block px-2 py-0.5 rounded-full bg-blue-950 text-blue-300 font-bold text-[10px] border border-blue-900/60">
                                                    {displayRole}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Navigation Links */}
                                    <div className="py-1 px-1.5 mt-1 space-y-0.5">
                                        <Link
                                            href={profileUrl}
                                            className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors font-medium"
                                            onClick={() => setUserDropdownOpen(false)}
                                        >
                                            <UserIcon className="w-4 h-4 text-blue-400" />
                                            <span>My Profile</span>
                                        </Link>
                                    </div>

                                    <div className="pt-1.5 mt-1 border-t border-slate-800 px-1.5">
                                        <button
                                            onClick={handleLogout}
                                            className="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-400 hover:bg-rose-950/40 hover:text-rose-300 transition-colors cursor-pointer"
                                        >
                                            <LogOut className="w-4 h-4" />
                                            <span>Sign Out</span>
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Mobile Menu Toggle */}
                        <button
                            onClick={() => setMobileOpen(!mobileOpen)}
                            className="lg:hidden w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800"
                        >
                            {mobileOpen ? <X className="w-4 h-4" /> : <Menu className="w-4 h-4" />}
                        </button>
                    </div>
                </header>

                {/* Mobile Drawer Menu */}
                {mobileOpen && (
                    <div className="lg:hidden mt-2 p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-1">
                        {navItems.map((item) => (
                            <Link
                                key={item.label}
                                href={item.href}
                                onClick={() => setMobileOpen(false)}
                                className={`block px-3.5 py-2 rounded-xl text-xs font-semibold ${
                                    item.active
                                        ? 'bg-white text-slate-950 font-bold'
                                        : 'text-slate-300 hover:bg-slate-800'
                                }`}
                            >
                                {item.label}
                            </Link>
                        ))}
                        <div className="pt-2 mt-2 border-t border-slate-800 space-y-1">
                            <Link
                                href={profileUrl}
                                onClick={() => setMobileOpen(false)}
                                className="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800"
                            >
                                <UserIcon className="w-4 h-4 text-blue-400" />
                                <span>My Profile</span>
                            </Link>
                        </div>
                    </div>
                )}
            </div>

            {/* FLASH NOTIFICATIONS */}
            {flash?.success && (
                <div className="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 mt-4 print:hidden no-print">
                    <div className="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2.5 shadow-sm">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{flash.success}</span>
                    </div>
                </div>
            )}

            {flash?.error && (
                <div className="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 mt-4 print:hidden no-print">
                    <div className="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/50 flex items-center gap-2.5 shadow-sm">
                        <AlertCircle className="w-4 h-4 text-rose-600 shrink-0" />
                        <span>{flash.error}</span>
                    </div>
                </div>
            )}

            {/* MAIN DASHBOARD CANVAS */}
            <main className="relative z-10 flex-1 w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 print:p-0 print:m-0 print:max-w-none print:w-full">
                {children}
            </main>

            {/* ENRICHED ENTERPRISE FOOTER */}
            <footer className="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-xs font-medium text-slate-400 dark:text-slate-500 border-t border-slate-200/60 dark:border-slate-800/80 print:hidden no-print">
                <span>© 2026 Natanem Engineering &amp; Construction PLC</span>
                <span className="hidden sm:inline mx-1.5 text-slate-300 dark:text-slate-700">•</span>
                <span className="block sm:inline text-[11px] sm:text-xs text-slate-400/90 dark:text-slate-500">Enterprise Resource Planning. All rights reserved.</span>
            </footer>
        </div>
    );
}
