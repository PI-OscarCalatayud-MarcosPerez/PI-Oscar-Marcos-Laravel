<script setup>
import { useRole } from '../composables/useRole';

const props = defineProps({
  permission: {
    type: String,
    required: true
  }
});

const { can } = useRole();
</script>

<template>
  <div v-if="can(props.permission)">
    <slot></slot>
  </div>
</template>
