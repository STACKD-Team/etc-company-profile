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

function initPublicRegistrationProgress() {
    const storageKey = 'etc-registration-progress';
    const path = window.location.pathname.replace(/\/+$/, '') || '/';
    const inferredProgress = [
        { pattern: /^\/registration\/form(?:\/|$)/, progress: 1 },
        { pattern: /^\/registration\/payment(?:\/|$)/, progress: 2 },
        { pattern: /^\/registration\/confirmation(?:\/|$)/, progress: 3 },
        { pattern: /^\/student\/payments(?:\/|$)/, progress: 4 },
        { pattern: /^\/student\/classes(?:\/|$)/, progress: 5 },
    ].find(({ pattern }) => pattern.test(path))?.progress;

    if (Number.isInteger(inferredProgress)) {
        const storedProgress = Number.parseInt(window.localStorage?.getItem(storageKey) || '0', 10);
        window.localStorage?.setItem(storageKey, String(Math.max(storedProgress, inferredProgress)));
    }

    document.querySelectorAll('[data-registration-flow-start]').forEach((link) => {
        link.addEventListener('click', () => window.localStorage?.setItem(storageKey, '0'));
    });

    document.querySelectorAll('[data-public-registration-flow]').forEach((flow) => {
        const steps = [...flow.querySelectorAll('[data-registration-flow-step]')];
        const progress = Math.min(steps.length, Math.max(0, Number.parseInt(
            window.localStorage?.getItem(storageKey) || '0',
            10,
        )));
        const lineProgress = steps.length > 1 && progress > 1
            ? ((progress - 1) / (steps.length - 1)) * 80
            : 0;

        flow.style.setProperty('--registration-progress', `${lineProgress}%`);
        flow.dataset.progress = String(progress);

        steps.forEach((step, index) => {
            const completed = index < progress;

            step.classList.toggle('is-complete', completed);
            step.setAttribute('aria-current', index === progress && progress < steps.length ? 'step' : 'false');
        });
    });
}

