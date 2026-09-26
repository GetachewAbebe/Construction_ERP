import { useState, useEffect } from 'react';
import { Calendar as CalendarIcon } from 'lucide-react';
import {
    getCalendarPreference,
    setCalendarPreference,
} from '@/Utils/ethiopianCalendar';

export default function CalendarToggle({ className = '' }) {
    const [preference, setPreference] = useState(getCalendarPreference());
    const [isOpen, setIsOpen] = useState(false);

    useEffect(() => {
        const handlePrefChange = (e) => {
            setPreference(e.detail);
        };
        window.addEventListener('calendar-preference-changed', handlePrefChange);
        return () => {
            window.removeEventListener('calendar-preference-changed', handlePrefChange);
        };
    }, []);

    const options = [
        { id: 'dual', label: 'ዓ.ም + GC', fullLabel: 'Dual (ዓ.ም + GC)' },
        { id: 'ec', label: 'ዓ.ም', fullLabel: 'Ethiopian (ዓ.ም)' },
        { id: 'gc', label: 'GC', fullLabel: 'Gregorian (GC)' },
    ];

    const currentOption = options.find((o) => o.id === preference) || options[0];

    return (
        <div className={`relative inline-block text-left ${className}`}>
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-xs font-semibold text-slate-200 hover:text-white transition-all cursor-pointer shadow-xs"
                title="Switch Calendar System (Ethiopian / Gregorian)"
            >
                <CalendarIcon className="w-3.5 h-3.5 text-blue-400 shrink-0" />
                <span className="font-bold text-[11px]">{currentOption.label}</span>
            </button>

            {isOpen && (
                <>
                    <div
                        className="fixed inset-0 z-40"
                        onClick={() => setIsOpen(false)}
                    />
                    <div className="absolute right-0 top-full mt-2 w-44 rounded-2xl bg-[#0b0f19] border border-slate-700 shadow-2xl py-1.5 z-50 text-xs text-slate-200 ring-1 ring-white/10 animate-in fade-in duration-100">
                        <div className="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            Calendar System
                        </div>
                        {options.map((opt) => (
                            <button
                                key={opt.id}
                                type="button"
                                onClick={() => {
                                    setCalendarPreference(opt.id);
                                    setIsOpen(false);
                                }}
                                className={`w-full flex items-center justify-between px-3 py-2 text-left hover:bg-slate-800 transition-colors cursor-pointer ${
                                    preference === opt.id
                                        ? 'text-blue-400 font-bold bg-blue-950/40'
                                        : 'text-slate-300'
                                }`}
                            >
                                <span>{opt.fullLabel}</span>
                                {preference === opt.id && (
                                    <span className="w-1.5 h-1.5 rounded-full bg-blue-400" />
                                )}
                            </button>
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}
