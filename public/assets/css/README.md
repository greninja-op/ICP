# Design System Documentation

## Overview

This design system provides a comprehensive set of CSS custom properties, glassmorphism components, responsive utilities, and dark mode support for the University Portal frontend migration project.

## File Structure

```
public/assets/css/
├── main.css          # Core design system with CSS custom properties
├── components.css    # Glassmorphism components (cards, navigation, modals, etc.)
├── utilities.css     # Responsive utility classes
└── README.md         # This file
```

## CSS Custom Properties

### Color Palette

**Primary Colors:**
- `--color-primary`: #137fec
- `--color-primary-light`: #3b82f6
- `--color-primary-dark`: #0c5fb8

**Semantic Colors:**
- `--color-success`: #10b981
- `--color-warning`: #f59e0b
- `--color-error`: #ef4444
- `--color-info`: #3b82f6

**Text Colors (Light Mode):**
- `--color-text-primary`: #1a202c
- `--color-text-secondary`: #4a5568
- `--color-text-tertiary`: #718096
- `--color-text-muted`: #a0aec0

**Background Colors (Light Mode):**
- `--color-bg-gradient-start`: #e0f2ff
- `--color-bg-gradient-end`: #ffffff
- `--color-bg-base`: #ffffff
- `--color-bg-secondary`: #f8fafc

### Spacing Scale

- `--space-1`: 0.25rem (4px)
- `--space-2`: 0.5rem (8px)
- `--space-3`: 0.75rem (12px)
- `--space-4`: 1rem (16px)
- `--space-6`: 1.5rem (24px)
- `--space-8`: 2rem (32px)
- `--space-12`: 3rem (48px)
- `--space-16`: 4rem (64px)

### Border Radius

- `--radius-sm`: 0.25rem (4px)
- `--radius-md`: 0.5rem (8px)
- `--radius-lg`: 0.75rem (12px)
- `--radius-xl`: 1.5rem (24px)
- `--radius-full`: 9999px (fully rounded)

### Typography

**Font Family:**
- `--font-family-base`: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif

**Font Sizes:**
- `--font-size-xs`: 0.75rem (12px)
- `--font-size-sm`: 0.875rem (14px)
- `--font-size-base`: 1rem (16px)
- `--font-size-lg`: 1.125rem (18px)
- `--font-size-xl`: 1.25rem (20px)
- `--font-size-2xl`: 1.5rem (24px)
- `--font-size-3xl`: 1.875rem (30px)
- `--font-size-4xl`: 2.25rem (36px)
- `--font-size-5xl`: 3rem (48px)

**Font Weights:**
- `--font-weight-normal`: 400
- `--font-weight-medium`: 500
- `--font-weight-bold`: 700
- `--font-weight-black`: 900

## Glassmorphism Components

### Glass Card

```html
<div class="glass-card">
  <h3>Card Title</h3>
  <p>Card content goes here...</p>
</div>
```

**Variants:**
- `.glass-card-sm` - Smaller padding
- `.glass-card-lg` - Larger padding
- `.glass-card-strong` - Stronger background
- `.glass-card-light` - Lighter background

### Glass Navigation

**Bottom Navigation:**
```html
<nav class="glass-nav glass-nav-bottom">
  <a href="#" class="glass-nav-item-bottom active">
    <i class="fas fa-home"></i>
    <span>Home</span>
  </a>
  <!-- More items... -->
</nav>
```

**Sidebar Navigation:**
```html
<nav class="glass-nav glass-nav-sidebar">
  <a href="#" class="glass-nav-item active">
    <i class="fas fa-dashboard"></i>
    <span>Dashboard</span>
  </a>
  <!-- More items... -->
</nav>
```

### Glass Modal

