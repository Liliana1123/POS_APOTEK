/**
 * Count-Up Number Animation Engine
 * Animates numbers smoothly from 0 to target value on page load
 */
(function () {
    'use strict';

    function formatNumberIndo(num) {
        return Math.floor(num).toLocaleString('id-ID');
    }

    function animateCounter(el) {
        if (el.dataset.countupRunning === 'true') return;

        const target = parseFloat(el.dataset.target || '0');
        const prefix = el.dataset.prefix ? el.dataset.prefix.replace(/ /g, '\u00A0') : '';
        const suffix = el.dataset.suffix || '';
        const duration = parseInt(el.dataset.duration || '850', 10);

        if (isNaN(target) || target <= 0) return;

        el.dataset.countupRunning = 'true';
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Ease-out cubic curve: fast start, soft gentle landing
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentVal = Math.floor(easeProgress * target);

            el.innerHTML = prefix + formatNumberIndo(currentVal) + (suffix ? ('<span class="text-xs font-medium text-gray-400">' + suffix + '</span>') : '');

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.innerHTML = prefix + formatNumberIndo(target) + (suffix ? ('<span class="text-xs font-medium text-gray-400">' + suffix + '</span>') : '');
                el.dataset.countupDone = 'true';
                delete el.dataset.countupRunning;
            }
        }

        requestAnimationFrame(step);
    }

    function initCountUp() {
        const counters = document.querySelectorAll('[data-countup="true"]');
        if (counters.length === 0) return;

        // Use IntersectionObserver if available so counters offscreen animate when scrolled into view
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            counters.forEach(el => observer.observe(el));
        } else {
            // Fallback for browsers without IntersectionObserver
            counters.forEach(animateCounter);
        }
    }

    window.runCountUp = initCountUp;

    document.addEventListener('DOMContentLoaded', function () {
        // Small initial delay so fonts & DOM are fully painted
        setTimeout(initCountUp, 50);
    });

    // Also re-run on live-filter updates if dynamic
    document.addEventListener('live-filter:updated', function () {
        initCountUp();
    });
})();
