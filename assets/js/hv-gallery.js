/**
 * Hand & Vision - Gallery Module
 * Modern, accessible gallery with filtering and lightbox
 * @version 2.0.0
 */

(function () {
	"use strict";

	/**
	 * Gallery Configuration
	 */
	const CONFIG = {
		selectors: {
			filterContainer: ".hv-gallery-filters",
			filterButton: ".hv-gallery-filters__btn",
			grid: ".hv-gallery-bento__grid",
			item: ".hv-gallery-bento__item",
			loadingIndicator: ".hv-gallery__loading",
			emptyState: ".hv-gallery__empty",
			counter: ".hv-gallery__counter"
		},
		classes: {
			active: "is-active",
			hidden: "hv-gallery-bento__item--hidden",
			revealed: "revealed",
			loading: "is-loading",
			noResults: "no-results"
		},
		animation: {
			duration: 400,
			stagger: 50
		}
	};

	/**
	 * Gallery Class
	 * Manages filtering, animations, and interactions
	 */
	class Gallery {
		constructor() {
			this.container = document.querySelector(".hv-gallery-page");
			if (!this.container) return;

			this.grid = this.container.querySelector(CONFIG.selectors.grid);
			this.filterButtons = this.container.querySelectorAll(CONFIG.selectors.filterButton);
			this.items = this.container.querySelectorAll(CONFIG.selectors.item);
			this.loadingIndicator = this.container.querySelector(CONFIG.selectors.loadingIndicator);
			this.emptyState = this.container.querySelector(CONFIG.selectors.emptyState);
			this.counter = this.container.querySelector(CONFIG.selectors.counter);

			this.currentFilter = "0";
			this.isAnimating = false;
			this.observer = null;

			this.init();
		}

		init() {
			this.bindEvents();
			this.initIntersectionObserver();
			this.updateCounter();
			this.preloadImages();
		}

		/**
		 * Bind event listeners
		 */
		bindEvents() {
			// Filter button clicks
			this.filterButtons.forEach(btn => {
				btn.addEventListener("click", e => this.handleFilterClick(e));
				btn.addEventListener("keydown", e => this.handleFilterKeydown(e));
			});

			// Gallery item hover effects
			this.items.forEach(item => {
				item.addEventListener("mouseenter", () => this.handleItemHover(item, true));
				item.addEventListener("mouseleave", () => this.handleItemHover(item, false));
			});
		}

		/**
		 * Handle filter button click
		 */
		handleFilterClick(e) {
			const btn = e.currentTarget;
			const filterId = btn.dataset.artistId;

			if (filterId === this.currentFilter || this.isAnimating) return;

			this.setActiveFilter(btn, filterId);
			this.filterItems(filterId);
		}

		/**
		 * Handle keyboard navigation for filters
		 */
		handleFilterKeydown(e) {
			const buttons = Array.from(this.filterButtons);
			const currentIndex = buttons.indexOf(e.currentTarget);

			let nextIndex;

			switch (e.key) {
				case "ArrowRight":
					e.preventDefault();
					nextIndex = (currentIndex + 1) % buttons.length;
					buttons[nextIndex].focus();
					break;
				case "ArrowLeft":
					e.preventDefault();
					nextIndex = (currentIndex - 1 + buttons.length) % buttons.length;
					buttons[nextIndex].focus();
					break;
				case "Enter":
				case " ":
					e.preventDefault();
					this.handleFilterClick({ currentTarget: e.currentTarget });
					break;
			}
		}

		/**
		 * Set active filter button
		 */
		setActiveFilter(activeBtn, filterId) {
			// Update button states
			this.filterButtons.forEach(btn => {
				btn.classList.remove(CONFIG.classes.active);
				btn.setAttribute("aria-selected", "false");
				btn.setAttribute("tabindex", "-1");
			});

			activeBtn.classList.add(CONFIG.classes.active);
			activeBtn.setAttribute("aria-selected", "true");
			activeBtn.setAttribute("tabindex", "0");

			this.currentFilter = filterId;
		}

		/**
		 * Filter gallery items with smooth animation
		 */
		filterItems(filterId) {
			this.isAnimating = true;
			this.showLoading();

			const visibleItems = [];
			const hiddenItems = [];

			this.items.forEach(item => {
				const itemArtist = item.dataset.artistId;
				const shouldShow = filterId === "0" || itemArtist === filterId;

				if (shouldShow) {
					visibleItems.push(item);
				} else {
					hiddenItems.push(item);
				}
			});

			// Animate out hidden items
			this.animateItems(hiddenItems, false, () => {
				// Update visibility
				hiddenItems.forEach(item => item.classList.add(CONFIG.classes.hidden));
				visibleItems.forEach(item => item.classList.remove(CONFIG.classes.hidden));

				// Animate in visible items
				this.animateItems(visibleItems, true, () => {
					this.isAnimating = false;
					this.hideLoading();
					this.updateCounter(visibleItems.length);
					this.updateEmptyState(visibleItems.length);
				});
			});
		}

		/**
		 * Animate items in/out
		 */
		animateItems(items, show, callback) {
			if (items.length === 0) {
				if (callback) callback();
				return;
			}

			items.forEach((item, index) => {
				const delay = index * CONFIG.animation.stagger;

				setTimeout(() => {
					if (show) {
						item.style.opacity = "0";
						item.style.transform = "translateY(20px) scale(0.95)";
						item.style.display = "block";

						requestAnimationFrame(() => {
							item.style.transition = `all ${CONFIG.animation.duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
							item.style.opacity = "1";
							item.style.transform = "translateY(0) scale(1)";
						});
					} else {
						item.style.transition = `all ${CONFIG.animation.duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
						item.style.opacity = "0";
						item.style.transform = "translateY(20px) scale(0.95)";
					}

					if (index === items.length - 1 && callback) {
						setTimeout(callback, CONFIG.animation.duration);
					}
				}, delay);
			});
		}

		/**
		 * Show loading indicator
		 */
		showLoading() {
			if (this.loadingIndicator) {
				this.loadingIndicator.classList.add(CONFIG.classes.loading);
			}
			this.grid.classList.add(CONFIG.classes.loading);
		}

		/**
		 * Hide loading indicator
		 */
		hideLoading() {
			if (this.loadingIndicator) {
				this.loadingIndicator.classList.remove(CONFIG.classes.loading);
			}
			this.grid.classList.remove(CONFIG.classes.loading);
		}

		/**
		 * Update item counter
		 */
		updateCounter(count) {
			if (!this.counter) return;

			const visibleCount = count !== undefined ? count : this.items.length;
			const totalCount = this.items.length;

			this.counter.textContent = `${visibleCount} / ${totalCount}`;
			this.counter.setAttribute("aria-live", "polite");
		}

		/**
		 * Update empty state visibility
		 */
		updateEmptyState(visibleCount) {
			if (!this.emptyState) return;

			if (visibleCount === 0) {
				this.emptyState.classList.remove(CONFIG.classes.hidden);
				this.grid.classList.add(CONFIG.classes.noResults);
			} else {
				this.emptyState.classList.add(CONFIG.classes.hidden);
				this.grid.classList.remove(CONFIG.classes.noResults);
			}
		}

		/**
		 * Handle item hover
		 */
		handleItemHover(item, isHovering) {
			// Add subtle parallax effect on hover
			const img = item.querySelector(".hv-gallery-bento__img");
			if (!img) return;

			if (isHovering) {
				img.style.transform = "scale(1.08)";
			} else {
				img.style.transform = "";
			}
		}

		/**
		 * Initialize intersection observer for reveal animations
		 */
		initIntersectionObserver() {
			if (!("IntersectionObserver" in window)) {
				// Fallback: show all items
				this.items.forEach(item => item.classList.add(CONFIG.classes.revealed));
				return;
			}

			this.observer = new IntersectionObserver(
				entries => {
					entries.forEach(entry => {
						if (entry.isIntersecting) {
							entry.target.classList.add(CONFIG.classes.revealed);
							this.observer.unobserve(entry.target);
						}
					});
				},
				{
					threshold: 0.1,
					rootMargin: "0px 0px -50px 0px"
				}
			);

			this.items.forEach(item => {
				this.observer.observe(item);
			});
		}

		/**
		 * Preload images for better UX
		 */
		preloadImages() {
			const images = this.container.querySelectorAll("img[data-src]");

			// Load all images immediately - don't wait for intersection
			// This ensures images are visible without JavaScript dependency
			images.forEach(img => this.loadImage(img));
		}

		/**
		 * Load a single image
		 */
		loadImage(img) {
			const src = img.dataset.src;
			if (!src) return;

			// Only swap if data-src is different from current src
			if (img.src === src) {
				img.classList.add("loaded");
				return;
			}

			const tempImg = new Image();
			tempImg.onload = () => {
				img.src = src;
				img.classList.add("loaded");
			};
			tempImg.onerror = () => {
				img.classList.add("error");
				console.warn("Failed to load image:", src);
			};
			tempImg.src = src;
		}
	}

	/**
	 * Gallery Search/Filter Enhancement
	 */
	class GallerySearch {
		constructor() {
			this.container = document.querySelector(".hv-gallery-page");
			if (!this.container) return;

			this.searchInput = this.container.querySelector(".hv-gallery-search__input");
			if (!this.searchInput) return;

			this.items = this.container.querySelectorAll(CONFIG.selectors.item);
			this.debounceTimer = null;

			this.init();
		}

		init() {
			this.searchInput.addEventListener("input", e => this.handleInput(e));
			this.searchInput.addEventListener("keydown", e => {
				if (e.key === "Escape") {
					this.clear();
				}
			});
		}

		handleInput(e) {
			const query = e.target.value.toLowerCase().trim();

			clearTimeout(this.debounceTimer);
			this.debounceTimer = setTimeout(() => {
				this.filterBySearch(query);
			}, 300);
		}

		filterBySearch(query) {
			this.items.forEach(item => {
				const title = item.querySelector(".hv-gallery-bento__title")?.textContent.toLowerCase() || "";
				const meta = item.querySelector(".hv-gallery-bento__meta")?.textContent.toLowerCase() || "";
				const artist = item.dataset.artistName?.toLowerCase() || "";

				const matches = title.includes(query) || meta.includes(query) || artist.includes(query);

				if (matches) {
					item.classList.remove(CONFIG.classes.hidden);
				} else {
					item.classList.add(CONFIG.classes.hidden);
				}
			});
		}

		clear() {
			this.searchInput.value = "";
			this.items.forEach(item => item.classList.remove(CONFIG.classes.hidden));
			this.searchInput.blur();
		}
	}

	/**
	 * Initialize Gallery on DOM Ready
	 */
	function initGallery() {
		// Check if gallery exists on this page
		if (!document.querySelector(".hv-gallery-page")) return;

		// Initialize modules
		new Gallery();
		new GallerySearch();
	}

	// Initialize when DOM is ready
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initGallery);
	} else {
		initGallery();
	}

	// Re-initialize after AJAX content loads (if needed)
	document.addEventListener("gallery:updated", initGallery);
})();
