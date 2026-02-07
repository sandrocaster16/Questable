document.addEventListener('DOMContentLoaded', () => {
    const themeBtn = document.getElementById('themeToggle');
    if (!themeBtn) return;

    const themeIcon = themeBtn.querySelector('i');
    const themeText = themeBtn.querySelector('span');
    const body = document.body;

    // Обновление интерфейса кнопки
    function updateButtonUI(isDark) {
        if (isDark) {
            if (themeIcon) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
            if (themeText) themeText.textContent = "Светлая тема";
        } else {
            if (themeIcon) {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
            if (themeText) themeText.textContent = "Темная тема";
        }
    }

    // Проверка сохраненной темы или системной предпочтения
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: light)').matches;
    const isDark = savedTheme === 'light' || (!savedTheme && systemPrefersDark);

    if (!isDark) {
        body.classList.add('dark-theme');
    }
    updateButtonUI(isDark);

    // Переключение темы по клику
    themeBtn.addEventListener('click', () => {
        body.classList.toggle('dark-theme');
        const isDarkNow = body.classList.contains('dark-theme');
        localStorage.setItem('theme', isDarkNow ? 'dark' : 'light');
        updateButtonUI(isDarkNow);
    });
});
