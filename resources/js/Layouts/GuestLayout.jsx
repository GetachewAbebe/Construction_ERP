import { useState, useEffect } from 'react';
import { Moon, Sun } from 'lucide-react';

export default function GuestLayout({ children }) {
    const [darkMode, setDarkMode] = useState(() => {
        if (typeof window === 'undefined') return false;
        const saved = localStorage.getItem('erp-theme');
        return saved === 'natanem-dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
    });

    useEffect(() => {
        const theme = darkMode ? 'natanem-dark' : 'natanem';
        const htmlRoot = document.getElementById('html-root');
        if (htmlRoot) {
            htmlRoot.setAttribute('data-theme', theme);
        }
        localStorage.setItem('erp-theme', theme);
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }, [darkMode]);

    const toggleTheme = () => {
        setDarkMode(prev => !prev);
    };

    return (
        <div className="relative min-h-screen flex flex-col justify-between items-center p-4 sm:p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 selection:bg-blue-900 selection:text-white transition-colors duration-300 overflow-hidden">
            {/* Subtle Engineering Blueprint Ambient Background */}
            <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,#e2e8f01a_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f01a_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#1e293b2a_1px,transparent_1px),linear-gradient(to_bottom,#1e293b2a_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            
            {/* Ambient Gradient Glows */}
            <div className="pointer-events-none absolute -top-40 -left-40 w-96 h-96 rounded-full bg-blue-600/10 dark:bg-blue-500/10 blur-3xl"></div>
            <div className="pointer-events-none absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-amber-500/10 dark:bg-amber-400/5 blur-3xl"></div>

            {/* Top Bar with Theme Toggle */}
            <header className="relative z-10 w-full max-w-md flex items-center justify-end py-2">
                <button
                    onClick={toggleTheme}
                    type="button"
                    title={darkMode ? 'Switch to light mode' : 'Switch to dark mode'}
                    className="p-2.5 rounded-2xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-white/80 dark:bg-slate-900/80 hover:bg-white dark:hover:bg-slate-900 shadow-sm border border-slate-200/80 dark:border-slate-800/80 backdrop-blur-md transition-all cursor-pointer"
                >
                    {darkMode ? (
                        <Sun className="w-4 h-4 text-amber-400" />
                    ) : (
                        <Moon className="w-4 h-4 text-slate-700" />
                    )}
                </button>
            </header>

            {/* Main Content Area */}
            <main className="relative z-10 w-full max-w-md my-auto py-4">
                {children}
            </main>

            {/* Meaningful Enterprise Footer */}
            <footer className="relative z-10 w-full max-w-lg text-center text-xs text-slate-400 dark:text-slate-500 py-3 leading-relaxed">
                <span>© 2026 Natanem Engineering &amp; Construction PLC</span>
                <span className="hidden sm:inline mx-1.5 text-slate-300 dark:text-slate-700">•</span>
                <span className="block sm:inline text-[11px] sm:text-xs text-slate-400/90 dark:text-slate-500">Enterprise Resource Planning. All rights reserved.</span>
            </footer>
        </div>
    );
}
