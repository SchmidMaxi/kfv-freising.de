// Design System — Bootstrap 5
import '../../Public/Scss/layout.scss'

// Bootstrap JS-Komponenten (selektiv statt komplettes Bundle — jede Komponente registriert
// ihre eigene data-bs-toggle-Data-API automatisch beim Import, keine manuelle Init nötig)
import 'bootstrap/js/dist/collapse'
import 'bootstrap/js/dist/dropdown'
import 'bootstrap/js/dist/offcanvas'
import 'bootstrap/js/dist/tab'

// Lightbox
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

// Splide-Slider (für Card-Slider und Hero-Slider Content-Blocks)
import Splide from '@splidejs/splide'
import '@splidejs/splide/css/core'

// Colormode-Toggle (setzt data-bs-theme auf <html>)
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
