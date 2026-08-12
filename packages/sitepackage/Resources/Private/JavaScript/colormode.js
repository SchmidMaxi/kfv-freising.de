(() => {
    'use strict'

    const STORAGE_KEY = 'kfv-ui-theme'

    const getStored = () => localStorage.getItem(STORAGE_KEY)
    const setStored = theme => localStorage.setItem(STORAGE_KEY, theme)

    const getTheme = () => getStored() || 'light'

    // data-bs-theme: Bootstrap 5.3 native dark mode selector
    const applyTheme = theme => document.documentElement.setAttribute('data-bs-theme', theme)

    // Apply immediately before DOMContentLoaded to avoid flash
    applyTheme(getTheme())

    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark'
                setStored(next)
                applyTheme(next)
            })
        })
    })
})()
