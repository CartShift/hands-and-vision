/**
 * Swiper Initializations for Hands and Vision
 * Replaces custom DragScroll implementation
 * Uses Swiper 12+ with auto-registered modules from bundled CDN
 */
(function () {
	"use strict";

	const isRtl = document.documentElement.dir === 'rtl' || document.body.dir === 'rtl';

	const initSwiper = (selector, options) => {
		if (typeof Swiper === "undefined") {
			console.warn("Swiper library not loaded. Skipping initialization for:", selector);
			return null;
		}
		const el = document.querySelector(selector);
		if (!el) {
			console.warn("Swiper element not found:", selector);
			return null;
		}

		const finalOptions = {
			...options,
			rtl: isRtl
		};

		try {
			return new Swiper(selector, finalOptions);
		} catch (error) {
			console.error("Error initializing Swiper for", selector, ":", error);
			return null;
		}
	};

	// 1. Gallery Carousel - Premium Coverflow Effect
	if (document.querySelector(".hv-gallery-carousel")) {
		initSwiper(".hv-gallery-carousel", {
			effect: "coverflow",
			coverflowEffect: {
				rotate: 0,
				stretch: 0,
				depth: 100,
				modifier: 2.5,
				slideShadows: false
			},
			parallax: true,
			loop: false,
			speed: 1000,
			autoplay: {
				delay: 3000,
				disableOnInteraction: false,
				pauseOnMouseEnter: true
			},
			grabCursor: true,
			centeredSlides: true,
			slidesPerView: "auto",
			breakpoints: {
				320: { slidesPerView: 1.3, spaceBetween: 20 },
				768: { slidesPerView: 1.8, spaceBetween: 30 },
				1024: { slidesPerView: 2.5, spaceBetween: 40 }
			},
			navigation: {
				nextEl: ".hv-gallery-carousel__btn--next",
				prevEl: ".hv-gallery-carousel__btn--prev"
			},
			pagination: {
				el: ".swiper-pagination",
				clickable: true,
				dynamicBullets: true
			},
			keyboard: {
				enabled: true
			},
			a11y: {
				prevSlideMessage: isRtl ? "שקופית קודמת" : "Previous slide",
				nextSlideMessage: isRtl ? "שקופית הבאה" : "Next slide"
			}
		});
	}

	// 2. Services Carousel - Clean Scroll
	if (document.querySelector(".hv-services-carousel")) {
		initSwiper(".hv-services-carousel", {
			loop: false,
			speed: 600,
			grabCursor: true,
			slidesPerView: 1.2,
			spaceBetween: 16,
			freeMode: true,
			breakpoints: {
				480: { slidesPerView: 2.2, spaceBetween: 20 },
				768: { slidesPerView: 3.2, spaceBetween: 24 },
				1200: { slidesPerView: 4, spaceBetween: 32 }
			}
		});
	}

	// 3. Artists Showcase - Clean Scroll
	if (document.querySelector(".hv-artists-showcase")) {
		initSwiper(".hv-artists-showcase", {
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
	}
})();
