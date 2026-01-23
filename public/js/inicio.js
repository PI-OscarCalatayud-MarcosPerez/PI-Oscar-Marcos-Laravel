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

        // Debug: Log loaded categories to help diagnosis
        const uniqueCategories = [
            ...new Set(
                products.map((p) =>
                    p.categoria ? p.categoria.toLowerCase().trim() : "null",
                ),
            ),
        ];
        console.log("Categorías cargadas desde BD:", uniqueCategories);

        const renderCarousel = (categoryId, containerId) => {
            const container = document.getElementById(containerId);
            if (!container) return;

            // Filter with loose comparison (trim + lowercase)
            let filtered = products.filter(
                (p) =>
                    p.categoria &&
                    p.categoria.toLowerCase().trim() ===
                        categoryId.toLowerCase().trim(),
            );

            // Fallback strategies if specific tags aren't found (to prevent empty carousels)
            if (filtered.length === 0) {
                console.warn(
                    `No products found for category '${categoryId}'. Available:`,
                    uniqueCategories,
                );

                if (
                    categoryId === "nuevos" &&
                    uniqueCategories.includes("software")
                ) {
                    // Fallback: 'nuevos' section is titled "Software..." in HTML, so try 'software'
                    filtered = products.filter(
                        (p) =>
                            p.categoria &&
                            p.categoria.toLowerCase().trim() === "software",
                    );
                } else if (categoryId === "comprados" && products.length > 0) {
                    // Fallback: Show random/first products for 'Best Sellers' if tag missing
                    filtered = products.slice(0, 8);
                } else if (categoryId === "ofertas" && products.length > 0) {
                    // Fallback: Show cheap products
                    filtered = products
                        .filter((p) => {
                            const price = parseFloat(p.precio || p.preu || 0);
                            return price > 0 && price < 20;
                        })
                        .slice(0, 8);
                    if (filtered.length === 0) filtered = products.slice(0, 8);
                }
            }

            if (filtered.length > 0) {
                container.innerHTML = filtered.map(createCard).join("");
            } else {
                container.innerHTML =
                    "<p style='padding:20px; color: #333; text-align: center; width: 100%;'>No hay productos disponibles.</p>";
            }
        };

        renderCarousel("comprados", "lista-comprados");
        renderCarousel("ofertas", "lista-ofertas");
        renderCarousel("nuevos", "lista-nuevos");
    } catch (error) {
        console.error("Error cargando productos:", error);
    }
});
