/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

const tenantSidebar = document.querySelector('[data-tenant-sidebar]');

if (tenantSidebar) {
    const toggleButton = document.querySelector('[data-tenant-sidebar-toggle]');
    const closeButton = document.querySelector('[data-tenant-sidebar-close]');
    const backdrop = document.querySelector('[data-tenant-sidebar-backdrop]');
    let sidebarOpen = false;

    const setSidebarOpen = (open) => {
        sidebarOpen = open;
        tenantSidebar.classList.toggle('-translate-x-full', !open);
        tenantSidebar.classList.toggle('translate-x-0', open);
        backdrop?.classList.toggle('hidden', !open);
        toggleButton?.setAttribute('aria-expanded', String(open));
        document.body.classList.toggle('overflow-hidden', open);
    };

    toggleButton?.addEventListener('click', () => setSidebarOpen(!sidebarOpen));
    closeButton?.addEventListener('click', () => setSidebarOpen(false));
    backdrop?.addEventListener('click', () => setSidebarOpen(false));

    tenantSidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setSidebarOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebarOpen) {
            setSidebarOpen(false);
            toggleButton?.focus();
        }
    });

    window.matchMedia('(min-width: 64rem)').addEventListener('change', (event) => {
        if (event.matches) {
            setSidebarOpen(false);
        }
    });
}
