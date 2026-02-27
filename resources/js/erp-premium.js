/**
 * ERP Premium Global Logic
 * Handles Glassmorphism effects, Command Palette, Toasts, and Modals
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Glass Card Mouse Tracking Effect
    const glassCards = document.querySelectorAll('.hardened-glass');
    glassCards.forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (x > 0 && x < rect.width && y > 0 && y < rect.height) {
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px) scale(1.02)`;
            } else {
                card.style.transform = '';
            }
        });
        card.addEventListener('mouseleave', () => card.style.transform = '');
    });

    // 2. Stagger Entrance Initialization
    const staggerElements = document.querySelectorAll('.stagger-entrance');
    staggerElements.forEach((el, index) => {
        el.style.animationDelay = `${(index + 1) * 0.1}s`;
    });

    // 3. Command Palette Logic
    const cmdInput = document.getElementById('cmdInput');
    const cmdResults = document.getElementById('cmdResults');
    const commandPalette = document.getElementById('commandPalette');

    if (cmdInput && cmdResults && commandPalette) {
        window.renderCommandResults = function (filter = '') {
            if (!window.paletteLinks) return;
            const filtered = window.paletteLinks.filter(l => l.name.toLowerCase().includes(filter.toLowerCase()));
            cmdResults.innerHTML = filtered.map(l => `
                <a href="${l.url}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-erp-deep rounded-3 hover-bg-light transition-all">
                    <i class="bi ${l.icon} fs-5 opacity-75"></i>
                    <span class="fw-bold">${l.name}</span>
                </a>
            `).join('');
        };

        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                commandPalette.style.display = 'flex';
                cmdInput.focus();
                window.renderCommandResults();
            }
            if (e.key === 'Escape') {
                commandPalette.style.display = 'none';
            }
        });

        cmdInput.addEventListener('input', (e) => window.renderCommandResults(e.target.value));
        commandPalette.addEventListener('click', (e) => {
            if (e.target === commandPalette) commandPalette.style.display = 'none';
        });
    }

    // 4. Initialize Bootstrap Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

/**
 * Global Utilities (Exposed to window)
 */

window.markNotificationRead = function (id) {
    fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    });
};

window.openGlobalRejectModal = function (actionUrl, typeLabel, notificationId) {
    const modalEl = document.getElementById('globalRejectModal');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    document.getElementById('globalRejectForm').action = actionUrl;
    document.getElementById('rejectTypeLabel').innerText = typeLabel;

    document.getElementById('globalRejectForm').onsubmit = function () {
        window.markNotificationRead(notificationId);
        return true;
    };
    modal.show();
};

window.showToast = function (message, type = 'success', title = 'System Update') {
    const toastEl = document.getElementById('erpToast');
    if (!toastEl) return;
    const iconEl = document.getElementById('toastIcon');
    const iconContainer = document.getElementById('toastIconContainer');
    const titleEl = document.getElementById('toastTitle');
    const messageEl = document.getElementById('toastMessage');

    iconContainer.className = 'me-3 rounded-circle d-flex align-items-center justify-content-center ';
    if (type === 'success') {
        iconContainer.classList.add('bg-success-soft', 'text-success');
        iconEl.className = 'bi bi-check-circle-fill';
    } else if (type === 'danger' || type === 'error') {
        iconContainer.classList.add('bg-danger-soft', 'text-danger');
        iconEl.className = 'bi bi-exclamation-triangle-fill';
    } else {
        iconContainer.classList.add('bg-primary-soft', 'text-primary');
        iconEl.className = 'bi bi-info-circle-fill';
    }

    titleEl.innerText = title;
    messageEl.innerText = message;
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();
};

window.premiumConfirm = function (title, message, formId, name = '', imageUrl = '') {
    try {
        const modalEl = document.getElementById('premiumConfirmModal');
        if (!modalEl) {
            if (confirm(message)) document.getElementById(formId).submit();
            return;
        }

        document.getElementById('confirmTitle').innerText = title;
        document.getElementById('confirmMessage').innerText = message;
        document.getElementById('confirmUserName').innerText = name || '';
        document.getElementById('confirmUserName').style.display = name ? 'block' : 'none';

        const userImg = document.getElementById('confirmUserImage');
        const userInitial = document.getElementById('confirmUserInitial');

        if (imageUrl) {
            userImg.src = imageUrl;
            userImg.style.display = 'block';
            userInitial.style.display = 'none';
        } else {
            userImg.style.display = 'none';
            userInitial.style.display = 'flex';
            userInitial.innerText = name ? name.charAt(0).toUpperCase() : '?';
        }

        const confirmBtn = document.getElementById('confirmActionBtn');
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

        newBtn.addEventListener('click', function () {
            const form = document.getElementById(formId);
            if (form) form.submit();
            bootstrap.Modal.getInstance(modalEl).hide();
        });

        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } catch (e) {
        console.error('Confirm Error:', e);
        if (confirm(message)) document.getElementById(formId).submit();
    }
};
