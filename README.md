# MFG Dashboard

A web-based business intelligence and data synchronization system for Mode Fashion Group retail operations. Built with Yii Framework, it provides real-time visibility into store revenue, inventory tracking, and seamless data synchronization between warehouses, stores, and cashier systems.

## 🚀 Features

### Core Functionality
- **Revenue Management**: Track daily revenue per store with date range filtering
- **Sales Analytics**: Sales recaps, weekly reports, and global summaries
- **Inventory Tracking**: Monitor item history, distribution, and stock levels
- **Data Synchronization**: Real-time sync between warehouse (Sisgud), cashier (Sikasir), and central server
- **Reporting**: Generate revenue reports with chart visualizations
- **User Management**: Role-based access control (RBAC) with session management

### Key Capabilities
- ✅ Multi-store revenue tracking and grouping
- ✅ Item distribution management
- ✅ Sales transaction history
- ✅ Store IP address tracking
- ✅ Automated data synchronization
- ✅ Interactive charts and visualizations
- ✅ Secure authentication and authorization

## 🛠 Tech Stack

- **Framework**: Yii 1.1.x (PHP)
- **Database**: MySQL
- **Language**: PHP 8.3+
- **Web Server**: Nginx + PHP-FPM
- **Frontend**: Chart.js, jQuery
- **Container**: Docker
- **Orchestration**: Kubernetes

## 📋 Prerequisites

- PHP 8.3+ with required extensions (pdo_mysql, gd, zip, mcrypt, redis)
- MySQL 5.7+ or 8.0+
- Composer (for dependencies)
- Docker & Docker Compose (for containerized deployment)
- Kubernetes cluster (for K8s deployment)

## 🏃 Quick Start

### Local Development

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd mfg-dashboard
   ```

2. **Configure database**
   - Create MySQL database: `mode_dashboard`
   - Import schema: `docs/dashboard.sql`
   - Update `protected/config/main.php` with database credentials

3. **Set up Yii Framework**
   - Ensure Yii framework is available at `../../framework/yii.php` (relative to index.php)
   - Or update the path in `index.php`

4. **Configure application**
   - Copy `protected/config/main.php` and update database settings
   - Set up environment variables (see [KUBERNETES_ENV.md](KUBERNETES_ENV.md))

5. **Run with Docker Compose**
   ```bash
   docker-compose up -d
   ```

6. **Access the application**
   - Open browser: `http://localhost`
   - Default credentials: Check `docs/daftar_auth.txt`

### Docker Deployment

```bash
# Build image
docker build -t mfg-dashboard:latest -f build/Dockerfile .

# Run container
docker run -d -p 80:80 \
  -e DB_HOST=mysql-host \
  -e DB_NAME=mode_dashboard \
  -e DB_USER=user \
  -e DB_PASSWORD=password \
  mfg-dashboard:latest
```

## ⚙️ Configuration

### Environment Variables

The application supports configuration via environment variables for Kubernetes deployment:

**Database Configuration:**
- `DB_HOST` - MySQL hostname
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASSWORD` - Database password
- `DB_CHARSET` - Character set (default: utf8)
- `DB_PORT` - Database port (optional)

**Application Parameters:**
- `APP_SALT` - Password encryption salt
- `APP_TIMEZONE` - Application timezone (default: Asia/Jakarta)
- `APP_TIMEOUT` - Session timeout in seconds (default: 900)
- `APP_ADMIN_EMAIL` - Administrator email
- `APP_COMPANY_NAME` - Company name
- `APP_PAGINATION_SIZE` - Pagination size (default: 10)

See [KUBERNETES_ENV.md](KUBERNETES_ENV.md) for complete documentation.

### Configuration Files

- `protected/config/main.php` - Main application configuration
- `protected/config/console.php` - Console application configuration
- `protected/config/test.php` - Test environment configuration

## 🚢 Deployment

### Kubernetes Deployment

Complete Kubernetes manifests are available in the `k8s/` directory.

**Quick Deploy:**
```bash
# 1. Create secrets
kubectl create secret generic mfg-dashboard-secret \
  --from-literal=DB_HOST=mysql-service \
  --from-literal=DB_PASSWORD=your-password \
  --from-literal=APP_SALT=your-salt \
  --namespace=mfg-dashboard

# 2. Deploy application
kubectl apply -f k8s/
```

See [k8s/README.md](k8s/README.md) and [k8s/DEPLOYMENT_GUIDE.md](k8s/DEPLOYMENT_GUIDE.md) for detailed instructions.

### CI/CD

The project uses GitHub Actions for automated builds and deployments.

**Setup:**
1. Add Docker Hub secrets to GitHub:
   - `DOCKER_USERNAME`
   - `DOCKER_PASSWORD`

2. Push a tag to trigger build:
   ```bash
   git tag v1.0.0
   git push origin v1.0.0
   ```

See [.github/DOCKER_HUB_SETUP.md](.github/DOCKER_HUB_SETUP.md) for setup instructions.

## 📁 Project Structure

```
mfg-dashboard/
├── protected/          # Application core
│   ├── config/        # Configuration files
│   ├── controllers/   # MVC Controllers
│   ├── models/        # Data models
│   ├── views/         # View templates
│   └── components/    # Custom components
├── themes/            # UI themes (ace, classic)
├── css/              # Stylesheets
├── images/           # Static images
├── k8s/              # Kubernetes manifests
├── build/            # Build files and Dockerfiles
├── docs/             # Documentation and SQL files
└── .github/          # GitHub Actions workflows
```

## 🔧 Development

### Running Tests

```bash
# PHPUnit tests
phpunit tests/
```

### Code Generation

Gii code generator is available at:
- URL: `http://localhost/index.php?r=gii`
- Password: `123456` (change in production!)

### Database Migrations

Import database schema:
```bash
mysql -u user -p mode_dashboard < docs/dashboard.sql
```

## 📚 Documentation

- **[KUBERNETES_ENV.md](KUBERNETES_ENV.md)** - Environment variables reference
- **[k8s/README.md](k8s/README.md)** - Kubernetes deployment guide
- **[k8s/DEPLOYMENT_GUIDE.md](k8s/DEPLOYMENT_GUIDE.md)** - Detailed deployment instructions
- **[GITHUB_ACTIONS_MIGRATION.md](GITHUB_ACTIONS_MIGRATION.md)** - CI/CD migration guide
- **[.github/DOCKER_HUB_SETUP.md](.github/DOCKER_HUB_SETUP.md)** - Docker Hub setup
- **[memory-bank/](memory-bank/)** - Project documentation and context

## 🔐 Security

- Role-based access control (RBAC)
- Session timeout management (15 minutes default)
- Password encryption with custom salt
- Secure API endpoints for synchronization
- Input validation and sanitization

**Important**: Change default passwords and secrets before production deployment!

## 🤝 Contributing

1. Create a feature branch
2. Make your changes
3. Test thoroughly
4. Submit a pull request

## 📝 License

Copyright © Mode Fashion Group. All Rights Reserved.

## 🆘 Support

For issues, questions, or contributions:
- Check documentation in `docs/` and `memory-bank/`
- Review Kubernetes deployment guides in `k8s/`
- Check CI/CD setup in `.github/`

## 🔄 Version History

- **v2.0** - Kubernetes support, environment variable configuration
- **v1.0** - Initial release with core functionality

---

**Built with ❤️ for Mode Fashion Group**
