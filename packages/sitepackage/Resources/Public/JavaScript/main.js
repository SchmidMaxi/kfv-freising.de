const lightbox = GLightbox({
    autoplayVideos: true,
    selector: '.lightbox'
});


document.addEventListener( 'DOMContentLoaded', function() {
    var elms = document.getElementsByClassName( 'splide-slider' );
    for ( var i = 0; i < elms.length; i++ ) {
        new Splide( elms[ i ] ).mount();
    }
} );

/**
 * Macht eine HTML-Tabelle sortierbar.
 * * Um diese Funktion zu nutzen, geben Sie Ihrer Tabelle eine ID (z.B. id="sortier-tabelle")
 * und rufen Sie auf: makeTableSortable(document.getElementById('sortier-tabelle'));
 */
function makeTableSortable(table) {
    const headers = table.querySelectorAll('th');
    let sortDirection = []; // Speichert die Sortierrichtung für jede Spalte

    headers.forEach((header, index) => {
        sortDirection[index] = 'asc'; // Standard-Sortierrichtung
        header.style.cursor = 'pointer';

        header.addEventListener('click', () => {
            sortTableByColumn(table, index, sortDirection[index]);
            // Wechselt die Sortierrichtung für den nächsten Klick
            sortDirection[index] = sortDirection[index] === 'asc' ? 'desc' : 'asc';
        });
    });
}

function sortTableByColumn(table, columnIndex, direction) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    const sortedRows = rows.sort((a, b) => {
        const aColText = a.querySelector(`td:nth-child(${columnIndex + 1})`).textContent.trim();
        const bColText = b.querySelector(`td:nth-child(${columnIndex + 1})`).textContent.trim();

        // Einfacher Vergleich für Strings und Zahlen
        if (direction === 'asc') {
            return aColText > bColText ? 1 : -1;
        } else {
            return bColText > aColText ? 1 : -1;
        }
    });

    // Alte Zeilen entfernen
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

    // Sortierte Zeilen wieder einfügen
    tbody.append(...sortedRows);
}
