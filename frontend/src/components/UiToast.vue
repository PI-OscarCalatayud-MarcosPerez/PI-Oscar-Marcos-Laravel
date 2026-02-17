<script setup>
import { storeToRefs } from 'pinia'
import { useUiStore } from '@/stores/uiStore'

const ui = useUiStore()
const { toast } = storeToRefs(ui)
</script>

<template>
    <div v-if="toast" class="toast">{{ toast.message }}</div>
</template>

<style scoped>
.toast {
    position: fixed;
    right: 16px;
    bottom: 16px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #ddd;
    background: #fff;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
    max-width: 360px;
    z-index: 9999;
}
</style>
