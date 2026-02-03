/**
 * HAND AND VISION - UI Refinements JavaScript
 * Micro-interactions and scroll-reveal enhancements
 *
 * @package HandAndVision
 * @version 2.0.0
 */

(function () {
	"use strict";

	/**
	 * ========================================
	 * SCROLL REVEAL ANIMATIONS
	 * ========================================
	 */

	const revealElements = () => {
		document.documentElement.classList.add("hv-js-ready");
		const elements = document.querySelectorAll(".hv-reveal, .hv-animate, .hv-stagger");

		if (!elements.length) return;

		const observer = new IntersectionObserver(
			entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add("revealed", "hv-animate--visible");
						observer.unobserve(entry.target);
					}
				});
			},
			{
				threshold: 0.1,
				rootMargin: "-20px 0px -50px 0px"
			}
		);

		elements.forEach(el => observer.observe(el));
	};

	/**
	 * ========================================
	 * SMOOTH MAGNETIC BUTTONS
	 * ========================================
	 */

	const initMagneticButtons = () => {
		const buttons = document.querySelectorAll(".hv-btn--primary-gold, .hv-btn--primary");

		buttons.forEach(button => {
			button.addEventListener("mousemove", e => {
				const rect = button.getBoundingClientRect();
				const x = e.clientX - rect.left - rect.width / 2;
				const y = e.clientY - rect.top - rect.height / 2;

				const strength = 0.15;
				button.style.transform = `translate(${x * strength}px, ${y * strength}px)`;
			});

			button.addEventListener("mouseleave", () => {
				button.style.transform = "";
			});
		});
	};

	/**
	 * ========================================
	 * ENHANCED IMAGE LAZY LOADING
	 * ========================================
	 */

	const initImageLoading = () => {
		const images = document.querySelectorAll(".hv-gallery-mosaic__item img, .hv-artist-card__portrait img, .hv-service-card__media img");

		images.forEach(img => {
			if (img.complete) {
				img.classList.add("loaded");
				return;
			}

			img.classList.add("hv-img-loading");

			img.addEventListener("load", () => {
				img.classList.remove("hv-img-loading");
				img.classList.add("loaded");
				img.style.opacity = "1";
			});

			img.addEventListener("error", () => {
				img.classList.remove("hv-img-loading");
				img.classList.add("error");
			});
		});
	};

	/**
	 * ========================================
	 * HEADER SCROLL BEHAVIOR
	 * ========================================
	 */

	const initHeaderScroll = () => {
		const header = document.querySelector(".hv-header");
		if (!header) return;

		let lastScrollY = 0;
		let ticking = false;

		const updateHeader = () => {
			const scrollY = window.scrollY;

			// Add scrolled class when past threshold
			if (scrollY > 50) {
				header.classList.add("scrolled");
			} else {
				header.classList.remove("scrolled");
			}

			// Hide/show header on scroll direction
			if (scrollY > 200) {
				if (scrollY > lastScrollY && scrollY - lastScrollY > 5) {
					header.classList.add("header-hidden");
				} else if (scrollY < lastScrollY && lastScrollY - scrollY > 5) {
					header.classList.remove("header-hidden");
				}
			} else {
				header.classList.remove("header-hidden");
			}

			lastScrollY = scrollY;
			ticking = false;
		};

		window.addEventListener(
			"scroll",
			() => {
				if (!ticking) {
					requestAnimationFrame(updateHeader);
					ticking = true;
				}
			},
			{ passive: true }
		);
	};

	/**
	 * ========================================
	 * CURSOR EFFECTS
	 * ========================================
	 */

	const initCursorEffects = () => {
		// Add cursor class to interactive elements on hover
		const interactiveElements = document.querySelectorAll(".hv-btn, .hv-gallery-mosaic__item, .hv-artist-card, .hv-service-card, .hv-nav-menu a");

		interactiveElements.forEach(el => {
			el.addEventListener("mouseenter", () => {
				document.body.classList.add("cursor-hover");
			});

			el.addEventListener("mouseleave", () => {
				document.body.classList.remove("cursor-hover");
			});
		});
	};

	/**
	 * ========================================
	 * PARALLAX EFFECTS
	 * ========================================
	 */

	const initParallax = () => {
		const heroContent = document.querySelector(".hv-hero-video__content");
		if (!heroContent) return;

		let ticking = false;

		const updateParallax = () => {
			const scrollY = window.scrollY;
			const heroHeight = window.innerHeight;

			if (scrollY < heroHeight) {
				const progress = scrollY / heroHeight;
				const translateY = scrollY * 0.3;
				const opacity = 1 - progress * 0.8;

				heroContent.style.transform = `translateY(${translateY}px)`;
				heroContent.style.opacity = opacity;
			}

			ticking = false;
		};

		window.addEventListener(
			"scroll",
			() => {
				if (!ticking) {
					requestAnimationFrame(updateParallax);
					ticking = true;
				}
			},
			{ passive: true }
		);
	};

	/**
	 * ========================================
	 * FORM ENHANCEMENTS
	 * ========================================
	 */

	const initFormEnhancements = () => {
		const formInputs = document.querySelectorAll(".hv-contact-form input, .hv-contact-form textarea, .hv-contact-form select");

		formInputs.forEach(input => {
			// Add focus class to parent
			input.addEventListener("focus", () => {
				input.closest(".hv-form-group")?.classList.add("focused");
			});

			input.addEventListener("blur", () => {
				input.closest(".hv-form-group")?.classList.remove("focused");
			});

			// Add filled class when input has value
			input.addEventListener("input", () => {
				if (input.value.trim()) {
					input.classList.add("filled");
				} else {
					input.classList.remove("filled");
				}
			});
		});
	};

	/**
	 * ========================================
	 * RIPPLE EFFECT FOR BUTTONS
	 * ========================================
	 */

	const initRippleEffect = () => {
		const buttons = document.querySelectorAll(".hv-btn");

		buttons.forEach(button => {
			button.addEventListener("click", function (e) {
				const rect = this.getBoundingClientRect();
				const x = e.clientX - rect.left;
				const y = e.clientY - rect.top;

				const ripple = document.createElement("span");
				ripple.className = "hv-ripple";
				ripple.style.left = `${x}px`;
				ripple.style.top = `${y}px`;

				this.appendChild(ripple);

				setTimeout(() => {
					ripple.remove();
				}, 600);
			});
		});
	};

	/**
	 * ========================================
	 * SMOOTH SCROLL ENHANCEMENT
	 * ========================================
	 */

	const initSmoothScroll = () => {
		const links = document.querySelectorAll('a[href^="#"]:not([href="#"])');

		links.forEach(link => {
			link.addEventListener("click", e => {
				const targetId = link.getAttribute("href").slice(1);
				const target = document.getElementById(targetId);

				if (target) {
					e.preventDefault();
					const headerHeight = document.querySelector(".hv-header")?.offsetHeight || 80;
					const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight;

					window.scrollTo({
						top: targetPosition,
						behavior: "smooth"
					});
				}
			});
		});
	};

	/**
	 * ========================================
	 * GALLERY HOVER TILTS
	 * ========================================
	 */

	const initGalleryTilt = () => {
		const items = document.querySelectorAll(".hv-gallery-mosaic__item");

		items.forEach(item => {
			item.addEventListener("mousemove", e => {
				const rect = item.getBoundingClientRect();
				const x = (e.clientX - rect.left) / rect.width;
				const y = (e.clientY - rect.top) / rect.height;

				const tiltX = (y - 0.5) * 5;
				const tiltY = (x - 0.5) * -5;

				item.style.transform = `perspective(500px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale(1.02)`;
			});

			item.addEventListener("mouseleave", () => {
				item.style.transform = "";
			});
		});
	};

	/**
	 * ========================================
	 * GALLERY CAROUSEL
	 * ========================================
	 */
	const initGalleryCarousel = () => {
		const track = document.querySelector(".hv-gallery-carousel__track");
		const prevBtn = document.querySelector(".hv-gallery-carousel__btn--prev");
		const nextBtn = document.querySelector(".hv-gallery-carousel__btn--next");

		if (!track || !prevBtn || !nextBtn) return;

		const scrollAmount = () => track.offsetWidth * 0.8;

		prevBtn.addEventListener("click", () => {
			track.scrollBy({
				left: -scrollAmount(),
				behavior: "smooth"
			});
		});

		nextBtn.addEventListener("click", () => {
			track.scrollBy({
				left: scrollAmount(),
				behavior: "smooth"
			});
		});
	};

	/**
	 * ========================================
	 * INITIALIZATION
	 * ========================================
	 */

	const init = () => {
		// Run immediately for above-the-fold content
		initHeaderScroll();

		// Run after DOM is loaded
		if (document.readyState === "loading") {
			document.addEventListener("DOMContentLoaded", onDOMReady);
		} else {
			onDOMReady();
		}

		// Run after page is fully loaded
		window.addEventListener("load", onPageLoad);
	};

	const onDOMReady = () => {
		revealElements();
		initImageLoading();
		initFormEnhancements();
		initSmoothScroll();
		initGalleryCarousel();
	};

	const onPageLoad = () => {
		initMagneticButtons();
		initCursorEffects();
		initParallax();
		initRippleEffect();
		initGalleryTilt();
	};

	// Start initialization
	init();

	// Add CSS for ripple effect
	const style = document.createElement("style");
	style.textContent = `
        .hv-ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: hv-ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes hv-ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .header-hidden {
            transform: translateY(-100%);
            transition: transform 0.3s ease-out;
        }

        .hv-header {
            transition: transform 0.3s ease-out, background 0.3s ease-out;
        }

        .hv-animate {
            transform: translateY(20px);
        }

        .hv-animate--visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
	document.head.appendChild(style);
})();
