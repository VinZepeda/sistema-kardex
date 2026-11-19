// ========================================
// MENÚ LATERAL
// ========================================

function alternarMenu() {

    const sidebar = document.getElementById("sidebar");

    if (sidebar) {

        sidebar.classList.toggle("sidebar-hidden");

    }

}


// ========================================
// BUSCAR EMPLEADO
// ========================================

function buscarEmpleado() {

    const input =
        document.getElementById("buscarPersonal");

    const tabla =
        document.getElementById("tablaPersonal");

    const contador =
        document.getElementById(
            "contadorResultados"
        );

    if (!input || !tabla) {
        return;
    }

    const texto =
        input.value.toLowerCase();

    const filas =
        tabla.getElementsByTagName("tr");

    let visibles = 0;

    for (let i = 0; i < filas.length; i++) {

        const contenido =
            filas[i].textContent.toLowerCase();

        if (contenido.includes(texto)) {

            filas[i].style.display = "";
            visibles++;

        } else {

            filas[i].style.display = "none";

        }

    }

    if (contador) {
        contador.textContent = visibles;
    }
}

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const input =
            document.getElementById(
                "buscarPersonal"
            );

        if (input) {

            buscarEmpleado();

            input.addEventListener(
                "input",
                buscarEmpleado
            );

        }

    }
);


// MENÚ DESPLEGABLE DE PERFIL

const profileButton = document.getElementById("profileButton");
const profileDropdown = document.getElementById("profileDropdown");

if (profileButton && profileDropdown) {

    profileButton.addEventListener("click", function(event) {

        event.stopPropagation();

        profileDropdown.classList.toggle("show");

    });

    document.addEventListener("click", function(event) {

        if (!profileDropdown.contains(event.target)) {

            profileDropdown.classList.remove("show");

        }

    });

}


// ========================================
// BANNER DE NOTICIAS (ROTACIÓN 10s + ADMIN)
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  const banner = document.querySelector(".Banner-informativo");
  const contenedorCarrusel = banner?.querySelector(".carrusel-noticias") || banner;
  const indicadoresContenedor = document.getElementById("indicadoresNoticias");
  const btnPrev = document.getElementById("btnPrevNoticia");
  const btnNext = document.getElementById("btnNextNoticia");

  if (!banner || !contenedorCarrusel) return;

  const TIEMPO_ROTACION_MS = 10000; // 10 segundos
  const MAX_NOTICIAS = 2; // Máximo de noticias a conservar; al agregar una nueva se borra la más vieja

  let cards = Array.from(contenedorCarrusel.querySelectorAll(".noticia-card"));
  let indiceActual = Math.max(0, cards.findIndex((c) => c.classList.contains("activa")));
  let intervaloRotacion = null;
  let dots = [];

  function renderizarIndicadores() {
    if (!indicadoresContenedor) return;
    indicadoresContenedor.innerHTML = "";
    dots = cards.map((_, idx) => {
      const dot = document.createElement("button");
      dot.type = "button";
      dot.className = "carrusel-dot" + (idx === indiceActual ? " activa" : "");
      dot.setAttribute("aria-label", `Ir a noticia ${idx + 1}`);
      dot.addEventListener("click", () => {
        mostrarNoticia(idx);
        reiniciarRotacion();
      });
      indicadoresContenedor.appendChild(dot);
      return dot;
    });
  }

  function mostrarNoticia(nuevoIndice) {
    cards = Array.from(contenedorCarrusel.querySelectorAll(".noticia-card"));
    if (cards.length === 0) return;

    cards.forEach((c) => c.classList.remove("activa"));
    dots.forEach((d) => d.classList.remove("activa"));

    indiceActual = ((nuevoIndice % cards.length) + cards.length) % cards.length;
    cards[indiceActual].classList.add("activa");
    if (dots[indiceActual]) dots[indiceActual].classList.add("activa");
  }

  function siguienteNoticia() {
    mostrarNoticia(indiceActual + 1);
  }

  function anteriorNoticia() {
    mostrarNoticia(indiceActual - 1);
  }

  function detenerRotacion() {
    if (intervaloRotacion) {
      clearInterval(intervaloRotacion);
      intervaloRotacion = null;
    }
  }

  function iniciarRotacion() {
    detenerRotacion();
    cards = Array.from(contenedorCarrusel.querySelectorAll(".noticia-card"));
    if (cards.length > 1) {
      intervaloRotacion = setInterval(siguienteNoticia, TIEMPO_ROTACION_MS);
    }
  }

  function reiniciarRotacion() {
    iniciarRotacion();
  }

  function agregarNoticiaAdmin({ tag, titulo, mensaje }) {
    cards = Array.from(contenedorCarrusel.querySelectorAll(".noticia-card"));

    while (cards.length >= MAX_NOTICIAS) {
      const noticiaMasVieja = cards.shift();
      noticiaMasVieja.remove();
    }

    const article = document.createElement("article");
    article.className = "noticia-card";
    article.innerHTML = `
      <span class="noticia-tag">${escaparHTML(tag || "AVISO")}</span>
      <h3>${escaparHTML(titulo)}</h3>
      <p>${escaparHTML(mensaje)}</p>
    `;

    contenedorCarrusel.appendChild(article);
    cards = Array.from(contenedorCarrusel.querySelectorAll(".noticia-card"));

    renderizarIndicadores();
    mostrarNoticia(cards.length - 1);
    reiniciarRotacion();
  }

  function escaparHTML(texto) {
    const div = document.createElement("div");
    div.textContent = texto;
    return div.innerHTML;
  }

  const formNoticia = document.getElementById("formNuevaNoticia");
  if (formNoticia) {
    formNoticia.addEventListener("submit", (e) => {
      e.preventDefault();
      const tag = document.getElementById("noticiaTag")?.value.trim();
      const titulo = document.getElementById("noticiaTitulo")?.value.trim();
      const mensaje = document.getElementById("noticiaMensaje")?.value.trim();

      if (!titulo || !mensaje) return;

      agregarNoticiaAdmin({ tag, titulo, mensaje });
      formNoticia.reset();
    });
  }

  btnNext?.addEventListener("click", () => {
    siguienteNoticia();
    reiniciarRotacion();
  });

  btnPrev?.addEventListener("click", () => {
    anteriorNoticia();
    reiniciarRotacion();
  });

  banner.addEventListener("mouseenter", detenerRotacion);
  banner.addEventListener("mouseleave", iniciarRotacion);

  renderizarIndicadores();
  mostrarNoticia(indiceActual);
  iniciarRotacion();
});
