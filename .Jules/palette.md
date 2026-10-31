## 2026-10-30 - ARIA Labels for Icon-Only and Symbol-Based Action Buttons
**Learning:** Symbol-only buttons (`&times;` close buttons) and emoji-based action buttons (like `🔑 Pass`) lack readable text for assistive technologies. Adding explicit `aria-label` and `:aria-expanded` attributes ensures screen readers properly convey button intent.
**Action:** Always verify icon-only and symbol-based interactive elements in Blade views and add explicit `aria-label` descriptions.
