/**
 * HAND AND VISION - Drag to Scroll
 * Drag scrolling for carousels (artists, services)
 * Version: 1.0.0
 *
 * @package HandAndVision
 */

(function () {
	"use strict";

	/**
	 * Enable drag scrolling for a container
	 */
	function enableDragScroll(container) {
		if (!container) return;

		let isDown = false;
		let startX;
		let scrollLeft;

		container.addEventListener("mousedown", function (e) {
			isDown = true;
			container.style.cursor = "grabbing";
			container.style.scrollSnapType = "none";
			startX = e.pageX - container.offsetLeft;
			scrollLeft = container.scrollLeft;
			e.preventDefault();
		});

		container.addEventListener("mouseleave", function () {
			isDown = false;
			container.style.cursor = "grab";
		});

		container.addEventListener("mouseup", function () {
			isDown = false;
			container.style.cursor = "grab";
			container.style.scrollSnapType = "x mandatory";
		});

		document.addEventListener("mouseup", function () {
			if (isDown) {
				isDown = false;
				container.style.cursor = "grab";
				container.style.scrollSnapType = "x mandatory";
			}
		});

		container.addEventListener("mousemove", function (e) {
			if (!isDown) return;
			e.preventDefault();
			const x = e.pageX - container.offsetLeft;
			const walk = (x - startX) * 1.5;
			container.scrollLeft = scrollLeft - walk;
		});
	}

	// Initialize on DOM ready
	document.addEventListener("DOMContentLoaded", function () {
		const artistsShowcase = document.querySelector(".hv-artists-showcase");
		enableDragScroll(artistsShowcase);

		const servicesCarousel = document.querySelector(".hv-services-carousel");
		enableDragScroll(servicesCarousel);
	});
})();
