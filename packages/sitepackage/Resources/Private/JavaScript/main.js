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
