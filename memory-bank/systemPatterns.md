# System Patterns: MFG Dashboard

## Architecture Overview

### Framework Pattern
- **MVC Architecture**: Yii 1.1.x framework
- **Active Record Pattern**: All models extend `CActiveRecord`
- **Controller Hierarchy**: All controllers extend custom `Controller` class
- **View Rendering**: Theme-based views with layouts

### Directory Structure
```
mfg-dashboard/
├── protected/          # Application core (protected from web access)
│   ├── controllers/   # MVC Controllers
│   ├── models/        # Data models (ActiveRecord)
│   ├── views/         # View templates
│   ├── components/    # Custom components
│   ├── config/        # Configuration files
│   └── extensions/    # Third-party extensions
├── themes/            # UI themes (ace, classic)
├── css/              # Stylesheets
├── images/           # Static images
└── docs/             # Documentation and SQL files
```

## Key Design Patterns

### 1. Base Controller Pattern
**Location**: `protected/components/Controller.php`

All controllers extend a custom `Controller` class that provides:
- **Session Management**: Automatic session validation and timeout
- **Access Control**: `checkIfHasAccess()` method for permission checking
- **Authentication**: `authenticate()` method for user verification
- **Auto-discovery**: Automatically registers controller actions in `AllController` model

**Key Methods**:
- `beforeAction()`: Validates session, checks timeout
- `hasValidSession()`: Validates session key and expiration
- `checkIfHasAccess()`: RBAC permission checking
- `authenticate()`: User credential verification

### 2. Active Record Pattern
All models extend `CActiveRecord` and follow Yii conventions:
- `model()`: Static factory method
- `tableName()`: Returns database table name
- `rules()`: Validation rules
- `relations()`: Model relationships
- `search()`: Returns `CActiveDataProvider` for grid views

**Example Models**:
- `StoreRevenue`: Revenue tracking
- `SoldItem`: Sales item tracking
- `ItemHistory`: Item movement history
- `Users`: User management

### 3. Synchronization Pattern
**Controllers**: `SyncronizeKasirController`, `SyncronizeGudangController`

**Pattern**:
1. HTTP Basic Authentication for API access
2. POST request with JSON data
3. Find or create pattern for data records
4. Return JSON status response

**Example Flow**:
```php
// 1. Authenticate
if($this->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))

// 2. Decode JSON data
$data = CJSON::decode($_POST['data']);

// 3. Find or create record
$model = Model::model()->findByAttributes([...]);
if(empty($model)) $model = new Model();

// 4. Save and respond
if($model->save())
    echo CJSON::encode(['status'=>'ok']);
```

### 4. Access Control Pattern
**RBAC Implementation**: Uses Yii's `CDbAuthManager`

**Tables**:
- `authitem`: Permissions/roles
- `authitemchild`: Role hierarchy
- `authassignment`: User-role assignments

**Access Check Pattern**:
```php
// In views/layouts
'visible'=>$this->checkIfHasAccess('controller','action')

// In controllers
if(!Yii::app()->user->checkAccess('controller_action'))
    throw new CHttpException(403);
```

### 5. Data Provider Pattern
Models use `CActiveDataProvider` for grid views:

```php
public function search()
{
    $criteria = new CDbCriteria;
    // ... build criteria ...
    return new CActiveDataProvider($this, [
        'criteria' => $criteria,
        'pagination' => ['pageSize' => 10]
    ]);
}
```

### 6. Theme Pattern
**Theme System**: Yii theme with override capability

**Structure**:
- `themes/ace/views/`: ACE theme views
- `themes/classic/views/`: Classic theme views
- Views in `protected/views/` are base templates

**Layout Hierarchy**:
- `main.php`: Main application layout
- `column1.php`: Single column layout
- `column2.php`: Two column layout
- `login.php`: Login page layout

### 7. Session Management Pattern
**Custom Session Tracking**: Uses `Session` model

**Flow**:
1. User logs in → Create `Session` record with `session_key`
2. Each request → Validate session key and expiration
3. Update `valid_until` timestamp on valid access
4. Timeout after 900 seconds (15 minutes) of inactivity
5. Clear session on logout or timeout

**Implementation**:
- `Controller::beforeAction()`: Validates session
- `Controller::hasValidSession()`: Checks session validity
- `UserIdentity`: Creates session on login

### 8. Password Encryption Pattern
**Custom Encryption**: `Users::encrypt()`

**Algorithm**:
1. SHA1 hash of password
2. Split hash and salt into chunks
3. Interleave chunks
4. Final SHA1 of interleaved string

**Usage**:
```php
$encrypted = Users::encrypt($password);
$user->passwd = $encrypted;
```

## Component Relationships

### Model Relationships
- `StoreRevenue` ↔ `Store` (via `store_code`)
- `SoldItem` ↔ `Store` (via `shop_code`)
- `ItemHistory` ↔ `Store` (via `store_code`)
- `Sales` ↔ `SaleItems` (one-to-many)

### Controller Dependencies
- All controllers → `Controller` (base class)
- `SiteController` → `StoreRevenue` model (dashboard)
- Sync controllers → Authentication middleware

### View Dependencies
- Views → Layouts (main, column1, column2)
- Grid views → `CGridView` widget
- Charts → `chartjs` extension

## Extension Usage

### Chart.js Integration
**Component**: `chartjs` (preloaded in config)
**Widget**: `chartjs.widgets.ChBars`
**Usage**: Revenue visualization on dashboard

### Select2 Integration
**Extension**: `select2`
**Usage**: Enhanced dropdown selects

### PHPExcel Integration
**Extension**: `phpexcel`
**Usage**: Excel export functionality

### TCPDF Integration
**Extension**: `tcpdf`
**Usage**: PDF generation

## Database Patterns

### Table Naming
- Singular nouns: `store`, `item`, `user`
- Junction tables: `sale_items`, `item_distribution`
- Auth tables: `authitem`, `authassignment`

### Primary Keys
- Standard: `id` (auto-increment integer)
- Composite keys: `authassignment` (itemname, userid)

### Timestamps
- `sync_time`: Unix timestamp for sync operations
- `last_updated`: Unix timestamp for last update
- `created_time`, `updated_time`: User creation/update times

## Security Patterns

### Authentication
- HTTP Basic Auth for sync endpoints
- Form-based auth for web interface
- Session-based state management

### Authorization
- RBAC with controller_action naming
- Permission checking in views and controllers
- Access rules in controller `accessRules()`

### Data Validation
- Model validation rules
- Input sanitization in controllers
- SQL injection prevention via parameterized queries