function initStudentDashboardPage() {
    const page = document.querySelector('[data-student-dashboard-page]');

    if (!page) {
        return;
    }

    const showToast = createToast('dashboard-toast');

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

function initStudentRevealCards() {
    document.querySelectorAll('[data-reveal-card]').forEach((card, index) => {
        setTimeout(() => card.classList.add('is-visible'), index * 90);
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
    const suggestions = widget.querySelector('[data-chatbot-suggestions]');
    const submit = widget.querySelector('[data-chatbot-submit]');
    const isPublicDiscoveryChatbot = widget.classList.contains('public-discovery-chatbot');
    let sessionId = window.localStorage?.getItem('etc_public_chatbot_session') || null;

    const setOpen = (isOpen) => {
        panel?.classList.toggle('hidden', !isOpen);
        panel?.classList.toggle('is-open', isOpen);
        widget.classList.toggle('is-open', isOpen);
        toggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            input?.focus();
        }
    };

    const addMessage = (message, fromUser = false) => {
        if (isPublicDiscoveryChatbot) {
            const row = document.createElement('div');
            const bubble = document.createElement('p');

            row.className = fromUser
                ? 'public-discovery-chatbot__user-row'
                : 'public-discovery-chatbot__bot-row';
            bubble.className = fromUser
                ? 'public-discovery-chatbot__bubble public-discovery-chatbot__bubble--user'
                : 'public-discovery-chatbot__bubble public-discovery-chatbot__bubble--bot';
            bubble.textContent = message;

            if (!fromUser) {
                const avatar = document.createElement('span');
                const icon = document.createElement('span');

                avatar.className = 'public-discovery-chatbot__mini-avatar';
                icon.className = 'material-symbols-outlined';
                icon.textContent = 'smart_toy';
                avatar.appendChild(icon);
                row.appendChild(avatar);
            }

            row.appendChild(bubble);
            messages?.appendChild(row);
            messages?.scrollTo({ top: messages.scrollHeight, behavior: 'smooth' });

            return;
        }

        const bubble = document.createElement('p');
        bubble.className = fromUser
            ? 'ml-auto max-w-[85%] rounded-card bg-etc-magenta px-4 py-3 text-sm leading-6 text-white shadow-soft'
            : 'max-w-[85%] rounded-card bg-etc-surface px-4 py-3 text-sm leading-6 text-etc-on-muted shadow-soft';
        bubble.textContent = message;
        messages?.appendChild(bubble);
        messages?.scrollTo({ top: messages.scrollHeight, behavior: 'smooth' });
    };

    const showTypingIndicator = () => {
        if (!isPublicDiscoveryChatbot || !messages) {
            return null;
        }

        const row = document.createElement('div');
        const avatar = document.createElement('span');
        const avatarIcon = document.createElement('span');
        const indicator = document.createElement('span');

        row.className = 'public-discovery-chatbot__bot-row';
        row.dataset.chatbotTyping = 'true';
        avatar.className = 'public-discovery-chatbot__mini-avatar';
        avatarIcon.className = 'material-symbols-outlined';
        avatarIcon.textContent = 'smart_toy';
        indicator.className = 'public-discovery-chatbot__typing';
        indicator.innerHTML = '<span></span><span></span><span></span>';
        avatar.appendChild(avatarIcon);
        row.append(avatar, indicator);
        messages.appendChild(row);
        messages.scrollTo({ top: messages.scrollHeight, behavior: 'smooth' });

        return row;
    };

    toggle?.addEventListener('click', () => setOpen(panel?.classList.contains('hidden') ?? true));
    close?.addEventListener('click', () => setOpen(false));

    widget.querySelectorAll('[data-chatbot-suggestion]').forEach((suggestion) => {
        suggestion.addEventListener('click', () => {
            if (!input || !form) {
                return;
            }

            input.value = suggestion.dataset.chatbotSuggestion || suggestion.textContent.trim();
            suggestions?.classList.add('hidden');
            form.requestSubmit();
        });
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = input?.value.trim();

        if (!message || !endpoint || !token) {
            return;
        }

        input.value = '';
        addMessage(message, true);
        suggestions?.classList.add('hidden');
        const typingIndicator = showTypingIndicator();
        submit?.setAttribute('disabled', 'disabled');

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
        } finally {
            typingIndicator?.remove();
            submit?.removeAttribute('disabled');
            input?.focus();
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

function initPublicStatCounters() {
    const counters = [...document.querySelectorAll('[data-public-stat-counter]')];

    if (counters.length === 0) {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const numberFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
    const animations = new WeakMap();

    const renderValue = (counter, value) => {
        const suffix = counter.dataset.counterSuffix || '';
        counter.textContent = `${numberFormatter.format(Math.round(value))}${suffix}`;
    };

    const stopAnimation = (counter) => {
        const frame = animations.get(counter);

        if (frame) {
            window.cancelAnimationFrame(frame);
            animations.delete(counter);
        }
    };

    const animateCounter = (counter) => {
        const target = Number(counter.dataset.counterTarget || 0);

        stopAnimation(counter);

        if (reducedMotion || target <= 0) {
            renderValue(counter, target);
            return;
        }

        const startedAt = performance.now();
        const duration = 1200;

        const tick = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const easedProgress = 1 - Math.pow(1 - progress, 3);

            renderValue(counter, target * easedProgress);

            if (progress < 1) {
                animations.set(counter, window.requestAnimationFrame(tick));
                return;
            }

            animations.delete(counter);
            renderValue(counter, target);
        };

        animations.set(counter, window.requestAnimationFrame(tick));
    };

    if (!('IntersectionObserver' in window)) {
        counters.forEach(animateCounter);
        return;
    }

    counters.forEach((counter) => renderValue(counter, 0));

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            animateCounter(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.45,
    });

    counters.forEach((counter) => observer.observe(counter));
}

function initPublicHomeCarousels() {
    const carousels = [...document.querySelectorAll('[data-public-carousel]')];

    if (carousels.length === 0) {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    carousels.forEach((carousel) => {
        const viewport = carousel.querySelector('[data-carousel-viewport]');
        const track = carousel.querySelector('[data-carousel-track]');
        let slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
        const previous = carousel.querySelector('[data-carousel-prev]');
        const next = carousel.querySelector('[data-carousel-next]');

        if (!viewport || !track || slides.length === 0) {
            return;
        }

        const originalSlideCount = slides.length;

        if (originalSlideCount > 1) {
            const originals = [...slides];

            while (slides.length < originalSlideCount * 3) {
                originals.forEach((slide) => {
                    const clone = slide.cloneNode(true);

                    clone.dataset.carouselClone = 'true';
                    clone.setAttribute('aria-hidden', 'true');
                    clone.removeAttribute('id');

                    if (clone.matches('a, button, input, select, textarea, [tabindex]')) {
                        clone.setAttribute('tabindex', '-1');
                    }

                    clone.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach((element) => {
                        element.setAttribute('tabindex', '-1');
                    });
                    clone.querySelectorAll('[id]').forEach((element) => element.removeAttribute('id'));
                    clone.querySelectorAll('video').forEach((video) => {
                        video.preload = 'none';
                        video.querySelectorAll('source').forEach((source) => source.removeAttribute('src'));
                    });
                    track.append(clone);
                });

                slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
            }
        }

        let autoplayTimer = null;
        let isVisible = false;
        let isPointerDown = false;
        let isDragging = false;
        let pointerStartX = 0;
        let scrollStart = 0;
        let resumeTimer = null;
        let loopResetTimer = null;

        const slideStep = () => {
            if (slides.length < 2) {
                return viewport.clientWidth;
            }

            return slides[1].offsetLeft - slides[0].offsetLeft;
        };

        const currentIndex = () => {
            const step = slideStep();

            return step > 0
                ? Math.max(0, Math.min(slides.length - 1, Math.round(viewport.scrollLeft / step)))
                : 0;
        };

        const updateState = () => {
            const canScroll = viewport.scrollWidth > viewport.clientWidth + 2;

            carousel.classList.toggle('is-static', !canScroll);
            previous?.toggleAttribute('disabled', !canScroll);
            next?.toggleAttribute('disabled', !canScroll);
        };

        const goTo = (index) => {
            const targetIndex = Math.max(0, Math.min(slides.length - 1, index));

            viewport.scrollTo({
                left: slides[targetIndex].offsetLeft - slides[0].offsetLeft,
                behavior: reducedMotion ? 'auto' : 'smooth',
            });
        };

        const jumpTo = (index) => {
            const targetIndex = Math.max(0, Math.min(slides.length - 1, index));
            const previousScrollBehavior = viewport.style.scrollBehavior;

            viewport.style.scrollBehavior = 'auto';
            viewport.scrollLeft = slides[targetIndex].offsetLeft - slides[0].offsetLeft;
            window.requestAnimationFrame(() => {
                viewport.style.scrollBehavior = previousScrollBehavior;
            });
        };

        const resetLoopPosition = () => {
            if (originalSlideCount < 2) {
                return;
            }

            const index = currentIndex();

            if (index >= originalSlideCount && index < originalSlideCount * 2) {
                return;
            }

            const logicalIndex = ((index % originalSlideCount) + originalSlideCount) % originalSlideCount;
            const middleIndex = originalSlideCount + logicalIndex;

            jumpTo(middleIndex);
        };

        const scheduleLoopReset = () => {
            window.clearTimeout(loopResetTimer);
            loopResetTimer = window.setTimeout(resetLoopPosition, 180);
        };

        const stopAutoplay = () => {
            if (autoplayTimer) {
                window.clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        };

        const startAutoplay = () => {
            stopAutoplay();

            if (reducedMotion || !isVisible || carousel.classList.contains('is-static')) {
                return;
            }

            autoplayTimer = window.setInterval(() => {
                goTo(currentIndex() + 1);
            }, 3200);
        };

        const pauseThenResume = () => {
            stopAutoplay();
            window.clearTimeout(resumeTimer);
            resumeTimer = window.setTimeout(startAutoplay, 1800);
        };

        previous?.addEventListener('click', () => {
            goTo(currentIndex() - 1);
            pauseThenResume();
        });

        next?.addEventListener('click', () => {
            goTo(currentIndex() + 1);
            pauseThenResume();
        });

        viewport.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
                return;
            }

            event.preventDefault();
            goTo(currentIndex() + (event.key === 'ArrowRight' ? 1 : -1));
            pauseThenResume();
        });

        viewport.addEventListener('pointerdown', (event) => {
            if (event.button !== 0) {
                return;
            }

            isPointerDown = true;
            isDragging = false;
            pointerStartX = event.clientX;
            scrollStart = viewport.scrollLeft;
            viewport.setPointerCapture?.(event.pointerId);
            stopAutoplay();
        });

        viewport.addEventListener('pointermove', (event) => {
            if (!isPointerDown) {
                return;
            }

            const distance = event.clientX - pointerStartX;

            if (Math.abs(distance) > 6) {
                isDragging = true;
                viewport.classList.add('is-dragging');
            }

            if (isDragging) {
                viewport.scrollLeft = scrollStart - distance;
            }
        });

        const finishDrag = (event) => {
            if (!isPointerDown) {
                return;
            }

            isPointerDown = false;
            viewport.releasePointerCapture?.(event.pointerId);
            viewport.classList.remove('is-dragging');

            if (isDragging) {
                goTo(currentIndex());
            }

            window.setTimeout(() => {
                isDragging = false;
            }, 0);
            pauseThenResume();
        };

        viewport.addEventListener('pointerup', finishDrag);
        viewport.addEventListener('pointercancel', finishDrag);
        viewport.addEventListener('click', (event) => {
            if (isDragging) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);
        viewport.addEventListener('scroll', () => {
            updateState();
            scheduleLoopReset();
        }, { passive: true });
        const visibilityObserver = 'IntersectionObserver' in window
            ? new IntersectionObserver(([entry]) => {
                isVisible = entry.isIntersecting;

                if (isVisible) {
                    startAutoplay();
                } else {
                    stopAutoplay();
                }
            }, { threshold: 0.25 })
            : null;

        isVisible = visibilityObserver === null;
        visibilityObserver?.observe(carousel);
        window.addEventListener('resize', () => {
            const logicalIndex = currentIndex() % originalSlideCount;

            updateState();
            jumpTo(originalSlideCount + logicalIndex);
            startAutoplay();
        });

        updateState();
        if (originalSlideCount > 1) {
            window.requestAnimationFrame(() => {
                jumpTo(originalSlideCount);
            });
        }
        startAutoplay();
    });
}

function initPublicDiscoveryNavbar() {
    const navbar = document.querySelector('[data-public-discovery-navbar]');
    const mobilePanel = document.querySelector('[data-public-nav-mobile-panel]');
    const mobileToggle = document.querySelector('[data-public-nav-mobile-toggle]');

    if (!navbar) {
        return;
    }

    const closeDesktopMenus = (except = null) => {
        navbar.querySelectorAll('[data-public-nav-menu]').forEach((menu) => {
            if (menu === except) {
                return;
            }

            menu.querySelector('[data-public-nav-menu-panel]')?.classList.add('hidden');
            menu.querySelector('[data-public-nav-menu-toggle]')?.setAttribute('aria-expanded', 'false');
            menu.querySelector('[data-public-nav-menu-chevron]')?.classList.remove('is-open');
        });
    };

    navbar.querySelectorAll('[data-public-nav-menu]').forEach((menu) => {
        const toggle = menu.querySelector('[data-public-nav-menu-toggle]');
        const panel = menu.querySelector('[data-public-nav-menu-panel]');
        const chevron = menu.querySelector('[data-public-nav-menu-chevron]');

        toggle?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const opening = panel?.classList.contains('hidden') ?? false;
            closeDesktopMenus(menu);
            panel?.classList.toggle('hidden', !opening);
            toggle.setAttribute('aria-expanded', opening ? 'true' : 'false');
            chevron?.classList.toggle('is-open', opening);
        });
    });

    const setMobileMenuOpen = (isOpen) => {
        mobilePanel?.classList.toggle('hidden', !isOpen);
        mobileToggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        document.body.classList.toggle('public-discovery-menu-open', isOpen);
    };

    mobileToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setMobileMenuOpen(mobilePanel?.classList.contains('hidden') ?? false);
    });

    document.addEventListener('click', (event) => {
        if (!navbar.contains(event.target)) {
            closeDesktopMenus();
        }

        if (!mobilePanel?.contains(event.target) && !mobileToggle?.contains(event.target)) {
            setMobileMenuOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeDesktopMenus();
            setMobileMenuOpen(false);
        }
    });
}

