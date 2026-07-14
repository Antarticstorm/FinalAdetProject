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

        let current = 0;

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

        const updateCounter = () => {

            current += increment;

            if(current >= target){

                counter.textContent = target;

                return;

            }

            counter.textContent = current;

            requestAnimationFrame(updateCounter);

        };

        updateCounter();

        counterObserver.unobserve(counter);

    });

},{
    threshold:.6
});

counters.forEach(counter=>{

    counterObserver.observe(counter);

});

window.addEventListener("scroll",()=>{

let current="";

sections.forEach(section=>{

const top=section.offsetTop-120;

if(window.scrollY>=top){

current=section.id;

}

});

links.forEach(link=>{

link.classList.remove("active");

if(link.getAttribute("href")==="#"+current){

link.classList.add("active");

}

});

});

window.addEventListener("scroll",()=>{

    hero.style.backgroundPositionY=

    `${window.scrollY * .35}px`;

});

window.addEventListener("scroll",()=>{

    if(window.scrollY>80){

        indicator.style.opacity="0";

        indicator.style.pointerEvents="none";

    }

    else{

        indicator.style.opacity="1";

    }

});
    

window.addEventListener("scroll",()=>{

    navbar.classList.toggle(

        "scrolled",

        window.scrollY > 60

    );

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
/* ==========================================
   PAGE TRANSITIONS
========================================== */

document.querySelectorAll("a").forEach(link=>{

    if(

        link.hostname===window.location.hostname &&

        !link.hasAttribute("target")

    ){

        link.addEventListener("click",(e)=>{

            const href=link.getAttribute("href");

            if(!href.startsWith("#")){

                e.preventDefault();

                document.body.classList.add("fade-out");

                setTimeout(()=>{

                    window.location=href;

                },300);

            }

        });

    }

});