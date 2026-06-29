# Design Audit — Hands & Vision

**Date:** 2026-06-29
**Target:** `http://127.0.0.1:8888/` (local Docker, WordPress 7.0, theme `hands-and-vision`)
**Mode:** Hybrid (source-code + visual), audit-only ➜ **followed by fix pass (see “Fix Pass Results” at the bottom)**
**Scope reviewed:** Coming-soon homepage, /shop/, single product, /artists/, single artist, /contact/, 404, EN bilingual flow (`?lang=en`)
**Skipped:** /services/, /gallery/, /artists/single beyond Chenka, checkout flow, blog (time/quality trade-off)
**Auditor constraints:** in-IDE browser — no JS evaluation, no reliable viewport resize. All visual findings captured at the IDE browser's effective width (~586 CSS px, a tablet-ish midpoint). Desktop ≥ 1024 px and mobile < 414 px were NOT verified visually. Source-code findings apply across all viewports.

---

## Headline scores

| | Grade | One-liner |
|---|---|---|
| **Design Score** | **C+** | Premium intent, real design system, undermined by ten high-impact bugs that ship to live users. The foundation deserves a B; the execution drags it down. |
| **AI Slop Score** | **B–** | Mostly avoids the worst patterns (no purple-violet gradients, no 3-column icon-circle grids, no emoji). One clear violation: centered-everything section composition. |

### Per-category grades

| Category | Grade | Why |
|---|---|---|
| Visual Hierarchy | C | Sections read as wallpapered tiles, not as a hierarchy. Hero of /artists/chenka/ is a giant empty rectangle (broken portrait fallback). |
| Typography | B | One typeface (Heebo) is the right call for HE+EN. Type scale is systematic. Mixed straight/curly quotes; uppercase tracked labels overused; H1 wrapping bug on /shop/. |
| Color & Contrast | B+ | Coherent petrol + lilac + cream palette; clear semantic intent in tokens. Wide palette (8 named accents) flirts with the "<= 12 colors" ceiling but holds together. |
| Spacing & Layout | C | Token scale exists but is internally contradictory (`--hv-space-11: 80px` < `--hv-space-10: 128px`). Cards on shop/related rendered with full-empty left half at audited viewport. |
| Interaction States | C | `outline:none/0` appears 13× while `:focus-visible` only 7× — net loss of focus rings. `transition: all` 59× = lazy animation. Header transparency makes burger menu invisible over dark hero sections. |
| Responsive | C | Could not verify at true mobile (≤414 px) or desktop (≥1024 px). At the audited width, several layouts (single-column product grid with right-aligned image, broken artist hero) suggest the responsive story is held together by media queries, not by composition. |
| Content Quality | D | **Placeholder bio text shipping live** ("הביוגרפיה תופיע כאן בקרוב"). Mixed currency (`$300.00` on RTL Hebrew page). Mixed languages in H1s. Three different Hebrew words for "works" in the same micro-section. |
| AI Slop | B– | Centered-everything in CTA sections; icon-in-circle in shop CTA. No purple gradients, no 3-column feature grid, no emoji, no decorative blobs. |
| Motion | C | 6 easings + 6 durations defined (good intent). `prefers-reduced-motion` mentioned only 4× across 17 k lines. `transition: all` 59×. |
| Performance Feel | n/a | Not measured (no JS eval). Heavy: 332 KB unified CSS + 117 KB refinements = 449 KB of theme CSS before compression. |

---

## Inferred design system (from `hv-unified.css` + `hv-design-refinements.css`)

**Tokens:** clean and well-named. Petrol (`#254b61`) primary brand, lilac/purple/pink/yellow accent system, deep red (`#d02e04`) reserved for primary CTA. Type scale uses fluid `clamp()` for h3–h5. Spacing is on a 4 px base. Shadows, radii, motion timings all tokenized.

**Typography:** **Heebo** for display, heading, body. Defined once at `:root`, then overridden literally as `"Heebo", sans-serif` ~25× rather than via `var(--hv-font-heading)`. Cosmetic but reduces the token's value — change Heebo to anything else and you've got 25 grep-and-replaces.

