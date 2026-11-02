## 2026-10-30 - ARIA Labels for Icon-Only and Symbol-Based Action Buttons
**Learning:** Symbol-only buttons (`&times;` close buttons) and emoji-based action buttons (like `🔑 Pass`) lack readable text for assistive technologies. Adding explicit `aria-label` and `:aria-expanded` attributes ensures screen readers properly convey button intent.
**Action:** Always verify icon-only and symbol-based interactive elements in Blade views and add explicit `aria-label` descriptions.

## 2026-11-01 - Skip to Main Content Link for Deep Sidebar Navigation
**Learning:** In desktop and responsive applications with extensive sidebar navigation trees (containing dozens of links across modules), keyboard-only and screen reader users must tab through every navigation item before reaching page content. An accessible `sr-only focus:not-sr-only` skip link targeting `<main id="main-content" tabindex="-1">` allows keyboard users to bypass navigation immediately.
**Action:** Always provide a styled skip navigation link right after the `<body>` tag pointing to the primary content wrapper with `tabindex="-1"`.
