# Project Structure

## Root Directory Layout

```
/
├── index.html              # Main landing page with navigation grid
├── server.js              # Express server for static file serving
├── shared-styles.css      # Common styles across all pages
├── favicon.svg            # Site favicon
├── package.json           # Node.js dependencies and scripts
└── package-lock.json      # Dependency lock file
```

## Module Organization

Each feature module follows a consistent structure:

```
/module-name/
├── module-name.html       # Page template
├── module-name.css        # Module-specific styles
└── module-name.js         # Module functionality (if needed)
```

### Current Modules
- `login/` - Authentication with role selection
- `dashboard/` - User profile and overview
- `subjects/` - Course enrollment and management
- `result/` - Exam results and grades
- `payments/` - Fee management with payment processing
- `notice/` - Announcements and notifications
- `analysis/` - Performance analytics

## Asset Directories

```
/assets/
└── js/                    # Shared JavaScript utilities

/image/                    # Static images and screenshots
/attached_assets/          # User-uploaded or generated assets
```

## React Version Structure

```
/StudentPortal-React/
├── src/                   # React source code
├── public/                # Static assets
├── dist/                  # Build output
├── package.json           # React project dependencies
├── vite.config.js         # Vite configuration
├── tailwind.config.js     # Tailwind CSS configuration
└── postcss.config.js      # PostCSS configuration
```

## Development Files

```
/.idx/                     # IDX environment configuration
/.kiro/                    # Kiro AI assistant configuration
/.git/                     # Git version control
/node_modules/             # Dependencies (both versions)
```

## Naming Conventions

- **Files**: kebab-case for HTML/CSS, camelCase for JavaScript
- **Classes**: BEM methodology preferred, kebab-case for utility classes
- **IDs**: camelCase for JavaScript interaction elements
- **Functions**: camelCase with descriptive names
- **Variables**: camelCase for JavaScript, kebab-case for CSS custom properties

## Code Organization Patterns

- Each module is self-contained with its own styles and scripts
- Shared functionality goes in `shared-styles.css` or `/assets/js/`
- Modal and interactive components use consistent naming patterns
- Event listeners are set up in DOMContentLoaded handlers
- Glassmorphism design system maintained across all modules