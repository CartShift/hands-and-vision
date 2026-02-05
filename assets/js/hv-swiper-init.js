/**
 * Swiper Initializations for Hands and Vision
 * Single source of truth for all Swiper carousels
 * Uses Swiper 12+ with auto-registered modules from bundled CDN
 */
(function () {
	"use strict";

	const isRtl = document.documentElement.dir === 'rtl' || document.body.dir === 'rtl';
	const initializedSwipers = new WeakMap();

	const initSwiper = (selector, options) => {
		if (typeof Swiper === "undefined") {
			console.warn("Swiper library not loaded. Skipping initialization for:", selector);
			return null;
		}
		
		const el = document.querySelector(selector);
		if (!el) {
			return null;
		}

		if (initializedSwipers.has(el)) {
			console.warn("Swiper already initialized for:", selector);
			return initializedSwipers.get(el);
		}

		const finalOptions = {
			...options,
			rtl: isRtl
		};

		try {
			const swiper = new Swiper(selector, finalOptions);
			initializedSwipers.set(el, swiper);
			return swiper;
		} catch (error) {
			console.error("Error initializing Swiper for", selector, ":", error);
			return null;
		}
	};

	const initAllSwipers = () => {
		if (typeof Swiper === "undefined") {
			setTimeout(initAllSwipers, 100);
			return;
		}

		const galleryCarousel = document.querySelector(".hv-gallery-carousel");
		if (galleryCarousel && !initializedSwipers.has(galleryCarousel)) {
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

		const servicesCarousel = document.querySelector(".hv-services-carousel");
		if (servicesCarousel && !initializedSwipers.has(servicesCarousel)) {
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

		const artistsShowcase = document.querySelector(".hv-artists-showcase");
		if (artistsShowcase && !initializedSwipers.has(artistsShowcase)) {
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
	};

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initAllSwipers);
	} else {
		initAllSwipers();
	}
})();
