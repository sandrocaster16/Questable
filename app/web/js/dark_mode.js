const themeBtn = document.getElementById('themeToggle');
const themeIcon = themeBtn.querySelector('i');
const themeText = themeBtn.querySelector('span'); // Находим текст
const body = document.body;

// Функция обновления вида кнопки
function updateButton(isDark) {
    if (isDark) {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        if(themeText) themeText.textContent = "Светлая тема";
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        if(themeText) themeText.textContent = "Темная тема";
    }
}

// 1. Проверка при загрузке
const savedTheme = localStorage.getItem('theme');
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
    body.classList.add('dark-theme');
    updateButton(true);
}

// 2. Клик
themeBtn.addEventListener('click', () => {
    body.classList.toggle('dark-theme');
    const isDark = body.classList.contains('dark-theme');
    
    // Сохраняем и обновляем
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateButton(isDark);
});