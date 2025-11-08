# Ez IT Solutions - Brand Guidelines

## Overview
This document defines the visual identity and design system for Ez IT Solutions WordPress plugins, ensuring consistency across all projects.

---

## Color Palette

### Dark Mode (Primary Theme)

#### Primary Colors
- **Lime Green** `#a3e635` - Primary accent, headings, interactive elements
- **Darker Lime** `#84cc16` - Hover states, secondary accents
- **Dark Background** `#0f1419` - Main background
- **Card Background** `#131a1f` - Card and panel backgrounds

#### Accent Colors
- **Orange Hint** `rgba(251, 146, 60, 0.1)` - Subtle warm accents (used sparingly)
- **White** `#ffffff` - Primary text, high contrast elements

#### Semantic Colors
- **Success Green** `rgba(163, 230, 53, 0.1)` - Success states, positive feedback
- **Info Blue** `rgba(59, 130, 246, 0.2)` - Informational elements
- **Warning Orange** `rgba(251, 146, 60, 0.2)` - Warning states
- **Error Red** `rgba(239, 68, 68, 0.2)` - Error states

### Light Mode (Secondary Theme)

#### Primary Colors
- **Dark Green** `#16a34a` - Primary accent, headings, interactive elements
- **Darker Green** `#15803d` - Hover states, secondary accents
- **Light Background** `#f9fafb` - Main background
- **White** `#ffffff` - Card and panel backgrounds

#### Text Colors
- **Primary Text** `#111827` - Main content text
- **Secondary Text** `#374151` - Supporting text, labels

#### Semantic Colors
- **Success Green** `rgba(34, 197, 94, 0.1)` - Success states
- **Borders** `#e5e7eb` - Subtle borders and dividers

---

## Typography

### Font Families
- **Primary**: System fonts (-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif)
- **Monospace**: 'Courier New', monospace (for code, tokens, technical data)

### Font Sizes
- **Page Title**: 1.5rem (24px) - Section headings
- **Card Title**: 1.25rem (20px) - Card headings
- **Sidebar Title**: 1.1rem (17.6px) - Sidebar headings
- **Body Text**: 1rem (16px) - Standard content
- **Small Text**: 0.875rem (14px) - Labels, descriptions
- **Tiny Text**: 0.75rem (12px) - Badges, metadata

### Font Weights
- **Bold**: 700 - Primary headings, stat values
- **Semibold**: 600 - Card titles, labels
- **Medium**: 500 - Emphasized text
- **Normal**: 400 - Body text

---

## UI Components

### Buttons

#### Primary Button
- **Dark Mode**: Lime green background `#a3e635`, dark text `#0b0f12`
- **Light Mode**: Green background `#22c55e`, white text
- **Hover**: Darker shade with lift effect and shadow
- **Border Radius**: 6px
- **Font Weight**: 600

#### Secondary Button
- **Dark Mode**: Lime green tinted background with lime text
- **Light Mode**: Green tinted background with green text
- **Border**: Matching color with transparency
- **Hover**: Increased opacity

### Cards
- **Border Radius**: 8px
- **Padding**: 24px
- **Dark Mode**: Background `#131a1f`
- **Light Mode**: Background `#ffffff` with border `#e5e7eb`
- **Title Color**: Matches theme primary color

### Toggle Switches
- **Width**: 50px
- **Height**: 26px
- **Off State**: Transparent background with subtle border
- **On State**: Primary color background
- **Transition**: 0.3s smooth animation

### Stats Cards
- **Background**: Subtle primary color tint
- **Border**: Primary color with transparency
- **Hover**: Lift effect with shadow
- **Value Color**: Primary theme color
- **Value Size**: 2.25rem
- **Label**: Uppercase, 0.875rem, letter-spacing 0.5px

### Tabs
- **Active Tab**: Primary color with 3px top border
- **Inactive Tab**: Subtle opacity
- **Hover**: Increased opacity
- **Border Radius**: 6px (top only)

---

## Spacing System

### Margins & Padding
- **Tiny**: 4px
- **Small**: 8px
- **Medium**: 12px
- **Default**: 16px
- **Large**: 20px
- **XLarge**: 24px
- **XXLarge**: 32px

### Grid Gaps
- **Tight**: 12px
- **Normal**: 16px
- **Loose**: 20px

---

## Animations & Transitions

### Standard Transitions
- **Duration**: 0.2s - 0.3s
- **Easing**: ease, ease-in-out
- **Properties**: all, opacity, transform, background, border-color

### Hover Effects
- **Lift**: `translateY(-2px)`
- **Shadow**: Matching primary color with transparency
- **Opacity Change**: 0.8 → 1.0

### Loading States
- **Spinner**: 0.6s linear infinite rotation
- **Modal**: Fixed position with overlay

---

## Design Principles

### 1. Consistency
- Use the same color for similar elements across all tabs
- Maintain consistent spacing and sizing
- Apply uniform border radius values

### 2. Hierarchy
- Larger, bolder text for important information
- Primary color for interactive elements
- Subtle backgrounds for secondary content

### 3. Accessibility
- High contrast ratios in both themes
- Clear focus states for keyboard navigation
- Descriptive labels and ARIA attributes

### 4. Responsiveness
- Mobile-first approach
- Flexible grid layouts
- Breakpoints at 768px and 1024px

### 5. Performance
- Minimal animations
- Optimized CSS selectors
- Hardware-accelerated transforms

---

## Theme Toggle

### Implementation
- Persistent storage in WordPress options
- Instant theme switching without page reload
- Smooth transitions between themes
- Button styled to match current theme

---

## Icon Usage

### Dashicons
- **Size**: 20px - 24px
- **Color**: Inherits from parent or primary color
- **Spacing**: 8px gap from adjacent text

### Common Icons
- `dashicons-dashboard` - Dashboard
- `dashicons-admin-network` - SSO & Tokens
- `dashicons-admin-users` - Impersonation
- `dashicons-database` - Migration Tools
- `dashicons-admin-settings` - Settings
- `dashicons-info` - About/Information
- `dashicons-admin-appearance` - Appearance
- `dashicons-admin-generic` - Advanced Settings

---

## Best Practices

### Do's ✓
- Use lime green (`#a3e635`) for all primary accents in dark mode
- Use dark green (`#16a34a`) for all primary accents in light mode
- Apply consistent border radius (6px-8px)
- Include hover states for interactive elements
- Use semantic color variables for states
- Test in both light and dark modes

### Don'ts ✗
- Don't mix different shades of green randomly
- Don't use pure black or pure white for backgrounds
- Don't create overly complex animations
- Don't forget focus states for accessibility
- Don't use colors without sufficient contrast

---

## Future Considerations

### Potential Additions
- **Purple Accent**: For premium features or special callouts
- **Blue Accent**: For informational notices
- **Gradient Backgrounds**: Subtle gradients for hero sections
- **Custom Illustrations**: Brand-specific graphics

### Expansion
- Apply this design system to other Ez IT Solutions plugins
- Create reusable component library
- Develop WordPress theme with matching design
- Build style guide website

---

## Version History

- **v1.0** (2025-11-07) - Initial brand guidelines established
  - Dark/Light mode color palettes
  - Typography system
  - Component specifications
  - Orange hint accent introduced

---

**Maintained by**: Ez IT Solutions | Chris Hultberg  
**Website**: [www.Ez-IT-Solutions.com](https://www.Ez-IT-Solutions.com)  
**Last Updated**: November 7, 2025
