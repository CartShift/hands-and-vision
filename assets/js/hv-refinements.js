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

		if (!track) return;

		// --------------------------------------------------------
		// 0. SETUP: Clone & Center Logic
		// --------------------------------------------------------
		const items = Array.from(track.children);
		const itemWidth = items[0].offsetWidth; // Approximate initial width
		const gap = 40; // CSS gap

		// 1. Double Clone for smoother infinite loop (Buffer of 2 viewport widths)
		// We clone enough to cover wide screens
		const leftClones = items.map(i => {
			const c = i.cloneNode(true);
			c.classList.add('hv-clone');
			c.setAttribute('aria-hidden', 'true');
			return c;
		});
		const rightClones = items.map(i => {
			const c = i.cloneNode(true);
			c.classList.add('hv-clone');
			c.setAttribute('aria-hidden', 'true');
			return c;
		});

		// Prepend reversed clones to start, Append regular clones to end
		// Note: We duplicate the whole set once on each side
		leftClones.reverse().forEach(c => track.insertBefore(c, track.firstChild));
		rightClones.forEach(c => track.appendChild(c));

		// Re-query all items including clones
		const allItems = Array.from(track.children);

		// Force initial scroll to the "Real" start set
		const realSetStartIndex = items.length; // Length of left buffer

		const centerOnItem = (index, behavior = 'auto') => {
			const target = allItems[index];
			if (!target) return;

			// Center calculation:
			// scrollLeft = targetCenter - viewportCenter
			const trackRect = track.getBoundingClientRect();
			const targetLeft = target.offsetLeft;
			const targetWidth = target.offsetWidth;

			const scrollPos = targetLeft - (trackRect.width / 2) + (targetWidth / 2);

			track.scrollTo({
				left: scrollPos,
				behavior: behavior
			});
		};

		// --------------------------------------------------------
		// 1. INTERACTION ENGINE (Momentum Drag + Snapping)
		// --------------------------------------------------------
		let isDown = false;
		let startX;
		let scrollLeft;
		let velocity = 0;
		let lastX = 0;
		let lastTime = 0;
		let animationId;
		let isDragging = false;

		const startDrag = (e) => {
			isDown = true;
			isDragging = false;
			track.classList.add('active');
			startX = (e.pageX || e.touches[0].pageX) - track.offsetLeft;
			scrollLeft = track.scrollLeft;

			// Reset momentum
			velocity = 0;
			lastX = e.pageX || e.touches[0].pageX;
			lastTime = Date.now();
			cancelAnimationFrame(animationId);
		};

		const endDrag = () => {
			if (!isDown) return;
			isDown = false;
			track.classList.remove('active');

			// Apply Momentum if velocity is significant
			if (Math.abs(velocity) > 0.5) {
				applyMomentum();
			} else {
				snapToNearest();
			}

			setTimeout(() => { isDragging = false; }, 50);
		};

		const moveDrag = (e) => {
			if (!isDown) return;
			e.preventDefault();
			isDragging = true;

			const x = (e.pageX || e.touches[0].pageX);
			const walk = (x - track.offsetLeft - startX) * 1.5; // 1.5x speed
			track.scrollLeft = scrollLeft - walk;

			// Calculate velocity
			const now = Date.now();
			const dt = now - lastTime;
			if (dt > 0) {
				velocity = (x - lastX) / dt;
				lastX = x;
				lastTime = now;
			}
		};

		const applyMomentum = () => {
			// Decay velocity
			velocity *= 0.95; // Friction
			track.scrollLeft -= velocity * 15; // Move based on velocity

			if (Math.abs(velocity) > 0.1) {
				animationId = requestAnimationFrame(applyMomentum);
			} else {
				snapToNearest();
			}
		};

		const snapToNearest = () => {
			// Find item closest to center
			const center = track.scrollLeft + (track.offsetWidth / 2);
			let minDist = Infinity;
			let closestIndex = -1;

			// Optimization: Search only visible items roughly
			// We can iterate all for robustness on modern devices
			for (let i = 0; i < allItems.length; i++) {
				const item = allItems[i];
				const itemCenter = item.offsetLeft + (item.offsetWidth / 2);
				const dist = Math.abs(center - itemCenter);
				if (dist < minDist) {
					minDist = dist;
					closestIndex = i;
				}
			}

			if (closestIndex !== -1) {
				centerOnItem(closestIndex, 'smooth');
			}
		};

		// Event Bindings
		track.addEventListener('mousedown', startDrag);
		track.addEventListener('touchstart', startDrag, { passive: true });

		track.addEventListener('mouseleave', endDrag);
		track.addEventListener('mouseup', endDrag);
		track.addEventListener('touchend', endDrag);

		track.addEventListener('mousemove', moveDrag);
		track.addEventListener('touchmove', moveDrag, { passive: false });

		// Prevent link clicks during drag
		track.querySelectorAll('a').forEach(link => {
			link.addEventListener('click', (e) => {
				if (isDragging) e.preventDefault();
			});
		});

		// --------------------------------------------------------
		// 2. VISUAL LOOP & ACTIVE STATE
		// --------------------------------------------------------
		const updateActiveState = () => {
			const center = track.scrollLeft + (track.offsetWidth / 2);

			allItems.forEach(item => {
				const itemCenter = item.offsetLeft + (item.offsetWidth / 2);
				const dist = Math.abs(center - itemCenter);
				const maxDist = track.offsetWidth / 2;

				// "Is Center" Class Logic (strict)
				if (dist < (item.offsetWidth / 2)) {
					if (!item.classList.contains('is-center')) {
						allItems.forEach(i => i.classList.remove('is-center'));
						item.classList.add('is-center');
					}
				}

				// Parallax Effect
				const img = item.querySelector('img');
				if (img && dist < maxDist) {
					// 0 at center, -10/10 at edges
					const move = (dist / maxDist) * 15 * (itemCenter > center ? -1 : 1);
					img.style.transform = `scale(1.1) translateX(${move}%)`;
				}
			});

			// Infinite Loop Jump Logic
			const totalWidth = track.scrollWidth;
			// If we are in the left buffer zone
			if (track.scrollLeft < (track.offsetWidth / 4)) {
				// Jump forward to real set
				// Calculate relative position
				const setWidth = totalWidth / 3;
				track.scrollLeft += setWidth;
			}
			// If we are in the right buffer zone
			else if (track.scrollLeft > (totalWidth - (track.offsetWidth / 2))) {
				const setWidth = totalWidth / 3;
				track.scrollLeft -= setWidth;
			}
		};

		track.addEventListener('scroll', () => {
			requestAnimationFrame(updateActiveState);
		});

		// --------------------------------------------------------
		// 3. INITIALIZATION
		// --------------------------------------------------------
		// Wait for layout repaint then center
		setTimeout(() => {
			centerOnItem(realSetStartIndex, 'auto');
			updateActiveState();
		}, 100);

		// Button Controls
		if (prevBtn) prevBtn.addEventListener('click', () => {
			// Find current centered item index
			const current = allItems.findIndex(i => i.classList.contains('is-center'));
			if (current > 0) centerOnItem(current - 1, 'smooth');
		});

		if (nextBtn) nextBtn.addEventListener('click', () => {
			const current = allItems.findIndex(i => i.classList.contains('is-center'));
			if (current < allItems.length - 1) centerOnItem(current + 1, 'smooth');
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
