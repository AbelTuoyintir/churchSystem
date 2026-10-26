## 2026-10-23 - ARIA accessibility on mobile sidebar drawers and icon dismiss buttons
**Learning:** In Blade/Alpine responsive layouts, drawer sidebars need explicit `id` and `aria-label` linkage with the hamburger button (`aria-controls`, `:aria-expanded`) and icon-only dismiss buttons (`&times;`) require descriptive `aria-label` attributes for screen readers.
**Action:** Always verify mobile drawer triggers and alert close buttons have matching ARIA relationships (`aria-controls`, `:aria-expanded`, `aria-label`).
