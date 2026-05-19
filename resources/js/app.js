const formatRupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

function createToast(className) {
    const toast = document.createElement('div');
    toast.className = className;
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    document.body.appendChild(toast);

    let timer;

    return (message) => {
        toast.textContent = message;
        toast.classList.add('is-visible');
        clearTimeout(timer);
        timer = setTimeout(() => toast.classList.remove('is-visible'), 2200);
    };
}

function initRegistrationProgramPage() {
    const page = document.querySelector('[data-registration-program-page]');

    if (!page) {
        return;
    }

    const showToast = createToast('registration-toast');
    const summary = page.querySelector('.registration-summary');
    const summaryIcon = page.querySelector('[data-summary-icon]');
    const summaryName = page.querySelector('#summary-name');
    const summaryPrice = page.querySelector('#summary-price');
    const summaryTotal = page.querySelector('#summary-total');
    const continueButton = page.querySelector('[data-registration-continue]');
    const cards = [...page.querySelectorAll('.registration-program-card')];
    const radios = [...page.querySelectorAll('[data-program-radio]')];
    const stepperItems = [...page.querySelectorAll('.stepper-item')];

    const markSelectedCard = (radio) => {
        cards.forEach((card) => card.classList.toggle('is-selected', card.contains(radio)));
    };

    const flashSummary = () => {
        if (!summary) {
            return;
        }

        summary.classList.remove('is-updated');
        void summary.offsetWidth;
        summary.classList.add('is-updated');
    };

    radios.forEach((radio) => {
        if (radio.checked) {
            markSelectedCard(radio);
        }

        radio.addEventListener('change', () => {
            const card = radio.closest('.registration-program-card');
            const icon = card?.querySelector('[data-program-icon]');
            const price = Number(radio.dataset.price || 0);
            const formattedPrice = formatRupiah.format(price).replace('IDR', 'Rp').trim();

            if (summaryIcon && icon) {
                summaryIcon.innerHTML = icon.innerHTML;
            }

            if (summaryName) {
                summaryName.textContent = radio.dataset.name || 'Program ETC Planet';
            }

            if (summaryPrice) {
                summaryPrice.textContent = formattedPrice;
            }

            if (summaryTotal) {
                summaryTotal.textContent = formattedPrice;
            }

            markSelectedCard(radio);
            flashSummary();
            showToast(`${radio.dataset.name || 'Program'} dipilih.`);
        });
    });

    continueButton?.addEventListener('click', (event) => {
        event.preventDefault();

        const selectedProgram = page.querySelector('[data-program-radio]:checked')?.dataset.name || 'program ini';
        stepperItems[1]?.classList.add('is-preview');
        showToast(`Lanjut ke data pribadi untuk ${selectedProgram}.`);

        setTimeout(() => stepperItems[1]?.classList.remove('is-preview'), 1400);
    });
}

function initStudentDashboardPage() {
    const page = document.querySelector('[data-student-dashboard-page]');

    if (!page) {
        return;
    }

    const showToast = createToast('dashboard-toast');

    page.querySelectorAll('[data-stat-card]').forEach((card, index) => {
        setTimeout(() => card.classList.add('is-visible'), index * 120);
    });

    page.querySelectorAll('[data-stat-value]').forEach((value) => {
        const target = Number(value.textContent);

        if (!Number.isFinite(target)) {
            return;
        }

        let current = 0;
        const step = Math.max(1, Math.ceil(target / 24));
        value.textContent = '0';

        const counter = setInterval(() => {
            current = Math.min(target, current + step);
            value.textContent = String(current);

            if (current === target) {
                clearInterval(counter);
            }
        }, 28);
    });

    page.querySelectorAll('[data-progress-bar]').forEach((bar) => {
        const target = Math.min(100, Math.max(0, Number(bar.dataset.progressTarget || 0)));

        requestAnimationFrame(() => {
            bar.style.width = `${target}%`;
        });
    });

    page.querySelectorAll('[data-dashboard-action]').forEach((action) => {
        action.addEventListener('click', (event) => {
            event.preventDefault();
            showToast(action.dataset.dashboardAction || 'Aksi sedang disiapkan.');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initRegistrationProgramPage();
    initStudentDashboardPage();
});