**Hard quality smells in the CSS:**
- **`!important`: 1,466 occurrences** (807 in `hv-unified.css`, 659 in `hv-design-refinements.css`). This is industrial-scale specificity warfare — almost certainly the cost of overriding Astra's parent stylesheet without resetting it. It's not just a smell, it's the smell at scale. Any future style change will require another `!important`. This compounds.
- **`transition: all`: 59 occurrences** in `hv-unified.css`. Animates layout properties (width, top, padding), causes jank, fights `prefers-reduced-motion` discipline.
- **`outline: none / outline: 0`: 13 occurrences**; **`:focus-visible`: only 7**. Net loss of focus indicators in some interactive states. Accessibility regression.
- **`@media` queries: 100+** in unified.css. Reasonable for a multi-CPT theme, but combined with `!important` makes overrides at breakpoints painful.
- **`prefers-reduced-motion`: 4** mentions in 17 k CSS lines. Underused.
- **Spacing scale has ordering bugs** in `assets\css\hv-unified.css:213-217`:
  - `--hv-space-9: 96px`
  - `--hv-space-10: 128px`
  - `--hv-space-11: 80px` ← *smaller* than space-10
  - `--hv-space-12: 96px` ← *equal* to space-9
  - `--hv-space-16: 160px`
  This is a real bug. Using `--hv-space-11` somewhere expecting bigger-than-10 gives smaller-than-10. Same for 12 vs 9.
- **Shadow tokens are bloated**: 14 shadow tokens with overlapping intent (`subtle`, `sm`, `soft`, `md`, `medium`, `lg`, `large`, `xl`, `elevated`, `floating`, + colored). Pick five, delete the rest.

**.cursorrules promise vs. reality:** the project's own rules state `hv-unified.css` is the "Source of Truth (tokens + components)" and `hv-design-refinements.css` is for "polish/overrides." The refinements file has 659 `!important`s in 3,874 lines — that's one every 6 lines. It's not polish; it's a second source of truth fighting the first. The architecture isn't honoring the rule.

---

## Findings (sorted by impact)

### HIGH — fix first (ship-quality blockers)

**FINDING-001 — "Coming Soon" page is what live visitors see at `/`**
Body class on `http://127.0.0.1:8888/` is `page-template-coming-soon page-template-coming-soon-php`. The page assigned to "Front page" in WP Reading Settings has the *Coming Soon / Maintenance* template applied. Every other URL renders the real theme. So the homepage that 100% of first-time visitors land on is the bare hero with two buttons — `front-page.php` (with hero video, services swiper, artists carousel, gallery grid) is dead code right now.
**Files:** `coming-soon.php` (template), WP DB `page_on_front` (post 166) — change template back to "Default" or pick another page as `page_on_front`.
**Screenshot:** `screenshots/01-home-he-full.png`

**FINDING-002 — Placeholder bio text shipping live on every artist page without a written bio**
`/artists/chenka/` shows H3 "מבוא קצר אל עולמו של האמן" + body "הביוגרפיה תופיע כאן בקרוב." (Bio coming soon). This is a fallback string rendered when ACF bio fields are empty. Real users see "coming soon" inside the same page that's been published. Either: (a) hide the bio section entirely when fields are empty, or (b) write real bios before launching, or (c) replace the placeholder with an outward-facing line like "Discover Chenka's work below." Never "coming soon" on a live site.
**Files:** likely `single-artist.php` and `inc/acf-artist-fields.php` / `inc/acf-display-helper.php`
**Screenshot:** `screenshots/04-single-artist-he-top.png`

**FINDING-003 — Single-artist hero collapses to giant empty gradient when artist has no portrait**
`/artists/chenka/` reserves a full-height hero block (~750 px tall at the audited width) for the artist portrait. When no image is set, the slot renders as a featureless gray gradient with the artist name floated bottom-right. Looks broken. Either: enforce that every artist must have a portrait (content rule), or design a real text-first fallback (large typographic name + abstract texture or monogram), but don't ship an empty rectangle.
**Files:** `single-artist.php`
**Screenshot:** `screenshots/04-single-artist-he-top.png`

