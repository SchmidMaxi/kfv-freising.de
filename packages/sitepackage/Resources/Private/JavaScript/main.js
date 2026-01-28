// SCSS importieren
import '../../Public/Scss/layout.scss'
import '../../Public/Scss/icons.scss'

// Bibliotheken importieren
import * as bootstrap from 'bootstrap'
import GLightbox from 'glightbox'
import Splide from '@splidejs/splide'
import 'glightbox/dist/css/glightbox.min.css'
import '@splidejs/splide/css'

// Colormode (inline, da es früh geladen werden muss)
import './colormode.js'

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
 * Um diese Funktion zu nutzen, geben Sie Ihrer Tabelle eine ID (z.B. id="sortier-tabelle")
 * und rufen Sie auf: makeTableSortable(document.getElementById('sortier-tabelle'));
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

        if (direction === 'asc') {
            return aColText > bColText ? 1 : -1
        } else {
            return bColText > aColText ? 1 : -1
        }
    })

    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild)
    }

    tbody.append(...sortedRows)
}
// test change
