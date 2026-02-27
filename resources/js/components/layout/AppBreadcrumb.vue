<template>
  <div class="breadcrumb-wrapper">
    <Breadcrumb :home="home" :model="items" />
  </div>
</template>

<script setup>
import { ref } from 'vue'

const home = ref({
  icon: 'pi pi-home',
  to: '/'
})

const items = ref([
  { label: 'Dashboard' }
])
</script>

<style scoped>
.breadcrumb-wrapper {
  background: white;
  padding: 1rem 2rem;
  border-bottom: 1px solid #eee;
}
</style>
