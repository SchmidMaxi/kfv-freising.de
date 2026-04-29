// Design System & Tailwind CSS
import '../Css/main.css'

// Bootstrap Icons (lokale Schrift wird in main.css geladen, CSS-Klassen hier)
import 'bootstrap-icons/font/bootstrap-icons.css'

// Bootstrap JS — für Navbar-Offcanvas, Dropdowns und Theme-Toggle
// wird in Phase 2 ersetzt, sobald die Templates auf Tailwind umgestellt sind
import 'bootstrap'

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
    const toggle = document.getElementById('mobile-menu-toggle')
    const menu   = document.getElementById('mobile-menu')
    const icon   = document.getElementById('mobile-menu-icon')

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden')
            menu.classList.toggle('hidden')
            toggle.setAttribute('aria-expanded', String(!isOpen))
            if (icon) {
                icon.className = isOpen
                    ? 'bi bi-list text-2xl'
                    : 'bi bi-x-lg text-xl'
            }
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

/**
 * Macht eine HTML-Tabelle sortierbar.
 * Aufruf: makeTableSortable(document.getElementById('meine-tabelle'))
 */
export function makeTableSortable(table) {
    const headers = table.querySelectorAll('th')
    let sortDirection = []

    headers.forEach((header, index) => {
        sortDirection[index] = 'asc'
        header.style.cursor = 'pointer'

        header.addEventListener('click', () => {
            sortTableByColumn(table, index, sortDirection[index])
            sortDirection[index] = sortDirection[index] === 'asc' ? 'desc' : 'asc'
        })
    })
}

function sortTableByColumn(table, columnIndex, direction) {
    const tbody = table.querySelector('tbody')
    const rows = Array.from(tbody.querySelectorAll('tr'))

    const sortedRows = rows.sort((a, b) => {
        const aColText = a.querySelector(`td:nth-child(${columnIndex + 1})`).textContent.trim()
        const bColText = b.querySelector(`td:nth-child(${columnIndex + 1})`).textContent.trim()

        return direction === 'asc'
            ? aColText > bColText ? 1 : -1
            : bColText > aColText ? 1 : -1
    })

    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild)
    }

    tbody.append(...sortedRows)
}
