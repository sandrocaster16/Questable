document.addEventListener('DOMContentLoaded', () => {

    // --- Логика для сайдбара ---
    const sidebar = document.getElementById('sidebar');
    const logoBtn = document.getElementById('logoBtn');
    const closeBtn = document.getElementById('close-btn');
    const overlay = document.getElementById('overlay');

    if (sidebar && logoBtn && closeBtn && overlay) {
        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        logoBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    }

    // --- Логика для модального окна настроек ---
    const settingsBtn = document.getElementById('settings-btn');
    const settingsModal = document.getElementById('settings-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelSettingsBtn = document.getElementById('cancel-settings-btn');
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    if (settingsBtn && settingsModal) {
        function openSettingsModal() {
            settingsModal.classList.add('open');
        }

        function closeSettingsModal() {
            settingsModal.classList.remove('open');
        }

        settingsBtn.addEventListener('click', openSettingsModal);

        if (closeModalBtn) closeModalBtn.addEventListener('click', closeSettingsModal);
        if (cancelSettingsBtn) cancelSettingsBtn.addEventListener('click', closeSettingsModal);

        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        avatarPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    }

    // --- Логика для Слайдера с Авто-прокруткой ---
    function setupAutoSlider(trackId, prevBtnId, nextBtnId, interval = 5000) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);
        if (!prevBtn || !nextBtn) return;

        const cards = Array.from(track.children);
        if (cards.length === 0) return;

        const cardCount = cards.length;

        if (cardCount <= 3) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }

        const cardWidth = cards[0].offsetWidth;
        const gap = parseInt(getComputedStyle(track).gap) || 0;
        const moveDistance = cardWidth + gap;

        const startClones = cards.slice(-3).map(card => card.cloneNode(true));
        const endClones = cards.slice(0, 3).map(card => card.cloneNode(true));

        startClones.forEach(clone => track.prepend(clone));
        endClones.forEach(clone => track.appendChild(clone));

        let currentPosition = 3;
        track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
        let isAnimating = false;
        let autoScrollInterval;

        function moveSlider(direction) {
            if (isAnimating) return;
            isAnimating = true;

            currentPosition += direction;
            track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
        }

        function resetAutoScroll() {
            clearInterval(autoScrollInterval);
            autoScrollInterval = setInterval(() => {
                moveSlider(1); // Двигаем вправо
            }, interval);
        }

        track.addEventListener('transitionend', () => {
            if (currentPosition >= cardCount + 3) {
                track.style.transition = 'none';
                currentPosition = 3;
                track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
            }
            if (currentPosition <= 2) {
                track.style.transition = 'none';
                currentPosition = cardCount + 2;
                track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
            }

            setTimeout(() => {
                track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                isAnimating = false;
            }, 50);
        });

        prevBtn.addEventListener('click', () => {
            moveSlider(-1);
            resetAutoScroll(); // Сбрасываем таймер
        });

        nextBtn.addEventListener('click', () => {
            moveSlider(1);
            resetAutoScroll(); // Сбрасываем таймер
        });

        // Запускаем авто-прокрутку при инициализации
        resetAutoScroll();
    }

    // Инициализируем только слайдер "Успейте посетить"
    setupAutoSlider('promo-track', 'promo-prev-btn', 'promo-next-btn', 5000);

});