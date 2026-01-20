# Project Brief: MFG Dashboard

## Overview
MFG Dashboard (Mode Fashion Group Dashboard) is a web-based business intelligence and data synchronization system built on the Yii PHP framework. It serves as a central hub for managing retail operations, tracking sales revenue, monitoring inventory, and synchronizing data between multiple store locations, warehouses, and cashier systems.

## Core Purpose
The dashboard provides real-time visibility into:
- Store revenue and sales performance across multiple locations
- Inventory tracking and item distribution
- Sales transaction data and item history
- Synchronization between warehouse (Sisgud), cashier (Sikasir), and intermediary server systems

## Key Business Requirements

### 1. Revenue Management
- Track daily revenue per store
- Aggregate revenue by store groups/coalitions (koalisi)
- Generate revenue reports with date range filtering
- Display revenue charts and visualizations

### 2. Data Synchronization
- **Warehouse Sync (Sisgud)**: Push item distribution data to intermediary server
- **Cashier Sync (Sikasir)**: Pull item distribution data from intermediary server
- **Store Sync**: Synchronize store IP addresses, revenue, items, sales, and item history
- Support for resumable synchronization (track progress, handle connection failures)
- Prevent data duplication during sync operations

### 3. Inventory Management
- Track items sold per store
- Monitor item history (warehouse, store, Jakarta locations)
- Manage item distribution from warehouse to stores
- Track item categories and stock levels

### 4. Reporting & Analytics
- Sales recap per store
- Weekly sales reports
- Global sales summaries
- Item category performance
- Revenue grouping by store coalitions

### 5. User Management & Security
- Role-based access control (RBAC) using Yii's CDbAuthManager
- Session management with timeout (900 seconds default)
- User authentication with encrypted passwords
- Access control per controller/action

## Technical Foundation
- **Framework**: Yii 1.1.x (PHP framework)
- **Database**: MySQL (mode_dashboard)
- **Language**: PHP 5.x
- **UI**: Custom themes (ace, classic) with Chart.js for visualizations

## Target Users
- Store managers
- Warehouse staff
- System administrators
- Business analysts/management

## Success Criteria
- Real-time revenue visibility across all stores
- Reliable data synchronization without duplication
- Secure access control for different user roles
- Comprehensive reporting for business decision-making
