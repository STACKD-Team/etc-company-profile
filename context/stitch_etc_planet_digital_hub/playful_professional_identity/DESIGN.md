---
name: Playful Professional Identity
colors:
  base: '#F5F5F5'
  neutral: '#27171C'
  accent: '#E6007F'
typography:
  h1:
    fontFamily: Plus Jakarta Sans
    fontSize: 56px
    fontWeight: '700'
    lineHeight: '1.1'
  h2:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '700'
    lineHeight: '1.2'
  h3:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: '1.3'
  body:
    fontFamily: Work Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.5'
  button:
    fontFamily: Plus Jakarta Sans
    fontSize: 15px
    fontWeight: '700'
    lineHeight: '1'
  small:
    fontFamily: Work Sans
    fontSize: 13px
    fontWeight: '400'
    lineHeight: '1.4'
rounded:
  box: 0.5rem
  field: 0.25rem
  selector: 0.5rem
sizes:
  xs: 16px
  sm: 20px
  md: 24px
  lg: 28px
  xl: 32px
spacing:
  base-unit: 4px
  gutter: 24px
  margin: 32px
  container-max: 1200px
  section-padding: 80px
---

## Brand & Style
The brand personality for this design system is "Playful Professional." It balances the rigor of academic excellence with the warmth of a community-focused language institution. The UI is designed to evoke a sense of energy and inclusivity, ensuring that students feel welcomed and motivated while maintaining a clean, trustworthy aesthetic that parents and professionals respect.

The style is a synthesis of **Minimalism** and **Modern Corporate**, utilizing generous whitespace and a restricted color palette to maintain clarity. This is softened by organic, decorative elements such as pink blob shapes and dynamic diagonal section cuts that break the rigidity of standard web grids. The resulting interface is approachable yet authoritative, ensuring a seamless user journey from discovery to enrollment.

## Colors
This design system employs a strict 60-30-10 distribution to ensure visual balance and focus. 

