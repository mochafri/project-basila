document.addEventListener('DOMContentLoaded', () => {
    const checkAll = document.getElementById('checkAll');

    checkAll.addEventListener('change', () => {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = checkAll.checked);
    });

    // Opsional: update "Check All" jika user ubah salah satu checkbox
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('row-checkbox')) {
            const all = document.querySelectorAll('.row-checkbox');
            const checked = document.querySelectorAll('.row-checkbox:checked');
            document.getElementById('checkAll').checked = (all.length === checked.length);
        }
    });

    const selectedNIMs = [...document.querySelectorAll('.row-checkbox:checked')]
    .map(cb => cb.value);
    console.log(selectedNIMs);

});
