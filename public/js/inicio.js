// frontend/js/inicio.js

document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Updated to use Laravel API
        const response = await fetch("/api/products");

        if (!response.ok) {
            throw new Error(
                `Error HTTP: ${response.status} - API no disponible`,
            );
        }

        const data = await response.json();
        const products = Array.isArray(data)
            ? data
            : data.products
              ? data.products
              : [];

        const createCard = (product) => {
            // Mapping fields from DB (nombre, precio, etc) to UI
            const nombre = product.nombre || product.nom || "Sin nombre";
            const precioVal = product.precio || product.preu || 0;
            const precio = parseFloat(precioVal).toFixed(2);

            const descRaw = product.descripcion || product.descripcio || "";
            const descripcion =
                descRaw.length > 80
                    ? descRaw.substring(0, 80) + "..."
                    : descRaw;

            let imagen =
                product.imagen_url || product.img || "img/placeholder.jpg";
            if (
                imagen &&
                !imagen.startsWith("http") &&
                !imagen.startsWith("/")
            ) {
                imagen = "/" + imagen;
            }

            return `
            <div class="item"
                 onclick="window.location.href='/products/${product.id}'"
                 tabindex="0"
                 role="button"
                 aria-label="Juego ${nombre}, precio ${precio} euros"
                 onkeydown="if(event.key === 'Enter' || event.key === ' ') window.location.href='/products/${product.id}'">

                <div class="item-imagen">
                    <img src="${imagen}" alt="Carátula de ${nombre}" loading="lazy" />
                </div>
                <div class="item-info">
                    <p class="item-titulo">${nombre}</p>
                    <p class="item-descripcion-hover">${descripcion}</p>
                    <p class="price">${precio}€</p>
                </div>
            </div>
        `;
        };

        // Lógica de Renderizado del Carrusel
        const renderCarousel = (sectionId, containerId) => {
            const container = document.getElementById(containerId);
            if (!container) return; // Si no existe el contenedor, salimos

            // Filtramos productos que coincidan con la sección (ej. 'comprados', 'ofertas')
            // Usamos la propiedad 'seccion' que viene de la base de datos
            const filtrados = products.filter(
                (p) =>
                    p.seccion &&
                    p.seccion.toLowerCase().trim() ===
                        sectionId.toLowerCase().trim(),
            );

            // Si hay productos, los mostramos
            if (filtrados.length > 0) {
                container.innerHTML = filtrados.map(createCard).join("");
            } else {
                container.innerHTML =
                    "<p style='padding:20px; text-align: center;'>No hay productos disponibles en esta sección.</p>";
            }
        };

        // Renderizamos las 3 secciones principales de la home
        renderCarousel("comprados", "lista-comprados");
        renderCarousel("ofertas", "lista-ofertas");
        renderCarousel("software", "lista-nuevos"); // Nota: En el HTML el ID es 'lista-nuevos' pero cargamos software
    } catch (error) {
        console.error("Error cargando productos:", error);
    }
});