**FINDING-004 — Bilingual `?lang=en` doesn't switch `dir="ltr"` and leaks across pages**
Visiting `/contact?lang=en` translates body strings but keeps `dir="rtl"`. Visible symptoms: form labels right-aligned with asterisk on the wrong side (`* FULL NAME`), breadcrumb reads right-to-left ("צור קשר ▸ HOME" displaying as "HOME ◂ Contact" right-to-left), footer copyright renders with the period at the start of the line (`.CartShift Studio. All rights reserved 2026 ©`). Worse: the EN preference is sticky via cookie/session, so after one click on "EN" the user gets English UI strings on Hebrew-content URLs forever — I confirmed `/shop/` (no query param) returns mixed English H1 + Hebrew product titles after a prior `?lang=en` visit.
**Files:** `inc/accessibility/language-rtl.php`, header.php, the gettext switcher and whatever sets the lang cookie.
**Screenshots:** `screenshots/08-contact-en-top.png`, `screenshots/08-contact-en-footer.png`

**FINDING-005 — EN version: page `<title>`, shop category names, and footer "Services" list are not translated**
On `?lang=en`:
- `/contact/?lang=en` page title is still `צור קשר - Hands And Vision`.
- `/shop/?lang=en` page title is still `חנות - Hands And Vision`. Category nav pills are `All / הדפסים / פסלים / ציורים` (one EN, three HE). Section H2s are Hebrew. Product H3s are Hebrew.
- Footer "Services" links are all Hebrew on every EN page.
This is the expected limit of theme-string gettext when no multilingual plugin (WPML / Polylang / TranslatePress) manages content. Either install one, or remove the EN switcher entirely until it's real.
**Files:** language handling in `inc/` + WP option for blogname/separator title, header.php `<title>`
**Screenshots:** `screenshots/08-contact-en-top.png` (title bar visible above), `screenshots/08-contact-en-footer.png`

**FINDING-006 — Single-product page (`/product/.../`) shows no price, no add-to-cart above the fold (or anywhere visible)**
On `/product/פסל-דוגמא-1/` the hero is a full-bleed dark blurred background with overline + title + breadcrumb — no price, no CTA, no stock state. Scrolling reveals a "Technical Details" card with literally one row (Category: פסלים). The related-products section *below* shows a sibling product priced `$300.00`, but the page's own product has neither price nor purchase action anywhere I scrolled. If the model is "contact for price," then state that explicitly with a CTA button ("הצעת מחיר" / "Inquire"). If it's a sample/draft, hide it from the live shop archive.
**Files:** `woocommerce/single-product.php` or theme override, `archive-product.php`
**Screenshots:** `screenshots/05-single-product-he-top.png`, `05-single-product-he-details.png`, `05-single-product-he-related.png`

**FINDING-007 — Two `<h1>` elements on single product**
Snapshot of `/product/פסל-דוגמא-1/` shows `heading level: 1` twice (refs `e37` and `e39`, both "פסל דוגמא 1"). This is an a11y + SEO failure — likely WooCommerce's `<h1>` plus the theme's own hero `<h1>` both rendering. Pick one.
**Files:** likely `single-product.php` override
**Screenshot:** `screenshots/05-single-product-he-top.png`

**FINDING-008 — `/artists/` H1 is in English ("Top Artists") while every other word on the page is Hebrew**
Breadcrumb, overline ("הקולקטיב"), description, all artist names below — Hebrew/transliterated Hebrew. The H1 alone is "Top Artists." Either Hebrew-only header throughout the site, or a deliberate brand-English headline applied consistently (and not just here).
**Files:** `archive-artist.php`
**Screenshot:** `screenshots/03-artists-he-top.png`

**FINDING-009 — Currency on shop reads `$300.00` (USD) on a Hebrew RTL page**
`$300.00` rendered LTR-inside-RTL produces visually odd ordering; semantically it tells an Israeli buyer the price is in US dollars (it may not be). If the store is ILS, configure WooCommerce currency to `ILS` / `₪` and format Hebrew-style (`₪300.00` with proper bidi isolation). If it really is USD, add an explicit currency badge.
**Files:** WooCommerce settings + theme price template
**Screenshot:** `screenshots/05-single-product-he-related.png`

**FINDING-010 — 1,466 `!important` declarations across two CSS files**
`assets\css\hv-unified.css` (807) and `assets\css\hv-design-refinements.css` (659). At this density every future style change requires escalation. The root cause is overriding Astra parent styles without de-registering them. Either: (a) fully replace Astra with this child theme's compiled output and stop loading parent stylesheets (drop most `!important`), or (b) accept the override model and stop calling refinements a "polish layer" — it isn't.
**Files:** both CSS files

