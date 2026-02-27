<template>
  <div class="page-header">
    <div>
      <h2>{{ title }}</h2>
      <small>{{ subtitle }}</small>
    </div>

    <div>
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { default: 'Dashboard' },
  subtitle: { default: '' }
})
</script>

<style scoped>
.page-header {
  padding: 1.5rem 2rem;
  background: white;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
}
</style>
