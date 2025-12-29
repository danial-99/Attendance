#!/bin/bash

echo "🔍 Server Diagnostic Script"
echo "=========================="

echo ""
echo "📊 System Information:"
echo "OS: $(lsb_release -d | cut -f2)"
echo "User: $(whoami)"

echo ""
echo "🌐 Port Usage Check:"
echo "Port 80 (HTTP):"
sudo netstat -tlnp | grep :80 || echo "Port 80 is free"

echo "Port 443 (HTTPS):"
sudo netstat -tlnp | grep :443 || echo "Port 443 is free"

echo "Port 3306 (MySQL):"
sudo netstat -tlnp | grep :3306 || echo "Port 3306 is free"

echo ""
echo "📦 Installed Packages:"
echo "Apache2: $(dpkg -l | grep apache2 | wc -l) packages"
echo "PHP: $(php -v 2>/dev/null | head -1 || echo 'Not installed')"
echo "MySQL: $(mysql --version 2>/dev/null || echo 'Not installed')"

echo ""
echo "🔧 Service Status:"
echo "Apache2: $(systemctl is-active apache2 2>/dev/null || echo 'inactive/not installed')"
echo "MySQL: $(systemctl is-active mysql 2>/dev/null || echo 'inactive/not installed')"

echo ""
echo "📁 Directory Check:"
echo "Web root exists: $([ -d /var/www/html ] && echo 'Yes' || echo 'No')"
echo "Project exists: $([ -d /var/www/html/Attendance ] && echo 'Yes' || echo 'No')"

echo ""
echo "🔍 Apache Error Check:"
if [ -f /var/log/apache2/error.log ]; then
    echo "Last 5 Apache errors:"
    sudo tail -5 /var/log/apache2/error.log
else
    echo "No Apache error log found"
fi

echo ""
echo "💡 Recommendations:"
if ! systemctl is-active --quiet apache2; then
    echo "❌ Apache is not running"
    echo "   Try: sudo systemctl start apache2"
    echo "   Or use PHP server: php -S localhost:8000"
fi

if ! systemctl is-active --quiet mysql; then
    echo "❌ MySQL is not running"
    echo "   Try: sudo systemctl start mysql"
fi

echo ""
echo "🚀 Quick Start Options:"
echo "1. Fix Apache: sudo systemctl start apache2"
echo "2. Use PHP server: cd /var/www/html/Attendance && php -S localhost:8000"
echo "3. Check logs: sudo journalctl -xeu apache2.service"