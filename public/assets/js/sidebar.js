const toggleButton = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const logo = document.getElementById('logo');
        const name = document.getElementById('sidebar-name');
        const nim = document.getElementById('sidebar-nim');

        let isSidebarCollapsed = false; // status asli sidebar

        // Toggle sidebar via button
        toggleButton.addEventListener('click', () => {
            isSidebarCollapsed = !isSidebarCollapsed;

            if (isSidebarCollapsed) {
                logo.classList.add('hidden');
                name.classList.add('hidden');
                nim.classList.add('hidden');
            } else {
                logo.classList.remove('hidden');
                name.classList.remove('hidden');
                nim.classList.remove('hidden');
            }
        });

        // Tampilkan isi saat hover jika sidebar sedang collapse
        sidebar.addEventListener('mouseenter', () => {
            if (isSidebarCollapsed) {
                logo.classList.remove('hidden');
                name.classList.remove('hidden');
                nim.classList.remove('hidden');
            }
        });

        sidebar.addEventListener('mouseleave', () => {
            if (isSidebarCollapsed) {
                logo.classList.add('hidden');
                name.classList.add('hidden');
                nim.classList.add('hidden');
            }
        });