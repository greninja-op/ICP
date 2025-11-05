# Technology Stack

## Backend
- **Runtime**: Node.js
- **Framework**: Express.js 4.18.2
- **Server**: Static file serving with development-friendly caching disabled

## Frontend - Legacy Version
- **Languages**: HTML5, CSS3, Vanilla JavaScript
- **Styling**: CSS Grid, Flexbox, CSS Custom Properties
- **Icons**: Font Awesome 6.5.1 (CDN)
- **Design System**: Glassmorphism with backdrop-filter effects
- **Responsive**: Mobile-first approach with CSS media queries

## Frontend - React Version
- **Framework**: React 19.0.0
- **Build Tool**: Vite 6.0.7
- **Routing**: React Router DOM 7.9.4
- **Styling**: Tailwind CSS 3.4.17
- **Animations**: Motion 11.15.0
- **UI Components**: liquid-glass-react 1.1.1
- **Image Processing**: react-image-crop 11.0.10

## Development Tools
- **Package Manager**: npm
- **CSS Processing**: PostCSS, Autoprefixer
- **Module System**: ES Modules (React version)

## Common Commands

### Legacy Version
```bash
# Start development server
npm start

# Server runs on http://localhost:5000
```

### React Version
```bash
# Navigate to React project
cd StudentPortal-React

# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## File Structure Conventions
- Static assets served from root directory
- Each module has its own folder with HTML, CSS, and JS files
- Shared styles in `shared-styles.css`
- React version follows standard Vite project structure