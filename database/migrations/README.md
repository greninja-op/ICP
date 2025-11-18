# Database Migrations

This directory contains database migration files for the University Portal.

## Migration File Naming Convention

Migration files should follow this naming pattern:
```
YYYY_MM_DD_HHMMSS_description.php
```

Example: `2025_01_15_120000_create_users_table.php`

## Running Migrations

### Run all pending migrations
```bash
php database/migrate.php up
```

### Rollback last batch of migrations
```bash
php database/migrate.php down
```

### Show migration status
```bash
php database/migrate.php status
```

## Creating a New Migration

1. Create a new PHP file in this directory following the naming convention
2. Write your SQL statements in the file
3. Run `php database/migrate.php up` to execute the migration

## Migration File Structure

Each migration file should contain SQL statements:

```php
<?php
// 2025_01_15_120000_create_example_table.php

CREATE TABLE example (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Notes

- Migrations are executed in alphabetical order (timestamp ensures correct order)
- Each migration is tracked in the `migrations` table
- Migrations are grouped into batches for rollback purposes
- Always test migrations on a development database first
