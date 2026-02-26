<script setup>
import { computed } from 'vue';
import { usePrefsStore } from '../stores/prefsStore';
import { RouterLink } from 'vue-router';

const prefsStore = usePrefsStore();
const t = computed(() => prefsStore.t);
</script>

<template>
    <div class="about-container">
        <div class="about-hero">
            <h1>{{ t.about.title }}</h1>
            <p class="tagline">{{ t.about.subtitle }}</p>
        </div>

        <div class="about-content">
            <section class="about-section">
                <h2>{{ t.about.missionTitle }}</h2>
                <p>
                    {{ t.about.discover }}
                    <br>{{ t.about.missionDesc }}
                </p>
            </section>

            <section class="about-section features">
                <h2>{{ t.about.whyChooseUs }}</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>{{ t.about.guaranteeTitle }}</h3>
                        <p>{{ t.about.guaranteeDesc }}</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h3>{{ t.about.deliveryTitle }}</h3>
                        <p>{{ t.about.deliveryDesc }}</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💰</div>
                        <h3>{{ t.home.cheapest }}</h3>
                        <p>{{ t.about.missionDesc }}</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h3>{{ t.about.secureTitle }}</h3>
                        <p>{{ t.about.secureDesc }}</p>
                    </div>
                </div>
            </section>

            <section class="about-section team">
                <h2>{{ t.about.ourTeam }}</h2>
                <p class="team-description">
                    {{ t.about.teamDesc }}
                </p>
                <div class="team-stats">
                    <div class="stat">
                        <div class="stat-number">10k+</div>
                        <div class="stat-label">{{ t.about.happyCustomers }}</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">5000+</div>
                        <div class="stat-label">{{ t.about.gamesDelivered }}</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">{{ t.about.supportTitle }}</div>
                    </div>
                </div>
            </section>

            <section class="about-section cta">
                <h2>{{ t.about.joinTitle }}</h2>
                <p>{{ t.about.joinDesc }}</p>
                <RouterLink to="/products" class="btn-cta">{{ t.nav.buy }}</RouterLink>
            </section>
        </div>
    </div>
</template>

<style>
@import '../assets/css/about.css';
</style>
