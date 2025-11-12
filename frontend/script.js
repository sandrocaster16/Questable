const sidebar = document.getElementById('sidebar');
const logoBtn = document.getElementById('logoBtn');
const closeBtn = document.getElementById('close-btn');
const overlay = document.getElementById('overlay');

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

// Slider functionality
function setupSlider(trackId, prevBtnId, nextBtnId) {
    const track = document.getElementById(trackId);
    const prevBtn = document.getElementById(prevBtnId);
    const nextBtn = document.getElementById(nextBtnId);
    const cards = Array.from(track.children);
    const cardCount = cards.length;

    if (cardCount <= 3) {
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }

    const cardWidth = cards[0].offsetWidth;
    const gap = parseInt(getComputedStyle(track).gap);
    const moveDistance = cardWidth + gap;

    // Clone cards for infinite effect
    const startClones = cards.slice(-3).map(card => card.cloneNode(true));
    const endClones = cards.slice(0, 3).map(card => card.cloneNode(true));

    startClones.forEach(clone => track.prepend(clone));
    endClones.forEach(clone => track.appendChild(clone));

    let currentPosition = 3;
    track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;

    let isAnimating = false;


    function moveSlider(direction) {
            if (isAnimating) return;
            isAnimating = true;
            
            currentPosition += direction;
            track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            track.style.transform = `translateX(-${currentPosition * moveDistance}px)`;
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

    prevBtn.addEventListener('click', () => moveSlider(-1));
    nextBtn.addEventListener('click', () => moveSlider(1));
}

// Initialize sliders
// setupSlider('history-track', 'history-prev-btn', 'history-next-btn');
setupSlider('promo-track', 'promo-prev-btn', 'promo-next-btn');
