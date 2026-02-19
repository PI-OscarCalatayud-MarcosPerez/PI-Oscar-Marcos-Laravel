<script setup>
defineProps({
    role: { type: String, default: 'user' }
})
</script>

<template>
    <span class="badge">{{ role }}</span>
</template>

<style>
@import '../../../assets/css/role-badge.css';
</style>
