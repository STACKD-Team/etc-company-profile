---
name: Playful Professional Identity
colors:
  surface: '#fff8f8'
  surface-dim: '#f0d3da'
  surface-bright: '#fff8f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#fff0f2'
  surface-container: '#ffe8ed'
  surface-container-high: '#ffe1e8'
  surface-container-highest: '#f9dbe2'
  on-surface: '#27171c'
  on-surface-variant: '#5a3f47'
  inverse-surface: '#3e2b31'
  inverse-on-surface: '#ffecf0'
  outline: '#8e6f78'
  outline-variant: '#e2bdc7'
  surface-tint: '#b90065'
  primary: '#b90065'
  on-primary: '#ffffff'
  primary-container: '#e6007f'
  on-primary-container: '#130006'
  inverse-primary: '#ffb0c9'
  secondary: '#5f5e5e'
  on-secondary: '#ffffff'
  secondary-container: '#e4e2e1'
  on-secondary-container: '#656464'
  tertiary: '#006e11'
  on-tertiary: '#ffffff'
  tertiary-container: '#008a18'
  on-tertiary-container: '#ffffff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffd9e3'
  primary-fixed-dim: '#ffb0c9'
  on-primary-fixed: '#3e001e'
  on-primary-fixed-variant: '#8e004c'
  secondary-fixed: '#e4e2e1'
  secondary-fixed-dim: '#c8c6c6'
  on-secondary-fixed: '#1b1c1c'
  on-secondary-fixed-variant: '#474747'
  tertiary-fixed: '#88fc7d'
  tertiary-fixed-dim: '#6cdf64'
  on-tertiary-fixed: '#002202'
  on-tertiary-fixed-variant: '#00530a'
  background: '#fff8f8'
  on-background: '#27171c'
  surface-variant: '#f9dbe2'
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
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
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
- **Neutral (30%):** Warm Plum Charcoal (#3A2C33) is used for primary text and structural elements, providing high contrast with a softer, warmer tone that pairs naturally with the Magenta-pink accent.
- **Primary/Accent (10%):** Hot Magenta-pink (#e6007f) is the energetic heart of the brand, reserved for high-priority calls to action, brand highlights, and interactive states.

Supporting tints like Light Pink (#FFE6F3) are utilized for background containers and decorative "blobs" to soften the interface without distracting from the content. Status colors (Success, Warning, Error) follow standard conventions to ensure immediate recognition and accessibility.

## Typography
The typography strategy leverages two distinct families to represent the "Playful Professional" dichotomy. 

For **Headings and Accent text**, the design system utilizes a rounded, friendly sans-serif (mapping to **Plus Jakarta Sans**). This typeface provides a soft, approachable geometry that feels modern and welcoming. For **Body text**, a highly legible and grounded sans-serif (mapping to **Work Sans**) is used to ensure clarity during long-form reading, such as course descriptions or tutorial notes.

Weight is strictly limited to Regular (400) and Bold (700) to maintain a clean hierarchy without unnecessary complexity. Headlines should always use the Bold weight to anchor the page, while the Body uses Regular for maximum readability.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy, centering content within a 1200px maximum width container on desktop. A 12-column grid is used for desktop layouts, transitioning to a 4-column grid for mobile devices. 

Spacing is governed by a 4px base unit, with standard increments (8, 16, 24, 32, 48, 64) used to create rhythm. Section transitions are a core stylistic feature: diagonal cuts (approximately 3-5 degrees) should be used to separate major background color shifts, injecting a sense of motion and energy into the scroll experience. Soft pink blob shapes should be placed behind key imagery or in the corners of sections to break up linear alignments.

## Elevation & Depth
To maintain a modern and clean aesthetic, this design system avoids heavy shadows in favor of **Ambient Depth**. 

Hierarchy is primarily established through **Tonal Layers**, using the Light Pink tint (#FFE6F3) to lift secondary content blocks off the warm white base. When elevation is required (such as on cards or floating buttons), a soft, multi-layered shadow is applied: `0 2px 8px rgba(0,0,0,0.06)`. This creates a subtle lift that feels tactile without appearing dated. Interactive elements may slightly intensify this shadow on hover to provide immediate feedback.

## Shapes
The shape language is consistently rounded to reinforce the "Playful" brand pillar. 

- **Cards:** 16px radius, creating a soft but structured container for course details and testimonials.
- **Input Fields:** 8px radius, providing enough roundedness to feel modern while maintaining a functional look.
- **Pills/Buttons:** 24px radius (full pill), used for all buttons and category tags to maximize the "friendly" aesthetic and provide clear tap targets.

Decorative blobs should be asymmetrical and organic, avoiding perfect circles to maintain a hand-drawn, inclusive feel.

## Components
Consistent component behavior is vital for the trustworthy aspect of the "Playful Professional" style.

- **Buttons:** All buttons must be pill-shaped with a minimum height of 48px to ensure accessibility. Primary buttons use the Magenta-pink (#e6007f) with white text. Secondary buttons use the Warm Plum Charcoal (#3A2C33) or an outline style.
- **Input Fields:** Use 8px rounded corners with a 1px border (#BDBDBD). On focus, the border should transition to Magenta-pink with a soft glow.
- **Cards:** Use a 16px radius and the ambient shadow. Content within cards should have a minimum of 24px internal padding.
- **Chips/Pills:** Used for course categories (e.g., "English," "TOEFL"). These use the 24px radius and the Light Pink background (#FFE6F3).
- **Checkboxes & Radios:** Should be oversized with 24x24px hit areas, using the primary brand color for the active state to ensure they are easy to interact with on mobile.
- **Decorative Blobs:** Use these as background elements for "Hero" images or behind "Teacher Profiles" to create a sense of depth and personality.
