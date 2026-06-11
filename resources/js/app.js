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
    const radios = cards
        .map((card) => card.querySelector('input[type="radio"]'))
        .filter(Boolean);
    const stepperItems = [...page.querySelectorAll('.stepper-item')];

    const programData = (radio) => radio.closest('[data-program-radio]')?.dataset || radio.dataset;

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
            const data = programData(radio);
            const price = Number(data.price || 0);
            const formattedPrice = formatRupiah.format(price).replace('IDR', 'Rp').trim();

            if (summaryIcon && icon) {
                summaryIcon.innerHTML = icon.innerHTML;
            }

            if (summaryName) {
                summaryName.textContent = data.name || 'Program ETC Planet';
            }

            if (summaryPrice) {
                summaryPrice.textContent = formattedPrice;
            }

            if (summaryTotal) {
                summaryTotal.textContent = formattedPrice;
            }

            if (continueButton && data.nextUrl) {
                continueButton.href = data.nextUrl;
            }

            markSelectedCard(radio);
            flashSummary();
            showToast(`${data.name || 'Program'} dipilih.`);
        });
    });

    continueButton?.addEventListener('click', (event) => {
        const selectedRadio = radios.find((radio) => radio.checked);
        const selectedProgram = selectedRadio ? (programData(selectedRadio).name || 'program ini') : 'program ini';
        stepperItems[1]?.classList.add('is-preview');
        showToast(`Membuka form pendaftaran untuk ${selectedProgram}.`);

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

function initDashboardShell() {
    const shell = document.querySelector('[data-dashboard-shell]');

    if (!shell) {
        return;
    }

    const sidebar = shell.querySelector('[data-dashboard-sidebar]');
    const backdrop = shell.querySelector('[data-sidebar-backdrop]');
    const sidebarToggles = [...shell.querySelectorAll('[data-sidebar-toggle]')];
    const sidebarLabels = [...shell.querySelectorAll('[data-sidebar-label]')];
    const sidebarLinks = [...shell.querySelectorAll('[data-sidebar-nav-link]')];
    const brandRow = shell.querySelector('[data-sidebar-brand-row]');
    const brandLink = shell.querySelector('[data-sidebar-brand-link]');
    const toggleIcons = [...shell.querySelectorAll('[data-sidebar-toggle-icon]')];
    const profileTrigger = shell.querySelector('[data-dashboard-profile-trigger]');
    const profileMenu = shell.querySelector('[data-dashboard-profile-menu]');
    const desktopQuery = window.matchMedia('(min-width: 768px)');

    if (!sidebar) {
        return;
    }

    let sidebarCollapsed = window.localStorage?.getItem('etc-dashboard-sidebar-collapsed') === '1';
    let sidebarMobileOpen = false;
    let profileMenuOpen = false;

    const syncPrePaintState = () => {
        document.documentElement.dataset.dashboardSidebarCollapsed = sidebarCollapsed ? 'true' : 'false';
    };

    backdrop?.removeAttribute('x-cloak');
    profileMenu?.removeAttribute('x-cloak');

    const setElementHidden = (element, hidden) => {
        if (!element) {
            return;
        }

        element.classList.toggle('hidden', hidden);
        element.style.display = hidden ? 'none' : '';
    };

    const syncProfileMenu = () => {
        setElementHidden(profileMenu, !profileMenuOpen);
        profileTrigger?.setAttribute('aria-expanded', profileMenuOpen ? 'true' : 'false');
    };

    const closeProfileMenu = () => {
        profileMenuOpen = false;
        syncProfileMenu();
    };

    const syncSidebar = () => {
        const isDesktop = desktopQuery.matches;
        const collapsedDesktop = isDesktop && sidebarCollapsed && !sidebarMobileOpen;

        if (isDesktop) {
            sidebarMobileOpen = false;
        }

        sidebar.classList.toggle('translate-x-0', sidebarMobileOpen);
        sidebar.classList.toggle('-translate-x-full', !sidebarMobileOpen);
        sidebar.classList.toggle('md:w-16', sidebarCollapsed);
        sidebar.classList.toggle('md:px-2', sidebarCollapsed);
        sidebar.classList.toggle('md:w-64', !sidebarCollapsed);

        brandRow?.classList.toggle('md:px-0', collapsedDesktop);

        brandLink?.classList.toggle('md:pointer-events-none', collapsedDesktop);
        brandLink?.classList.toggle('md:w-0', collapsedDesktop);
        brandLink?.classList.toggle('md:flex-none', collapsedDesktop);
        brandLink?.classList.toggle('md:overflow-hidden', collapsedDesktop);
        brandLink?.classList.toggle('md:p-0', collapsedDesktop);
        brandLink?.classList.toggle('p-1', !collapsedDesktop);
        brandLink?.classList.toggle('pr-2', !collapsedDesktop);

        sidebarLinks.forEach((link) => {
            link.classList.toggle('md:justify-center', collapsedDesktop);
            link.classList.toggle('md:px-0', collapsedDesktop);
        });

        sidebarLabels.forEach((label) => {
            label.classList.toggle('hidden', collapsedDesktop);
        });

        setElementHidden(backdrop, !sidebarMobileOpen || isDesktop);

        sidebarToggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', isDesktop ? String(!sidebarCollapsed) : String(sidebarMobileOpen));
            toggle.setAttribute(
                'aria-label',
                sidebarMobileOpen ? 'Tutup sidebar' : (sidebarCollapsed && isDesktop ? 'Buka sidebar' : 'Ringkas sidebar'),
            );
        });

        toggleIcons.forEach((icon) => {
            icon.textContent = sidebarMobileOpen ? 'close' : (sidebarCollapsed ? 'menu' : 'menu_open');
        });
    };

    const toggleSidebar = () => {
        if (desktopQuery.matches) {
            sidebarCollapsed = !sidebarCollapsed;
            window.localStorage?.setItem('etc-dashboard-sidebar-collapsed', sidebarCollapsed ? '1' : '0');
            syncPrePaintState();
        } else {
            sidebarMobileOpen = !sidebarMobileOpen;
        }

        syncSidebar();
    };

    const closeMobileSidebar = () => {
        sidebarMobileOpen = false;
        syncSidebar();
    };

    sidebarToggles.forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggleSidebar();
        });
    });

    backdrop?.addEventListener('click', closeMobileSidebar);

    sidebarLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (!desktopQuery.matches) {
                closeMobileSidebar();
            }
        });
    });

    profileTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        profileMenuOpen = !profileMenuOpen;
        syncProfileMenu();
    });

    document.addEventListener('click', (event) => {
        if (!profileMenuOpen) {
            return;
        }

        if (profileMenu?.contains(event.target) || profileTrigger?.contains(event.target)) {
            return;
        }

        closeProfileMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        closeMobileSidebar();
        closeProfileMenu();
    });

    desktopQuery.addEventListener('change', syncSidebar);

    syncPrePaintState();
    syncSidebar();
    syncProfileMenu();
    requestAnimationFrame(() => {
        shell.dataset.dashboardHydrated = 'true';
    });
}

