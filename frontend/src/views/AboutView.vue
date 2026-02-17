<script setup>
// Página Sobre Nosotros - MOKeys
</script>

<template>
    <div class="about-container">
        <div class="about-hero">
            <h1>Sobre Nosotros</h1>
            <p class="tagline">Tu tienda de confianza para software y videojuegos</p>
        </div>

        <div class="about-content">
            <section class="about-section">
                <h2>¿Quiénes Somos?</h2>
                <p>
                    MOKeys es tu plataforma de confianza para la compra y venta de claves de software y videojuegos.
                    Nos especializamos en ofrecer las mejores ofertas del mercado con garantía de autenticidad.
                </p>
            </section>

            <section class="about-section features">
                <h2>¿Por Qué Elegirnos?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>Claves Auténticas</h3>
                        <p>Todas nuestras claves son 100% originales y verificadas</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h3>Entrega Instantánea</h3>
                        <p>Recibe tu clave al momento después de la compra</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💰</div>
                        <h3>Mejores Precios</h3>
                        <p>Ofertas competitivas y descuentos exclusivos</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h3>Compra Segura</h3>
                        <p>Transacciones protegidas y soporte 24/7</p>
                    </div>
                </div>
            </section>

            <section class="about-section team">
                <h2>Nuestro Equipo</h2>
                <p class="team-description">
                    Somos un equipo apasionado por los videojuegos y la tecnología, comprometidos en ofrecerte
                    la mejor experiencia de compra online.
                </p>
                <div class="team-stats">
                    <div class="stat">
                        <div class="stat-number">10k+</div>
                        <div class="stat-label">Clientes Satisfechos</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">5000+</div>
                        <div class="stat-label">Productos Disponibles</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Soporte</div>
                    </div>
                </div>
            </section>

            <section class="about-section cta">
                <h2>¿Listo para Empezar?</h2>
                <p>Explora nuestro catálogo y encuentra las mejores ofertas</p>
                <RouterLink to="/products" class="btn-cta">Ver Productos</RouterLink>
            </section>
        </div>
    </div>
</template>

<style>
@import '../assets/css/about.css';
</style>
