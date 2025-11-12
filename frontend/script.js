const sidebar = document.getElementById('sidebar');
const openBtn = document.getElementById('open-btn');
const closeBtn = document.getElementById('close-btn');
const overlay = document.getElementById('overlay');
const sidebarLogo = document.getElementById('sidebar-logo');
let isAnimating = false;

function openSidebar() {
    if (isAnimating) return;
    isAnimating = !0;
    const t = openBtn.getBoundingClientRect();
    sidebarLogo.style.opacity = "0", openBtn.style.opacity = "0";
    const e = openBtn.cloneNode(!0);
    e.classList.add("logo-clone"), document.body.appendChild(e), e.style.top = `${t.top}px`, e.style.left = `${t.left}px`, e.style.width = `${t.width}px`, e.style.height = `${t.height}px`, sidebar.classList.add("open"), overlay.classList.add("active"), requestAnimationFrame(() => {
        const t = sidebarLogo.getBoundingClientRect();
        e.style.top = `${t.top}px`, e.style.left = `${t.left}px`, e.style.width = `${t.width}px`, e.style.height = `${t.height}px`
    }), e.addEventListener("transitionend", () => { sidebarLogo.style.opacity = "1", e.remove(), isAnimating = !1 }, { once: !0 })
}

function closeSidebar() {
    if (isAnimating) return;
    isAnimating = !0;
    const t = sidebarLogo.getBoundingClientRect();
    sidebarLogo.style.opacity = "0";
    const e = sidebarLogo.cloneNode(!0);
    e.classList.add("logo-clone"), document.body.appendChild(e), e.style.top = `${t.top}px`, e.style.left = `${t.left}px`, e.style.width = `${t.width}px`, e.style.height = `${t.height}px`, sidebar.classList.remove("open"), overlay.classList.remove("active"), requestAnimationFrame(() => {
        const t = openBtn.getBoundingClientRect();
        e.style.top = `${t.top}px`, e.style.left = `${t.left}px`, e.style.width = `${t.width}px`, e.style.height = `${t.height}px`
    }), e.addEventListener("transitionend", () => { openBtn.style.opacity = "1", e.remove(), isAnimating = !1 }, { once: !0 })
}
openBtn.addEventListener("click", openSidebar), closeBtn.addEventListener("click", closeSidebar), overlay.addEventListener("click", closeSidebar);


// цикличный слайдер
function setupSlider(sectionId) {
    const section = document.getElementById(sectionId);
    const track = section.querySelector('.cards-track');
    const prevBtn = section.querySelector('.prev-btn');
    const nextBtn = section.querySelector('.next-btn');
    const originalCards = Array.from(track.children);

    if (originalCards.length <= 3) {
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }

    const itemsVisible = 3;
    const moveDistance = originalCards[0].offsetWidth + parseInt(getComputedStyle(track).gap);

    // Клонируем элементы для создания "бесконечного" эффекта
    const clonesStart = originalCards.slice(-itemsVisible).map(card => card.cloneNode(true));
    const clonesEnd = originalCards.slice(0, itemsVisible).map(card => card.cloneNode(true));

    clonesStart.forEach(clone => track.prepend(clone));
    clonesEnd.forEach(clone => track.append(clone));

    // Начальная позиция, чтобы были видны настоящие первые карточки
    let currentIndex = itemsVisible;
    track.style.transform = `translateX(-${currentIndex * moveDistance}px)`;

    let isMoving = false;

    const move = (direction) => {
        if (isMoving) return;
        isMoving = true;

        currentIndex += direction;
        track.style.transition = 'transform 0.3s cubic-bezier(0.1, 0.5, 0.5, 1)';
        track.style.transform = `translateX(-${currentIndex * moveDistance}px)`;
    };

    // Когда анимация заканчивается, проверяем, не нужно ли "телепортировать" ленту
    track.addEventListener('transitionend', () => {
        if (currentIndex >= originalCards.length + itemsVisible) {
            track.style.transition = 'none'; // Убираем анимацию
            currentIndex = itemsVisible;
            track.style.transform = `translateX(-${currentIndex * moveDistance}px)`;
        }
        if (currentIndex <= itemsVisible - 1) {
            track.style.transition = 'none'; // Убираем анимацию
            currentIndex = originalCards.length + itemsVisible - 1;
            track.style.transform = `translateX(-${currentIndex * moveDistance}px)`;
        }
        // Возвращаем анимацию обратно через микро-задержку
        setTimeout(() => {
            track.style.transition = 'transform 0.3s cubic-bezier(0.1, 0.5, 0.5, 1)';
            isMoving = false;
        });
    });

    nextBtn.addEventListener('click', () => move(1));
    prevBtn.addEventListener('click', () => move(-1));
}

setupSlider('history-section');
setupSlider('promo-section');