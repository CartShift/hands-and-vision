/**
 * Hands and Vision - View Transitions Logic
 * Handles dynamic assignment of view-transition-names for smooth MPA transitions.
 */
(function() {
    'use strict';

    // Check browser support
    if (!document.startViewTransition) return;

    document.addEventListener('click', (e) => {
        // Find link (Artist or Product)
        const link = e.target.closest('.hv-artist-card__link, .hv-artist-card-premium__link, .hv-product-card__link, .hv-product-card-minimal__link');
        if (!link) return;

        // Check ID type
        const artistId = link.dataset.artistId;
        const productId = link.dataset.productId;

        let transitionName = '';
        let targetSelector = '';

        if (artistId) {
            transitionName = `artist-portrait-${artistId}`;
            targetSelector = '.hv-artist-card__portrait, .hv-artist-card-premium__media';
        } else if (productId) {
            // Check if this is a product-image link click usually we target the image wrapper
            transitionName = `product-img-${productId}`;
            targetSelector = '.hv-product-card__image img, .hv-product-card-minimal__image img';
        }

        if (!transitionName) return;

        // Apply View Transition Name
        const media = link.querySelector(targetSelector);
        if (media) {
            media.style.viewTransitionName = transitionName;
        }
    });

    // 2. Prefetching Logic (Optimization)
    const prefetchSet = new Set();

    document.addEventListener('mouseenter', (e) => {
        const link = e.target.closest('a[href]');
        if (!link || !link.href.startsWith(window.location.origin) || prefetchSet.has(link.href)) return;

        // Only prefetch relevant links
        if (!link.matches('.hv-artist-card__link, .hv-artist-card-premium__link, .hv-product-card__link')) return;

        // Respect Data Saver
        if (navigator.connection && (navigator.connection.saveData || navigator.connection.effectiveType.includes('2g'))) return;

        // Create Link Prefetch
        const prefetchLink = document.createElement('link');
        prefetchLink.rel = 'prefetch';
        prefetchLink.href = link.href;

        document.head.appendChild(prefetchLink);
        prefetchSet.add(link.href);
    }, true);

})();