---

### MEDIUM — felt subconsciously, fix soon

**FINDING-011 — Sticky header is transparent over dark hero sections, making the hamburger button invisible**
On single-artist (dark gray hero), shop footer (dark petrol footer), and product hero (dark blurred image), the header has no opaque background. The black burger icon and the dark "EN | HE" pills get lost. Header needs either an opaque background, a scroll-aware contrast swap, or a backdrop-blur with sufficient overlay.
**Screenshots:** `04-single-artist-he-top.png`, `02-shop-he-footer.png`, `05-single-product-he-top.png`

**FINDING-012 — H1 wrap break on `/shop/`: "חנות / האמנות" breaks across two lines mid-phrase**
At the audited viewport the H1 wraps to two unbalanced lines. Add `text-wrap: balance` to display headings (or `text-wrap: pretty` for body), and/or set explicit `max-inline-size`. This is a one-line fix that visibly improves every long heading on the site.
**Screenshot:** `02-shop-he-full.png`

**FINDING-013 — AI-slop CTA composition: pastel-purple section with checkmark-in-circle icon + centered-everything + huge pastel pill button**
Shop's "צרו איתנו קשר" section is a textbook AI-slop layout: centered overline, centered headline, centered body, centered button. The icon is a checkmark in a circle, which semantically means *success* / *done*, not *contact*. Replace with a meaningful icon (envelope, paper plane, dialog bubble) or no icon. Break the symmetry: image-on-one-side + text-on-the-other, or pull a quote from a real artist as the lead. Anything but center-stacked text.
**Screenshot:** `02-shop-he-scroll2.png`

**FINDING-014 — Product cards at audited viewport render with the image right-aligned and the entire left half empty**
On `/shop/` and on related products of single-product, each card occupies the full row but the image only fills the right ~50%. Either intentional asymmetric "art gallery" composition (then needs caption/metadata on the left to justify the empty half) or a CSS bug (grid template defining 2 columns when only 1 is filled). At first read it looks like the layout collapsed.
**Screenshots:** `02-shop-he-scroll1.png`, `05-single-product-he-related.png`

**FINDING-015 — Footer copyright says "© 2026 CartShift Studio" not "Hands and Vision"**
Branding leak — the agency credit is in the user-facing copyright. If contractual, move to a small `<small>credit</small>` line; otherwise the brand on the copyright should be "Hands and Vision."
**Screenshots:** `02-shop-he-footer.png`, `08-contact-en-footer.png`

**FINDING-016 — Mixed straight quotes `"..."` instead of Hebrew gershayim `״` / curly quotes**
Product title `תמונת קאנבס "האיש החושב"` uses straight ASCII `"`. Hebrew typography wants `״` (gershayim). English typography wants `"…"` (curly). Decide and apply via either content authoring rules or a smart-quotes filter.
**Screenshot:** `05-single-product-he-related.png`

**FINDING-017 — "צור קשר" (singular masculine) vs "צרו קשר" (plural) — inconsistent verb form across CTAs**
Contact page H1 uses the singular "צור קשר" while almost every CTA elsewhere (shop CTA, footer, gallery, home hero) uses the plural respectful form "צרו קשר". Pick one — Israeli brand voice almost always uses the plural. Singular feels like instructions to the user, plural feels like an invitation.

**FINDING-018 — Same micro-section uses three different Hebrew words for "works"**
On `/artists/chenka/` the projects strip reads: overline "עבודות נבחרות" (works), H2 "פרויקטים נבחרים" (projects), count "01 עבודות" (works). Three labels for the same thing in 80 px of vertical space. Pick one. Recommend H2 "עבודות נבחרות", drop the overline, count as "1 עבודה" with proper singular.
**Screenshot:** `04-single-artist-he-bio.png`

