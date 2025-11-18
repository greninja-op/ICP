# Setup Instructions

## Prerequisites Installation

### 1. Install Composer (PHP Dependency Manager)

#### Windows
1. Download Composer from: https://getcomposer.org/Composer-Setup.exe
2. Run the installer
3. Follow the installation wizard
4. Verify installation:
   ```bash
   composer --version
   ```

#### Linux/Mac
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Install PHP Dependencies

Once Composer is installed, run:

```bash
composer install
```

This will install:
- PHPMailer (Email notifications)
- TCPDF (PDF generation)
- PHPUnit (Testing framework)
- Pest (Testing framework)

### 3. Install Node.js Dependencies (for React components)

```bash
cd react-components
npm install
cd ..
```

### 4. Configure Environment

```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:
- Database credentials
- Mail server settings
- Application settings

### 5. Set Up Database

Create a MySQL database:

```sql
CREATE DATABASE university_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Configure Web Server

#### Option A: PHP Built-in Server (Development)

```bash
php -S localhost:8000 -t public
```

Then visit: http://localhost:8000

#### Option B: Apache

1. Enable mod_rewrite:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. Configure virtual host (see README.md for details)

#### Option C: Nginx

Configure server block (see README.md for details)

### 7. Verify Installation

Visit your configured URL. You should see:
- "University Portal - Setup Complete" message
- PHP version information
- Environment details

## Next Steps

1. Run database migrations (to be implemented in Task 2)
2. Seed sample data
3. Build React components
4. Start development

## Troubleshooting

### Composer not found
- Ensure Composer is in your PATH
- Restart your terminal after installation

### Permission errors
```bash
chmod -R 755 public/uploads
chmod -R 755 logs
```

### Database connection errors
- Verify MySQL is running
- Check credentials in .env file
- Ensure database exists

### Apache .htaccess not working
- Ensure mod_rewrite is enabled
- Check AllowOverride is set to All in Apache config
