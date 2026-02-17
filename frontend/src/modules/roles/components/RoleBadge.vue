<script setup>
defineProps({
    role: { type: String, default: 'user' }
})
</script>

<template>
    <span class="badge">{{ role }}</span>
</template>

<style scoped>
.badge {
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid #ccc;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
</style>
