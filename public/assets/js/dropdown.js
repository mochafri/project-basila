const activeDropdowns = [];

function toggleDropdown(btnId, dropdownId, selectedId, caretDown) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    const selected = document.getElementById(selectedId);
    const caret = document.getElementById(caretDown);
    let isOpen = false;

    if (!btn || !dropdown || !selected || !caretDown) {
        console.warn(`Element with ID ${btnId}, ${dropdownId}, or ${selectedId} not found.`);
        console.log("Dropdown not found");
        return;
    }

    dropdown.classList.add('hidden');
    activeDropdowns.push({ btn, dropdown });
    console.log("Active !!");

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHiden = dropdown.classList.contains('hidden');

        activeDropdowns.forEach(e => {
            if (e.dropdown !== dropdown) {
                e.dropdown.classList.add('hidden');
            }
        });

        if (isHiden) {
            dropdown.classList.remove('hidden');
            isOpen = true;
        } else {
            dropdown.classList.add('hidden');
            isOpen = false;
        }

        caret.style.transform = isOpen ? 'rotate(-360deg)' : 'rotate(-90deg)';
    });

    dropdown.addEventListener('click', (e) => {
        if (e.target.tagName === 'LI') {
            console.log("bla bla", e.target.textContent);
            selected.textContent = e.target.textContent;
            dropdown.classList.add('hidden');
        }
    });
}

window.addEventListener('click', () => {
    activeDropdowns.forEach(e => {
        e.dropdown.classList.add('hidden');
    });
    activeDropdowns.length = 0;
});


// Dropdown Penetapan yudisium
toggleDropdown('semester-btn', 'semester-dropdown', 'semester-selected', 'caret-down');

// Dropdown Aproval yudisium
toggleDropdown('fakultas-btn', 'fakultas-dropdown', 'fakultas-selected', 'caret-down');
toggleDropdown('semester', 'semester-drpdwn', 'semester-select', 'caret-down2');

// Dropdown Laporan yudisium
toggleDropdown('status-btn', 'status-dropdown', 'status-selected', 'caret-down');
toggleDropdown('format-btn', 'format-dropdown', 'format-selected', 'caret-down2');

// Dropdown tambah penetapan yudisium
toggleDropdown('btn', 'dropdown', 'selected', 'caret-down');
toggleDropdown('btn2', 'dropdown2', 'selected2', 'caret-down2');
toggleDropdown('btn3', 'dropdown3', 'selected3', 'caret-down3');
