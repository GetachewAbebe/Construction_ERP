Natanem Engineering ERP

A modular Laravel 12 ERP platform designed for construction companies to manage Inventory, Employees, Finance, and Item Lending workflows with built-in role-based access control.

📌 Features
🔐 Authentication

Custom login page (app.blade + home.blade).

Role-based dashboard routing.

Prevent-back-history middleware to avoid cached pages after logout.

👥 User & Role Management

Administrators can:

Create, edit, update, and delete users.

Assign roles (Administrator, Human Resource Manager, Inventory Manager, Financial Manager).

Uses Spatie Laravel Permission behind the scenes.

🏗 Modules
1️⃣ Human Resource Module

Manage Employees (CRUD).

Manage Leave Requests.

Admin approval system for submitted leave.

View approved leaves history.

2️⃣ Inventory Module

Manage inventory items (CRUD).

Track quantities dynamically.

Create item lending (loan) requests to employees.

View pending, approved, returned, and rejected loans.

Admin approval workflow:

Approve loan → deduct quantity.

Reject loan → no quantity change.

Mark returned → restore quantity.

3️⃣ Finance Module

Finance dashboard ready for expansion (invoices, payments, expenses).

4️⃣ Admin Module

System overview dashboard.

User management.

All approvals in one place

Leave approvals

Item lending approvals

🧱 Tech Stack
Category Technology
Backend Laravel 12 (PHP 8.2)
Frontend Blade, TailwindCSS
Database PostgreSQL
Permissions Spatie/laravel-permission
UI Tailwind, Bootstrap Icons
Authentication Laravel Auth
Logging Laravel logging + custom controllers
Deployment Laravel artisan + environment configs
📁 Project Structure
app/
 ├── Http/
 │    ├── Controllers/
 │    │     ├── AdminUserController.php
 │    │     ├── DashboardController.php
 │    │     ├── Admin/
 │    │     │     └── InventoryLoanApprovalController.php
 │    │     ├── HR/
 │    │     │     ├── EmployeeController.php
 │    │     │     └── LeaveRequestController.php
 │    │     ├── Inventory/
 │    │     │     ├── InventoryItemController.php
 │    │     │     └── InventoryLoanController.php
 │    ├── Middleware/
 │    │     ├── RoleMiddleware.php
 │    │     └── PreventBackHistory.php
 ├── Models/
 │    ├── User.php
 │    ├── Employee.php
 │    ├── InventoryItem.php
 │    ├── InventoryLoan.php
 │    └── EmployeeOnLeave.php
resources/
 ├── views/
 │    ├── layouts/app.blade.php
 │    ├── dashboards/
 │    ├── admin/
 │    ├── inventory/
 │    ├── hr/
 │    └── finance/
routes/
 └── web.php
