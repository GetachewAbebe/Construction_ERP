import { useState, useEffect } from 'react';
import {
    formatCalendarDate,
    gregorianToEthiopian,
    getCalendarPreference,
} from '@/Utils/ethiopianCalendar';

/**
 * Universal date display component adhering to the user's active
 * calendar preference (Dual / Ethiopian / Gregorian).
 */
export default function CalendarDate({
    date,
    preference: explicitPreference,
    className = '',
    showTooltip = true,
}) {
    const [preference, setPreference] = useState(
        explicitPreference || getCalendarPreference()
    );

    useEffect(() => {
        if (explicitPreference) {
            setPreference(explicitPreference);
            return;
        }

        const handlePreferenceChange = (e) => {
            setPreference(e.detail);
        };

        window.addEventListener('calendar-preference-changed', handlePreferenceChange);
        return () => {
            window.removeEventListener('calendar-preference-changed', handlePreferenceChange);
        };
    }, [explicitPreference]);

    if (!date) return <span className="text-slate-400">—</span>;

    const ec = gregorianToEthiopian(date);
    if (!ec) return <span className={className}>{String(date)}</span>;

    const formatted = formatCalendarDate(date, preference);

    let d;
    if (typeof date === 'string') {
        const parts = date.split('T')[0].split(' ')[0].split('-');
        if (parts.length === 3) {
            d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        } else {
            d = new Date(date);
        }
    } else {
        d = new Date(date);
    }

    const gcFormatted = d.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });

    const tooltip =
        preference === 'ec'
            ? `Gregorian: ${gcFormatted}`
            : preference === 'gc'
            ? `Ethiopian: ${ec.formattedAm}`
            : `GC: ${gcFormatted} | EC: ${ec.formattedAm}`;

    return (
        <span
            className={`inline-flex items-center gap-1 font-mono ${className}`}
            title={showTooltip ? tooltip : undefined}
        >
            {formatted}
        </span>
    );
}