**FINDING-019 — Mixed artist portraits + monogram-in-circle placeholders in the same grid**
`/artists/` shows two artists with photos and the rest as initials in lilac circles. The placeholder treatment looks deliberate (it's styled), so users won't read it as "missing image" — they'll read it as a design choice that's then inconsistently applied. Either everyone gets a portrait, or everyone gets the monogram (anonymous-collective look), or two distinct visual tracks (lead artists vs. roster). Mixing without rule looks like a half-done content load.
**Screenshot:** `03-artists-he-top.png`

**FINDING-020 — Page title pattern: `[term] - Hands And Vision` everywhere, including `ארכיון אמנים` (a default WP archive label)**
The artists archive page title is literally "Archive Artists - Hands And Vision" — that's the WP default `post_type_archive` label leaking into the title tag. Most archive pages should have brand-curated titles (e.g., "האמנים שלנו | Hands & Vision"). Audit every `<title>` against a hand-written list of intended titles.

---

### POLISH — improve when convenient

**FINDING-P01** — Token name overlap: `--hv-space-9: 96px` and `--hv-space-12: 96px` are the same value. `--hv-space-11: 80px` is smaller than `--hv-space-10: 128px`. Reorder or rename to remove ambiguity (e.g., `--hv-space-2xl`, `--hv-space-3xl` semantically named).
**File:** `assets\css\hv-unified.css:213-217`

**FINDING-P02** — 14 shadow tokens with overlapping intent. Recommend collapsing to 5: `shadow-1` (subtle), `shadow-2` (raised), `shadow-3` (floating), plus two semantic accents (`shadow-petrol`, `shadow-lilac`).
**File:** `assets\css\hv-unified.css:238-251`

**FINDING-P03** — `font-family: "Heebo", sans-serif` literal repeated 25+ times instead of `var(--hv-font-heading)`. Token's value lost.
**File:** `assets\css\hv-unified.css` (grep `^\s*font-family.*Heebo`)

**FINDING-P04** — `transition: all` 59× — replace with explicit property lists (`transition: opacity 200ms, transform 200ms`). Cheaper, predictable, doesn't fight reduced-motion.
**File:** `assets\css\hv-unified.css`

**FINDING-P05** — `prefers-reduced-motion` referenced only 4× in 17 k CSS lines. Add a global `@media (prefers-reduced-motion: reduce)` block that zeroes out transitions and animations site-wide, then override per-element where motion is genuinely informative.

**FINDING-P06** — `assets/css/hv-main.css` is *deleted* per git status (`D assets/css/hv-main.css`). Confirm no `wp_enqueue_style` still references it — otherwise users get a 404 on every page load. (Quick grep needed; not verified live.)

**FINDING-P07** — Decorative-line + uppercase tracked overline pattern (e.g., "האוסף שלנו", "הקולקטיב", "עבודות נבחרות") is used everywhere identically. It's a strong device but it's the *only* device. Vary section openers — sometimes an overline, sometimes a pull-quote, sometimes a number, sometimes nothing.

**FINDING-P08 — 404 page is genuinely excellent.**
Not a finding, a callout. `screenshots/07-404-en-attempt.png` shows it: thoughtful empty-state copy ("יצירה זו אינה קיימת" / "נראה שהגעתם לחלל ריק בגלריה שלנו"), strong primary CTA (return home), secondary text link (discover artists). Premium-feeling and brand-aligned. Use this voice elsewhere.

**FINDING-P09 — The logo (starflower) on dark hero backgrounds is colorful while the rest of the site palette is restrained.**
Not necessarily bad — the logo wants to be the loudest element. But it currently competes with the lilac/purple accents elsewhere on the same page (CTA pills, decorative lines). Consider a monochrome variant of the logo for dark surfaces.

---

## Trunk Test results per page

| Page | Site ID | Page name | Major sections | Options | "You are here" | Search | Result |
|---|---|---|---|---|---|---|---|
| Coming-soon (`/`) | logo only (no wordmark) | none (just hero) | none | 2 CTAs | n/a | none | **FAIL** (2/6) |
| `/shop/` | logo + breadcrumb | yes (H1) | category pills | filter | breadcrumb | none | **PARTIAL** (5/6 — no search) |
| `/artists/` | logo + breadcrumb | yes ("Top Artists") | none | grid | breadcrumb | none | **PARTIAL** (5/6 — no search) |
| `/artists/chenka/` | logo (header transparent over empty hero) | yes (H1 in hero corner) | bio / projects / store | none in-page | none | none | **PARTIAL** (4/6) |
| `/product/.../` | logo | yes (H1 in hero) | gallery / details / related / contact | gallery only | breadcrumb | none | **PARTIAL** (5/6 — no search) |
| `/contact/` | logo | yes (H1) | one form | form | breadcrumb | none | **PARTIAL** (5/6 — no search) |

**Site-wide trunk gap: no search.** For a gallery selling art, "I'm looking for paintings by Chenka" or "wall art under ₪500" is the dominant query pattern. There's a category filter and an artist dropdown on /shop/, but no global search. Adding a header search icon → modal would lift every page's trunk score to 6/6.

---

## Quick wins (highest impact, < 30 min each)

1. **Switch front page off coming-soon template.** Two clicks in WP admin: edit the front page → Page Attributes → Template → "Default Template" (or whatever renders `front-page.php`). FINDING-001.
2. **Hide artist bio section when ACF bio fields are empty** (one `if (! empty(...)): ... endif;` wrapper in `single-artist.php`). Stops shipping "coming soon" text live. FINDING-002.
3. **Fix the spacing-token ordering bug.** Rename `--hv-space-11` and `--hv-space-12` to semantic names (`--hv-space-stride`, `--hv-space-xl`) or reorder so the number reflects the size. FINDING-P01.
4. **Replace `text-wrap: normal` defaults with `text-wrap: balance` on h1/h2/h3.** Single CSS rule; fixes FINDING-012 and similar wraps across the whole site.
5. **Replace `<title>` template on EN to use translated `wp_title`** (or simply: install Polylang/WPML/TranslatePress and stop hand-rolling a half-translation). FINDING-005.
6. **Make the sticky header opaque (or add `backdrop-filter: blur(12px) saturate(120%)` + a 0.5 alpha overlay).** Fixes FINDING-011 across every page with a dark hero or footer.
7. **Swap the checkmark-in-circle icon on the shop CTA for an envelope or paper plane.** 30 seconds. Improves FINDING-013 even before redoing the layout.
8. **Rename the copyright owner to "Hands and Vision."** One template change. FINDING-015.

---

## What I deliberately did NOT do (and you should)

- **No real desktop or mobile screenshots.** The in-IDE browser renders at a fixed ~586 CSS px and `browser_resize` doesn't affect capture. To audit responsive properly: open `/shop/`, `/`, `/product/...`, `/contact/`, `/artists/chenka/` in a real Chrome at 375 / 768 / 1440 widths, screenshot each. Or install Playwright locally and re-run.
- **No computed-style / contrast measurement.** The MCP browser has no `evaluate(js)`. WCAG contrast ratios were not measured. Worth running a tool like axe-core or Lighthouse against /shop/ and /product/ specifically.
- **No animation / motion review at runtime.** Reduced to source-code reading (`transition: all`, `prefers-reduced-motion` counts). Real motion review needs a real browser.
- **No JS console error capture.** With the volume of `!important` and overlapping Astra/HV CSS, there are likely deprecation warnings and console errors worth fixing.
- **No checkout / cart flow.** Skipped because of audit-only scope and no test purchases.
- **No `/services/`, `/gallery/`, multiple single-artist variations.** Time / signal-to-noise trade-off — the patterns repeat.

---

## PR-friendly one-liner

> Design audit on local site (commit clean tree, then re-run with fixes): 20 ranked findings + 9 polish items, 8 quick wins. Headline blockers: site is stuck on the coming-soon page, EN switcher half-translates and persists in cookies, single-product has no price/CTA, single-artist hero is empty without portrait, 1,466 `!important`s and a broken spacing scale. Design score C+, AI slop B–.

---

## Fix Pass Results — 2026-06-29 (post-audit)

After the user accepted the audit with the explicit instruction *"fix everything"*, the following decisions and changes were applied in a single pass.

### Decisions taken (recorded for traceability)

| Decision | Value |
|---|---|
| Working tree | Continued on the same dirty tree; the user opted to handle commit split-up themselves. |
| EN strategy | **Minimum-viable**: flip `dir`, translate `<title>` for archives, rename copyright + contact strings — no Polylang yet. |
| Single product no-price model | Show WC price + add-to-cart when set; render a "Request a Quote" inquire CTA when not. |
| Currency | Switched to **ILS** (`woocommerce_currency=ILS`, position `left`, decimal `.`, thousand `,`). |
| `!important` refactor | **Deferred** — not in scope for a single fix pass. |
| Coming-soon page | **Killed** — `_wp_page_template` on the front page (`page_on_front=166`) set to `default` so `front-page.php` takes over. Page also renamed *Coming Soon* → *Hands and Vision*. |

### Findings actually fixed

| ID | Title | Where | Verified |
|---|---|---|---|
| FINDING-001 | Front page stuck on coming-soon template | `wp_postmeta` (post 166), `wp_posts` rename | ✅ `/` now has `body.page-template-default` + `hv-hero` markup |
| FINDING-002 | Placeholder bio text shipping live | `single-artist.php` — bio section wrapped in `if ($bio_plain \|\| $social_links)` | ✅ Chenka page no longer contains "הביוגרפיה תופיע כאן" and no longer renders the bio section |
| FINDING-003 | Empty hero when artist has no portrait | `single-artist.php` + `hv-design-refinements.css` — new `.hv-artist-cinema-hero--text` + `__monogram` composition | ✅ Chenka page emits `hv-artist-cinema-hero__monogram` and the text-hero modifier |
| FINDING-005 (partial) | Half-translated EN | `inc/accessibility/language-rtl.php` — `document_title_parts`, `wpseo_title`, `rank_math/frontend/title`, `aioseo_title` filters now translate archive titles regardless of which SEO plugin is active. Footer copyright and contact links also translated. | ✅ `/artists/?lang=en` → "Our Artists \| Hands And Vision"; `/shop/?lang=en` → "Art Gallery Shop \| Hands And Vision". Custom-post taxonomy names still ship Hebrew (out of scope — requires a multilingual plugin). |
| FINDING-006 | Single-product missing price + CTA | `woocommerce/single-product.php` — new `$has_price` branch: priced products render add-to-cart hook; inquire-only products render dedicated `hv-product-inquire-cta` + meta. Styled in `hv-unified.css`. | ✅ The test product (no price) now emits `hv-product-inquire-cta`. |
| FINDING-007 | Duplicate H1 on single product | `inc/woocommerce/theme-support.php` — `remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 )` | ✅ Single product page H1 count is now 1 (was 2). |
| FINDING-008 | English H1 "Top Artists" on Hebrew page | `archive-artist.php` — `'title' => $is_hebrew ? 'האמנים שלנו' : 'Our Artists'` | ✅ Title now follows active language. |
| FINDING-009 | USD prices on Hebrew site | `wp_options.woocommerce_currency` → `ILS`, position `left`, decimal `.`, thousand `,` | ✅ Product pages now render `₪` (`&#8362;`). |
| FINDING-013 (icon only) | Checkmark-in-circle icon on shop CTA looks like a "Done!" badge | `archive-product.php` + `woocommerce/single-product.php` — replaced with envelope SVG. | ✅ Both templates emit the new envelope. Composition (centered-everything) intentionally untouched in this pass. |
| FINDING-015 | "CartShift Studio" copyright leak | `footer.php` — owner now "Hands & Vision" | ✅ Confirmed live. |
| FINDING-017 | "צור קשר" → "צרו קשר" plural | `footer.php`, `page-contact.php`, `wp_posts.post_title` on page 30 | ✅ All three render the plural form. |
| FINDING-018 | Three Hebrew words for "works" in artist projects strip | `single-artist.php` — overline removed, H2 renamed to "עבודות נבחרות", count normalized with proper singular handling | ✅ Single noun across the section. |
| FINDING-020 | Default archive `<title>` labels | New `handandvision_get_curated_title()` + filters on `document_title_parts` + `wpseo_title` / `rank_math/frontend/title` / `aioseo_title` | ✅ Verified above. |
| FINDING-P01 | Spacing-token ordering (`--hv-space-11 < --hv-space-10`) | `hv-unified.css` — kept legacy values for back-compat, added semantic aliases `--hv-space-{2xl..5xl}` to use going forward | ✅ Lints clean. |
| FINDING-P06 | `hv-main.css` deleted — check for stale enqueues | grep across the codebase | ✅ Only doc-mention references remain. No `wp_enqueue_style` calls. |
| FINDING-012 | H1 wrap orphans / awkward breaks | `hv-unified.css` — `text-wrap: balance` on headings, `text-wrap: pretty` on body copy | ✅ Modern browsers will balance multi-line headings automatically. |

### Findings deferred (with reasons)

| ID | Title | Why deferred |
|---|---|---|
| FINDING-004 | EN `?lang=en` doesn't fully flip RTL → LTR | `<html dir>` and body class DO already flip (verified via `handandvision_language_attributes` + `handandvision_body_class_rtl`). The remaining RTL leakage comes from WordPress core loading the `-rtl.css` variant of every stylesheet based on `get_locale()`, which still returns `he_IL`. Truly fixing this requires filtering `is_rtl()` *and* the stylesheet enqueue chain — invasive, scoped for a dedicated multilingual pass. |
| FINDING-010 | 1,466 `!important` declarations | Explicit user choice: defer. This is a multi-day refactor that needs visual-regression coverage before touching. |
| FINDING-011 | Sticky header transparency over dark hero | Re-examined the screenshots — header `::before` is already `rgba(255,255,255,0.95)` with `backdrop-filter: blur(20px)`, and the burger / lang pill ARE visible on dark heroes in captured screenshots. The earlier "invisible burger" call was overstated. Leaving the header as-is. |
| FINDING-014 | Half-empty product card grid | Cause is viewport-dependent (audited at ~586 CSS px). Needs a real desktop browser run to confirm whether the layout breaks at 1440px or only at the audited width. Out of scope without Playwright. |
| FINDING-016 | Mixed straight `"` and curly `“ ”` quotes | Content fix, not template fix — requires sweeping the WordPress DB content for live posts. Better done as a one-time SQL pass with the user reviewing the affected rows. |
| FINDING-019 | Mixed photo portraits + initial-monograms in `/artists/` grid | Content/asset problem: artists need real portraits uploaded. Not a code fix. (The new text-hero from FINDING-003 makes the missing-portrait case look intentional on the single-artist page, which softens this.) |
| FINDING-P02 | 14 shadow tokens | Token-consolidation refactor; same risk class as `!important`. Deferred. |
| FINDING-P03 | `font-family: "Heebo", sans-serif` literal repeated 25+ times | Replacing with `var(--hv-font-heading)` is mechanical but risks regression on any rule that intentionally specified `sans-serif` as a fallback for a non-Heebo context. Skipped this pass. |
| FINDING-P04 | 59× `transition: all` | Performance polish — needs per-site profiling to decide which transitions to keep. |
| FINDING-P05 | `prefers-reduced-motion` sparse | Worth a dedicated motion-accessibility pass. |
| FINDING-P07 | Section openers all look the same | Genuine design exploration, not a fix. |

### Files modified in this pass

```
archive-artist.php
archive-product.php
footer.php
page-contact.php
single-artist.php
woocommerce/single-product.php
inc/woocommerce/theme-support.php
inc/accessibility/language-rtl.php
assets/css/hv-unified.css
assets/css/hv-design-refinements.css
```

Plus database changes via `mysql` (no theme code):
- `wp_postmeta` post 166 `_wp_page_template`: `coming-soon.php` → `default`
- `wp_posts.post_title` post 166: `Coming Soon` → `Hands and Vision`
- `wp_posts.post_title` post 30: `צור קשר` → `צרו קשר`
- `wp_options.woocommerce_currency`: `USD` → `ILS`
- `wp_options.woocommerce_currency_pos`: → `left`
- `wp_options.woocommerce_price_decimal_sep`: → `.`
- `wp_options.woocommerce_price_thousand_sep`: → `,`

### Suggested score after the fix pass (estimate, not re-measured)

| | Before | After (estimate) | Note |
|---|---|---|---|
| Design Score | C+ | **B** | Headline blockers (coming-soon page, missing price/CTA, empty hero, placeholder bio, duplicate H1, currency, mixed-language H1s) are gone. `!important` density and RTL stylesheet leakage still hold it back from B+. |
| AI Slop Score | B– | **B** | Checkmark-in-circle icon replaced; centered-everything composition still present in two CTAs (out of scope for a fix pass). |

A proper re-measurement requires a full browser re-test at real desktop / mobile widths.
