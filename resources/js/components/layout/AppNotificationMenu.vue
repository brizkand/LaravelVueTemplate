<template>
  <Menu ref="menu" :model="items" popup />
  <Button icon="pi pi-bell" text @click="toggle" />
</template>

<script setup>
import { ref } from 'vue'

const menu = ref()

const items = [
  { label: 'New proposal submitted' },
  { label: 'User registered' }
]

const toggle = (event) => menu.value.toggle(event)
</script>
