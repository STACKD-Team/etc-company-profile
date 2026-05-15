const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

if (!prefersReducedMotion) {
    const revealItems = document.querySelectorAll(
        ".hero-copy, .program-card, .price-card, .footer-custom .container"
    );

    revealItems.forEach((item) => {
        item.classList.add("js-reveal");
    });

    const revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.14,
            rootMargin: "0px 0px -40px",
        }
    );

    revealItems.forEach((item) => revealObserver.observe(item));

    const hero = document.querySelector(".program-hero");
    let latestPointer = { x: 0, y: 0 };
    let ticking = false;

    const updateHeroMotion = () => {
        if (!hero) return;

        const x = (latestPointer.x / window.innerWidth - 0.5) * 18;
        const y = (latestPointer.y / window.innerHeight - 0.5) * 18;

        hero.style.setProperty("--hero-orb-x", `${x}px`);
        hero.style.setProperty("--hero-orb-y", `${y}px`);
        ticking = false;
    };

    window.addEventListener("pointermove", (event) => {
        latestPointer = { x: event.clientX, y: event.clientY };

        if (!ticking) {
            window.requestAnimationFrame(updateHeroMotion);
            ticking = true;
        }
    });
}
