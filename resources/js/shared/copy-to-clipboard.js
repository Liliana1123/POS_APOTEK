/**
 * Global Click-to-Copy Engine with Micro-Tooltip Feedback
 * Listens for click events on elements with [data-copy] or .copyable
 */
(function () {
    'use strict';

    function initCopyToClipboard() {
        document.addEventListener('click', async (e) => {
            const trigger = e.target.closest('[data-copy], .copyable');
            if (!trigger) return;

            const textToCopy = trigger.getAttribute('data-copy') || trigger.innerText.trim();
            if (!textToCopy || textToCopy === '—' || textToCopy === '-') return;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(textToCopy);
                } else {
                    const textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    textarea.style.position = 'fixed';
                    textarea.style.left = '-9999px';
                    textarea.style.top = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                }

                showCopyFeedback(trigger);
            } catch (err) {
                console.warn('Gagal menyalin teks ke clipboard:', err);
            }
        });
    }

    function showCopyFeedback(triggerEl) {
        if (triggerEl._copyTooltipActive) return;
        triggerEl._copyTooltipActive = true;

        const tooltip = document.createElement('div');
        tooltip.className = 'copy-tooltip-bubble';
        tooltip.innerHTML = `
            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Tersalin!</span>
        `;

        document.body.appendChild(tooltip);

        const rect = triggerEl.getBoundingClientRect();
        const tooltipWidth = 84;
        const tooltipHeight = 28;

        let top = rect.top + window.scrollY - tooltipHeight - 6;
        let left = rect.left + window.scrollX + (rect.width / 2) - (tooltipWidth / 2);

        if (rect.top < tooltipHeight + 12) {
            top = rect.bottom + window.scrollY + 6;
        }

        tooltip.style.top = `${top}px`;
        tooltip.style.left = `${Math.max(8, left)}px`;

        requestAnimationFrame(() => {
            tooltip.classList.add('copy-tooltip-visible');
        });

        setTimeout(() => {
            tooltip.classList.remove('copy-tooltip-visible');
            tooltip.classList.add('copy-tooltip-leaving');
            setTimeout(() => {
                if (tooltip.parentNode) {
                    tooltip.parentNode.removeChild(tooltip);
                }
                triggerEl._copyTooltipActive = false;
            }, 180);
        }, 1200);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCopyToClipboard);
    } else {
        initCopyToClipboard();
    }
})();