function initPublicChatbot() {
    const widget = document.querySelector('[data-chatbot-widget]');

    if (!widget) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const endpoint = widget.dataset.chatbotEndpoint;
    const toggle = widget.querySelector('[data-chatbot-toggle]');
    const close = widget.querySelector('[data-chatbot-close]');
    const panel = widget.querySelector('[data-chatbot-panel]');
    const form = widget.querySelector('[data-chatbot-form]');
    const input = form?.querySelector('input[name="message"]');
    const messages = widget.querySelector('[data-chatbot-messages]');
    let sessionId = window.localStorage?.getItem('etc_public_chatbot_session') || null;

    const setOpen = (isOpen) => {
        panel?.classList.toggle('hidden', !isOpen);
        toggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            input?.focus();
        }
    };

    const addMessage = (message, fromUser = false) => {
        const bubble = document.createElement('p');
        bubble.className = fromUser
            ? 'ml-auto max-w-[85%] rounded-card bg-etc-magenta px-4 py-3 text-sm leading-6 text-white shadow-soft'
            : 'max-w-[85%] rounded-card bg-etc-surface px-4 py-3 text-sm leading-6 text-etc-on-muted shadow-soft';
        bubble.textContent = message;
        messages?.appendChild(bubble);
        messages?.scrollTo({ top: messages.scrollHeight, behavior: 'smooth' });
    };

    toggle?.addEventListener('click', () => setOpen(panel?.classList.contains('hidden') ?? true));
    close?.addEventListener('click', () => setOpen(false));

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = input?.value.trim();

        if (!message || !endpoint || !token) {
            return;
        }

        input.value = '';
        addMessage(message, true);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    message,
                    session_id: sessionId,
                }),
            });
            const data = response.ok ? await response.json() : null;

            if (data?.session_id) {
                sessionId = data.session_id;
                window.localStorage?.setItem('etc_public_chatbot_session', sessionId);
            }

            addMessage(data?.reply || 'Maaf, jawaban belum tersedia. Silakan hubungi tim ETC melalui halaman kontak.');
        } catch {
            addMessage('Koneksi chatbot sedang bermasalah. Silakan coba lagi sebentar lagi.');
        }
    });
}

function initPublicReveal() {
    const elements = [...document.querySelectorAll('[data-public-reveal]')];

    if (elements.length === 0) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        elements.forEach((element) => element.classList.add('is-visible'));

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.16 });

    elements.forEach((element) => observer.observe(element));
}

