// Design System & Tailwind CSS
import '../Css/main.css'

// Lightbox
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

// Splide-Slider (für Card-Slider und Hero-Slider Content-Blocks)
import Splide from '@splidejs/splide'
import '@splidejs/splide/css/core'

// Colormode-Toggle (setzt data-bs-theme auf <html>)
import './colormode.js'

// Mobile-Menü Toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggle    = document.getElementById('mobile-menu-toggle')
    const menu      = document.getElementById('mobile-menu')
    const iconOpen  = document.getElementById('mobile-icon-open')
    const iconClose = document.getElementById('mobile-icon-close')

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden')
            menu.classList.toggle('hidden')
            toggle.setAttribute('aria-expanded', String(!isOpen))
            iconOpen?.classList.toggle('hidden')
            iconClose?.classList.toggle('hidden')
        })
    }
})

// GLightbox initialisieren
const lightbox = GLightbox({
    autoplayVideos: true,
    selector: '.lightbox'
})

// Splide-Slider initialisieren
document.addEventListener('DOMContentLoaded', function() {
    const elms = document.getElementsByClassName('splide-slider')
    for (let i = 0; i < elms.length; i++) {
        new Splide(elms[i]).mount()
    }
})

// Tabs-Container: Tab-Switching
document.addEventListener('DOMContentLoaded', () => {
    const ACTIVE = {
        pills:   ['bg-primary', 'text-primary-foreground', 'border-primary'],
        default: ['bg-card', 'text-foreground', 'shadow-sm'],
    }
    const INACTIVE = {
        pills:   ['bg-transparent', 'text-foreground'],
        default: ['text-muted-foreground'],
    }

    document.querySelectorAll('[data-tabs]').forEach(container => {
        const group  = container.dataset.tabs
        const isPills = container.dataset.tabsLayout === 'pills'
        const activeClasses   = isPills ? ACTIVE.pills   : ACTIVE.default
        const inactiveClasses = isPills ? INACTIVE.pills : INACTIVE.default
        const triggers = container.querySelectorAll(`[data-tab-group="${group}"]`)
        const panels   = container.querySelectorAll(`[data-tab-panel="${group}"]`)

        triggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const targetId = trigger.dataset.tabTarget

                triggers.forEach(t => {
                    const isActive = t.dataset.tabTarget === targetId
                    t.setAttribute('aria-selected', String(isActive))
                    activeClasses.forEach(c => t.classList.toggle(c, isActive))
                    inactiveClasses.forEach(c => t.classList.toggle(c, !isActive))
                })

                panels.forEach(panel => {
                    panel.classList.toggle('hidden', panel.id !== targetId)
                })
            })
        })
    })
})
