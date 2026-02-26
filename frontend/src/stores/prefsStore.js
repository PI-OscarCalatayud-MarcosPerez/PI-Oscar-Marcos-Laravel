import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import es from '../i18n/es'
import en from '../i18n/en'

const translations = { es, en }

export const usePrefsStore = defineStore('prefs', () => {
    const lang = ref(localStorage.getItem('app-lang') || 'es')
    const darkMode = ref(localStorage.getItem('app-theme') === 'dark')

    const t = computed(() => translations[lang.value] || translations.es)

    const toggleLang = () => {
        lang.value = lang.value === 'es' ? 'en' : 'es'
    }

    const toggleDark = () => {
        darkMode.value = !darkMode.value
    }

    watch(lang, (val) => {
        localStorage.setItem('app-lang', val)
    })

    watch(darkMode, (val) => {
        localStorage.setItem('app-theme', val ? 'dark' : 'light')
        if (val) {
            document.documentElement.classList.add('dark-mode')
        } else {
            document.documentElement.classList.remove('dark-mode')
        }
    }, { immediate: true })

    return { lang, darkMode, t, toggleLang, toggleDark }
})
