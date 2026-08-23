/**
 * Swiper Initializations for Hands and Vision
 * Following Swiper.js official best practices
 * Uses Swiper 12+ with auto-registered modules from bundled CDN
 */
(function () {
	"use strict";

	const isRtl = document.documentElement.dir === 'rtl' || document.body.dir === 'rtl';
	const initializedSwipers = new Map();
	const initAttempts = new WeakMap();
	const MAX_INIT_ATTEMPTS = 10;
	let resizeTimeout = null;

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
			return initializedSwipers.get(el);
		}

		const attempts = initAttempts.get(el) || 0;
		if (attempts >= MAX_INIT_ATTEMPTS) {
			console.error("Max initialization attempts reached for:", selector);
			return null;
		}
		initAttempts.set(el, attempts + 1);

		const wrapper = el.querySelector('.swiper-wrapper');
		const slides = wrapper ? wrapper.querySelectorAll('.swiper-slide') : null;
		
		if (!wrapper || !slides || slides.length === 0) {
			console.warn("Swiper structure incomplete for:", selector, "- retrying...");
			setTimeout(() => initSwiper(selector, options), 200);
			return null;
		}

		const finalOptions = {
			...options,
			rtl: isRtl,
			watchOverflow: true,
			updateOnWindowResize: true,
			preloadImages: true,
			updateOnImages: true,
			resistance: true,
			resistanceRatio: 0.85,
			preventClicks: true,
			preventClicksPropagation: true,
			on: {
				init: function() {
					this.update();
					this.updateSlidesClasses();
				},
				imagesReady: function() {
					this.update();
				},
				resize: function() {
					this.update();
				},
				...(options.on || {})
			}
		};

		if (finalOptions.navigation) {
			const nextEl = typeof finalOptions.navigation.nextEl === 'string' 
				? el.closest('.hv-gallery-carousel-section')?.querySelector(finalOptions.navigation.nextEl) 
				: finalOptions.navigation.nextEl;
			const prevEl = typeof finalOptions.navigation.prevEl === 'string' 
				? el.closest('.hv-gallery-carousel-section')?.querySelector(finalOptions.navigation.prevEl) 
				: finalOptions.navigation.prevEl;
			
			if (!nextEl || !prevEl) {
				delete finalOptions.navigation;
			} else {
				finalOptions.navigation.nextEl = nextEl;
				finalOptions.navigation.prevEl = prevEl;
			}
		}

		if (finalOptions.pagination) {
			const paginationEl = typeof finalOptions.pagination.el === 'string'
				? el.querySelector(finalOptions.pagination.el)
				: finalOptions.pagination.el;
			
			if (!paginationEl) {
				delete finalOptions.pagination;
			} else {
				finalOptions.pagination.el = paginationEl;
			}
		}

		try {
			const swiper = new Swiper(selector, finalOptions);
			initializedSwipers.set(el, swiper);
			initAttempts.delete(el);
			return swiper;
		} catch (error) {
			console.error("Error initializing Swiper for", selector, ":", error);
			return null;
		}
	};

	const handleResize = () => {
		if (resizeTimeout) {
			clearTimeout(resizeTimeout);
		}
		resizeTimeout = setTimeout(() => {
			initializedSwipers.forEach((swiper) => {
				if (swiper && typeof swiper.update === 'function') {
					try {
						swiper.update();
					} catch (e) {
						console.warn("Error updating Swiper on resize:", e);
					}
				}
			});
		}, 250);
	};

	const initArtistsNativeLoop = () => {
		// Artists carousel is handled by ArtistsCarouselLoop in hv-main.js.
	};

	const initAllSwipers = () => {
		if (typeof Swiper === "undefined") {
			setTimeout(initAllSwipers, 100);
			return;
		}

		const galleryCarousel = document.querySelector(".hv-gallery-carousel");
		if (galleryCarousel && !initializedSwipers.has(galleryCarousel)) {
			initSwiper(".hv-gallery-carousel", {
				slidesPerView: 1.2,
				centeredSlides: false,
				spaceBetween: 20,
				speed: 600,
				grabCursor: true,
				loop: false,
				navigation: {
					nextEl: ".hv-gallery-carousel__btn--next",
					prevEl: ".hv-gallery-carousel__btn--prev"
				},
				pagination: {
					el: ".swiper-pagination",
					clickable: true
				},
				keyboard: {
					enabled: true,
					onlyInViewport: true
				},
				breakpoints: {
					640: { slidesPerView: 2, spaceBetween: 24 },
					1024: { slidesPerView: 3, spaceBetween: 30 }
				}
			});
		}

		const servicesCarousel = document.querySelector(".hv-services-carousel");
		if (servicesCarousel && !initializedSwipers.has(servicesCarousel)) {
			initSwiper(".hv-services-carousel", {
				loop: false,
				speed: 600,
				grabCursor: true,
				initialSlide: 0,
				centeredSlides: false,
				slidesOffsetBefore: 0,
				slidesOffsetAfter: 0,
				slidesPerView: "auto",
				spaceBetween: 0,
				freeMode: {
					enabled: true,
					sticky: false,
					momentumRatio: 0.8
				},
				on: {
					init: function() {
						this.slideTo(0, 0, false);
						this.update();
					},
					resize: function() {
						this.slideTo(0, 0, false);
						this.update();
					}
				}
			});
		}

		initArtistsNativeLoop();

		const artistProjects = document.querySelector(".hv-artist-projects-carousel");
		if (artistProjects && !initializedSwipers.has(artistProjects)) {
			initSwiper(".hv-artist-projects-carousel", {
				loop: true,
				speed: 600,
				grabCursor: true,
				slidesPerView: 1.15,
				spaceBetween: 20,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false,
					pauseOnMouseEnter: true
				},
				pagination: {
					el: ".hv-artist-projects-carousel .swiper-pagination",
					clickable: true
				},
				breakpoints: {
					640: { slidesPerView: 2.1, spaceBetween: 24 },
					1024: { slidesPerView: 2.8, spaceBetween: 28 }
				}
			});
		}
	};

	const initWhenReady = () => {
		if (document.readyState === "loading") {
			document.addEventListener("DOMContentLoaded", () => {
				setTimeout(initAllSwipers, 100);
			});
		} else {
			setTimeout(initAllSwipers, 100);
		}
	};

	initWhenReady();

	window.addEventListener("load", () => {
		setTimeout(initAllSwipers, 200);
	});

	window.addEventListener("resize", handleResize, { passive: true });

	if (window.MutationObserver) {
		const observer = new MutationObserver((mutations) => {
			let shouldReinit = false;
			mutations.forEach((mutation) => {
				if (mutation.addedNodes.length > 0) {
					mutation.addedNodes.forEach((node) => {
						if (node.nodeType === 1) {
							if (node.classList && node.classList.contains('hv-gallery-carousel') ||
								node.querySelector && node.querySelector('.hv-gallery-carousel')) {
								shouldReinit = true;
							}
						}
					});
				}
			});
			if (shouldReinit) {
				setTimeout(initAllSwipers, 300);
			}
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true
		});
	}

	if (window.addEventListener) {
		document.addEventListener('visibilitychange', () => {
			if (!document.hidden) {
				setTimeout(() => {
					initializedSwipers.forEach((swiper) => {
						if (swiper && typeof swiper.update === 'function') {
							try {
								swiper.update();
							} catch (e) {
								console.warn("Error updating Swiper on visibility change:", e);
							}
						}
					});
				}, 100);
			}
		});
	}
})();
