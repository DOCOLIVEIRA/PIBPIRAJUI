gsap.registerPlugin(ScrollTrigger,ScrollSmoother,ScrollToPlugin,SplitText);

const isDesktop = window.matchMedia("(min-width: 1024px)").matches;

const smoother = ScrollSmoother.create({
	smooth: isDesktop ? 1 : 0, // rolagem nativa fluida no mobile, suave no desktop
	effects: isDesktop, // parallax data-speed apenas no desktop para evitar travamentos no mobile
	smoothTouch: false
});

const navbar = document.querySelector(".navbar");
const menuMobileToggle = document.querySelector(".menu-mobile-toggle");
const navItems = document.querySelectorAll(".opcoes-navbar li");
const navTriggers = document.querySelectorAll(".navbar [data-target]");

if (menuMobileToggle && navbar) {
    menuMobileToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        const isOpen = navbar.classList.toggle("menu-aberto");
        menuMobileToggle.setAttribute("aria-expanded", String(isOpen));
    });

    document.addEventListener("click", (e) => {
        if (navbar.classList.contains("menu-aberto") && !navbar.contains(e.target)) {
            navbar.classList.remove("menu-aberto");
            menuMobileToggle.setAttribute("aria-expanded", "false");
        }
    });
}

navTriggers.forEach(trigger => {
    trigger.addEventListener("click", () => {
        const targetSelector = trigger.getAttribute("data-target");
        const targetEl = document.querySelector(targetSelector);
        if (!targetEl) return;

        if (navbar && navbar.classList.contains("menu-aberto")) {
            navbar.classList.remove("menu-aberto");
            if (menuMobileToggle) {
                menuMobileToggle.setAttribute("aria-expanded", "false");
            }
        }

        const parentLi = trigger.closest(".opcoes-navbar li");
        if (parentLi) {
            navItems.forEach(li => {
                li.classList.remove("ativo");
                li.removeAttribute("id");
            });
            parentLi.classList.add("ativo");
        }

        if (smoother) {
            smoother.scrollTo(targetEl, true, "top top");
        } else {
            gsap.to(window, {
                duration: 1,
                scrollTo: targetEl,
                ease: "power2.inOut"
            });
        }
    });
});

gsap.from(".card-introducao", {
    opacity: 0,
    duration: 1,
    filter: "blur(10px)",
    stagger: 0.3,
    scrollTrigger: {
        trigger: ".cards-introducao",
        markers: false,
        start: "0%, 90%",
        end: "100% 100%",
        scrub: false
    }
});

// Carrega os 3 últimos vídeos sincronizados diariamente às 06h00 em assets/data/ultimos-videos.json
async function carregarUltimosVideosYoutube() {
    const cards = document.querySelectorAll(".cards-introducao .card-introducao");
    if (!cards.length) return;

    // Vincula clique dos botões já presentes no HTML
    cards.forEach(card => {
        const btn = card.querySelector(".container-botao .botao");
        if (btn && !btn.dataset.boundClick) {
            btn.dataset.boundClick = "true";
            btn.addEventListener("click", () => {
                const url = btn.getAttribute("data-video-url") || "https://www.youtube.com/@PIBPirajui";
                window.open(url, "_blank", "noopener,noreferrer");
            });
        }
    });

    try {
        const cacheKey = new Date().toISOString().slice(0, 13);
        let response = await fetch(`update-youtube.php?v=${cacheKey}`, { cache: "no-store" });
        const contentType = response.headers.get("content-type") || "";
        if (!response.ok || !contentType.includes("application/json")) {
            response = await fetch(`assets/data/ultimos-videos.json?v=${cacheKey}`, { cache: "no-store" });
        }
        if (!response.ok) return;

        const data = await response.json();
        if (!data || !Array.isArray(data.videos)) return;

        data.videos.slice(0, 3).forEach((video, index) => {
            const card = cards[index];
            if (!card || !video.videoId) return;

            const iframe = card.querySelector("iframe");
            const tituloEl = card.querySelector(".titulo-video");
            const botaoEl = card.querySelector(".container-botao .botao");

            const embedUrl = video.embedUrl || `https://www.youtube.com/embed/${video.videoId}`;
            const watchUrl = video.url || `https://www.youtube.com/watch?v=${video.videoId}`;

            if (iframe && iframe.getAttribute("src") !== embedUrl) {
                iframe.setAttribute("src", embedUrl);
            }
            if (tituloEl && video.title) {
                tituloEl.textContent = video.title;
            }
            if (botaoEl) {
                botaoEl.setAttribute("data-video-url", watchUrl);
            }
        });
    } catch (err) {
        console.warn("Usando vídeos padrão do HTML:", err);
    }
}

carregarUltimosVideosYoutube();

const grupoTextoSplit = document.querySelectorAll(".textoSplit");

grupoTextoSplit.forEach(textoUnicoSplit => {
    const split = SplitText.create(textoUnicoSplit, {
        type: "lines, words, chars",
        mask: true
    })

    gsap.from(split.chars, {
        y: 1,
        opacity: 0,
        duration: 0.3,
        filter: "blur(2px)",
        stagger: 0.007,
        scrollTrigger: {
            trigger: textoUnicoSplit
        }
    })
})

gsap.from(".faca-parte .card:not(.card-oculto)", {
    opacity: 0,
    y: 10,
    duration: 1,
    filter: "blur(10px)",
    stagger: 0.2,
    scrollTrigger: {
        trigger: ".cards-faca-parte",
        markers: false,
        start: "0% 90%",
        end: "100% 100%",
        scrub: false
    }
})

gsap.from(".programacao-semanal .card", {
    opacity: 0,
    y: 5,
    duration: 1,
    filter: "blur(5px)",
    stagger: 0.1,
    scrollTrigger: {
        trigger: ".programacao-semanal",
        markers: false,
        start: "0% 90%",
        end: "100% 100%",
        scrub: false
    }
})

gsap.from(".programacao-mensal .card", {
    opacity: 0,
    y: 5,
    duration: 1,
    filter: "blur(5px)",
    stagger: 0.1,
    scrollTrigger: {
        trigger: ".programacao-mensal",
        markers: false,
        start: "0% 90%",
        end: "100% 100%",
        scrub: false
    }
})

gsap.from(".foto-container img", {
    scale: 1.2,
    filter: "blur(10px)",
    duration: 1,
    stagger: 0.2,
    scrollTrigger: {
        trigger: "footer .fotos",
        markers: false,
        start: "50% 90%",
        end: "100% 100%",
        scrub: false
    }
})