<template>
  <div class="layout-wrapper">

    <AppTopbar />

    <div class="layout-body">
      <AppSidebar class="desktop-only" />
      <AppMobileDrawer class="mobile-only" />

      <div class="layout-main">
        <AppBreadcrumb />
        <AppPageHeader />

        <div class="layout-page">
          <router-view />
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import AppTopbar from '@/components/layout/AppTopbar.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppMobileDrawer from '@/components/layout/AppMobileDrawer.vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import AppPageHeader from '@/components/layout/AppPageHeader.vue'
</script>

<style scoped>
.layout-body {
  display: flex;
  height: calc(100vh - 60px);
}

.layout-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: var(--surface-ground);
}

.layout-page {
  padding: 2rem;
  overflow-y: auto;
}

.mobile-only {
  display: none;
}

@media (max-width: 768px) {
  .desktop-only { display: none }
  .mobile-only { display: block }
}
</style>
