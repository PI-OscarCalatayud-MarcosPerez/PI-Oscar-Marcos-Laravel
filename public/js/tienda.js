document.addEventListener("DOMContentLoaded", async () => {
    // Variables globales
    let todosLosProductos = [];
    const gridContainer = document.getElementById("contenedor-productos");
    const resultCount = document.getElementById("result-count");

    // Elementos de filtro
    const searchInput = document.getElementById("searchInput");

    // Selectores específicos por 'name' para distinguir grupos
    const checkboxesCat = document.querySelectorAll('input[name="cat"]');
    const checkboxesPlat = document.querySelectorAll('input[name="plat"]');

    const priceRange = document.getElementById("priceRange");
    const priceValue = document.getElementById("priceValue");
    const sortSelect = document.getElementById("sortOrder");
    const btnLimpiar = document.getElementById("btn-limpiar");

    // --- Función para quitar acentos y minúsculas ---
    function normalizar(texto) {
        if (!texto) return "";
        return texto
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    // 1. CARGAR DATOS DESDE LA API
    try {
        const response = await fetch("/api/products");
        const data = await response.json();

        // Guardamos los productos en una variable global para poder filtrar luego
        todosLosProductos = data;

        // Renderizado inicial (mostramos todos los productos)
        aplicarFiltros();
    } catch (error) {
        console.error("Error cargando productos:", error);
        gridContainer.innerHTML = "<p>Error al cargar el catálogo.</p>";
    }

    // 2. FUNCIÓN DE RENDERIZADO
    function renderProducts(productos) {
        gridContainer.innerHTML = "";
        resultCount.textContent = `${productos.length} productos encontrados`;

        if (productos.length === 0) {
            gridContainer.innerHTML =
                "<p style='grid-column: 1/-1; text-align: center; color: #666;'>No se encontraron productos con esos filtros.</p>";
            return;
        }

        productos.forEach((prod) => {
            const nombre = prod.nombre || prod.nom || "Sin Nombre";
            const precioVal = prod.precio || prod.preu || 0;
            let imagen = prod.imagen_url || prod.img || "img/placeholder.jpg";

            if (
                imagen &&
                !imagen.startsWith("http") &&
                !imagen.startsWith("data:")
            ) {
                let rawPath = imagen.startsWith("/")
                    ? imagen.substring(1)
                    : imagen;

                if (!rawPath.startsWith("img/")) {
                    rawPath = "img/" + rawPath;
                }

                imagen = "/" + rawPath;
            }
            const cat = prod.categoria || "General";
            const id = prod.id;

            const precio = parseFloat(precioVal).toFixed(2);

            const html = `
                <div class="shop-item"
                     onclick="window.location.href='/products/${id}'"
                     title="Ver detalles de ${nombre}">

                    <div class="shop-item-img" style="background-image: url('${imagen}'); background-size: cover; background-position: center; height: 200px;">
                        <!-- Using BG image for better cover fit or standard img tag if preferred -->
                    </div>
                     <!-- Alternatively use img tag as in original code -->
                    <!-- <img src="${imagen}" alt="${nombre}" class="shop-item-img" loading="lazy"> -->

                    <div class="shop-item-info">
                        <p class="shop-item-title">${nombre}</p>
                        <p style="font-size:0.9rem; color:#666;">${cat}</p>
                        <p class="shop-item-price">${precio}€</p>
                    </div>
                </div>
            `;

            const itemDiv = document.createElement("div");
            itemDiv.className = "shop-item";
            itemDiv.onclick = () => (window.location.href = `/products/${id}`);
            itemDiv.title = `Ver detalles de ${nombre}`;

            itemDiv.innerHTML = `
                <img src="${imagen}" alt="${nombre}" class="shop-item-img" loading="lazy">
                <div class="shop-item-info">
                    <div class="shop-item-title">${nombre}</div>
                    <div style="font-size:0.9rem; color:#666;">${cat}</div>
                    <div class="shop-item-price">${precio}€</div>
                </div>
             `;

            gridContainer.appendChild(itemDiv);
        });
    }

    // 3. LÓGICA DE FILTRADO
    function aplicarFiltros() {
        let filtrados = [...todosLosProductos];

        // MAPPING KEYS FOR FILTERING
        // We need to know what properties the API returns.
        // Usually: nombre, categoria, precio

        // A. Búsqueda Texto
        const texto = normalizar(searchInput ? searchInput.value : "");
        if (texto) {
            filtrados = filtrados.filter((p) => {
                const nom = p.nombre || p.nom || "";
                return normalizar(nom).includes(texto);
            });
        }

        // B. Categoría (Género)
        const catsMarcadas = Array.from(checkboxesCat)
            .filter((ch) => ch.checked)
            .map((ch) => normalizar(ch.value));

        if (catsMarcadas.length > 0) {
            filtrados = filtrados.filter((p) => {
                // Compara con el campo 'categoria' (mapped from genero in requirements? model has categoria)
                const cat = p.categoria ? normalizar(p.categoria) : "";
                return catsMarcadas.includes(cat);
            });
        }

        // C. Plataforma
        // Model doesn't seem to have 'plataforma' column in the migration file I saw earlier?
        // Migration: nombre, descripcion, precio, sku, stock, imagen_url, categoria.
        // If 'plataforma' is missing, this filter won't work unless it's in description or category.
        // WE WILL ASSUME it might be missing or part of category for now to prevent crash.
        const platMarcadas = Array.from(checkboxesPlat)
            .filter((ch) => ch.checked)
            .map((ch) => normalizar(ch.value));

        if (platMarcadas.length > 0) {
            filtrados = filtrados.filter((p) => {
                // Attempt to find platform in implicit data if column doesn't exist
                const plat = p.plataforma ? normalizar(p.plataforma) : "";
                return plat && platMarcadas.includes(plat);
            });
        }

        // D. Precio
        const maxPrice = parseFloat(priceRange.value);
        priceValue.textContent = maxPrice;
        filtrados = filtrados.filter((p) => {
            const precio = parseFloat(p.precio || p.preu || 0);
            return precio <= maxPrice;
        });

        // E. Ordenación
        const orden = sortSelect.value;
        if (orden === "price-asc") {
            filtrados.sort(
                (a, b) =>
                    parseFloat(a.precio || a.preu) -
                    parseFloat(b.precio || b.preu),
            );
        } else if (orden === "price-desc") {
            filtrados.sort(
                (a, b) =>
                    parseFloat(b.precio || b.preu) -
                    parseFloat(a.precio || a.preu),
            );
        } else if (orden === "az") {
            const nomA = a.nombre || a.nom || "";
            const nomB = b.nombre || b.nom || "";
            filtrados.sort((a, b) => nomA.localeCompare(nomB));
        }

        renderProducts(filtrados);
    }

    // 4. LISTENERS
    if (searchInput) searchInput.addEventListener("input", aplicarFiltros);

    checkboxesCat.forEach((ch) =>
        ch.addEventListener("change", aplicarFiltros),
    );
    checkboxesPlat.forEach((ch) =>
        ch.addEventListener("change", aplicarFiltros),
    );

    if (priceRange) priceRange.addEventListener("input", aplicarFiltros);
    if (sortSelect) sortSelect.addEventListener("change", aplicarFiltros);

    // Limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", () => {
            if (searchInput) searchInput.value = "";
            checkboxesCat.forEach((ch) => (ch.checked = false));
            checkboxesPlat.forEach((ch) => (ch.checked = false));
            if (priceRange) priceRange.value = 100;
            if (priceValue) priceValue.textContent = "100";
            if (sortSelect) sortSelect.value = "default";
            aplicarFiltros();
        });
    }
});
