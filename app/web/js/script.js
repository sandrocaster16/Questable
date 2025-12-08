document.addEventListener('DOMContentLoaded', () => {
    
    // === 1. ЛОГИКА САЙДБАРА (Меню) ===
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const openBtn = document.getElementById('sidebarToggle'); 
    const closeBtn = document.getElementById('sidebarClose');

    function toggleMenu() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    }

    if(openBtn) openBtn.addEventListener('click', toggleMenu);
    if(closeBtn) closeBtn.addEventListener('click', toggleMenu);
    if(overlay) overlay.addEventListener('click', toggleMenu);


    // === 2. ЛОГИКА СЛАЙДЕРА (Адаптированная) ===
    // Примечание: Для простой работы мы используем простую прокрутку,
    // так как в CSS мы сделали scroll-snap для мобилок.
    // Но для ПК оставим кнопки.
    const sliderTrack = document.getElementById('promo-track'); // ID из твоего HTML
    const prevBtn = document.getElementById('promo-prev-btn');
    const nextBtn = document.getElementById('promo-next-btn');

    if (sliderTrack && prevBtn && nextBtn) {
        // Прокрутка на ширину одной карточки + отступ (320 + 25)
        const scrollAmount = 345; 

        nextBtn.addEventListener('click', () => {
            sliderTrack.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            sliderTrack.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });
    }


    // === 3. ПРЕВЬЮ АВАТАРКИ В ПРОФИЛЕ ===
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('click', () => {
            // Сброс value, чтобы если выбрали тот же файл, событие сработало
            avatarInput.value = null; 
        });

        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    avatarPreview.src = evt.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }


    // === 4. ДОБАВЛЕНИЕ СТАНЦИЙ (ИСПРАВЛЕНО) ===
    const addStationBtn = document.getElementById('addStationBtn');
    const stationsList = document.getElementById('stationsList');

    if (addStationBtn && stationsList) {
        
        // Функция пересчета номеров станций
        function updateStationNumbers() {
            const cards = stationsList.children;
            // Пробегаемся по всем существующим карточкам
            Array.from(cards).forEach((card, index) => {
                const title = card.querySelector('.station-title');
                if (title) {
                    title.textContent = `Станция #${index + 1}`;
                }
            });
        }

        addStationBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            
            // Применяем стили через CSS переменные, чтобы было красиво и в темной, и в светлой теме
            div.style.border = '1px solid var(--border)';
            div.style.background = 'var(--bg-surface)';
            div.style.padding = '25px';
            div.style.borderRadius = 'var(--radius)';
            div.style.marginBottom = '20px';
            div.style.boxShadow = 'var(--shadow-sm)';
            
            div.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <h4 class="station-title" style="margin:0;">Станция</h4>
                    <button type="button" class="remove-btn" style="color: #ef4444; background:none; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:5px;">
                        <i class="fas fa-trash"></i> Удалить
                    </button>
                </div>
                <div class="form-group">
                    <label>Название локации</label>
                    <input type="text" class="input-field" placeholder="Например: Памятник Пушкину">
                </div>
                <div class="form-group">
                    <label>Тип задания</label>
                    <select class="input-field">
                        <option value="info">Просто информация</option>
                        <option value="quiz">Викторина (Ответ на вопрос)</option>
                        <option value="photo">Фото-подтверждение</option>
                        <option value="geo">Геолокация (GPS)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Текст задания / Вопрос</label>
                    <textarea class="input-field" rows="2"></textarea>
                </div>
            `;
            
            stationsList.appendChild(div);

            // Навешиваем событие удаления на кнопку ВНУТРИ этой карточки
            const removeBtn = div.querySelector('.remove-btn');
            removeBtn.addEventListener('click', () => {
                div.remove();           // Удаляем блок
                updateStationNumbers(); // Пересчитываем номера оставшихся
            });

            // Обновляем номера (чтобы у новой стало правильно: 1, 2, 3...)
            updateStationNumbers();
        });
    }

    
    // === 5. ТЕМНАЯ ТЕМА (НОВОЕ) ===
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        const themeIcon = themeBtn.querySelector('i');
        const themeText = themeBtn.querySelector('span');
        const body = document.body;

        // Обновление интерфейса кнопки
        function updateButtonUI(isDark) {
            if (isDark) {
                if(themeIcon) { themeIcon.classList.remove('fa-moon'); themeIcon.classList.add('fa-sun'); }
                if(themeText) themeText.textContent = "Светлая тема";
            } else {
                if(themeIcon) { themeIcon.classList.remove('fa-sun'); themeIcon.classList.add('fa-moon'); }
                if(themeText) themeText.textContent = "Темная тема";
            }
        }

        // Проверка при загрузке
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            body.classList.add('dark-theme');
            updateButtonUI(true);
        }

        // Клик по кнопке
        themeBtn.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateButtonUI(isDark);
        });
    }
});