/**
 * Hands and Vision - Premium Scroll Parallax
 * Handles smooth parallax effects for hero images and designated elements.
 */
(function() {
    'use strict';

    class HVParallax {
        constructor() {
            this.items = document.querySelectorAll('.hv-parallax-img');
            this.heroVideo = document.querySelector('.hv-hero-video__media video'); // Special case for homepage
            this.rafId = null;
            this.init();
        }

        init() {
            if (!this.items.length && !this.heroVideo) return;

            // Check for reduced motion preference
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReducedMotion) return;

            this.bindEvents();
            this.update(); // Initial calculation
        }

        bindEvents() {
            window.addEventListener('scroll', () => {
                if (!this.rafId) {
                    this.rafId = requestAnimationFrame(this.update.bind(this));
                }
            }, { passive: true });

            window.addEventListener('resize', this.update.bind(this), { passive: true });
        }

        update() {
            const scrollY = window.scrollY;
            const viewportHeight = window.innerHeight;

            // 1. Process Standard Parallax Items (e.g., Artist Portrait)
            this.items.forEach(img => {
                const container = img.parentElement;
                if (!container) return;

                const rect = container.getBoundingClientRect();

                // Only animate if in view
                if (rect.top < viewportHeight && rect.bottom > 0) {
                    const speed = parseFloat(img.dataset.parallaxSpeed) || 0.1;

                    // Calculate center point relative to viewport
                    const centerOffset = (viewportHeight / 2) - (rect.top + rect.height / 2);

                    // Simple parallax: move vertically based on scroll
                    // If centerOffset is 0 (centered), translate is 0.
                    // If scrolling down (container moves up), translate moves up (slower)
                    const yPos = centerOffset * speed;

                    img.style.transform = `translateY(${yPos}px) scale(1.1)`; // Scale helps avoid edges
                }
            });

            // 2. Homepage Hero Video Parallax (if present)
            if (this.heroVideo) {
                // Determine if hero is still visible
                const heroSection = this.heroVideo.closest('.hv-hero-video');
                if (heroSection) {
                    const rect = heroSection.getBoundingClientRect();
                    if (rect.bottom > 0) {
                        // Move video down slower than scroll
                        const yPos = scrollY * 0.3; // 30% speed
                        this.heroVideo.style.transform = `translateY(${yPos}px) scale(1.05)`; // Scale to prevent gaps

                        // Optional: Blur effect on scroll out
                        if (scrollY > 100) {
                            const blurAmount = Math.min((scrollY - 100) / 50, 10);
                            this.heroVideo.style.filter = `blur(${blurAmount}px) brightness(${1 - blurAmount/30})`;
                        } else {
                            this.heroVideo.style.filter = 'none';
                        }
                    }
                }
            }

            this.rafId = null;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new HVParallax();
    });

})();