```html
<div class="glass-modal-backdrop active">
  <div class="glass-modal">
    <div class="glass-modal-header">
      <h3 class="glass-modal-title">Modal Title</h3>
      <button class="glass-modal-close">&times;</button>
    </div>
    <div class="glass-modal-body">
      <p>Modal content...</p>
    </div>
    <div class="glass-modal-footer">
      <button class="glass-btn">Cancel</button>
      <button class="glass-btn glass-btn-primary">Confirm</button>
    </div>
  </div>
</div>
```

### Glass Buttons

```html
<button class="glass-btn">Default</button>
<button class="glass-btn glass-btn-primary">Primary</button>
<button class="glass-btn glass-btn-success">Success</button>
<button class="glass-btn glass-btn-warning">Warning</button>
<button class="glass-btn glass-btn-error">Error</button>

<!-- Sizes -->
<button class="glass-btn glass-btn-sm">Small</button>
<button class="glass-btn glass-btn-lg">Large</button>

<!-- Icon Button -->
<button class="glass-btn glass-btn-icon">
  <i class="fas fa-heart"></i>
</button>
```

### Glass Form Inputs

```html
<input type="text" class="glass-input" placeholder="Enter text...">
<textarea class="glass-input glass-textarea"></textarea>
<select class="glass-select">
  <option>Option 1</option>
</select>
<input type="checkbox" class="glass-checkbox">
<input type="radio" class="glass-radio">
```

### Glass Badges

```html
<span class="glass-badge">Default</span>
<span class="glass-badge glass-badge-primary">Primary</span>
<span class="glass-badge glass-badge-success">Success</span>
<span class="glass-badge glass-badge-warning">Warning</span>
<span class="glass-badge glass-badge-error">Error</span>
```

### Glass Alerts

```html
<div class="glass-alert glass-alert-success">
  <i class="fas fa-check-circle glass-alert-icon"></i>
  <div class="glass-alert-content">
    <div class="glass-alert-title">Success!</div>
    <div class="glass-alert-message">Operation completed successfully.</div>
  </div>
</div>
```

## Responsive Breakpoints

The design system uses a mobile-first approach with the following breakpoints:

- **Base**: < 640px (Mobile)
- **sm**: ≥ 640px (Small devices)
- **md**: ≥ 768px (Medium devices/Tablets)
- **lg**: ≥ 1024px (Large devices/Desktops)
- **xl**: ≥ 1280px (Extra large devices)

### Responsive Utilities

```html
<!-- Display utilities -->
<div class="hidden md:block">Visible on medium screens and up</div>
<div class="block md:hidden">Visible only on mobile</div>

<!-- Grid utilities -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
  <!-- Grid items -->
</div>

<!-- Flexbox utilities -->
<div class="flex flex-col md:flex-row items-center justify-between">
  <!-- Flex items -->
</div>
```

## Dark Mode

### Enabling Dark Mode

Dark mode is controlled by adding the `.dark` class to the `<html>` or `<body>` element.

### JavaScript Integration

Include the dark mode script:

```html
<script src="/assets/js/dark-mode.js"></script>
```

### Toggle Button

```html
<button 
  data-dark-mode-toggle
  data-icon-light="fas fa-moon"
  data-icon-dark="fas fa-sun"
  aria-label="Toggle dark mode"
  class="glass-btn glass-btn-icon"
>
  <i class="fas fa-moon"></i>
</button>
```

### Manual Control

```javascript
// Enable dark mode
DarkMode.enable();

// Disable dark mode
DarkMode.disable();

// Toggle dark mode
DarkMode.toggle();

// Check if dark mode is enabled
const isDark = DarkMode.isEnabled();

// Listen for dark mode changes
document.addEventListener('darkModeChange', (e) => {
  console.log('Dark mode:', e.detail.isDark);
});
```

### Dark Mode Colors

When `.dark` class is applied, the following colors change automatically:

**Background Colors:**
- `--color-bg-gradient-start`: #1e3a8a
- `--color-bg-gradient-end`: #101922
- `--color-bg-base`: #1a202c
- `--color-bg-secondary`: #2d3748

