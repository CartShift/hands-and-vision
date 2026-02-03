/**
 * HAND AND VISION - Main JavaScript
 * Lightbox, animations, and smooth scrolling
 * Version: 2.0.0
 * Accessibility: WCAG 2.1 AA Compliant
 *
 * @package HandAndVision
 */

(function () {
	"use strict";

	/**
	 * Accessibility Utilities
	 */
	const A11yUtils = {
		/**
		 * Trap focus within an element (for modals, lightboxes)
		 */
		trapFocus: function (element) {
			const focusableElements = element.querySelectorAll('a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select');
			if (!focusableElements.length) return;
			const firstFocusable = focusableElements[0];
			const lastFocusable = focusableElements[focusableElements.length - 1];
			element.addEventListener("keydown", function (e) {
				if (e.key !== "Tab") return;
				if (e.shiftKey) {
					if (document.activeElement === firstFocusable) {
						lastFocusable.focus();
						e.preventDefault();
					}
				} else {
					if (document.activeElement === lastFocusable) {
						firstFocusable.focus();
						e.preventDefault();
					}
				}
			});
		},

		/**
		 * Announce message to screen readers
		 */
		announce: function (message, priority) {
			priority = priority || "polite";
			const announcement = document.createElement("div");
			announcement.setAttribute("role", "status");
			announcement.setAttribute("aria-live", priority);
			announcement.setAttribute("aria-atomic", "true");
			announcement.className = "hv-sr-only";
			announcement.style.cssText = "position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden;";
			announcement.textContent = message;
			document.body.appendChild(announcement);

			setTimeout(function () {
				document.body.removeChild(announcement);
			}, 1000);
		},

		/**
		 * Check if user prefers reduced motion
		 */
		prefersReducedMotion: function () {
			return window.matchMedia("(prefers-reduced-motion: reduce)").matches;
		}
	};

	/**
	 * Lightbox functionality - Enhanced for Accessibility
	 */
	const Lightbox = {
		overlay: null,
		image: null,
		caption: null,
		currentIndex: 0,
		images: [],
		previousActiveElement: null,

		init: function () {
			this.createOverlay();
			this.bindEvents();
		},

		createOverlay: function () {
			const overlay = document.createElement("div");
			overlay.className = "hv-lightbox-overlay";
			overlay.setAttribute("role", "dialog");
			overlay.setAttribute("aria-modal", "true");
			overlay.setAttribute("aria-label", typeof hv_i18n !== "undefined" && hv_i18n.lightbox_label ? hv_i18n.lightbox_label : "Image lightbox");
			overlay.setAttribute("aria-describedby", "hv-lightbox-caption");
			overlay.innerHTML = `
                <button class="hv-lightbox-close" aria-label="Close lightbox">&times;</button>
                <button class="hv-lightbox-nav hv-lightbox-prev" aria-label="Previous image">&lsaquo;</button>
                <button class="hv-lightbox-nav hv-lightbox-next" aria-label="Next image">&rsaquo;</button>
                <div class="hv-lightbox-content">
                    <img class="hv-lightbox-image" src="" alt="">
                    <p class="hv-lightbox-caption" id="hv-lightbox-caption" role="status" aria-live="polite"></p>
                </div>
            `;
			document.body.appendChild(overlay);

			this.overlay = overlay;
			this.image = overlay.querySelector(".hv-lightbox-image");
			this.caption = overlay.querySelector(".hv-lightbox-caption");

			// Enable focus trapping
			A11yUtils.trapFocus(overlay);
		},

		bindEvents: function () {
			const self = this;
			document.querySelectorAll(".hv-lightbox").forEach(function (link) {
				const href = (link.getAttribute("href") || "").trim();
				if (!href || href === "#") {
					link.addEventListener("click", function (e) {
						e.preventDefault();
					});
					return;
				}
				const idx = self.images.length;
				self.images.push({ src: link.href, caption: link.dataset.caption || "" });
				link.addEventListener("click", function (e) {
					e.preventDefault();
					self.open(idx);
				});
			});

			// Close button
			this.overlay.querySelector(".hv-lightbox-close").addEventListener("click", function () {
				self.close();
			});

			// Navigation
			this.overlay.querySelector(".hv-lightbox-prev").addEventListener("click", function () {
				self.prev();
			});

			this.overlay.querySelector(".hv-lightbox-next").addEventListener("click", function () {
				self.next();
			});

			// Close on overlay click
			this.overlay.addEventListener("click", function (e) {
				if (e.target === self.overlay) {
					self.close();
				}
			});

			document.addEventListener("keydown", function (e) {
				if (!self.overlay.classList.contains("active")) return;
				switch (e.key) {
					case "Escape":
						self.close();
						e.preventDefault();
						break;
					case "ArrowLeft":
						self.prev();
						e.preventDefault();
						break;
					case "ArrowRight":
						self.next();
						e.preventDefault();
						break;
				}
			});
		},

		open: function (index) {
			if (!this.images.length || index < 0 || index >= this.images.length) return;

			this.previousActiveElement = document.activeElement;
			this.currentIndex = index;

			const self = this;
			const openLogic = () => {
				self.updateImage();
				self.overlay.classList.add("active");
				document.body.style.overflow = "hidden";
				const closeBtn = self.overlay.querySelector(".hv-lightbox-close");
				if (closeBtn) closeBtn.focus();
				A11yUtils.announce("Image lightbox opened. Use arrow keys to navigate, Escape to close.");
			};

			// View Transition Support
			if (document.startViewTransition) {
				// 1. Find the source thumbnail that triggered this
				// Use a heuristic or data attribute if we stored it,
				// or find an image with matching src in the document
				const currentSrc = this.images[index].src;
				const thumb = Array.from(document.querySelectorAll('a[href="' + currentSrc + '"] img, img[src="' + currentSrc + '"]')).pop();

				if (thumb) {
					// Set temporary names
					thumb.style.viewTransitionName = "lightbox-img";
					this.image.style.viewTransitionName = "lightbox-img";

					const transition = document.startViewTransition(() => {
						openLogic();
						// Remove name from thumb during transition so it doesn't stay
						thumb.style.viewTransitionName = "";
					});

					transition.finished.finally(() => {
						// Cleanup lightbox name after transition
						this.image.style.viewTransitionName = "";
					});
				} else {
					openLogic();
				}
			} else {
				openLogic();
			}
		},

		close: function () {
			const self = this;
			const closeLogic = () => {
				self.overlay.classList.remove("active");
				document.body.style.overflow = "";
				if (self.previousActiveElement) {
					self.previousActiveElement.focus();
				}
				A11yUtils.announce("Lightbox closed");
			};

			if (document.startViewTransition && this.overlay.classList.contains("active")) {
				// Find target thumbnail again
				const currentSrc = this.image.src;
				// Need to strip host if absolute vs relative issues occur, but usually exact match works for lightbox links
				// We look for a link pointing to this image, and find its inner img
				const thumbLink = document.querySelector('a[href="' + currentSrc + '"]');
				const thumb = thumbLink ? thumbLink.querySelector("img") : null;

				if (thumb) {
					// Apply names for the closing transition
					thumb.style.viewTransitionName = "lightbox-img";
					this.image.style.viewTransitionName = "lightbox-img";

					const transition = document.startViewTransition(() => {
						closeLogic();
						// In the new state (closed), the lightbox image is gone (or hidden),
						// and the thumb is visible.
						// The API handles the morph from 'lightbox-img' (overlay) to 'lightbox-img' (thumb).
					});

					transition.finished.finally(() => {
						thumb.style.viewTransitionName = "";
						this.image.style.viewTransitionName = "";
					});
				} else {
					closeLogic();
				}
			} else {
				closeLogic();
			}
		},

		prev: function () {
			if (!this.images.length) return;
			this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
			this.updateImage();
		},

		next: function () {
			if (!this.images.length) return;
			this.currentIndex = (this.currentIndex + 1) % this.images.length;
			this.updateImage();
		},

		updateImage: function () {
			// Enhanced error handling
			if (!this.images.length) {
				console.error("Lightbox: No images loaded");
				A11yUtils.announce("Error: No images available", "assertive");
				this.close();
				return;
			}

			const current = this.images[this.currentIndex];
			if (!current) {
				console.error("Lightbox: Invalid image index", this.currentIndex);
				A11yUtils.announce("Error loading image", "assertive");
				return;
			}

			if (!this.image) {
				console.error("Lightbox: Image element not found in DOM");
				return;
			}

			this.image.src = current.src;
			this.image.alt = current.caption || "";
			if (this.caption) {
				this.caption.textContent = current.caption || "";
				this.caption.style.display = current.caption ? "block" : "none";
			}
		}
	};

	/**
	 * Scroll Animations using Intersection Observer
	 * Respects prefers-reduced-motion for accessibility
	 */
	const ScrollAnimations = {
		init: function () {
			document.documentElement.classList.add("hv-js-ready");
			if (A11yUtils.prefersReducedMotion()) {
				// Immediately show all animated elements without animation
				document.querySelectorAll(".hv-animate, .hv-reveal, .hv-stagger, .hv-line-draw").forEach(function (el) {
					el.classList.add("hv-animate--visible", "revealed");
					el.style.opacity = "1";
					el.style.transform = "none";
				});
				return;
			}

			// Original animate class
			const elements = document.querySelectorAll(".hv-animate");

			if (elements.length) {
				const observer = new IntersectionObserver(
					function (entries) {
						entries.forEach(function (entry) {
							if (entry.isIntersecting) {
								entry.target.classList.add("hv-animate--visible");
								observer.unobserve(entry.target);
							}
						});
					},
					{
						threshold: 0.1,
						rootMargin: "0px 0px -50px 0px"
					}
				);

				elements.forEach(function (el) {
					observer.observe(el);
				});
			}

			// Premium reveal animations
			const revealElements = document.querySelectorAll(".hv-reveal, .hv-stagger, .hv-line-draw");

			if (revealElements.length) {
				const revealObserver = new IntersectionObserver(
					function (entries) {
						entries.forEach(function (entry) {
							if (entry.isIntersecting) {
								entry.target.classList.add("revealed");
								revealObserver.unobserve(entry.target);
							}
						});
					},
					{
						threshold: 0.15,
						rootMargin: (function () {
							const h = getComputedStyle(document.documentElement).getPropertyValue("--hv-header-height").trim() || "80px";
							return "0px 0px -" + h + " 0px";
						})()
					}
				);

				revealElements.forEach(function (el) {
					revealObserver.observe(el);
				});
			}
		}
	};

	/**
	 * Smooth Scroll for anchor links
	 */
	const SmoothScroll = {
		init: function () {
			document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
				anchor.addEventListener("click", function (e) {
					const targetId = this.getAttribute("href");
					if (targetId === "#") return;

					const target = document.querySelector(targetId);
					if (target) {
						e.preventDefault();
						target.scrollIntoView({
							behavior: "smooth",
							block: "start"
						});
					}
				});
			});
		}
	};

	/**
	 * Mobile Menu Toggle - Enhanced with Focus Trap
	 */
	const MobileMenu = {
		header: null,
		toggle: null,
		nav: null,
		firstFocusable: null,
		lastFocusable: null,
		previouslyFocused: null,

		init: function () {
			this.header = document.querySelector(".hv-header");
			this.toggle = document.getElementById("hv-menu-toggle");
			this.nav = document.getElementById("hv-navigation");

			if (!this.toggle || !this.header) return;

			// Ensure proper ARIA attributes on init
			this.toggle.setAttribute("aria-expanded", "false");
			this.toggle.setAttribute("aria-controls", "hv-navigation");
			if (this.nav) {
				this.nav.setAttribute("role", "navigation");
				this.nav.setAttribute("aria-label", "Main navigation");
			}

			this.bindEvents();
		},

		bindEvents: function () {
			const self = this;

			// Toggle menu with keyboard support
			this.toggle.addEventListener("click", function (e) {
				e.preventDefault();
				e.stopPropagation();
				self.toggleMenu();
			});

			// Keyboard support for toggle button
			this.toggle.addEventListener("keydown", function (e) {
				if (e.key === "Enter" || e.key === " ") {
					e.preventDefault();
					self.toggleMenu();
				}
			});

			// Close menu when clicking a link
			if (this.nav) {
				const links = this.nav.querySelectorAll("a");
				links.forEach(function (link) {
					link.addEventListener("click", function () {
						self.close();
					});
				});
			}

			// Close on escape key - use capture for reliability
			document.addEventListener("keydown", function (e) {
				if (e.key === "Escape" && self.header.classList.contains("menu-open")) {
					self.close();
					self.toggle.focus();
				}
			});

			// Close on click outside
			document.addEventListener("click", function (e) {
				if (self.header.classList.contains("menu-open")) {
					if (!self.nav.contains(e.target) && !self.toggle.contains(e.target)) {
						self.close();
					}
				}
			});
		},

		toggleMenu: function () {
			const isOpen = this.header.classList.toggle("menu-open");
			document.body.classList.toggle("menu-open");

			// Update ARIA attributes
			this.toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");

			if (isOpen) {
				// Store previously focused element
				this.previouslyFocused = document.activeElement;
				// Setup focus trap
				this.setupFocusTrap();
				// Focus first nav item
				if (this.firstFocusable) {
					setTimeout(() => this.firstFocusable.focus(), 100);
				}
			} else {
				this.removeFocusTrap();
				// Return focus to toggle button
				if (this.previouslyFocused) {
					this.previouslyFocused.focus();
				}
			}

			// Prevent body scroll when menu is open
			document.body.style.overflow = isOpen ? "hidden" : "";

			// Announce state change
			A11yUtils.announce(isOpen ? "Menu opened. Use Tab to navigate, Escape to close." : "Menu closed");
		},

		setupFocusTrap: function () {
			if (!this.nav) return;

			const focusableElements = this.nav.querySelectorAll(
				'a[href], button:not([disabled]), textarea, input[type="text"], input[type="email"], input[type="tel"], input[type="radio"], input[type="checkbox"], select'
			);

			if (focusableElements.length) {
				this.firstFocusable = focusableElements[0];
				this.lastFocusable = focusableElements[focusableElements.length - 1];
			}

			// Bind tab key trapping
			this.handleTabKey = this.handleTabKey.bind(this);
			this.nav.addEventListener("keydown", this.handleTabKey);
		},

		removeFocusTrap: function () {
			if (!this.nav || !this.handleTabKey) return;
			this.nav.removeEventListener("keydown", this.handleTabKey);
			this.firstFocusable = null;
			this.lastFocusable = null;
		},

		handleTabKey: function (e) {
			if (e.key !== "Tab") return;

			if (!this.firstFocusable || !this.lastFocusable) return;

			if (e.shiftKey) {
				// Shift + Tab
				if (document.activeElement === this.firstFocusable) {
					e.preventDefault();
					this.lastFocusable.focus();
				}
			} else {
				// Tab
				if (document.activeElement === this.lastFocusable) {
					e.preventDefault();
					this.firstFocusable.focus();
				}
			}
		},

		close: function () {
			if (!this.header.classList.contains("menu-open")) return;

			this.header.classList.remove("menu-open");
			document.body.classList.remove("menu-open");
			document.body.style.overflow = "";
			if (this.toggle) this.toggle.setAttribute("aria-expanded", "false");
			this.removeFocusTrap();

			// Return focus to toggle button
			if (this.toggle) {
				this.toggle.focus();
			}
		}
	};

	/**
	 * Sticky Header with scroll effect
	 */
	const StickyHeader = {
		header: null,
		lastScrollY: 0,

		init: function () {
			this.header = document.querySelector(".hv-header");
			if (!this.header) return;

			this.bindEvents();
			this.checkScroll();
		},

		bindEvents: function () {
			const self = this;
			let ticking = false;

			window.addEventListener("scroll", function () {
				if (!ticking) {
					window.requestAnimationFrame(function () {
						self.checkScroll();
						ticking = false;
					});
					ticking = true;
				}
			});
		},

		checkScroll: function () {
			const scrollY = window.scrollY;

			if (scrollY > 50) {
				this.header.classList.add("scrolled");
				document.body.classList.add("scrolled");
				const scrollTopBtn = document.getElementById("ast-scroll-top");
				if (scrollTopBtn) scrollTopBtn.classList.add("show");
			} else {
				this.header.classList.remove("scrolled");
				document.body.classList.remove("scrolled");
				const scrollTopBtn = document.getElementById("ast-scroll-top");
				if (scrollTopBtn) scrollTopBtn.classList.remove("show");
			}

			this.lastScrollY = scrollY;
		}
	};

	/**
	 * Contact Form Submission
	 */
	const ContactForm = {
		form: null,
		feedback: null,
		submitBtn: null,

		init: function () {
			this.form = document.getElementById("hv-contact-form-ajax");
			if (!this.form) return;

			this.feedback = this.form.querySelector(".hv-form-feedback");
			this.submitBtn = this.form.querySelector(".hv-btn--submit");

			this.bindEvents();
		},

		bindEvents: function () {
			const self = this;

			this.form.addEventListener("submit", function (e) {
				e.preventDefault();
				self.handleSubmit();
			});
		},

		handleSubmit: function () {
			const self = this;
			const formData = new FormData(this.form);

			// UI States
			this.setLoading(true);
			this.hideFeedback();

			fetch(hv_ajax.ajaxurl, {
				method: "POST",
				body: formData
			})
				.then(response => {
					if (!response.ok) {
						throw new Error(`HTTP error! status: ${response.status}`);
					}
					return response.json();
				})
				.then(data => {
					self.setLoading(false);

					if (data.success) {
						self.showFeedback(data.data.message, "success");
						self.form.reset();
						A11yUtils.announce("Form submitted successfully. " + data.data.message, "assertive");
					} else {
						const errorMsg = data.data.message || "Error occurred. Please try again.";
						self.showFeedback(errorMsg, "error");
						A11yUtils.announce("Form error: " + errorMsg, "assertive");
						console.error("Contact form error:", data);
					}
				})
				.catch(error => {
					self.setLoading(false);
					const errorMsg = "Connection error. Please check your internet connection and try again.";
					self.showFeedback(errorMsg, "error");
					A11yUtils.announce(errorMsg, "assertive");
					console.error("Contact form fetch error:", error);
				});
		},

		setLoading: function (isLoading) {
			if (!this.submitBtn) return;

			const btnText = this.submitBtn.querySelector(".hv-btn-text");
			const btnLoader = this.submitBtn.querySelector(".hv-btn-loader");

			if (isLoading) {
				this.submitBtn.disabled = true;
				if (btnText) btnText.classList.add("hv-hidden");
				if (btnLoader) btnLoader.classList.remove("hv-hidden");
			} else {
				this.submitBtn.disabled = false;
				if (btnText) btnText.classList.remove("hv-hidden");
				if (btnLoader) btnLoader.classList.add("hv-hidden");
			}
		},

		showFeedback: function (message, type) {
			if (!this.feedback) return;

			this.feedback.textContent = message;
			this.feedback.classList.remove("hv-hidden");
			this.feedback.classList.remove("hv-form-feedback--success", "hv-form-feedback--error");
			this.feedback.classList.add(type === "success" ? "hv-form-feedback--success" : "hv-form-feedback--error");

			this.feedback.scrollIntoView({ behavior: "smooth", block: "nearest" });
		},

		hideFeedback: function () {
			if (this.feedback) {
				this.feedback.classList.add("hv-hidden");
			}
		}
	};

	/**
	 * Drag to Scroll for Artists Showcase - Optimized with passive listeners
	 */
	const DragScroll = {
		init: function () {
			const containers = document.querySelectorAll(".hv-artists-showcase, .hv-services-carousel");
			const prefersReducedMotion = A11yUtils.prefersReducedMotion();

			containers.forEach(function (container) {
				let isDown = false;
				let startX;
				let scrollLeft;
				let rafId = null;

				// Mouse drag scrolling
				container.addEventListener("mousedown", function (e) {
					isDown = true;
					container.classList.add("dragging");
					startX = e.pageX - container.offsetLeft;
					scrollLeft = container.scrollLeft;
				});

				container.addEventListener("mouseleave", function () {
					isDown = false;
					container.classList.remove("dragging");
				});

				container.addEventListener("mouseup", function () {
					isDown = false;
					container.classList.remove("dragging");
				});

				// Use passive listener for touch events (performance)
				container.addEventListener(
					"touchstart",
					function (e) {
						// Allow native scrolling but track for enhanced behavior
					},
					{ passive: true }
				);

				container.addEventListener("mousemove", function (e) {
					if (!isDown) return;
					e.preventDefault();

					// Cancel any pending animation frame
					if (rafId) cancelAnimationFrame(rafId);

					rafId = requestAnimationFrame(function () {
						const x = e.pageX - container.offsetLeft;
						const walk = (x - startX) * 2; // Scroll speed multiplier
						container.scrollLeft = scrollLeft - walk;
					});
				});

				// Accessibility: Make draggable areas keyboard accessible
				container.setAttribute("role", "region");
				container.setAttribute("aria-label", "Scrollable content. Use arrow keys to navigate.");
				container.setAttribute("tabindex", "0");

				// Keyboard navigation
				container.addEventListener("keydown", function (e) {
					const scrollAmount = 200;
					const behavior = prefersReducedMotion ? "auto" : "smooth";

					switch (e.key) {
						case "ArrowLeft":
							e.preventDefault();
							container.scrollBy({ left: -scrollAmount, behavior: behavior });
							break;
						case "ArrowRight":
							e.preventDefault();
							container.scrollBy({ left: scrollAmount, behavior: behavior });
							break;
						case "Home":
							e.preventDefault();
							container.scrollTo({ left: 0, behavior: behavior });
							break;
						case "End":
							e.preventDefault();
							container.scrollTo({ left: container.scrollWidth, behavior: behavior });
							break;
					}
				});
			});

			// Navigation arrows
			const prevBtn = document.querySelector(".hv-carousel-prev");
			const nextBtn = document.querySelector(".hv-carousel-next");
			const showcase = document.querySelector(".hv-artists-showcase");

			if (prevBtn && nextBtn && showcase) {
				const scrollAmount = 200; // Pixels to scroll per click
				const behavior = prefersReducedMotion ? "auto" : "smooth";

				prevBtn.addEventListener("click", function () {
					showcase.scrollBy({
						left: -scrollAmount,
						behavior: behavior
					});
				});

				nextBtn.addEventListener("click", function () {
					showcase.scrollBy({
						left: scrollAmount,
						behavior: behavior
					});
				});
			}
		}
	};

	/**
	 * Hero Scroll Indicator (homepage + service archive/single)
	 */
	const HeroScroll = {
		init: function () {
			const videoScroll = document.querySelector(".hv-hero-video__scroll");
			if (videoScroll) {
				videoScroll.addEventListener("click", function () {
					const heroSection = document.querySelector(".hv-hero-video");
					if (heroSection && heroSection.nextElementSibling) {
						heroSection.nextElementSibling.scrollIntoView({ behavior: "smooth", block: "start" });
					} else {
						window.scrollBy({ top: window.innerHeight, behavior: "smooth" });
					}
				});
			}
			document.querySelectorAll(".hv-hero-scroll").forEach(function (el) {
				el.addEventListener("click", function (e) {
					const href = el.getAttribute("href");
					if (href && href.indexOf("#") === 0) {
						const target = document.querySelector(href);
						if (target) {
							e.preventDefault();
							target.scrollIntoView({ behavior: "smooth", block: "start" });
						}
					}
				});
			});
		}
	};

	/**
	 * Header Cart - Animation on add to cart (Vanilla JS - No jQuery Dependency)
	 */
	/**
	 * AJAX Shop Filters with View Transitions
	 */
	const AjaxFilters = {
		container: null,
		isAnimating: false,

		init: function () {
			// Find the main product container
			this.container = document.querySelector(".hv-shop-grid, .hv-product-grid-premium");
			if (!this.container) return;

			this.bindEvents();
		},

		bindEvents: function () {
			const self = this;

			// Delegate clicks for category links
			document.addEventListener("click", function (e) {
				const link = e.target.closest(".hv-shop-filters__categories a, .hv-pagination a, .woocommerce-pagination a");
				if (link && self.container.contains(document.querySelector(link.getAttribute("href")) || self.container)) {
					// Check if it's a link to the same shop page (query params)
					const url = new URL(link.href);
					if (url.origin === window.location.origin && url.pathname === window.location.pathname) {
						e.preventDefault();
						self.handleFilter(link.href);
					}
				}
			});

			// Handle select change
			const select = document.querySelector(".hv-artist-filter-form select, .hv-shop-filters select");
			if (select) {
				select.addEventListener("change", function (e) {
					e.preventDefault();

					// Construct URL from form data or value
					const form = select.closest("form");
					const formData = new FormData(form);
					const params = new URLSearchParams(formData);
					// Add filter_artist explicitly if needed
					if (!params.has('filter_artist') && select.name === 'filter_artist') {
						params.set('filter_artist', select.value);
					}

					const url = new URL(window.location.href);
					url.search = params.toString();

					self.handleFilter(url.toString());
				});
			}
		},

		handleFilter: function (url) {
			if (this.isAnimating) return;
			this.isAnimating = true;

			const self = this;

			// Add loading state
			this.container.classList.add("hv-loading");

			fetch(url)
				.then(response => response.text())
				.then(html => {
					const parser = new DOMParser();
					const newDoc = parser.parseFromString(html, "text/html");
					const newContainer = newDoc.querySelector(".hv-shop-grid, .hv-product-grid-premium");
					const newFilters = newDoc.querySelector(".hv-shop-filters-wrap");

					if (!newContainer) {
						window.location.href = url; // Fallback if parsing fails
						return;
					}

					// View Transition Logic
					if (document.startViewTransition) {
						document.startViewTransition(() => {
							self.swapContent(newContainer, newFilters, newDoc);
							// Update history
							window.history.pushState({}, "", url);
						});
					} else {
						// Fallback
						self.swapContent(newContainer, newFilters, newDoc);
						window.history.pushState({}, "", url);
					}
				})
				.catch(err => {
					console.error("Filter error:", err);
					window.location.href = url;
				})
				.finally(() => {
					this.container.classList.remove("hv-loading");
					this.isAnimating = false;

					// Re-init reveals
					if (window.ScrollAnimations) ScrollAnimations.init();
				});
		},

		swapContent: function (newContainer, newFilters, newDoc) {
			// Swap Grid
			if (this.container && newContainer) {
				this.container.innerHTML = newContainer.innerHTML;
				// Update pagination if exists
				const paginationObj = document.querySelector(".hv-pagination, .woocommerce-pagination");
				const newPagination = newDoc.querySelector(".hv-pagination, .woocommerce-pagination");
				if (paginationObj && newPagination) {
					paginationObj.outerHTML = newPagination.outerHTML;
				} else if (paginationObj && !newPagination) {
					paginationObj.remove();
				}
			}

			// Swap Filters (to update active states)
			const currentFilters = document.querySelector(".hv-shop-filters-wrap");
			if (currentFilters && newFilters) {
				currentFilters.outerHTML = newFilters.outerHTML;
				// Re-bind events for new filters
				this.bindEvents();
			}
		}
	};

	/**
	 * Header Cart - Animation on add to cart (Vanilla JS - No jQuery Dependency)
	 */
	const HeaderCart = {
		cartIcon: null,
		pendingAnimate: false,
		flyImage: null,

		init: function () {
			this.cartIcon = document.getElementById("hv-header-cart");
			this.bindEvents();
		},

		bindEvents: function () {
			const self = this;

			// Listen for standard WooCommerce add to cart click
			document.body.addEventListener("click", function (e) {
				if (e.target.classList.contains("ajax_add_to_cart")) {
					const button = e.target;
					const productCard = button.closest(".hv-product-card, .hv-product-card-minimal");
					if (productCard) {
						const img = productCard.querySelector("img");
						if (img) self.startFlyAnimation(img);
					}
				}
			});

			document.body.addEventListener("added_to_cart", function () {
				self.pendingAnimate = true;
				A11yUtils.announce("Item added to cart");
			});

			const refreshCallback = function () {
				self.cartIcon = document.getElementById("hv-header-cart");
				const cartCount = document.getElementById("hv-cart-count");
				if (cartCount) cartCount.classList.add("has-items");
				if (self.pendingAnimate && self.cartIcon) {
					self.pendingAnimate = false;
					self.animateCart();
				}
			};

			document.body.addEventListener("wc_fragments_refreshed", refreshCallback);
			document.body.addEventListener("wc_cart_fragments_refreshed", refreshCallback);
		},

		startFlyAnimation: function (sourceImg) {
			if (!this.cartIcon) return;

			// Ensure source image is actually visible in viewport
			const rect = sourceImg.getBoundingClientRect();
			if (rect.width === 0 || rect.height === 0) return;

			// Clone image
			const flyImg = sourceImg.cloneNode(true);

			// Style clone
			flyImg.setAttribute("aria-hidden", "true");
			flyImg.style.position = "fixed";
			flyImg.style.left = rect.left + "px";
			flyImg.style.top = rect.top + "px";
			flyImg.style.width = rect.width + "px";
			flyImg.style.height = rect.height + "px";
			flyImg.style.objectFit = "cover";
			flyImg.style.zIndex = "100000"; /* High Z-Index to beat sticky header */
			flyImg.style.borderRadius = "4px";
			flyImg.style.pointerEvents = "none";
			flyImg.style.transition = "all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1)";
			flyImg.style.opacity = "0.8";

			document.body.appendChild(flyImg);

			// Trigger animation
			requestAnimationFrame(() => {
				flyImg.style.left = (cartRect.left + cartRect.width / 2 - 20) + "px";
				flyImg.style.top = (cartRect.top + cartRect.height / 2 - 20) + "px";
				flyImg.style.width = "40px";
				flyImg.style.height = "40px";
				flyImg.style.opacity = "0";
				flyImg.style.borderRadius = "50%";
			});

			// Cleanup
			setTimeout(() => {
				flyImg.remove();
			}, 800);
		},

		animateCart: function () {
			const el = this.cartIcon || document.getElementById("hv-header-cart");
			if (!el) return;
			el.classList.remove("added");
			void el.offsetWidth;
			el.classList.add("added");
			setTimeout(() => el.classList.remove("added"), 500);
		}
	};

	/**
	 * Artist Bio Expander
	 */
	const ArtistInfo = {
		init: function() {
			document.querySelectorAll('.hv-artist-card-premium__expander').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation(); // prevent navigation
					const wrapper = this.closest('.hv-artist-card-premium__excerpt-wrap');
					const fullBio = wrapper.querySelector('.hv-artist-card-premium__full-bio');
					const isExpanded = this.getAttribute('aria-expanded') === 'true';
					const isHebrew = document.documentElement.lang === 'he-IL' || document.dir === 'rtl';

					if (isExpanded) {
						fullBio.hidden = true;
						this.setAttribute('aria-expanded', 'false');
						this.innerHTML = '<span class="hv-expander-text">' + (isHebrew ? 'קרא עוד' : 'Read More') + '</span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>';
					} else {
						fullBio.hidden = false;
						this.setAttribute('aria-expanded', 'true');
						this.innerHTML = '<span class="hv-expander-text">' + (isHebrew ? 'סגור' : 'Close') + '</span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 15l7-7 7 7"/></svg>';
					}
				});
			});
		}
	};

	/* ==========================================
	   QUICK VIEW FUNCTIONALITY
	   ========================================== */
	const QuickView = {
		init: function() {
			document.body.addEventListener('click', function(e) {
				if (e.target.closest('.hv-quick-view-btn')) {
					e.preventDefault();
					const btn = e.target.closest('.hv-quick-view-btn');
					const productId = btn.dataset.productId;
					QuickView.open(productId, btn);
				}
			});
		},

		open: function(productId, triggerBtn) {
			// Create modal structure if not exists
			if (!document.querySelector('.hv-quick-view-modal')) {
				const modal = document.createElement('div');
				modal.className = 'hv-quick-view-modal';
				modal.innerHTML = `
					<div class="hv-quick-view-modal__overlay"></div>
					<div class="hv-quick-view-modal__container">
						<button class="hv-quick-view-close" aria-label="Close">&times;</button>
						<div class="hv-quick-view-loader"></div>
						<div class="hv-quick-view-content-wrap"></div>
					</div>
				`;
				document.body.appendChild(modal);

				// Close events
				modal.querySelector('.hv-quick-view-close').addEventListener('click', this.close);
				modal.querySelector('.hv-quick-view-modal__overlay').addEventListener('click', this.close);
				document.addEventListener('keydown', (e) => {
					if (e.key === 'Escape' && document.body.classList.contains('hv-quick-view-open')) {
						this.close();
					}
				});
			}

			const modal = document.querySelector('.hv-quick-view-modal');
			const content = modal.querySelector('.hv-quick-view-content-wrap');
			const loader = modal.querySelector('.hv-quick-view-loader');

			// Reset
			content.innerHTML = '';
			loader.style.display = 'block';
			modal.classList.add('active');
			document.body.classList.add('hv-quick-view-open');

			// Fetch Data
			const data = new FormData();
			data.append('action', 'hv_quick_view');
			data.append('product_id', productId);
			// data.append('nonce', hv_ajax.nonce); // If implemented

			fetch(hv_ajax.ajaxurl, {
				method: 'POST',
				body: data
			})
			.then(response => response.json())
			.then(response => {
				loader.style.display = 'none';
				if (response.success) {
					content.innerHTML = response.data.html;
					// Re-init variations forms if needed (requires WC scripts)
					if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
						jQuery( '.variations_form' ).each( function() {
							jQuery( this ).wc_variation_form();
						});
					}
				} else {
					content.innerHTML = '<p class="hv-text-error">Error loading product.</p>';
				}
			})
			.catch(err => {
				loader.style.display = 'none';
				console.error(err);
				content.innerHTML = '<p class="hv-text-error">Connection error.</p>';
			});
		},

		close: function() {
			const modal = document.querySelector('.hv-quick-view-modal');
			if (modal) {
				modal.classList.remove('active');
				document.body.classList.remove('hv-quick-view-open');
				setTimeout(() => {
					modal.querySelector('.hv-quick-view-content-wrap').innerHTML = '';
				}, 300);
			}
		}
	};

	/**
	 * Initialize on DOM ready
	 */
	document.addEventListener("DOMContentLoaded", function () {
		Lightbox.init();
		ScrollAnimations.init();
		SmoothScroll.init();
		MobileMenu.init();
		StickyHeader.init();
		ContactForm.init();
		DragScroll.init();
		HeroScroll.init();
		HeaderCart.init();
		AjaxFilters.init();
		ArtistInfo.init();
		QuickView.init();
	});
})();
