const activeDropdowns = [];

function toggleDropdown(btnId, dropdownId, selectedId, caretDown) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    const selected = document.getElementById(selectedId);
    const caret = document.getElementById(caretDown);

    if (!btn || !dropdown || !selected || !caret) {
        console.warn(`Element with ID ${btnId}, ${dropdownId}, or ${selectedId} not found.`);
        console.log("Dropdown not found");
        return;
    }

    dropdown.classList.add('hidden');
    activeDropdowns.push({ btn, dropdown, caret });

    btn.addEventListener('click', (e) => {
        e.stopPropagation();

        const isHiden = dropdown.classList.contains('hidden');

        activeDropdowns.forEach(e => {
            if (e.dropdown !== dropdown) {
                e.dropdown.classList.add('hidden');
                caret.style.transform = 'rotate(-90deg)';
            }
        });

        if (isHiden) {
            dropdown.classList.remove('hidden');
            caret.style.transform = 'rotate(-360deg)';
        } else {
            dropdown.classList.add('hidden');
            caret.style.transform = 'rotate(-90deg)';
        }
    });

    dropdown.addEventListener('click', (e) => {
        if (e.target.tagName === 'LI') {
            selected.textContent = e.target.textContent;
            dropdown.classList.add('hidden');
            caret.style.transform = 'rotate(-90deg)';
        }
    });

    caret.style.transform = 'rotate(-90deg)';
}

window.addEventListener('click', (e) => {
    activeDropdowns.forEach(item => {
        if (!item.btn.contains(e.target) && !item.dropdown.contains(e.target)){
            item.dropdown.classList.add('hidden');
            item.caret.style.transform = 'rotate(-90deg)';
        }
    });
});

toggleDropdown('btn', 'dropdown', 'selected', 'caret-down');
toggleDropdown('btn2', 'dropdown2', 'selected2', 'caret-down2');
toggleDropdown('btn3', 'dropdown3', 'selected3', 'caret-down3');
