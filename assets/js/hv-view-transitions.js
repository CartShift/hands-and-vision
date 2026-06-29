/**
 * Hands and Vision — View Transitions (MPA + SPA helpers)
 *
 * Cross-document MPA transitions are enabled globally via CSS
 * (`@view-transition { navigation: auto; }`). This script:
 *
 *  1. Assigns matching `view-transition-name`s to shared elements
 *     on the source (card) just before navigation so the destination
 *     hero can morph into place.
 *  2. Prefetches likely targets on hover/focus for snappier nav.
 *  3. Is fully data-driven via `data-vt-*` attributes so any link or
 *     element can opt in without code changes.
 *
 * Data-attribute API (preferred for new code):
 *   <a data-vt-name="my-name" data-vt-target=".selector">…</a>
 *   - data-vt-name:   the view-transition-name to apply
 *   - data-vt-target: optional selector inside the link for the media
 *                     element (defaults to the link itself)
 *
 * Built-in card mappings remain for backward compatibility.
 */
(function () {
	'use strict';

	if (typeof document === 'undefined' || !('startViewTransition' in document)) {
		return;
	}

	const REDUCED_MOTION = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const CARD_MAP = [
		{
			linkSel: '.hv-artist-card__link, .hv-artist-card-premium__link',
			idAttr: 'artistId',
			name: (id) => `artist-portrait-${id}`,
			mediaSel: '.hv-artist-card__portrait img, .hv-artist-card-premium__media img, .hv-artist-card__portrait, .hv-artist-card-premium__media',
		},
		{
			linkSel: '.hv-product-card__link, .hv-product-card-minimal__link',
			idAttr: 'productId',
			name: (id) => `product-image-${id}`,
			mediaSel: '.hv-product-card__image, .hv-product-card-minimal__image, .hv-product-card__image img, .hv-product-card-minimal__image img',
		},
		{
			linkSel: '.hv-service-showcase-card__link, .hv-service-card__link',
			idAttr: 'serviceId',
			name: (id) => `service-image-${id}`,
			mediaSel: '.hv-service-showcase-card__media img, .hv-service-showcase-card__media, .hv-service-card__media img, .hv-service-card__media',
		},
		{
			linkSel: '.hv-post-card__link, .hv-blog-card__link',
			idAttr: 'postId',
			name: (id) => `post-image-${id}`,
			mediaSel: '.hv-post-card__image img, .hv-blog-card__image img, .hv-post-card__image, .hv-blog-card__image',
		},
	];

	function findMapping(link) {
		for (const m of CARD_MAP) {
			if (link.matches(m.linkSel)) return m;
		}
		return null;
	}

	function resolveTransition(link) {
		const explicitName = link.dataset.vtName;
		if (explicitName) {
			const target = link.dataset.vtTarget
				? link.querySelector(link.dataset.vtTarget)
				: link;
			return { name: explicitName, target };
		}

		const mapping = findMapping(link);
		if (!mapping) return null;

		const id = link.dataset[mapping.idAttr];
		if (!id) return null;

		const target = link.querySelector(mapping.mediaSel) || link;
		return { name: mapping.name(id), target };
	}

	function applyName(node, name) {
		if (!node || !name) return;
		node.style.viewTransitionName = name;
		node.classList.add('hv-shared-media');
	}

	document.addEventListener(
		'click',
		(e) => {
			if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) {
				return;
			}
			const link = e.target.closest('a[href]');
			if (!link || !link.href || link.target === '_blank') return;

			const resolved = resolveTransition(link);
			if (!resolved) return;

			applyName(resolved.target, resolved.name);
		},
		true
	);

	const prefetched = new Set();
	const PREFETCH_SELECTOR = CARD_MAP.map((m) => m.linkSel).join(', ') + ', a[data-vt-name]';

	function maybePrefetch(link) {
		if (!link || !link.href) return;
		if (!link.href.startsWith(window.location.origin)) return;
		if (prefetched.has(link.href)) return;
		if (!link.matches(PREFETCH_SELECTOR)) return;

		const conn = navigator.connection;
		if (conn && (conn.saveData || /(^|-)2g$/.test(conn.effectiveType || ''))) return;

		const el = document.createElement('link');
		el.rel = 'prefetch';
		el.href = link.href;
		el.as = 'document';
		document.head.appendChild(el);
		prefetched.add(link.href);
	}

	if (!REDUCED_MOTION) {
		document.addEventListener(
			'mouseenter',
			(e) => {
				const link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
				if (link) maybePrefetch(link);
			},
			true
		);
		document.addEventListener(
			'focusin',
			(e) => {
				const link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
				if (link) maybePrefetch(link);
			},
			true
		);
	}
})();
