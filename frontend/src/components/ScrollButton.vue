<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isAtTop = ref(true)

const handleScroll = () => {
    const scrollY = window.scrollY
    const docHeight = document.documentElement.scrollHeight - window.innerHeight
    isAtTop.value = scrollY < docHeight / 2
}

const scrollAction = () => {
    if (isAtTop.value) {
        window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' })
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' })
    }
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
    <button class="scroll-btn" @click="scrollAction" :title="isAtTop ? 'Ir abajo' : 'Ir arriba'">
        <span v-if="isAtTop">▼</span>
        <span v-else>▲</span>
    </button>
</template>

<style scoped>
.scroll-btn {
    position: fixed;
    bottom: 90px;
    right: 30px;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    border: none;
    background: #0e273f;
    color: #ffffff;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(14, 39, 63, 0.35);
    transition: all 0.3s ease;
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.scroll-btn:hover {
    background: #fa4841;
    transform: scale(1.1);
    box-shadow: 0 5px 18px rgba(250, 72, 65, 0.4);
}
</style>
