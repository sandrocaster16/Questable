// sidebar.js — теперь 100% работает с твоим header.php
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const openBtn = document.getElementById('sidebarToggle');    // ← твой реальный ID
    const closeBtn = document.getElementById('sidebarClose');    // ← твой реальный ID

    if (!sidebar || !overlay || !openBtn || !closeBtn) {
        console.warn('Sidebar elements not found. Check IDs: sidebar, overlay, sidebarToggle, sidebarClose');
        return;
    }

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Закрытие на Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });
});