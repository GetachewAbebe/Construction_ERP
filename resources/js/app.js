import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

// Global Command Palette keyboard shortcut (Cmd+K / Ctrl+K)
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('open-command-palette'));
    }
});
