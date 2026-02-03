/**
 * Hands and Vision - Add to Cart Parabola Animation
 * Creates a flying image effect from the product card to the cart icon.
 */
(function() {
    'use strict';

    document.addEventListener('click', function(e) {
        // Target standard WC add to cart buttons and custom ones
        const btn = e.target.closest('.add_to_cart_button, .hv-btn--add-to-cart, .single_add_to_cart_button');
        if (!btn) return;

        // Don't animate if disabled
        if (btn.classList.contains('disabled') || btn.classList.contains('loading')) return;

        // Find the Product Image
        // 1. Try closest card
        let card = btn.closest('.hv-product-card, .hv-product-card-minimal, .product');
        let img = null;

        if (card) {
            img = card.querySelector('img');
        } else {
            // 2. Fallback for single product page
            const singleGallery = document.querySelector('.woocommerce-product-gallery__image img');
            if (singleGallery) img = singleGallery;
        }

        if (!img) return;

        // Find the Cart Icon (Destination)
        const cartIcon = document.querySelector('.hv-header__cart');
        if (!cartIcon) return; // Cart not visible (e.g. mobile sometimes)

        // Perform Animation
        animateToCart(img, cartIcon);
    });

    function animateToCart(sourceImg, targetCart) {
        // Clone the image
        const clone = sourceImg.cloneNode(false);
        const rect = sourceImg.getBoundingClientRect();
        const targetRect = targetCart.getBoundingClientRect();

        // Style the clone to match source initially
        clone.style.position = 'fixed';
        clone.style.left = rect.left + 'px';
        clone.style.top = rect.top + 'px';
        clone.style.width = rect.width + 'px';
        clone.style.height = rect.height + 'px';
        clone.style.objectFit = 'cover';
        clone.style.zIndex = '999999';
        clone.style.pointerEvents = 'none';
        clone.style.transition = 'all 0.8s cubic-bezier(0.19, 1, 0.22, 1)'; // Smooth easing
        clone.style.borderRadius = '50%'; // Morph to circle
        clone.style.opacity = '0.8';

        document.body.appendChild(clone);

        // Trigger animation in next frame
        requestAnimationFrame(() => {
            clone.style.left = (targetRect.left + targetRect.width / 4) + 'px';
            clone.style.top = (targetRect.top + targetRect.height / 4) + 'px';
            clone.style.width = '20px'; // Shrink
            clone.style.height = '20px';
            clone.style.opacity = '0'; // Fade out at the very end
        });

        // Cleanup
        setTimeout(() => {
            clone.remove();
            // Optional: Shake the cart icon
            targetCart.classList.add('added');
            setTimeout(() => targetCart.classList.remove('added'), 500);
        }, 800);
    }

})();
