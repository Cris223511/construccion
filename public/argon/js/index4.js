/*--------------------------------------------------------------
# Preloader
--------------------------------------------------------------*/

let imagen = document.querySelector('.preloader .imagen');
let preloader = document.querySelector(".preloader");

document.body.style.overflow = "hidden";

imagen.classList.add('aparecer');
window.addEventListener("load", () => {
    setTimeout(() => {
        preloader.classList.add("desaparecer");
        preloader.addEventListener("animationend", () => {
            preloader.style.display = "none";
            document.body.style.overflow = "auto";
        });
    }, 2000);
});

/*--------------------------------------------------------------
# Cookies
--------------------------------------------------------------*/

const cookies = document.querySelector(".cookies");
const cancelarCookies = document.querySelector(".btn1");
const aceptarCookies = document.querySelector(".btn2");

window.addEventListener("load", function () {
    if (
        localStorage.getItem("cookiesAccepted") == null ||
        localStorage.getItem("cookiesAccepted") == "false"
    ) {
        localStorage.setItem("cookiesAccepted", false);
        setTimeout(function () {
            cookies.classList.add("active");
        }, 4000);
    } else {
        cookies.style.display = "none";
    }
});

cancelarCookies.addEventListener("click", function () {
    cookies.style.display = "none";
});

aceptarCookies.addEventListener("click", function () {
    setCookie("miCookie", "miValor", 7);
    cookies.style.display = "none";
    localStorage.setItem("cookiesAccepted", true);
});

function setCookie(name, value, expirationDays) {
    const expirationMs = expirationDays * 24 * 60 * 60 * 1000;
    const expirationDate = new Date(Date.now() + expirationMs).toUTCString();
    const cookieValue = `${name}=${encodeURIComponent(
        value
    )}; expires=${expirationDate}; path=/`;

    document.cookie = cookieValue;
    localStorage.setItem(name, value);
}

/*--------------------------------------------------------------
# Testimonials
--------------------------------------------------------------*/

new Swiper(".testimonios-slider", {
  speed: 600,
  loop: true,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
  slidesPerView: "auto",
  pagination: {
    el: ".swiper-pagination",
    type: "bullets",
    clickable: true,
  },
  breakpoints: {
    320: {
      slidesPerView: 1,
      spaceBetween: 20,
    },

    1200: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
  },
});

/*--------------------------------------------------------------
# Galleria (GLightbox)
--------------------------------------------------------------*/

const galelryLightbox = GLightbox({
    selector: ".galleria-lightbox",
});

const contenedorGaleria = document.querySelector('.gallery-container');
const botonVerMas = document.querySelector('.boton_ver_mas');
const botonVerMenos = document.querySelector('.boton_ver_menos');

const IMAGENES_POR_PAGINA = 3;

let paginaActual = 1;

const TOTAL_PAGINAS = Math.ceil(contenedorGaleria.children.length / IMAGENES_POR_PAGINA);

function mostrarPagina(pagina) {
    const indiceInicio = (pagina - 1) * IMAGENES_POR_PAGINA;
    const indiceFin = indiceInicio + IMAGENES_POR_PAGINA;
    for (let i = indiceInicio; i < indiceFin && i < contenedorGaleria.children.length; i++) {
        contenedorGaleria.children[i].classList.remove('d-none');
    }
}

function ocultarPagina(pagina) {
    const indiceInicio = (pagina - 1) * IMAGENES_POR_PAGINA;
    const indiceFin = indiceInicio + IMAGENES_POR_PAGINA;
    for (let i = indiceInicio; i < indiceFin && i < contenedorGaleria.children.length; i++) {
        contenedorGaleria.children[i].classList.add('d-none');
    }
}

function actualizarBotones() {
    if (paginaActual === 1) {
        botonVerMas.classList.remove('d-none');
        botonVerMenos.classList.add('d-none');
    } else if (paginaActual === TOTAL_PAGINAS) {
        botonVerMas.classList.add('d-none');
        botonVerMenos.classList.remove('d-none');
    } else {
        botonVerMas.classList.remove('d-none');
        botonVerMenos.classList.remove('d-none');
    }
}

mostrarPagina(paginaActual);
actualizarBotones();

botonVerMas.addEventListener('click', () => {
    paginaActual++;
    mostrarPagina(paginaActual);
    actualizarBotones();
    if (paginaActual === TOTAL_PAGINAS) {
        botonVerMas.classList.add('d-none');
    }
});

botonVerMenos.addEventListener('click', () => {
    ocultarPagina(paginaActual);
    paginaActual--;
    mostrarPagina(paginaActual);
    actualizarBotones();
    if (paginaActual === 1) {
        botonVerMenos.classList.add('d-none');
    }
    botonVerMas.classList.remove('d-none');
});