**Text Colors:**
- `--color-text-primary`: #f7fafc
- `--color-text-secondary`: #e2e8f0
- `--color-text-tertiary`: #cbd5e0

**Glassmorphism:**
- `--glass-bg`: rgba(45, 55, 72, 0.4)
- `--glass-bg-strong`: rgba(45, 55, 72, 0.6)
- `--glass-bg-light`: rgba(45, 55, 72, 0.2)

## Utility Classes

### Display

- `.hidden`, `.block`, `.inline-block`, `.flex`, `.grid`
- Responsive: `.sm:flex`, `.md:grid`, `.lg:block`, etc.

### Flexbox

- Direction: `.flex-row`, `.flex-col`
- Alignment: `.items-center`, `.justify-between`
- Gap: `.gap-1`, `.gap-2`, `.gap-4`, `.gap-8`

### Grid

- Columns: `.grid-cols-1`, `.grid-cols-2`, `.grid-cols-3`, `.grid-cols-4`
- Responsive: `.md:grid-cols-2`, `.lg:grid-cols-4`

### Spacing

- Margin: `.m-4`, `.mx-auto`, `.my-6`, `.mt-8`, `.mb-4`
- Padding: `.p-4`, `.px-6`, `.py-8`

### Text

- Size: `.text-xs`, `.text-sm`, `.text-base`, `.text-lg`, `.text-xl`, `.text-2xl`
- Weight: `.font-normal`, `.font-medium`, `.font-bold`, `.font-black`
- Color: `.text-primary`, `.text-secondary`, `.text-muted`
- Alignment: `.text-left`, `.text-center`, `.text-right`

### Border Radius

- `.rounded-sm`, `.rounded-md`, `.rounded-lg`, `.rounded-xl`, `.rounded-full`

### Width & Height

- `.w-full`, `.w-1/2`, `.w-1/3`, `.w-1/4`
- `.max-w-sm`, `.max-w-md`, `.max-w-lg`, `.max-w-xl`
- `.h-full`, `.min-h-screen`

## Usage Example

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>University Portal</title>
  
  <!-- Google Fonts - Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Design System CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/components.css">
  <link rel="stylesheet" href="/assets/css/utilities.css">
</head>
<body>
  <div class="container py-8">
    <!-- Dark Mode Toggle -->
    <button 
      data-dark-mode-toggle
      data-icon-light="fas fa-moon"
      data-icon-dark="fas fa-sun"
      class="glass-btn glass-btn-icon"
    >
      <i class="fas fa-moon"></i>
    </button>
    
    <!-- Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="glass-card">
        <h3 class="text-xl font-bold mb-3">Card 1</h3>
        <p class="text-secondary">Card content...</p>
      </div>
      <!-- More cards... -->
    </div>
  </div>
  
  <!-- Dark Mode Script -->
  <script src="/assets/js/dark-mode.js"></script>
</body>
</html>
```

## Demo

View the complete design system demo at: `/design-system-demo.html`

## Requirements Validation

This design system satisfies the following requirements from the specification:

- **Requirement 2.1**: Glassmorphism design with backdrop-filter blur effects ✓
- **Requirement 2.2**: Exact color palette (#137fec primary, gradient backgrounds) ✓
- **Requirement 2.3**: Inter font family with weights 400, 500, 700, 900 ✓
- **Requirement 2.4**: Spacing and border-radius values (0.25rem, 0.5rem, 0.75rem, 1.5rem, full) ✓
- **Requirement 2.6**: Responsive breakpoints (640px, 768px, 1024px, 1280px) ✓
- **Requirement 2.7**: Dark mode support with toggle mechanism ✓
- **Requirement 18.1**: Mobile-first responsive design ✓

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- iOS Safari (latest)
- Android Chrome (latest)

**Note**: Backdrop-filter requires modern browsers. Fallback styles are provided for older browsers.
