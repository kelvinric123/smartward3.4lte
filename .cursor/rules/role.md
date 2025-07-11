# User Roles

The system implements a role-based access control (RBAC) system with the following user roles:

## Super Admin
- Full system access
- Can manage all users, roles, and system settings
- Can access all dashboards and features
- Route: `/admin/dashboard`

## Hospital Admin
- Manages hospital-related functionality
- Access to staff, departments, and consultants management
- Reports and analytics for hospital operations
- Route: `/hospital/dashboard`

## Consultant
- Medical specialist with advanced access
- Manages appointments, patients, and referrals
- Access to specialized medical features
- Route: `/consultant/dashboard`

## GP Doctor
- General Practitioner doctor access
- Patient management and medical records
- Creates referrals to specialists/consultants
- Manages prescriptions
- Route: `/doctor/dashboard`

## Booking Agent
- Manages appointment bookings
- Handles patient scheduling
- Basic patient record access
- Route: `/booking/dashboard`

## Implementation Details

The role system is implemented using:
- A `roles` table with name, slug, and description fields
- A many-to-many relationship between users and roles via `user_role` pivot table
- Role middleware (`CheckRole`) for securing routes
- Helper methods on the `User` model like `hasRole()`, `hasAnyRole()`, and `hasAllRoles()`

Routes are protected using the middleware alias `role` which can be applied to controllers or routes:

```php
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    // Protected routes
});
```

Or directly in controllers:

```php
$this->middleware(['auth', 'role:consultant,super-admin']);
``` 