# Deployment Guide - Attendance Management Portal

## System Requirements

- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher
- **Apache**: 2.4 or higher (with mod_rewrite enabled)
- **Extensions**: PDO, PDO_MySQL, mbstring, openssl

## Installation Steps

### 1. Download and Extract Files
```bash
# Extract the project files to your web server directory
# For XAMPP: C:\xampp\htdocs\attendance-portal\
# For LAMP: /var/www/html/attendance-portal/
```

### 2. Database Setup
```bash
# Create database and import schema
mysql -u root -p
CREATE DATABASE attendance_portal;
exit

# Import the database schema
mysql -u root -p attendance_portal < database.sql
```

### 3. Configure Database Connection
Edit `config/database.php` and update the database credentials:

```php
private $host = 'localhost';
private $database = 'attendance_portal';
private $username = 'root';        // Your MySQL username
private $password = '';            // Your MySQL password
```

### 4. Set File Permissions (Linux/Mac)
```bash
# Make sure Apache can read the files
chmod -R 755 /var/www/html/attendance-portal/
chown -R www-data:www-data /var/www/html/attendance-portal/
```

### 5. Apache Configuration
Ensure mod_rewrite is enabled:

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# CentOS/RHEL
# mod_rewrite is usually enabled by default
```

### 6. Virtual Host Setup (Optional)
Create a virtual host for better URL structure:

```apache
<VirtualHost *:80>
    ServerName attendance.local
    DocumentRoot /var/www/html/attendance-portal
    
    <Directory /var/www/html/attendance-portal>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/attendance_error.log
    CustomLog ${APACHE_LOG_DIR}/attendance_access.log combined
</VirtualHost>
```

Add to `/etc/hosts`:
```
127.0.0.1 attendance.local
```

## Default Login Credentials

After successful installation, use these credentials to log in:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@school.com | admin123 |
| Teacher | teacher@school.com | teacher123 |
| Student | student@school.com | student123 |

**⚠️ Important: Change these default passwords immediately after first login!**

## Post-Installation Security

### 1. Change Default Passwords
- Log in as admin and change all default user passwords
- Create new admin users and delete the default admin account

### 2. Database Security
```sql
-- Create a dedicated database user
CREATE USER 'attendance_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT SELECT, INSERT, UPDATE, DELETE ON attendance_portal.* TO 'attendance_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. File Security
```bash
# Restrict access to sensitive files
chmod 600 config/database.php
chmod 644 .htaccess
```

### 4. SSL Certificate (Production)
- Install SSL certificate for HTTPS
- Update .htaccess to force HTTPS redirects

## Troubleshooting

### Common Issues

1. **"Database connection failed"**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists and user has permissions

2. **"404 - Page Not Found"**
   - Check if mod_rewrite is enabled
   - Verify .htaccess file exists and is readable
   - Check Apache error logs

3. **"Permission denied" errors**
   - Check file permissions (755 for directories, 644 for files)
   - Ensure Apache user owns the files

4. **Session issues**
   - Check PHP session configuration
   - Ensure session directory is writable

### Log Files
- Apache error log: `/var/log/apache2/error.log`
- PHP error log: Check `php.ini` for `error_log` location
- Application errors: Check browser console and network tab

## Backup Strategy

### Database Backup
```bash
# Create daily backup
mysqldump -u root -p attendance_portal > backup_$(date +%Y%m%d).sql

# Automated backup script
#!/bin/bash
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p attendance_portal > $BACKUP_DIR/attendance_$DATE.sql
find $BACKUP_DIR -name "attendance_*.sql" -mtime +30 -delete
```

### File Backup
```bash
# Backup application files
tar -czf attendance_backup_$(date +%Y%m%d).tar.gz /var/www/html/attendance-portal/
```

## Performance Optimization

### 1. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_attendance_date ON attendance(date);
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_attendance_class_subject ON attendance(class_id, subject_id);
```

### 2. PHP Configuration
Update `php.ini`:
```ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
```

### 3. Apache Configuration
Enable compression in `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## Maintenance

### Regular Tasks
1. **Weekly**: Review error logs
2. **Monthly**: Database backup verification
3. **Quarterly**: Security updates and password changes
4. **Annually**: Full system backup and disaster recovery testing

### Updates
- Keep PHP and MySQL updated
- Monitor for security patches
- Test updates in staging environment first

## Support

For technical support or questions:
1. Check the troubleshooting section above
2. Review Apache and PHP error logs
3. Verify database connectivity and permissions
4. Test with default credentials on fresh installation