/**
 * Swiper Initializations for Hands and Vision
 * Replaces custom DragScroll implementation
 */
(function() {
    'use strict';

    // Helper to initialize Swiper only if element exists
    const initSwiper = (selector, options) => {
        const el = document.querySelector(selector);
        if (el) {
            return new Swiper(selector, options);
        }
    };

    // 1. Gallery Carousel - Premium Coverflow Effect
    initSwiper('.hv-gallery-carousel', {
        modules: [Swiper.Autoplay, Swiper.Parallax, Swiper.Pagination, Swiper.Navigation, Swiper.EffectCoverflow], // Explicitly hint modules if using bundle, though often auto-detected. Safe to omit in bundle, but options key is crucial.
        effect: 'coverflow',
        coverflowEffect: {
            rotate: 0,
            stretch: 0,
            depth: 100,
            modifier: 2.5,
            slideShadows: false,
        },
        parallax: true, // Enable Parallax
        loop: true,
        speed: 1000,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true, // User friendly
        },
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        breakpoints: {
            320: { slidesPerView: 1.3, spaceBetween: 20 },
            768: { slidesPerView: 1.8, spaceBetween: 30 },
            1024: { slidesPerView: 2.5, spaceBetween: 40, coverflowEffect: { depth: 150 } }
        },
        navigation: {
            nextEl: '.hv-gallery-carousel__btn--next',
            prevEl: '.hv-gallery-carousel__btn--prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        keyboard: {
            enabled: true,
        },
        a11y: {
            prevSlideMessage: 'Previous slide',
            nextSlideMessage: 'Next slide',
        }
    });

    // 2. Services Carousel - Clean Scroll
    initSwiper('.hv-services-carousel', {
        loop: false, // Linear scroll feels better for services
        speed: 600,
        grabCursor: true,
        slidesPerView: 1.2,
        spaceBetween: 16,
        freeMode: true, // Allow flinging
        breakpoints: {
            480: { slidesPerView: 2.2, spaceBetween: 20 },
            768: { slidesPerView: 3.2, spaceBetween: 24 },
            1200: { slidesPerView: 4, spaceBetween: 32 }
        }
    });

    // 3. Artists Showcase - Clean Scroll
    initSwiper('.hv-artists-showcase', {
        loop: false,
        speed: 600,
        grabCursor: true,
        slidesPerView: 1.2,
        spaceBetween: 16,
        freeMode: true,
        breakpoints: {
            480: { slidesPerView: 2.2, spaceBetween: 20 },
            768: { slidesPerView: 3.5, spaceBetween: 24 },
            1200: { slidesPerView: 5, spaceBetween: 32 }
        }
    });

})();
