# University Portal

## Overview

The University Portal is a comprehensive web-based student management system built with Express.js and vanilla JavaScript. It provides students with a centralized platform to access academic information, manage payments, view results, track performance, and stay updated with university notices. The application features a modern, glassmorphism-based UI design with gradient backgrounds and card-based layouts.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture

**Design Pattern: Multi-Page Application (MPA)**
- The application uses a traditional multi-page architecture with separate HTML files for each feature module
- Each page has dedicated CSS and JavaScript files for styling and interactivity
- Shared UI components (sidebar navigation, glassmorphism cards) are replicated across pages with consistent styling through CSS variables

**UI Framework: Vanilla JavaScript + CSS3**
- No frontend framework dependencies; built with pure HTML5, CSS3, and vanilla JavaScript
- Glassmorphism design system implemented using CSS backdrop-filter and rgba colors
- Responsive grid layouts using CSS Grid for dashboard structure
- Font Awesome 6.5.1 CDN for iconography

**State Management: Client-Side Only**
- No centralized state management; each page operates independently
- Interactive features (collapsibles, modals, form toggles) use DOM manipulation
- Page-specific state stored in JavaScript variables (e.g., currentPayment in payments.js)

**Routing Strategy**
- File-based routing through direct HTML file navigation
- Relative paths used for inter-page navigation (e.g., `../dashboard/dashboard.html`)
- No client-side routing library

### Backend Architecture

**Server Framework: Express.js 5.1.0**
- Minimal Node.js server setup serving static files
- Express configured to serve all files from root directory
- Cache-Control headers disabled for development (`no-cache, no-store, must-revalidate`)
- Server listens on port 5000 with HOST set to `0.0.0.0` for container compatibility

**Application Structure**
- Feature-based directory organization (login, dashboard, payments, subjects, result, analysis, notice)
- Each feature module is self-contained with HTML, CSS, and JS files
- No backend API endpoints currently implemented; all data is hardcoded in frontend

**Data Layer: Not Implemented**
- No database integration present
- All student data, course information, payment records, and results are static mock data in HTML
- Authentication flow exists in UI but lacks backend validation

**Missing Backend Components**
- No authentication/authorization middleware
- No API routes for CRUD operations
- No session management
- No database ORM or query layer

### Core Features

**1. Authentication Module (login/)**
- Glassmorphism login form with username/password fields
- Role selector with animated highlight (Student/Staff/Admin)
- Client-side form validation only; no backend authentication

**2. Student Dashboard (dashboard/)**
- Welcome card with user greeting
- Academic progress visualization with SVG circular progress indicators
- Statistics cards for GPA, attendance, courses
- Quick action buttons for common tasks
- Clean, card-based layout with Material Design influence

**3. Payment Portal (payments/)**
- Payment summary with semester-wise breakdown
- Multiple payment options (QR code, UPI, card, net banking)
- Modal-based payment flow with method selection
- QR code generation for UPI payments
- Payment history tracking (frontend only)

**4. Course Management (subjects/)**
- Grid layout displaying enrolled courses
- Collapsible cards showing instructor details and topics
- Course code, instructor photo, and department information
- Interactive "View Details" expansion for additional content

**5. Results Display (result/)**
- Semester-wise result cards with collapsible content
- Tabular display of subject-wise marks (Internal/Theory/Total)
- SGPA calculation and total marks summary
- Grade display with visual distinction for lab subjects

**6. Performance Analysis (analysis/)**
- Statistical overview with GPA and attendance metrics
- Progress indicators using custom CSS circular progress bars
- Performance visualization components (structure present, charts not implemented)

**7. Notice Board (notice/)**
- Card-based notice listing
- Category tagging (Urgent, Academic, Event)
- Date metadata and priority indicators
- Support for poster attachments

### Design System

**Color Scheme**
- Primary gradient: `linear-gradient(135deg, #2b2e4a, #e84545)` (dark blue to red)
- Alternative gradient: `linear-gradient(135deg, #e0f2fe, #ffffff)` (light blue to white) for dashboard
- Accent colors defined in CSS variables for consistency
- Glassmorphism effects: `rgba(255, 255, 255, 0.1)` backgrounds with `backdrop-filter: blur(12px)`

**Layout Patterns**
- Dashboard grid: 260px fixed sidebar + flexible main content area
- Card-based content containers with consistent padding and border-radius
- Responsive grid layouts for content (subjects grid, stats grid)
- Sticky sidebar navigation with scrollable main content

**Interactive Components**
- Collapsible cards with CSS transition animations
- Modal overlays for payment flows
- Radio button selectors with sliding highlight animation
- Hover effects on navigation items and buttons

## External Dependencies

### CDN Resources
- **Font Awesome 6.5.1**: Icon library loaded via CDN for UI icons throughout the application

### NPM Packages
- **express ^5.1.0**: Web server framework for serving static files and handling HTTP requests

### Missing Integrations
- **Payment Gateway**: Payment modal UI exists but lacks integration with actual payment processors (Razorpay, Stripe, etc.)
- **Database System**: No database configured; future implementation likely needs PostgreSQL with Drizzle ORM or similar
- **Authentication Service**: No OAuth, JWT, or session management implementation
- **Email Service**: No notification system for notices or payment confirmations
- **File Storage**: No cloud storage integration for instructor photos, notice posters, or documents
- **Analytics Service**: Performance analysis page lacks chart library integration (Chart.js, D3.js recommended)