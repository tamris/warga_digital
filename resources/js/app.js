/**
 * Digital RW - Premium Dark Mode & Silky Smooth View Transitions
 * Best practice implementation:
 * - Zero FOUC (handled via inline head script)
 * - View Transitions API with seamless circular expansion from button center
 * - Graceful CSS transition fallback
 * - System preference listener
 * - Wire:navigate resilience
 */

function applyTheme(theme, save = true) {
    const isDark = theme === 'dark';
    if (isDark) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.colorScheme = 'light';
    }

    let meta = document.querySelector('meta[name="color-scheme"]');
    if (!meta) {
        meta = document.createElement('meta');
        meta.name = 'color-scheme';
        document.head.appendChild(meta);
    }
    meta.content = isDark ? 'dark' : 'light';

    if (save) {
        try {
            localStorage.setItem('theme', theme);
            // Sync with cookie so Laravel server renders <html class="dark"> on Livewire wire:navigate requests
            document.cookie = `theme=${encodeURIComponent(theme)}; path=/; max-age=31536000; SameSite=Lax`;
        } catch (e) {}
    }

    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
}

function toggleTheme(event) {
    const isDark = document.documentElement.classList.contains('dark');
    const nextTheme = isDark ? 'light' : 'dark';
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Fallback if View Transitions API is not supported or user prefers reduced motion
    if (!document.startViewTransition || prefersReducedMotion) {
        document.documentElement.classList.add('theme-transitioning');
        applyTheme(nextTheme);
        window.clearTimeout(window._themeTransitionTimer);
        window._themeTransitionTimer = window.setTimeout(() => {
            document.documentElement.classList.remove('theme-transitioning');
        }, 400);
        return;
    }

    // Determine exact click coordinates or fallback to toggle button center
    let x = window.innerWidth / 2;
    let y = 40;

    if (event) {
        if (typeof event.clientX === 'number' && event.clientX > 0) {
            x = event.clientX;
            y = event.clientY;
        } else if (event.currentTarget && typeof event.currentTarget.getBoundingClientRect === 'function') {
            const rect = event.currentTarget.getBoundingClientRect();
            x = rect.left + rect.width / 2;
            y = rect.top + rect.height / 2;
        }
    }

    const endRadius = Math.hypot(
        Math.max(x, window.innerWidth - x),
        Math.max(y, window.innerHeight - y)
    );

    const transition = document.startViewTransition(() => {
        applyTheme(nextTheme);
    });

    transition.ready.then(() => {
        const animation = document.documentElement.animate(
            {
                clipPath: [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ]
            },
            {
                duration: 480,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                pseudoElement: '::view-transition-new(root)'
            }
        );

        animation.onfinish = () => {
            document.documentElement.style.removeProperty('--transition-x');
        };
    }).catch(() => {
        applyTheme(nextTheme);
    });
}

// Initial cookie synchronization
try {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.cookie = `theme=${encodeURIComponent(currentTheme)}; path=/; max-age=31536000; SameSite=Lax`;
    }
} catch (e) {}

// MutationObserver Guard: Intercept any attempt by Livewire DOM morph to strip the 'dark' class
if (window.MutationObserver && !window._themeGuardActive) {
    window._themeGuardActive = true;
    const guardObserver = new MutationObserver(() => {
        const localTheme = localStorage.getItem('theme');
        const cookieMatch = document.cookie.match(/(?:^|; )theme=([^;]*)/);
        const cookieTheme = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
        const targetTheme = localTheme || cookieTheme;
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldBeDark = targetTheme === 'dark' || (!targetTheme && prefersDark);

        if (shouldBeDark && !document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        }
    });

    guardObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
}

// Listen to system preference changes when user has no explicit override
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches ? 'dark' : 'light', false);
        }
    });
}

// Livewire 3 wire:navigate lifecycle locks
document.addEventListener('livewire:navigating', () => {
    const theme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (theme === 'dark' || (!theme && prefersDark)) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
    }
});

document.addEventListener('livewire:navigated', () => {
    const theme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (theme === 'dark' || (!theme && prefersDark)) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.colorScheme = 'light';
    }
});

// Hook into Livewire morph lifecycle if available
const registerLivewireHooks = () => {
    if (window.Livewire && window.Livewire.hook) {
        window.Livewire.hook('morph.updating', ({ el, toEl }) => {
            if (el === document.documentElement) {
                const theme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && prefersDark)) {
                    toEl.classList.add('dark');
                    toEl.style.colorScheme = 'dark';
                }
            }
        });
    }
};

if (window.Livewire) {
    registerLivewireHooks();
} else {
    document.addEventListener('livewire:init', registerLivewireHooks);
}

// Expose globally
window.applyTheme = applyTheme;
window.toggleTheme = toggleTheme;
