document.addEventListener('DOMContentLoaded', () => {
    function setupSlider(trackId, prevBtnId, nextBtnId, interval = 5000) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);
        if (!prevBtn || !nextBtn) return;

        let cards = Array.from(track.children);
        if (cards.length === 0) return;

        let cardCount = cards.length;
        if (cardCount <= 3) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }

        // Функция для расчёта расстояния (для ресайза)
        function calculateMoveDistance() {
            const cardWidth = cards[0].offsetWidth;
            const gap = parseInt(getComputedStyle(track).gap) || 0;
            return cardWidth + gap;
        }

        let moveDistance = calculateMoveDistance();

        // Создание клонов
        const startClones = cards.slice(-3).map(card => card.cloneNode(true));
        const endClones = cards.slice(0, 3).map(card => card.cloneNode(true));
        startClones.forEach(clone => track.prepend(clone));
        endClones.forEach(clone => track.appendChild(clone));

        // Обновляем cards после клонов
        cards = Array.from(track.children); // Теперь с клонами
        cardCount = cards.length - 6; // Оригинальное количество (без клонов)

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
                moveSlider(1);
            }, interval);
        }

        track.addEventListener('transitionend', () => {
            if (currentPosition >= cardCount + 3) {
                track.style.transition = 'none';
                currentPosition = 3;
                track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
            } else if (currentPosition <= 2) {
                track.style.transition = 'none';
                currentPosition = cardCount + 2;
                track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
            }
            requestAnimationFrame(() => {
                isAnimating = false;
            });
        });

        prevBtn.addEventListener('click', () => {
            moveSlider(-1);
            resetAutoScroll();
        });

        nextBtn.addEventListener('click', () => {
            moveSlider(1);
            resetAutoScroll();
        });

        // Пауза при hover/touch
        track.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        track.addEventListener('mouseleave', resetAutoScroll);
        track.addEventListener('touchstart', () => clearInterval(autoScrollInterval));
        track.addEventListener('touchend', resetAutoScroll);

        // Recalculate на ресайз окна
        window.addEventListener('resize', () => {
            moveDistance = calculateMoveDistance();
            track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
        });
    }

    // Инициализация
    setupSlider('promo-track', 'promo-prev-btn', 'promo-next-btn', 5000);
});