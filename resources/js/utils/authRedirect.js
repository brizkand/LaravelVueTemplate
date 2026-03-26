const REDIRECT_KEY = 'auth_redirect'

export const setAuthRedirect = (path) => {
	if (typeof path !== 'string' || !path.trim()) return

	const existing = sessionStorage.getItem(REDIRECT_KEY)

	// Do not overwrite an existing redirect
	if (existing && existing.trim()) return

	sessionStorage.setItem(REDIRECT_KEY, path)
}

export const forceAuthRedirect = (path) => {
	if (typeof path === 'string' && path.trim()) {
		sessionStorage.setItem(REDIRECT_KEY, path)
	}
}

export const getAuthRedirect = () => {
	const value = sessionStorage.getItem(REDIRECT_KEY)
	return typeof value === 'string' && value.trim() ? value : null
}

export const clearAuthRedirect = () => {
	sessionStorage.removeItem(REDIRECT_KEY)
}
