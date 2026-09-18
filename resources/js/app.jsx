import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Natanem Engineering ERP';

const getInitialData = () => {
    if (typeof window === 'undefined') return undefined;
    const el = document.getElementById('app');
    if (el?.dataset?.page) {
        try {
            return JSON.parse(el.dataset.page);
        } catch (e) {
            console.error('Failed to parse initial page from dataset:', e);
        }
    }
    const scriptEl = document.querySelector('script[data-page="app"][type="application/json"]');
    if (scriptEl?.textContent) {
        try {
            return JSON.parse(scriptEl.textContent);
        } catch (e) {
            console.error('Failed to parse initial page from script:', e);
        }
    }
    return undefined;
};

const initialPage = getInitialData();

createInertiaApp({
    page: initialPage,
    title: (title) => title ? `${title} · ${appName}` : appName,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#1e3a8a',
        showSpinner: true,
    },
});
