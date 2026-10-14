/**
 * Bestellformular: Wird eine Feuerwehr aus „Weitere Feuerwehren“ gewählt, ist der
 * Einsatzabschnitt Pflicht (serverseitig ebenfalls geprüft).
 */
document.querySelectorAll('[data-katschutz-feuerwehr]').forEach((select) => {
    const form = select.closest('form');
    const feld = form.querySelector('[data-katschutz-abschnitt] input');
    const hinweis = form.querySelector('[data-katschutz-hinweis]');
    const pflicht = form.querySelector('[data-katschutz-pflicht]');

    const aktualisiere = () => {
        const option = select.options[select.selectedIndex];
        const gruppe = option ? option.parentElement : null;
        const fremd = gruppe !== null && gruppe.tagName === 'OPTGROUP' && gruppe.label === 'Weitere Feuerwehren';
        feld.required = fremd;
        hinweis.classList.toggle('d-none', !fremd);
        pflicht.classList.toggle('d-none', !fremd);
    };
    select.addEventListener('change', aktualisiere);
    aktualisiere();
});
