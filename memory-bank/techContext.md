# Technical Context: MFG Dashboard

## Technology Stack

### Core Technologies
- **PHP**: Version 5.x (likely 5.3+ based on code patterns)
- **Framework**: Yii 1.1.x (references to Yii 1.1.12 in docs)
- **Database**: MySQL (database: `mode_dashboard`)
- **Web Server**: Apache (based on .htaccess files)

### Frontend Technologies
- **JavaScript**: jQuery (via Yii framework)
- **Charts**: Chart.js (via yii-chartjs extension)
- **CSS**: Custom stylesheets (main.css, form.css, etc.)
- **UI Components**: Select2, Chosen

### Third-Party Extensions
- **yii-chartjs**: Chart.js integration for visualizations
- **select2**: Enhanced select dropdowns
- **EChosen**: Alternative select enhancement
- **phpexcel**: Excel file generation
- **tcpdf**: PDF generation
- **yii-redactor**: Rich text editor
- **imperavi-redactor-widget**: Alternative rich text editor

## Development Environment

### Configuration Files
- **Main Config**: `protected/config/main.php`
- **Test Config**: `protected/config/test.php`
- **Console Config**: `protected/config/console.php`

### Key Configuration Settings
```php
// Application name
'name' => 'Dashboard v2'

// Language
'language' => 'id' // Indonesian

// Database
'connectionString' => 'mysql:host=localhost;dbname=mode_dashboard'
'username' => 'root'
'password' => 'bolo'

// Timezone
'timezone' => 'Asia/Jakarta'

// Session timeout
'timeout' => 900 // 15 minutes

// Pagination
'pagination' => ['size' => '10']
```

### Framework Path
- Yii framework expected at: `../../framework/yii.php` (relative to index.php)
- This suggests framework is outside the project directory

## Database Schema

### Core Tables

#### Authentication & Authorization
- `users`: User accounts
- `authitem`: RBAC items (roles, tasks, operations)
- `authitemchild`: RBAC hierarchy
- `authassignment`: User-role assignments
- `session`: Active user sessions

#### Store Management
- `store`: Store information (code, name, address, etc.)
- `store_ip`: Store IP address tracking
- `store_revenue`: Daily revenue per store
- `store_items`: Item inventory per store

#### Inventory & Sales
- `items`: Master item catalog
- `item_distribution`: Item distribution from warehouse to stores
- `sold_item`: Items sold per category/store
- `item_history`: Item movement history
- `item_history_gudang`: Warehouse item history
- `sales`: Sales transactions
- `sale_items`: Items in each sale

#### Supporting Tables
- `category`: Item categories
- `supplier`: Supplier information
- `comment`: News/announcements
- `all_controller`: Auto-discovered controller actions

### Database Relationships
- Stores have multiple revenue records (one per day)
- Sales have multiple sale items
- Items distributed to multiple stores
- Users have role assignments

## Development Tools

### Code Generation
- **Gii**: Yii code generator (enabled in config)
  - Password: `123456`
  - IP filter: `127.0.0.1`, `::1`

### Testing
- **PHPUnit**: Test framework (phpunit.xml present)
- **Test Database**: SQLite (`protected/data/testdrive.db`)

### Version Control
- **Mercurial**: `.hgignore` file present
- Git status shows master branch

## File Structure Patterns

### Controllers
- Naming: `{Entity}Controller.php`
- Location: `protected/controllers/`
- Pattern: Extend `Controller` base class

### Models
- Naming: `{Entity}.php` (singular)
- Location: `protected/models/`
- Pattern: Extend `CActiveRecord`

### Views
- Location: `protected/views/{controller}/`
- Common partials: `_form.php`, `_search.php`, `_view.php`
- Actions: `index.php`, `admin.php`, `create.php`, `update.php`, `view.php`

### Themes
- Location: `themes/{theme_name}/views/`
- Override structure mirrors `protected/views/`

## API Endpoints

### Synchronization Endpoints
All sync endpoints use HTTP Basic Authentication

#### Cashier Sync (`SyncronizeKasirController`)
- `/syncronizeKasir/syncIp`: Update store IP
- `/syncronizeKasir/syncRevenue`: Sync store revenue
- `/syncronizeKasir/syncStoreItems`: Sync store items
- `/syncronizeKasir/syncItemHistory`: Sync item history
- `/syncronizeKasir/syncSales`: Sync sales transactions

#### Warehouse Sync (`SyncronizeGudangController`)
- `/syncronizeGudang/syncItemDistribution`: Sync item distribution
- `/syncronizeGudang/syncSupplier`: Sync supplier data

## Security Considerations

### Password Security
- Custom encryption algorithm (not standard bcrypt/argon2)
- Uses SHA1 with salt interleaving
- Salt stored in config: `phubeThAspADReDRuRatreprEwUba2Hu`

### Session Security
- Session keys stored in database
- Timeout-based expiration (900 seconds)
- Session validation on every request

### Access Control
- RBAC implementation
- Controller-action based permissions
- Permission checking in views and controllers

## Performance Considerations

### Database
- Indexes on foreign keys and search fields
- Composite indexes for unique constraints
- Query optimization via CDbCriteria

### Caching
- No explicit caching layer visible
- Yii framework provides built-in caching support

### Pagination
- Default page size: 10 items
- Configurable per data provider

## Deployment Considerations

### Server Requirements
- PHP 5.x with required extensions
- MySQL database
- Apache web server
- Yii framework installed

### Configuration for New Server (from docs/daftar_auth.txt)
1. Install `sync_ip.sh` script
2. Install `sync_omset.sh` script
3. Install App Synchronizer:
   - Install unzip
   - Download Yii framework
   - Configure Yii
   - Install php5-curl
4. Update Sikasir to latest repository
5. Clone synchronizer repository

### Environment Variables
- Database credentials in `protected/config/main.php`
- Framework path in `index.php`
- Debug mode: `YII_DEBUG` (currently true)

## Known Technical Debt

### Security
- Custom password encryption (not industry standard)
- HTTP Basic Auth for sync (consider token-based)
- Debug mode enabled in production code

### Code Quality
- Some inline SQL queries (could use ActiveRecord)
- Mixed Indonesian/English in code
- Hardcoded values in some places

### Framework Version
- Using Yii 1.1.x (legacy, consider migration to Yii2 or modern framework)

## Dependencies

### PHP Extensions Required
- `curl`: For HTTP requests in sync operations
- `json`: For JSON encoding/decoding
- `pdo_mysql`: For database access
- `gd`: Likely for image processing

### External Dependencies
- Yii framework (external to project)
- Chart.js library (via extension)
- jQuery (via Yii framework)
