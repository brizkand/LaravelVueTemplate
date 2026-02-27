<template>
  <Sidebar v-model:visible="mobileMenuVisible" position="left">
    <PanelMenu :model="menu" />
  </Sidebar>
</template>

<script setup>
import { useLayout } from '@/composables/useLayout'

const { mobileMenuVisible } = useLayout()

const menu = [
  { label: 'Dashboard', icon: 'pi pi-home', to: '/' }
]
</script>