function initPublicFaq() {
    document.querySelectorAll('[data-public-faq]').forEach((faqList) => {
        const items = [...faqList.querySelectorAll('[data-faq-item]')];

        const setOpen = (item, isOpen) => {
            const toggle = item.querySelector('[data-faq-toggle]');
            const answer = item.querySelector('[data-faq-answer]');

            item.classList.toggle('is-open', isOpen);
            answer?.classList.toggle('hidden', !isOpen);
            toggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        items.forEach((item) => {
            const toggle = item.querySelector('[data-faq-toggle]');

            toggle?.addEventListener('click', () => {
                const opening = toggle.getAttribute('aria-expanded') !== 'true';

                setOpen(item, opening);
            });
        });
    });
}

function initPublicReels() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const videos = [...document.querySelectorAll('[data-autoplay-reel="true"]')];
    let preferredMuted = window.localStorage.getItem('etc-reels-muted') === 'true';
    let soundUnlocked = false;
    let activeVideo = null;
    const indicatorTimers = new WeakMap();
    const soundControlTimers = new WeakMap();

    const syncSoundControls = () => {
        document.querySelectorAll('[data-reel-player]').forEach((player) => {
            const icon = player.querySelector('[data-reel-sound-icon]');
            const toggle = player.querySelector('[data-reel-sound-toggle]');

            if (icon) {
                icon.textContent = preferredMuted ? 'volume_off' : 'volume_up';
            }

            if (toggle) {
                const label = preferredMuted ? 'Nyalakan suara' : 'Matikan suara';
                toggle.setAttribute('aria-label', label);
                toggle.setAttribute('title', label);
            }
        });
    };

    const applySoundPreference = () => {
        videos.forEach((video) => {
            video.volume = 1;
            video.muted = preferredMuted;
        });

        window.localStorage.setItem('etc-reels-muted', preferredMuted ? 'true' : 'false');
        syncSoundControls();
    };

    const showSoundControl = (player) => {
        const control = player?.querySelector('[data-reel-sound-control]');

        if (!control) {
            return;
        }

        clearTimeout(soundControlTimers.get(control));
        control.classList.add('is-visible');
        soundControlTimers.set(control, window.setTimeout(() => {
            control.classList.remove('is-visible');
        }, 1100));
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
        }).catch(() => {});
    };

    const playWithSound = async (video) => {
        activeVideo = video;
        video.volume = 1;
        video.muted = preferredMuted;

        try {
            await video.play();
            soundUnlocked = !video.muted;
        } catch {
            video.muted = true;
            await video.play().catch(() => {});
        }
    };

    const unlockSound = async () => {
        if (!activeVideo || activeVideo.dataset.userPaused === 'true') {
            return;
        }

        activeVideo.muted = preferredMuted;
        activeVideo.volume = 1;

        try {
            await activeVideo.play();
            soundUnlocked = !activeVideo.muted;
        } catch {
            activeVideo.muted = true;
            soundUnlocked = false;
        }
    };

    const showPlaybackIndicator = (video, icon) => {
        const player = video.closest('[data-reel-player]');
        const indicator = player?.querySelector('[data-reel-playback-indicator]');
        const indicatorIcon = indicator?.querySelector('[data-reel-playback-icon]');

        if (!indicator || !indicatorIcon) {
            return;
        }

        clearTimeout(indicatorTimers.get(indicator));
        indicatorIcon.textContent = icon;
        indicator.classList.add('is-visible');

        indicatorTimers.set(indicator, window.setTimeout(() => {
            indicator.classList.remove('is-visible');
        }, 240));
    };

    const togglePlayback = async (video) => {
        if (video.paused) {
            video.dataset.userPaused = 'false';
            await playWithSound(video);
            showPlaybackIndicator(video, 'pause');
            return;
        }

        video.dataset.userPaused = 'true';
        video.pause();
        showPlaybackIndicator(video, 'play_arrow');
    };

    ['pointerdown', 'touchstart', 'wheel', 'keydown'].forEach((eventName) => {
        document.addEventListener(eventName, unlockSound, { passive: true });
    });

    document.querySelectorAll('[data-reel-player]').forEach((player) => {
        const video = player.querySelector('[data-autoplay-reel="true"]');

        video?.addEventListener('click', (event) => {
            event.stopPropagation();
            showSoundControl(player);
            togglePlayback(video);
        });

        player.addEventListener('click', (event) => {
            if (!video || event.target.closest('[data-reel-sound-control]')) {
                return;
            }

            showSoundControl(player);
            togglePlayback(video);
        });

        player.querySelector('[data-reel-sound-toggle]')?.addEventListener('click', (event) => {
            event.stopPropagation();
            preferredMuted = !preferredMuted;
            applySoundPreference();
            showSoundControl(player);
            unlockSound();
        });
    });

    applySoundPreference();

    const videoObserver = 'IntersectionObserver' in window
        ? new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const video = entry.target;

                if (entry.isIntersecting && entry.intersectionRatio >= 0.62) {
                    markViewed(video);
                    videos.filter((candidate) => candidate !== video).forEach((candidate) => candidate.pause());
                    document.querySelectorAll('[data-reel-slide]').forEach((slide) => {
                        slide.classList.toggle('is-active', slide.contains(video));
                    });

                    if (soundUnlocked) {
                        video.muted = preferredMuted;
                        video.volume = 1;
                        activeVideo = video;
                        if (video.dataset.userPaused !== 'true') {
                            video.play().catch(() => {});
                        }
                    } else {
                        if (video.dataset.userPaused !== 'true') {
                            playWithSound(video);
                        }
                    }

                    return;
                }

                video.pause();
                video.dataset.userPaused = 'false';
            });
        }, { threshold: [0, 0.62, 0.9] })
        : null;

    videos.forEach((video) => {
        if (videoObserver) {
            videoObserver.observe(video);
        } else {
            markViewed(video);
            playWithSound(video);
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            videos.forEach((video) => video.pause());
        } else if (activeVideo) {
            activeVideo.play().catch(() => {});
        }
    });

    document.querySelectorAll('[data-vertical-reels-feed]').forEach((feed) => {
        const slides = [...feed.querySelectorAll('[data-reel-slide]')];

        feed.tabIndex = feed.tabIndex >= 0 ? feed.tabIndex : 0;

        let activeIndex = 0;
        let navigating = false;
        let touchStartY = null;
        let touchStartX = null;
        let touchStartIndex = 0;
        let suppressClick = false;

        const currentIndex = () => {
            const feedTop = feed.scrollTop;

            return slides.reduce((closest, slide, index) => {
                const distance = Math.abs(slide.offsetTop - feedTop);

                return distance < closest.distance ? { index, distance } : closest;
            }, { index: 0, distance: Number.POSITIVE_INFINITY }).index;
        };

        const goTo = (index, behavior = 'smooth') => {
            const nextIndex = Math.max(0, Math.min(slides.length - 1, index));
            const target = slides[nextIndex];

            if (!target) {
                return;
            }

            activeIndex = nextIndex;
            navigating = true;
            feed.scrollTo({
                top: target.offsetTop,
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : behavior,
            });

            window.setTimeout(() => {
                navigating = false;
            }, behavior === 'smooth' ? 420 : 0);
        };

        const move = (direction) => goTo(activeIndex + direction);

        feed.addEventListener('wheel', (event) => {
            if (Math.abs(event.deltaY) < 6) {
                return;
            }

            event.preventDefault();

            if (navigating) {
                return;
            }

            activeIndex = currentIndex();
            move(event.deltaY > 0 ? 1 : -1);
        }, { passive: false });

        feed.addEventListener('keydown', (event) => {
            const nextKeys = ['ArrowDown', 'PageDown', ' '];
            const previousKeys = ['ArrowUp', 'PageUp'];

            if (![...nextKeys, ...previousKeys].includes(event.key)) {
                return;
            }

            event.preventDefault();
            activeIndex = currentIndex();
            move(nextKeys.includes(event.key) ? 1 : -1);
        });

        feed.addEventListener('touchstart', (event) => {
            const touch = event.changedTouches[0];

            touchStartY = touch?.clientY ?? null;
            touchStartX = touch?.clientX ?? null;
            touchStartIndex = currentIndex();
        }, { passive: true });

        feed.addEventListener('touchend', (event) => {
            if (touchStartY === null || touchStartX === null || navigating) {
                return;
            }

            const touch = event.changedTouches[0];
            const deltaY = touchStartY - (touch?.clientY ?? touchStartY);
            const deltaX = touchStartX - (touch?.clientX ?? touchStartX);

            touchStartY = null;
            touchStartX = null;

            if (Math.abs(deltaY) < 36 || Math.abs(deltaY) <= Math.abs(deltaX)) {
                return;
            }

            suppressClick = true;
            activeIndex = touchStartIndex;
            move(deltaY > 0 ? 1 : -1);
            window.setTimeout(() => {
                suppressClick = false;
            }, 450);
        }, { passive: true });

        feed.addEventListener('click', (event) => {
            if (suppressClick || event.target.closest('[data-reel-player], a, button')) {
                return;
            }

            activeIndex = currentIndex();
            move(1);
        });

        feed.addEventListener('scroll', () => {
            if (!navigating) {
                activeIndex = currentIndex();
            }
        }, { passive: true });

        slides[0]?.classList.add('is-active');
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
    initPublicRegistrationProgress();
    initStudentRevealCards();
    initStudentDashboardPage();
    initDashboardShell();
    initPublicChatbot();
    initPublicReveal();
    initPublicStatCounters();
    initPublicHomeCarousels();
    initPublicDiscoveryNavbar();
    initPublicFaq();
    initPublicReels();
    initDataTables();
});
