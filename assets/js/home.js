document.addEventListener("DOMContentLoaded", () => {

    const reveals = document.querySelectorAll(".reveal");
    const navbar = document.querySelector(".topbar");
    const indicator = document.querySelector(".scroll-indicator");
    const hero = document.querySelector(".hero");
    const sections=document.querySelectorAll("section");
    const links=document.querySelectorAll("nav a");
    /* ==========================================
   STATISTICS COUNTER
========================================== */

const counters = document.querySelectorAll(".counter");

const counterObserver = new IntersectionObserver((entries) => {

    entries.forEach(entry => {

        if (!entry.isIntersecting) return;

        const counter = entry.target;
        const target = parseInt(counter.dataset.target);

        const duration = 1500;
    const startTime = performance.now();

    function animate(time){

    const progress = Math.min((time - startTime) / duration, 1);

    const value = Math.floor(progress * target);

    counter.textContent = value;

    if(progress < 1){

        requestAnimationFrame(animate);

    }else{

        counter.textContent = target;

    }

}

requestAnimationFrame(animate);

        counterObserver.unobserve(counter);

    });

},{
    threshold:.6
});

counters.forEach(counter=>{

    counterObserver.observe(counter);

});

window.addEventListener("scroll", () => {

    const scrollY = window.scrollY;

    /* Active Navigation */

    let current = "";

    sections.forEach(section => {

        const top = section.offsetTop - 120;

        if (scrollY >= top) {

            current = section.id;

        }

    });

    links.forEach(link => {

        link.classList.toggle(
            "active",
            link.getAttribute("href") === "#" + current
        );

    });

    /* Hero Parallax */

    if (hero) {

        hero.style.backgroundPositionY =
            `${scrollY * .35}px`;

    }

    /* Scroll Indicator */

    if (indicator) {

        indicator.style.opacity =
            scrollY > 80 ? "0" : "1";

        indicator.style.pointerEvents =
            scrollY > 80 ? "none" : "auto";

    }

    /* Navbar */

    if (navbar) {

        navbar.classList.toggle(
            "scrolled",
            scrollY > 60
        );

    }

});

    const observer = new IntersectionObserver((entries) => {

        entries.forEach(entry => {

            if(entry.isIntersecting){

                entry.target.classList.add("active");

            }

        });

    },{
        threshold:0.18
    });

    reveals.forEach(section => {

        observer.observe(section);

    });

});