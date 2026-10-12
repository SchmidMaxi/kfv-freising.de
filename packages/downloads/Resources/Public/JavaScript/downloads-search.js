// Client-side filter for the Downloads plugin: single-select category pills + text search.
// No AJAX — the full list is server-rendered, this only toggles visibility.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-downloads-plugin]').forEach(initDownloadsPlugin)
})

function initDownloadsPlugin(root) {
    const searchInput = root.querySelector('[data-downloads-search]')
    const resetButton = root.querySelector('[data-category-reset]')
    const categoryButtons = root.querySelectorAll('.downloads-category-filter')
    const groups = root.querySelectorAll('[data-downloads-group]')
    const emptyMessage = root.querySelector('[data-downloads-empty]')
    let activeCategory = null

    function setActiveButton(button) {
        categoryButtons.forEach(function (btn) {
            const isActive = btn === button
            btn.classList.toggle('btn-primary', isActive)
            btn.classList.toggle('btn-outline-primary', !isActive)
        })
    }

    categoryButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activeCategory = button.dataset.categoryReset !== undefined ? null : button.dataset.category
            setActiveButton(button)
            applyFilters()
        })
    })

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters)
    }

    function applyFilters() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : ''
        let visibleGroups = 0

        groups.forEach(function (group) {
            const categoryMatches = activeCategory === null || activeCategory === group.dataset.category
            let visibleItems = 0

            group.querySelectorAll('[data-downloads-item]').forEach(function (item) {
                const searchText = (item.dataset.search || '').toLowerCase()
                const textMatches = query === '' || searchText.includes(query)
                const visible = categoryMatches && textMatches
                item.parentElement.classList.toggle('d-none', !visible)
                if (visible) {
                    visibleItems++
                }
            })

            group.classList.toggle('d-none', visibleItems === 0)
            if (visibleItems > 0) {
                visibleGroups++
            }
        })

        if (emptyMessage) {
            emptyMessage.classList.toggle('d-none', visibleGroups > 0)
        }
    }

    if (resetButton) {
        setActiveButton(resetButton)
    }
}
