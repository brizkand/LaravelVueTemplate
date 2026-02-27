import {ref, computed} from 'vue'

const darkMode = ref(false)
const sidebarCollapsed = ref(false)
const mobileMenuVisible = ref(false)

export function useLayout() {
	const toggleDarkMode = () => {
		darkMode.value = !darkMode.value
		document.documentElement.classList.toggle('my-app-dark')
	}

	const toggleSidebar = () => {
		sidebarCollapsed.value = !sidebarCollapsed.value
	}

	return {
		darkMode,
		sidebarCollapsed,
		mobileMenuVisible,
		toggleDarkMode,
		toggleSidebar,
	}
}