function initPublicReels() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const formatter = new Intl.NumberFormat('id-ID');

    const setLikeState = (button, liked, count = null) => {
        button.dataset.liked = liked ? 'true' : 'false';
        button.setAttribute('aria-pressed', liked ? 'true' : 'false');

        const icon = button.querySelector('[data-like-icon]');

        if (icon) {
            icon.style.fontVariationSettings = liked ? "'FILL' 1" : "'FILL' 0";
        }

        const countTarget = button.dataset.likesCountTarget
            ? document.getElementById(button.dataset.likesCountTarget)
            : button.querySelector('[data-likes-count]');

        if (countTarget && count !== null) {
            countTarget.textContent = formatter.format(count);
        }
    };

    const markViewed = (video) => {
        if (!token || !video.dataset.viewEndpoint || video.dataset.viewed === 'true') {
            return;
        }

        video.dataset.viewed = 'true';

        fetch(video.dataset.viewEndpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        })
            .then((response) => response.ok ? response.json() : null)
            .then((data) => {
                const target = video.dataset.viewCountTarget
                    ? document.getElementById(video.dataset.viewCountTarget)
                    : document.querySelector('[data-views-count]');

                if (target && data?.views_count !== undefined) {
                    target.textContent = formatter.format(data.views_count);
                }
            })
            .catch(() => {});
    };

    const videoObserver = 'IntersectionObserver' in window
        ? new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const video = entry.target;

                if (entry.isIntersecting && entry.intersectionRatio >= 0.62) {
                    markViewed(video);

                    if (video.dataset.autoplayReel === 'true') {
                        video.play().catch(() => {});
                    }

                    return;
                }

                if (video.dataset.autoplayReel === 'true') {
                    video.pause();
                }
            });
        }, { threshold: [0, 0.62, 0.9] })
        : null;

    document.querySelectorAll('[data-view-endpoint]').forEach((video) => {
        if (videoObserver) {
            videoObserver.observe(video);
        } else {
            markViewed(video);
        }
    });

    document.querySelectorAll('[data-like-endpoint]').forEach((button) => {
        setLikeState(button, button.dataset.liked === 'true');

        button.addEventListener('click', () => {
            if (!token || button.dataset.loading === 'true') {
                return;
            }

            button.dataset.loading = 'true';

            fetch(button.dataset.likeEndpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            })
                .then((response) => response.ok ? response.json() : null)
                .then((data) => {
                    if (!data) {
                        return;
                    }

                    setLikeState(button, Boolean(data.liked), data.likes_count);
                })
                .catch(() => {})
                .finally(() => {
                    button.dataset.loading = 'false';
                });
        });
    });

    document.querySelectorAll('[data-vertical-reels-feed]').forEach((feed) => {
        const slides = [...feed.querySelectorAll('[data-reel-slide]')];

        if (slides.length <= 1) {
            return;
        }

        feed.tabIndex = feed.tabIndex >= 0 ? feed.tabIndex : 0;

        let locked = false;
        let settleTimer;

        const currentIndex = () => {
            const feedTop = feed.scrollTop;

            return slides.reduce((closest, slide, index) => {
                const distance = Math.abs(slide.offsetTop - feedTop);

                return distance < closest.distance ? { index, distance } : closest;
            }, { index: 0, distance: Number.POSITIVE_INFINITY }).index;
        };

        const goTo = (index) => {
            const target = slides[Math.max(0, Math.min(slides.length - 1, index))];

            if (!target) {
                return;
            }

            target.scrollIntoView({
                block: 'start',
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
            });
        };

        const snapToNearest = () => goTo(currentIndex());

        feed.addEventListener('wheel', (event) => {
            if (Math.abs(event.deltaY) < 8) {
                return;
            }

            event.preventDefault();

            if (locked) {
                return;
            }

            locked = true;
            goTo(currentIndex() + (event.deltaY > 0 ? 1 : -1));
            window.setTimeout(() => {
                locked = false;
            }, 520);
        }, { passive: false });

        feed.addEventListener('keydown', (event) => {
            const nextKeys = ['ArrowDown', 'PageDown', ' '];
            const previousKeys = ['ArrowUp', 'PageUp'];

            if (![...nextKeys, ...previousKeys].includes(event.key)) {
                return;
            }

            event.preventDefault();
            goTo(currentIndex() + (nextKeys.includes(event.key) ? 1 : -1));
        });

        feed.addEventListener('scroll', () => {
            clearTimeout(settleTimer);
            settleTimer = window.setTimeout(snapToNearest, 140);
        }, { passive: true });
    });
}

function initDataTables() {
    document.querySelectorAll('[data-data-table-form]').forEach((form) => {
        let debounceTimer;

        const submit = () => {
            const pageInput = form.querySelector('input[name="page"]');

            if (pageInput) {
                pageInput.value = '1';
            }

            form.requestSubmit();
        };

        form.addEventListener('input', (event) => {
            if (!event.target.matches('[data-table-filter-debounce]')) {
                return;
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(submit, 400);
        });

        form.addEventListener('change', (event) => {
            if (!event.target.matches('[data-table-filter-immediate]')) {
                return;
            }

            submit();
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initRegistrationProgramPage();
    initStudentDashboardPage();
    initDashboardShell();
    initPublicChatbot();
    initPublicReveal();
    initPublicReels();
    initDataTables();
});