- **Base (60%):** A warm off-white (#F5F5F5) serves as the primary canvas, providing a soft, paper-like background that is easier on the eyes than pure white.
- **Neutral (30%):** Warm Plum Charcoal (#27171C) is used for primary text and structural elements, providing high contrast with a softer, warmer tone that pairs naturally with the Magenta-pink accent.
- **Primary/Accent (10%):** Hot Magenta-pink (#e6007f) is the energetic heart of the brand, reserved for high-priority calls to action, brand highlights, and interactive states.

No additional brand colors are allowed. Tonal layers, muted text, hover backgrounds, decorative shapes, and borders must be produced with opacity from these three colors rather than separate hex values. Pure white is not a dashboard surface token.

Semantic Success, Warning, and Error colors are allowed only for status badges, alerts, validation feedback, and destructive confirmations. They must not be used as decorative page or card colors.

## Typography
The typography strategy leverages two distinct families to represent the "Playful Professional" dichotomy. 

For **Headings and Accent text**, the design system utilizes a rounded, friendly sans-serif (mapping to **Plus Jakarta Sans**). This typeface provides a soft, approachable geometry that feels modern and welcoming. For **Body text**, a highly legible and grounded sans-serif (mapping to **Work Sans**) is used to ensure clarity during long-form reading, such as course descriptions or tutorial notes.

Weight is strictly limited to Regular (400) and Bold (700) to maintain a clean hierarchy without unnecessary complexity. Headlines should always use the Bold weight to anchor the page, while the Body uses Regular for maximum readability.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy, centering content within a 1200px maximum width container on desktop. A 12-column grid is used for desktop layouts, transitioning to a 4-column grid for mobile devices. 

Spacing is governed by a 4px base unit, with standard increments (8, 16, 24, 32, 48, 64) used to create rhythm. Section transitions are a core stylistic feature: diagonal cuts (approximately 3-5 degrees) should be used to separate major background color shifts, injecting a sense of motion and energy into the scroll experience. Soft pink blob shapes should be placed behind key imagery or in the corners of sections to break up linear alignments.

## Elevation & Depth
To maintain a modern and clean aesthetic, this design system avoids heavy shadows in favor of **Ambient Depth**. 

Hierarchy is primarily established through **Tonal Layers**, using low-opacity Magenta over the Off-white base to lift secondary content blocks. When elevation is required (such as on cards or floating buttons), a soft shadow derived from low-opacity Charcoal is applied. This creates a subtle lift that feels tactile without introducing another palette color. Interactive elements may slightly intensify this shadow on hover to provide immediate feedback.

## Shapes
The shape language is consistently rounded to reinforce the "Playful" brand pillar. 

- **Boxes:** Cards, panels, modals, drawers, alerts, and empty states use a `0.5rem` radius.
- **Fields:** Buttons, icon buttons, inputs, selects, textareas, date fields, search fields, tabs, and pagination controls use a `0.25rem` radius.
- **Selectors:** Checkboxes, radio controls, toggles, badges, and chips use a `0.5rem` radius.
- **Borders:** Every visible component border and focus border uses a `2px` width.

Decorative blobs should be asymmetrical and organic, avoiding perfect circles to maintain a hand-drawn, inclusive feel.

## Components
Consistent component behavior is vital for the trustworthy aspect of the "Playful Professional" style.

- **Buttons:** Buttons use the shared field radius (`0.25rem`) and one of the fixed field heights: `xs` 16px, `sm` 20px, `md` 24px, `lg` 28px, or `xl` 32px. Primary buttons use Magenta with off-white text. Secondary buttons use Charcoal or an outline style.
- **Input Fields:** Use `0.25rem` radius, a 2px Charcoal-opacity border, off-white background, Magenta focus border, and the same fixed field height scale as buttons.
- **Cards:** Use `0.5rem` radius, a 2px Charcoal-opacity border, and subtle ambient shadow. Content within cards should have a minimum of 24px internal padding.
- **Chips/Badges:** Use `0.5rem` radius, the fixed selector size scale (`xs` 16px through `xl` 32px), and tonal backgrounds derived from the three brand colors, except semantic status variants.
- **Checkboxes & Radios:** Use `0.5rem` selector radius, the fixed selector size scale, and the primary brand color for the active state.
- **Decorative Blobs:** Use these as background elements for "Hero" images or behind "Teacher Profiles" to create a sense of depth and personality.

All component dimensions, padding, gaps, and icon sizes use the 4px base unit. Field and selector heights follow the fixed `xs` 16px, `sm` 20px, `md` 24px, `lg` 28px, and `xl` 32px scale.

## Dashboard Shell

- The shared dashboard header contains only a sidebar toggle on the far left and the authenticated user's avatar, name, role, and dropdown trigger on the far right.
- Page title, eyebrow, and page-specific actions belong at the beginning of the content area, never inside the global header.
- The profile dropdown contains Logout. User identity, Help, and Logout controls do not appear in the sidebar.
- The desktop sidebar starts expanded and can collapse to an icon-only rail. The preference is stored in browser local storage and restored across dashboard pages.
- Collapsed menu icons provide accessible labels and hover/focus tooltips. Branding uses a logo icon without the “ETC Planet” wordmark.
- On mobile, the hamburger opens the full labeled sidebar as a left overlay with backdrop, Escape support, and a visible close action. Do not duplicate navigation in a bottom bar.

## Data Tables

- Dashboard list pages use the shared `x-ui.data-table` wrapper or a Filament Table with equivalent behavior.
- Global search and its Reset action remain visible in a plain toolbar above the table panel. The toolbar has no card, border, background, or container padding.
- Column headings occupy the first table-header row. Column filters occupy a second header row directly below their matching headings; action-only columns have an empty filter cell.
- Every data column that can be queried safely provides an appropriate filter: text, number, date, select, or database-backed autocomplete. Action-only columns have no filter.
- Text, number, and search inputs apply automatically after a 400ms debounce. Select, date, and autocomplete filters apply immediately after a valid change.
- Filter forms do not use an Apply button. Reset removes search, column filters, and pagination while retaining the active sort column and direction.
- Search, column filters, sort column, sort direction, and pagination are stored in query parameters so refreshes and shared URLs restore the same table state.
- Header filters stay visible on small screens and move together with their columns through horizontal table scrolling.
- Horizontal table scrollbars use a thin transparent track and a low-opacity Charcoal thumb; pure black scrollbar colors are not allowed.
- Sortable and filterable columns must be whitelisted server-side. Entity options and results must respect the authenticated user's authorization scope.
