const SESSION_DURATION = 15 * 60; // seconds

const countdownElement = document.createElement('div');
countdownElement.className = 'session-timer muted';

document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.top-nav');
    if (!nav) return;
    countdownElement.textContent = formatTime(SESSION_DURATION);
    const actionGroup = nav.querySelector('.top-nav__actions');
    if (actionGroup) {
        actionGroup.appendChild(countdownElement);
    } else {
        nav.appendChild(countdownElement);
    }

    let remaining = SESSION_DURATION;
    setInterval(() => {
        remaining = Math.max(remaining - 1, 0);
        countdownElement.textContent = `Session timeout in: ${formatTime(remaining)}`;
    }, 1000);
});

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}
