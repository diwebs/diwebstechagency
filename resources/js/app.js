import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Swipe Gesture Recognition for mobile responsiveness
let touchstartX = 0;
let touchendX = 0;
let touchstartY = 0;
let touchendY = 0;

function handleGesture() {
    const diffX = touchendX - touchstartX;
    const diffY = touchendY - touchstartY;

    if (Math.abs(diffX) > 100 && Math.abs(diffY) < 60) {
        if (diffX < 0) {
            window.dispatchEvent(new CustomEvent('swipe-left'));
        } else {
            window.dispatchEvent(new CustomEvent('swipe-right'));
        }
    }
}

document.addEventListener('touchstart', e => {
    touchstartX = e.changedTouches[0].screenX;
    touchstartY = e.changedTouches[0].screenY;
}, { passive: true });

document.addEventListener('touchend', e => {
    touchendX = e.changedTouches[0].screenX;
    touchendY = e.changedTouches[0].screenY;
    handleGesture();
}, { passive: true });

