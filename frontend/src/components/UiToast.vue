<script setup>
import { storeToRefs } from 'pinia'
import { useUiStore } from '@/stores/uiStore'

const ui = useUiStore()
const { toast } = storeToRefs(ui)
</script>

<template>
    <div v-if="toast" class="toast">{{ toast.message }}</div>
</template>

<style>
@import '../assets/css/ui-toast.css';
</style>
