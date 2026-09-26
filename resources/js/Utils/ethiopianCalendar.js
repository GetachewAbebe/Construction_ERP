/**
 * High-accuracy astronomical Ethiopian Calendar (EC / ዓ.ም) utility.
 * Implements the Kudlek-Beyene algorithm supporting leap year Pagume 5/6 variations.
 */

export const ETHIOPIC_EPOCH = 1723856;

export const AMHARIC_MONTHS = [
    '',
    'መስከረም',
    'ጥቅምት',
    'ኅዳር',
    'ታኅሣሥ',
    'ጥር',
    'የካቲት',
    'መጋቢት',
    'ሚያዝያ',
    'ግንቦት',
    'ሰኔ',
    'ሐምሌ',
    'ነሐሴ',
    'ጳጉሜን',
];

export const ENGLISH_MONTHS = [
    '',
    'Meskerem',
    'Tikimt',
    'Hidar',
    'Tahsas',
    'Tir',
    'Yekatit',
    'Megabit',
    'Miyazya',
    'Ginbot',
    'Sene',
    'Hamle',
    'Nehase',
    'Pagume',
];

export const AMHARIC_DAYS = [
    'እሑድ',
    'ሰኞ',
    'ማክሰኞ',
    'ረቡዕ',
    'ሐሙስ',
    'ዓርብ',
    'ቅዳሜ',
];

export function isEthiopianLeapYear(year) {
    return year % 4 === 3;
}

export function gregorianToJdn(year, month, day) {
    const a = Math.floor((14 - month) / 12);
    const y = year + 4800 - a;
    const m = month + 12 * a - 3;

    return (
        day +
        Math.floor((153 * m + 2) / 5) +
        365 * y +
        Math.floor(y / 4) -
        Math.floor(y / 100) +
        Math.floor(y / 400) -
        32045
    );
}

export function jdnToGregorian(jdn) {
    const a = jdn + 32044;
    const b = Math.floor((4 * a + 3) / 146097);
    const c = a - Math.floor((146097 * b) / 4);
    const d = Math.floor((4 * c + 3) / 1461);
    const e = c - Math.floor((1461 * d) / 4);
    const m = Math.floor((5 * e + 2) / 153);

    const day = e - Math.floor((153 * m + 2) / 5) + 1;
    const month = m + 3 - 12 * Math.floor(m / 10);
    const year = 100 * b + d - 4800 + Math.floor(m / 10);

    return [year, month, day];
}

export function gregorianToEthiopian(dateInput) {
    if (!dateInput) return null;

    let d;
    if (typeof dateInput === 'string') {
        const parts = dateInput.split('T')[0].split(' ')[0].split('-');
        if (parts.length === 3) {
            d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        } else {
            d = new Date(dateInput);
        }
    } else if (dateInput instanceof Date) {
        d = dateInput;
    } else {
        d = new Date(dateInput);
    }

    if (isNaN(d.getTime())) return null;

    const gYear = d.getFullYear();
    const gMonth = d.getMonth() + 1;
    const gDay = d.getDate();

    const jdn = gregorianToJdn(gYear, gMonth, gDay);
    const jdnDiff = jdn - ETHIOPIC_EPOCH;
    const n = jdnDiff % 1461;

    const ethYear = 4 * Math.floor(jdnDiff / 1461) + Math.floor(n / 365) - Math.floor(n / 1460);
    const dayOfYear = (n % 365) + 365 * Math.floor(n / 1460);

    const ethMonth = Math.floor(dayOfYear / 30) + 1;
    const ethDay = (dayOfYear % 30) + 1;

    const monthAm = AMHARIC_MONTHS[ethMonth] || '';
    const monthEn = ENGLISH_MONTHS[ethMonth] || '';
    const dayAm = AMHARIC_DAYS[d.getDay()] || '';

    return {
        year: ethYear,
        month: ethMonth,
        day: ethDay,
        monthNameAm: monthAm,
        monthNameEn: monthEn,
        dayNameAm: dayAm,
        formattedAm: `${monthAm} ${ethDay}, ${ethYear} ዓ.ም`,
        formattedEn: `${monthEn} ${ethDay}, ${ethYear} EC`,
        iso: `${ethYear}-${String(ethMonth).padStart(2, '0')}-${String(ethDay).padStart(2, '0')}`,
        isLeapYear: isEthiopianLeapYear(ethYear),
    };
}

export function ethiopianToGregorian(year, month, day) {
    const jdn = ETHIOPIC_EPOCH + 365 * year + Math.floor(year / 4) + 30 * month + day - 31;
    const [gYear, gMonth, gDay] = jdnToGregorian(jdn);
    return new Date(gYear, gMonth - 1, gDay);
}

/**
 * Format a date with user calendar preference.
 * preference: 'dual' | 'ec' | 'gc'
 */
export function formatCalendarDate(dateInput, preference = 'dual') {
    if (!dateInput) return '—';

    const ec = gregorianToEthiopian(dateInput);
    if (!ec) return String(dateInput);

    let d;
    if (typeof dateInput === 'string') {
        const parts = dateInput.split('T')[0].split(' ')[0].split('-');
        if (parts.length === 3) {
            d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        } else {
            d = new Date(dateInput);
        }
    } else {
        d = new Date(dateInput);
    }

    const gcFormatted = d.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });

    if (preference === 'ec') {
        return ec.formattedAm;
    }

    if (preference === 'gc') {
        return gcFormatted;
    }

    // Default 'dual'
    return `${gcFormatted} (${ec.formattedAm})`;
}

/**
 * Preference storage helper
 */
export function getCalendarPreference() {
    if (typeof window === 'undefined') return 'dual';
    return localStorage.getItem('natanem_calendar_preference') || 'dual';
}

export function setCalendarPreference(preference) {
    if (typeof window === 'undefined') return;
    localStorage.setItem('natanem_calendar_preference', preference);
    window.dispatchEvent(new CustomEvent('calendar-preference-changed', { detail: preference }));
}
