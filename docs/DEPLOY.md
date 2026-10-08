# THE DRIVE CLINIC — Production Deployment Guide

This guide covers deploying **The Drive Clinic** on Linux (Ubuntu 22.04 / 24.04 LTS) with **Nginx**, **PHP 8.2+ FPM**, **Node.js 20** (managed via PM2), and **MySQL 8.0**.

---

## 1. System Requirements & Packages

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx php8.2-fpm php8.2-mysql php8.2-cli php8.2-curl php8.2-xml php8.2-mbstring mysql-server git certbot python3-certbot-nginx

# Install Node.js 20 LTS & PM2
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
sudo npm install -g pm2
```

---

## 2. Directory Structure & Permissions

Place the application in `/var/www/drive-clinic`:
```bash
sudo mkdir -p /var/www/drive-clinic
sudo chown -R www-data:www-data /var/www/drive-clinic
sudo chmod -R 755 /var/www/drive-clinic
```

---

## 3. Node.js API Service (PM2)

In `/var/www/drive-clinic/api`:
```bash
cd /var/www/drive-clinic/api
npm install --omit=dev
cp .env.example .env
# Edit .env with your production MySQL credentials
```

Start and persist the API server using PM2:
```bash
pm2 start server.js --name "drive-clinic-api"
pm2 save
pm2 startup
```

---

## 4. Nginx Reverse Proxy & PHP-FPM Configuration

Create `/etc/nginx/sites-available/drive-clinic`:

```nginx
server {
    listen 80;
    server_name thedriveclinic.com www.thedriveclinic.com;

    root /var/www/drive-clinic/web/public;
    index index.php index.html;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # 1. Reverse Proxy Node.js API under /api/ (Zero CORS issues)
    location /api/ {
        proxy_pass http://127.0.0.1:5000/api/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;

        client_max_body_size 10k;
    }

    # 2. Static Assets Caching
    location ~* \.(css|js|svg|jpg|jpeg|png|webp|avif|ico|woff2|woff|ttf)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
        try_files $uri =404;
    }

    # 3. PHP Server-Rendered Site Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # 4. PHP-FPM Execution
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files (.env, .git, etc.)
    location ~ /\. {
        deny all;
    }
}
```

Enable the configuration:
```bash
sudo ln -s /etc/nginx/sites-available/drive-clinic /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 5. SSL Certificate Setup (Let's Encrypt)

```bash
sudo certbot --nginx -d thedriveclinic.com -d www.thedriveclinic.com
```

---

## 6. Apache `.htaccess` Setup (Alternative)

If using Apache instead of Nginx, the provided [`/web/public/.htaccess`](file:///d:/Car%20detailin%202.0/drive-clinic/web/public/.htaccess) handles clean URLs and security headers automatically when `mod_rewrite` is enabled.

To proxy `/api` requests to Node.js in Apache, add:
```apache
<IfModule mod_proxy.c>
    ProxyPreserveHost On
    ProxyPass /api http://127.0.0.1:5000/api
    ProxyPassReverse /api http://127.0.0.1:5000/api
</IfModule>
```
