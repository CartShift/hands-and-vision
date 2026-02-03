/**
 * Final Accurate Unused CSS Scanner
 * Precise selector matching - no partial matches
 */

const fs = require("fs");
const path = require("path");

// Configuration
const config = {
	cssFiles: ["assets/css/hv-unified.css", "assets/css/hv-main.css", "assets/css/hv-store-premium.css"],
	sourceDirs: [".", "template-parts", "woocommerce", "assets/js"],
	sourceExtensions: [".php", ".js", ".html", ".htm"],
	ignoreDirs: ["node_modules", "vendor", ".git", "inc/lib", "inc/assets", "admin", "assets/js/minified", "assets/js/unminified", "mcps", "terminals"],
	// WordPress and library patterns that should be preserved
	preservePatterns: [
		/^\.wp-/,
		/^body\.rtl$/,
		/^\.rtl$/,
		/^\.wc-/,
		/^\.woocommerce/,
		/^\.ast-/,
		/^#ast-/,
		/^\.astra/,
		/^\.active$/,
		/^\.active-/,
		/^\.is-/,
		/^\.has-/,
		/^\.product$/,
		/^\.products$/,
		/^\.cart$/,
		/^\.checkout$/
	]
};

// Extract CSS selectors accurately
function parseCSS(cssContent) {
	const selectors = new Set();

	// Remove comments
	const content = cssContent.replace(/\/\*[\s\S]*?\*\//g, "");

	// Track state
	let i = 0;
	let inRule = false;
	let inAtRule = false;
	let inKeyframes = false;
	let braceCount = 0;
	let currentSelector = "";

	while (i < content.length) {
		const char = content[i];

		// Skip @keyframes
		if (char === "@" && content.substring(i, i + 10) === "@keyframes") {
			inKeyframes = true;
			i += 10;
			continue;
		}
		if (char === "@" && !inKeyframes) {
			inAtRule = true;
			i++;
			continue;
		}

		if (inKeyframes) {
			if (char === "{") braceCount++;
			else if (char === "}") {
				braceCount--;
				if (braceCount <= 0) {
					inKeyframes = false;
					braceCount = 0;
				}
			}
			i++;
			continue;
		}

		if (inAtRule) {
			if (char === "{") braceCount++;
			else if (char === "}") {
				braceCount--;
				if (braceCount <= 0) {
					inAtRule = false;
					braceCount = 0;
				}
			}
			i++;
			continue;
		}

		if (char === "{") {
			inRule = true;
			braceCount = 1;

			const selectorStr = currentSelector.trim();
			if (selectorStr) {
				parseSelectorString(selectorStr, selectors);
			}

			currentSelector = "";
			i++;
		} else if (char === "}") {
			inRule = false;
			braceCount = 0;
			currentSelector = "";
			i++;
		} else if (!inRule) {
			currentSelector += char;
			i++;
		} else {
			i++;
		}
	}

	return selectors;
}

// Parse selector string and extract classes, IDs, and tags
function parseSelectorString(selectorStr, selectors) {
	// Remove pseudo-elements and pseudo-classes for matching
	const cleaned = selectorStr.replace(/::?[a-z-]+(?=[,\s\{])/g, "").replace(/\[[^\]]+\]/g, "");

	// Split comma-separated selectors
	const parts = cleaned.split(",").map(s => s.trim());

	for (const part of parts) {
		if (!part) continue;

		// Extract classes (.className)
		const classMatches = part.matchAll(/\.([a-zA-Z_][a-zA-Z0-9_-]*)/g);
		for (const match of classMatches) {
			selectors.add("." + match[1]);
		}

		// Extract IDs (#id)
		const idMatches = part.matchAll(/#([a-zA-Z_][a-zA-Z0-9_-]*)/g);
		for (const match of idMatches) {
			selectors.add("#" + match[1]);
		}

		// Extract element selectors (tags) - only if they're standalone
		const tagMatch = part.match(/^[a-z]+(?=[,\s\{]|$)/);
		if (tagMatch) {
			selectors.add(tagMatch[0]);
		}
	}
}

// Extract selectors from source files - EXACT match only
function extractFromSource(filePath) {
	try {
		const content = fs.readFileSync(filePath, "utf-8");
		const selectors = new Set();

		// Extract class attributes: class="xxx" or class='xxx'
		const classRegex = /class\s*=\s*["']([^"']+)["']/g;
		let match;

		while ((match = classRegex.exec(content)) !== null) {
			const classes = match[1].split(/\s+/).filter(c => c.trim());
			classes.forEach(c => {
				if (/^[a-zA-Z_][a-zA-Z0-9_-]*$/.test(c)) {
					selectors.add("." + c);
				}
			});
		}

		// Extract id attributes: id="xxx" or id='xxx'
		const idRegex = /id\s*=\s*["']([^"']+)["']/g;
		while ((match = idRegex.exec(content)) !== null) {
			const id = match[1].trim();
			if (/^[a-zA-Z_][a-zA-Z0-9_-]*$/.test(id)) {
				selectors.add("#" + id);
			}
		}

		// Extract from JS query selectors - EXACT match
		const queryRegex = /(?:querySelector|querySelectorAll|getElementById|getElementsByClassName)\s*\(\s*['"]([^'"]+)['"]/g;
		while ((match = queryRegex.exec(content)) !== null) {
			const query = match[1];

			// Extract exact classes
			const classMatches = query.matchAll(/\.([a-zA-Z_][a-zA-Z0-9_-]*)/g);
			for (const c of classMatches) {
				selectors.add("." + c[1]);
			}

			// Extract exact IDs
			const idMatches = query.matchAll(/#([a-zA-Z_][a-zA-Z0-9_-]*)/g);
			for (const i of idMatches) {
				selectors.add("#" + i[1]);
			}
		}

		// Extract from jQuery - EXACT match
		const jqueryRegex = /\$\s*\(\s*['"]([^'"]+)['"]/g;
		while ((match = jqueryRegex.exec(content)) !== null) {
			const query = match[1];

			const classMatches = query.matchAll(/\.([a-zA-Z_][a-zA-Z0-9_-]*)/g);
			for (const c of classMatches) {
				selectors.add("." + c[1]);
			}

			const idMatches = query.matchAll(/#([a-zA-Z_][a-zA-Z0-9_-]*)/g);
			for (const i of idMatches) {
				selectors.add("#" + i[1]);
			}
		}

		return selectors;
	} catch (error) {
		return new Set();
	}
}

// Find all source files
function findSourceFiles(dir) {
	const files = [];

	try {
		const items = fs.readdirSync(dir);

		for (const item of items) {
			const fullPath = path.join(dir, item);

			if (config.ignoreDirs.some(ignored => fullPath.includes(ignored))) {
				continue;
			}

			const stat = fs.statSync(fullPath);
			if (stat.isDirectory()) {
				files.push(...findSourceFiles(fullPath));
			} else if (stat.isFile()) {
				const ext = path.extname(fullPath).toLowerCase();
				if (config.sourceExtensions.includes(ext)) {
					files.push(fullPath);
				}
			}
		}
	} catch (error) {
		// Skip unreadable directories
	}

	return files;
}

// Check if selector should be preserved
function shouldPreserve(selector) {
	return config.preservePatterns.some(pattern => pattern.test(selector));
}

// Main analysis
async function analyzeCSS() {
	console.log("\n=== CSS USAGE ANALYSIS (ACCURATE VERSION) ===\n");
	console.log("Analyzing CSS files with exact selector matching...\n");

	const results = new Map();

	// Parse CSS files
	for (const cssFile of config.cssFiles) {
		const fullPath = path.join(process.cwd(), cssFile);

		if (!fs.existsSync(fullPath)) {
			console.log(`✗ File not found: ${cssFile}`);
			continue;
		}

		const cssContent = fs.readFileSync(fullPath, "utf-8");
		const cssSelectors = parseCSS(cssContent);

		results.set(cssFile, {
			selectors: cssSelectors,
			content: cssContent,
			size: cssContent.length
		});

		console.log(`✓ ${cssFile}`);
		console.log(`  Total selectors: ${cssSelectors.size}`);
	}

	// Scan source files
	console.log("\nScanning source files for exact matches...");
	const sourceFiles = [];
	const sourceSelectors = new Set();

	for (const dir of config.sourceDirs) {
		const fullPath = path.join(process.cwd(), dir);
		if (fs.existsSync(fullPath)) {
			const files = findSourceFiles(fullPath);
			sourceFiles.push(...files);

			for (const file of files) {
				const selectors = extractFromSource(file);
				selectors.forEach(s => sourceSelectors.add(s));
			}
		}
	}

	console.log(`✓ ${sourceFiles.length} source files scanned`);
	console.log(`✓ ${sourceSelectors.size} unique selectors found\n`);

	// Analyze usage
	const summary = {
		totalSelectors: 0,
		usedSelectors: 0,
		unusedSelectors: 0,
		preservedSelectors: 0,
		byFile: new Map()
	};

	for (const [cssFile, cssData] of results) {
		const fileResult = {
			used: new Set(),
			unused: new Set(),
			preserved: new Set(),
			cssSelectors: cssData.selectors
		};

		for (const selector of cssData.selectors) {
			summary.totalSelectors++;

			// Check if should be preserved
			if (shouldPreserve(selector)) {
				fileResult.preserved.add(selector);
				summary.preservedSelectors++;
				continue;
			}

			// EXACT match only - no partial matches
			if (sourceSelectors.has(selector)) {
				fileResult.used.add(selector);
				summary.usedSelectors++;
			} else {
				fileResult.unused.add(selector);
				summary.unusedSelectors++;
			}
		}

		summary.byFile.set(cssFile, fileResult);
	}

	// Generate reports
	generateConsoleReport(summary);
	generateDetailedReport(summary);
}

// Console report
function generateConsoleReport(summary) {
	console.log("=== SUMMARY ===\n");
	console.log(`Total CSS selectors: ${summary.totalSelectors}`);
	console.log(`Used selectors: ${summary.usedSelectors} (${((summary.usedSelectors / summary.totalSelectors) * 100).toFixed(1)}%)`);
	console.log(`Unused selectors: ${summary.unusedSelectors} (${((summary.unusedSelectors / summary.totalSelectors) * 100).toFixed(1)}%)`);
	console.log(`Preserved selectors: ${summary.preservedSelectors} (${((summary.preservedSelectors / summary.totalSelectors) * 100).toFixed(1)}%)`);

	console.log("\n=== BREAKDOWN BY FILE ===\n");

	for (const [cssFile, fileResult] of summary.byFile) {
		const total = fileResult.cssSelectors.size;
		console.log(`${cssFile}`);
		console.log(`  Total: ${total}`);
		console.log(`  Used: ${fileResult.used.size} (${((fileResult.used.size / total) * 100).toFixed(1)}%)`);
		console.log(`  Unused: ${fileResult.unused.size} (${((fileResult.unused.size / total) * 100).toFixed(1)}%)`);
		console.log(`  Preserved: ${fileResult.preserved.size} (${((fileResult.preserved.size / total) * 100).toFixed(1)}%)`);
		console.log("");
	}
}

// Detailed report
function generateDetailedReport(summary) {
	let report = "CSS USAGE ANALYSIS - ACCURATE REPORT\n";
	report += "=====================================\n\n";
	report += `Generated: ${new Date().toISOString()}\n\n`;
	report += `EXECUTIVE SUMMARY:\n`;
	report += `  Total CSS Selectors: ${summary.totalSelectors}\n`;
	report += `  Used Selectors: ${summary.usedSelectors} (${((summary.usedSelectors / summary.totalSelectors) * 100).toFixed(1)}%)\n`;
	report += `  Unused Selectors: ${summary.unusedSelectors} (${((summary.unusedSelectors / summary.totalSelectors) * 100).toFixed(1)}%)\n`;
	report += `  Preserved (WordPress/Libraries): ${summary.preservedSelectors} (${((summary.preservedSelectors / summary.totalSelectors) * 100).toFixed(1)}%)\n\n`;

	report += `${"=".repeat(70)}\n`;
	report += `DETAILED ANALYSIS BY FILE\n`;
	report += `${"=".repeat(70)}\n\n`;

	for (const [cssFile, fileResult] of summary.byFile) {
		const total = fileResult.cssSelectors.size;

		report += `\n${"─".repeat(70)}\n`;
		report += `${cssFile}\n`;
		report += `${"─".repeat(70)}\n`;
		report += `Total selectors: ${total}\n`;
		report += `Used: ${fileResult.used.size} (${((fileResult.used.size / total) * 100).toFixed(1)}%)\n`;
		report += `Unused: ${fileResult.unused.size} (${((fileResult.unused.size / total) * 100).toFixed(1)}%)\n`;
		report += `Preserved: ${fileResult.preserved.size} (${((fileResult.preserved.size / total) * 100).toFixed(1)}%)\n\n`;

		if (fileResult.unused.size > 0) {
			report += `UNUSED SELECTORS:\n`;
			report += `─────────────────\n`;
			const sortedUnused = Array.from(fileResult.unused).sort();
			sortedUnused.forEach(s => {
				report += `  ${s}\n`;
			});
			report += `\n`;
		}

		if (fileResult.preserved.size > 0) {
			report += `PRESERVED SELECTORS (WordPress/Libraries - Manually Verify):\n`;
			report += `───────────────────────────────────────────────────────────────\n`;
			const sortedPreserved = Array.from(fileResult.preserved).sort();
			sortedPreserved.forEach(s => {
				report += `  ${s}\n`;
			});
			report += `\n`;
		}
	}

	report += `${"=".repeat(70)}\n`;
	report += `VALIDATION NOTES:\n`;
	report += `${"=".repeat(70)}\n`;
	report += `• This scanner uses EXACT selector matching - no partial matches\n`;
	report += `• Only classes/IDs found directly in source files are considered "used"\n`;
	report += `• "Preserved" selectors are WordPress/library classes that may be needed\n`;
	report += `• Some unused selectors may be added dynamically via JavaScript\n`;
	report += `• Some may be used in conditionals not captured by static analysis\n\n`;

	report += `${"=".repeat(70)}\n`;
	report += `IMPLEMENTATION GUIDE\n`;
	report += `${"=".repeat(70)}\n\n`;
	report += `PHASE 1: PREPARATION\n`;
	report += `  1. Backup all CSS files before making changes\n`;
	report += `  2. Review the "Unused Selectors" list carefully\n`;
	report += `  3. Search codebase for any dynamic class additions\n`;
	report += `  4. Review "Preserved" selectors - some may still be removable\n\n`;
	report += `PHASE 2: TESTING\n`;
	report += `  1. Create a test branch\n`;
	report += `  2. Remove unused selectors from CSS files\n`;
	report += `  3. Test all pages on the website\n`;
	report += `  4. Check all breakpoints (mobile, tablet, desktop)\n`;
	report += `  5. Test all interactive states (hover, focus, active)\n`;
	report += `  6. Verify WooCommerce functionality\n\n`;
	report += `PHASE 3: DEPLOYMENT\n`;
	report += `  1. Commit changes with clear message\n`;
	report += `  2. Deploy to staging environment first\n`;
	report += `  3. Run full QA on staging\n`;
	report += `  4. Monitor for any visual issues\n`;
	report += `  5. If all tests pass, deploy to production\n\n`;

	report += `IMPORTANT NOTES:\n`;
	report += `  - Some selectors may be added via JavaScript libraries\n`;
	report += `  - WordPress classes vary based on plugins and settings\n`;
	report += `  - WooCommerce has conditional classes for different states\n`;
	report += `  - Always test on a staging site before production\n`;
	report += `  - Consider performance gain vs risk of breaking changes\n\n`;

	const reportPath = path.join(process.cwd(), "UNUSED-CSS-FINAL-REPORT.txt");
	fs.writeFileSync(reportPath, report);
	console.log(`✓ Final accurate report saved to: ${reportPath}\n`);
}

// Run
analyzeCSS().catch(error => {
	console.error("Error:", error);
	process.exit(1);
});
