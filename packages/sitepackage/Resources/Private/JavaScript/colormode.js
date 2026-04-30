(() => {
    'use strict'

    const STORAGE_KEY = 'kfv-ui-theme'

    const getStored = () => localStorage.getItem(STORAGE_KEY)
    const setStored = theme => localStorage.setItem(STORAGE_KEY, theme)

    const getTheme = () => getStored() || 'light'

    const applyTheme = theme => {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    }

    // Apply immediately before DOMContentLoaded to avoid flash
    applyTheme(getTheme())

    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark'
                setStored(next)
                applyTheme(next)
            })
        })
    })
})()
