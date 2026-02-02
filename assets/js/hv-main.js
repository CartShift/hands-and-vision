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
			this.updateImage();
			this.overlay.classList.add("active");
			document.body.style.overflow = "hidden";
			const closeBtn = this.overlay.querySelector(".hv-lightbox-close");
			if (closeBtn) closeBtn.focus();
			A11yUtils.announce("Image lightbox opened. Use arrow keys to navigate, Escape to close.");
		},

		close: function () {
			this.overlay.classList.remove("active");
			document.body.style.overflow = "";

			// Return focus to the element that opened the lightbox
			if (this.previousActiveElement) {
				this.previousActiveElement.focus();
			}

			A11yUtils.announce("Lightbox closed");
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
						rootMargin: "0px 0px -80px 0px"
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
			const containers = document.querySelectorAll(".hv-artists-showcase");
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
	const HeaderCart = {
		cartIcon: null,

		init: function () {
			this.cartIcon = document.getElementById("hv-header-cart");
			if (!this.cartIcon) return;

			this.bindEvents();
		},

		bindEvents: function () {
			const self = this;

			// Native JavaScript event delegation for WooCommerce add to cart
			// This replaces jQuery(document.body).on("added_to_cart")
			document.body.addEventListener("added_to_cart", function (event) {
				self.animateCart();
				A11yUtils.announce("Item added to cart");
			});

			// Listen for fragment refresh (cart update)
			document.body.addEventListener("wc_fragments_refreshed", function (event) {
				// Re-select the cart count in case it was replaced
				const cartCount = document.getElementById("hv-cart-count");
				if (cartCount) {
					cartCount.classList.add("has-items");
				}
			});

			// Fallback: Listen for wc_cart_fragments_refreshed (alternative WooCommerce event)
			document.body.addEventListener("wc_cart_fragments_refreshed", function (event) {
				const cartCount = document.getElementById("hv-cart-count");
				if (cartCount) {
					cartCount.classList.add("has-items");
				}
			});
		},

		animateCart: function () {
			if (!this.cartIcon) {
				console.error("Header cart icon not found - cannot animate cart");
				return;
			}

			// Remove class if already applied
			this.cartIcon.classList.remove("added");

			// Trigger reflow to restart animation
			void this.cartIcon.offsetWidth;

			// Add animation class
			this.cartIcon.classList.add("added");

			// Remove class after animation completes
			setTimeout(() => {
				this.cartIcon.classList.remove("added");
			}, 500);
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
	});
})();
