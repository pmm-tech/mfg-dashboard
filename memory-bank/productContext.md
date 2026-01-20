# Product Context: MFG Dashboard

## Why This Project Exists

### Business Problem
Mode Fashion Group operates multiple retail stores across different locations. Before this dashboard, the company faced challenges with:
- **Manual data collection**: Store revenue and sales data required manual collection via flash drives
- **Lack of real-time visibility**: No centralized view of business performance
- **Synchronization inefficiencies**: Data transfer between warehouse (Sisgud), cashier systems (Sikasir), and central server was manual and error-prone
- **Limited reporting**: Difficult to generate consolidated reports across stores

### Solution Provided
The MFG Dashboard solves these problems by:
1. **Automated Synchronization**: Eliminates need for flash drives by enabling online data sync
2. **Centralized Dashboard**: Provides real-time view of revenue, sales, and inventory across all stores
3. **Automated Reporting**: Generates reports for revenue, sales recaps, and item performance
4. **Data Integrity**: Prevents duplication and tracks synchronization progress

## How It Should Work

### User Experience Flow

#### 1. Store Manager/Admin Login
- User logs in with credentials
- Session timeout after 15 minutes of inactivity
- Role-based menu access based on permissions

#### 2. Dashboard Homepage
- Default view shows today's revenue by store
- Chart visualization of revenue by store group (koalisi)
- News/announcements section
- Search/filter by date range

#### 3. Revenue Management
- View revenue per store for selected date range
- See percentage contribution of each store
- Export/print revenue reports
- Group revenue by store coalitions

#### 4. Sales & Inventory Tracking
- View items sold per store
- Track item history (warehouse, store, Jakarta)
- Generate sales recaps (per store, weekly)
- Monitor item distribution

#### 5. Synchronization (Background Process)
- **From Warehouse (Sisgud)**: Push item distribution data per "bon" (delivery note)
- **To Cashier (Sikasir)**: Pull item distribution data from intermediary server
- Track which "bon" has been uploaded
- Resume sync if connection is interrupted

## User Experience Goals

### For Store Managers
- Quick access to their store's performance
- Easy-to-understand revenue charts
- Simple date range filtering

### For Administrators
- Comprehensive view of all stores
- Access to detailed reports
- User and permission management

### For System Integrators
- Reliable API endpoints for synchronization
- Clear error messages
- Progress tracking for large data transfers

## Key Features

### Core Features (Implemented)
1. ✅ Store revenue tracking and reporting
2. ✅ Sales item tracking
3. ✅ Item history management
4. ✅ Data synchronization endpoints
5. ✅ User authentication and authorization
6. ✅ Revenue grouping by store coalitions
7. ✅ Chart visualizations

### Planned Features (from docs/Fitur.txt)
- Enhanced synchronization with per-bon tracking
- Upload menu in Sisgud for data push
- List of unuploaded bons
- Better resume capability for interrupted syncs

## Integration Points

### External Systems
1. **Sisgud (Warehouse System)**: Pushes item distribution data
2. **Sikasir (Cashier System)**: Pulls item distribution data
3. **Intermediary Server**: Acts as data bridge between systems

### Data Flow
```
Sisgud → [Push] → Intermediary Server → [Pull] → Sikasir
                ↓
         MFG Dashboard (reads from intermediary DB)
```

## Business Value
- **Time Savings**: Eliminates manual data collection
- **Real-time Insights**: Immediate visibility into business performance
- **Data Accuracy**: Automated sync reduces human error
- **Scalability**: Easy to add new stores and locations
- **Decision Making**: Comprehensive reports support business decisions
