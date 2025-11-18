# University Portal

A comprehensive student portal system providing access to essential university services and information. This unified architecture uses primarily HTML/CSS/PHP (70%) with minimal React components (30%) for complex interactions.

## Features

- **Authentication**: Role-based login system (Student/Teacher/Admin)
- **Student Dashboard**: Academic overview, GPA tracking, assignments, notifications
- **Subjects Management**: Course enrollment and information
- **Results**: Exam results with automatic grade calculation
- **Payments**: Fee management with multiple payment methods (QR, Card, UPI)
- **Notice Board**: Announcements and notifications
- **Analytics**: Performance analysis with interactive charts
- **Admin Panel**: Complete system management
- **Teacher Portal**: Attendance and marks management

## Technology Stack

### Backend
- PHP 8.1+
- MySQL 8.0+
- Composer (Dependency management)
- PHPMailer (Email notifications)
- TCPDF (PDF generation)

### Frontend
- HTML5 (70%)
- CSS3 with custom properties (70%)
- Vanilla JavaScript (20%)
- React 19 (10% - for complex components only)
- Tailwind CSS

### Testing
- PHPUnit
- Pest PHP

## Requirements

- PHP >= 8.1
- MySQL >= 8.0
- Apache/Nginx with mod_rewrite enabled
- Composer
- Node.js and npm (for React components)

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd university-portal
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies (for React components)

```bash
cd react-components
npm install
cd ..
```

### 4. Configure environment

```bash
cp .env.example .env
```

Edit `.env` file with your database credentials and other settings.

### 5. Set up the database

Create a MySQL database:

```sql
CREATE DATABASE university_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations (to be implemented):

```bash
php database/migrate.php
```

### 6. Set up web server

#### Apache

Point your document root to the `public/` directory.

Example virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName university-portal.local
    DocumentRoot /path/to/university-portal/public
    
    <Directory /path/to/university-portal/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name university-portal.local;
    root /path/to/university-portal/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. Build React components

```bash
cd react-components
npm run build
cd ..
```

### 8. Set permissions

```bash
chmod -R 755 public/uploads
chmod -R 755 logs
```

## Development

### Start development server (PHP built-in)

```bash
php -S localhost:8000 -t public
```

### Watch React components

```bash
cd react-components
npm run dev
```

### Run tests

```bash
# All tests
composer test

# Unit tests only
composer test:unit

# Integration tests only
composer test:integration
```

## Project Structure

```
university-portal/
├── config/              # Configuration files
├── public/              # Web root (document root)
│   ├── assets/          # Static assets (CSS, JS, images)
│   ├── uploads/         # User uploaded files
│   └── index.php        # Entry point
├── src/                 # PHP source code
│   ├── Controllers/     # Request handlers
│   ├── Models/          # Data models
│   ├── Services/        # Business logic
│   ├── Middleware/      # Request middleware
│   └── Utils/           # Helper functions
├── views/               # HTML templates
│   ├── layouts/         # Layout templates
│   ├── components/      # Reusable components
│   ├── student/         # Student views
│   ├── teacher/         # Teacher views
│   ├── admin/           # Admin views
│   └── auth/            # Authentication views
├── react-components/    # React components (10%)
├── database/            # Database migrations and seeds
├── tests/               # Test files
└── vendor/              # Composer dependencies
```

## Security

- All passwords are hashed using bcrypt with cost factor 12
- CSRF protection on all forms
- Input sanitization to prevent XSS attacks
- Prepared statements to prevent SQL injection
- Rate limiting on login attempts
- Session timeout after 30 minutes of inactivity

## License

MIT License

## Support

For issues and questions, please open an issue on the repository.
