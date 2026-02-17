<script setup>
import { useRole } from '@/modules/roles/composables/useRole'

const props = defineProps({
    roles: { type: Array, default: () => [] },
    permission: { type: String, default: '' },
})

const { hasRole, can } = useRole()
</script>

<template>
    <slot
        v-if="(props.roles.length ? hasRole(...props.roles) : true) && (props.permission ? can(props.permission) : true)" />
</template>
