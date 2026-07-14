document.addEventListener("DOMContentLoaded", () => {
    const reveals = document.querySelectorAll(".reveal");
    const navbar = document.querySelector(".topbar");
    const indicator = document.querySelector(".scroll-indicator");
    const hero = document.querySelector(".hero");
    const counters = document.querySelectorAll(".counter");
    const navLinks = document.querySelectorAll("nav a");
    const sections = document.querySelectorAll("section[id]");

    // Always start at the top on refresh
    if ("scrollRestoration" in history) {
        history.scrollRestoration = "manual";
    }
    window.scrollTo(0, 0);

    // Reveal on scroll
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("active");
            }
        });
    }, {
        threshold: 0.18
    });

    reveals.forEach((section) => revealObserver.observe(section));

    // Counter animation
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const counter = entry.target;
            const target = parseInt(counter.dataset.target, 10) || 0;
            const duration = 1500;
            const startTime = performance.now();

            function animate(now) {
                const progress = Math.min((now - startTime) / duration, 1);
                counter.textContent = Math.floor(progress * target);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    counter.textContent = target;
                }
            }

            requestAnimationFrame(animate);
            counterObserver.unobserve(counter);
        });
    }, {
        threshold: 0.6
    });

    counters.forEach((counter) => counterObserver.observe(counter));

    let ticking = false;

    function updateOnScroll() {
        const scrollY = window.scrollY;

        // Navbar state
        if (navbar) {
            navbar.classList.toggle("scrolled", scrollY > 60);
        }

        // Scroll indicator
        if (indicator) {
            indicator.classList.toggle("hidden", scrollY > 80);
        }

        // Active nav link based on section IDs only
        let currentId = "";
        const triggerPoint = scrollY + 140;

        sections.forEach((section) => {
            if (triggerPoint >= section.offsetTop) {
                currentId = section.id;
            }
        });

        navLinks.forEach((link) => {
            link.classList.remove("active");

            const href = link.getAttribute("href") || "";
            if (currentId && href === `#${currentId}`) {
                link.classList.add("active");
            }
        });

        // Hero parallax
        if (hero) {
            hero.style.backgroundPositionY = `${scrollY * 0.2}px`;
        }

        ticking = false;
    }

    window.addEventListener("scroll", () => {
        if (!ticking) {
            window.requestAnimationFrame(updateOnScroll);
            ticking = true;
        }
    }, { passive: true });

    // Run once on load
    updateOnScroll();

    // Page transitions
    document.querySelectorAll("a[href]").forEach((link) => {
        link.addEventListener("click", (e) => {
            const href = link.getAttribute("href") || "";

            if (
                href.startsWith("#") ||
                href.startsWith("javascript:") ||
                link.hostname !== window.location.hostname ||
                link.hasAttribute("target")
            ) {
                return;
            }

            e.preventDefault();
            document.body.classList.add("fade-out");

            setTimeout(() => {
                window.location.href = href;
            }, 250);
        });
    });
});