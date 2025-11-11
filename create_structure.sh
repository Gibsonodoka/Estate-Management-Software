#!/bin/bash

# Laravel Estate Management System - Directory and File Generator
# Run this script from your Laravel project root: bash create_structure.sh

echo "🚀 Creating Laravel Estate Management Directory Structure..."
echo ""

# Create resources directories
echo "📁 Creating resources directories..."

# Views - Layouts
mkdir -p resources/views/layouts/partials

# Views - Admin
mkdir -p resources/views/admin/estates
mkdir -p resources/views/admin/properties
mkdir -p resources/views/admin/users
mkdir -p resources/views/admin/tenants
mkdir -p resources/views/admin/payments
mkdir -p resources/views/admin/maintenance

# Views - Auth
mkdir -p resources/views/auth

# Assets
mkdir -p resources/css
mkdir -p resources/js

echo "✅ Resources directories created!"
echo ""

# Create view files
echo "📄 Creating view files..."

# Layouts
touch resources/views/layouts/app.blade.php
touch resources/views/layouts/guest.blade.php
touch resources/views/layouts/partials/header.blade.php
touch resources/views/layouts/partials/sidebar.blade.php
touch resources/views/layouts/partials/footer.blade.php

# Admin - Dashboard
touch resources/views/admin/dashboard.blade.php

# Admin - Estates
touch resources/views/admin/estates/index.blade.php
touch resources/views/admin/estates/create.blade.php
touch resources/views/admin/estates/edit.blade.php
touch resources/views/admin/estates/show.blade.php

# Admin - Properties
touch resources/views/admin/properties/index.blade.php
touch resources/views/admin/properties/create.blade.php
touch resources/views/admin/properties/edit.blade.php

# Admin - Users
touch resources/views/admin/users/index.blade.php
touch resources/views/admin/users/show.blade.php

# Admin - Tenants
touch resources/views/admin/tenants/index.blade.php

# Admin - Payments
touch resources/views/admin/payments/index.blade.php

# Admin - Maintenance
touch resources/views/admin/maintenance/index.blade.php

# Auth
touch resources/views/auth/login.blade.php
touch resources/views/auth/register.blade.php

# Assets
touch resources/css/app.css
touch resources/js/app.js

echo "✅ View files created!"
echo ""

# Create controller directories
echo "📁 Creating controller directories..."
mkdir -p app/Http/Controllers/Admin

echo "✅ Controller directories created!"
echo ""

# Create controller files
echo "📄 Creating controller files..."
touch app/Http/Controllers/Admin/DashboardController.php
touch app/Http/Controllers/Admin/EstateController.php
touch app/Http/Controllers/Admin/PropertyController.php
touch app/Http/Controllers/Admin/UserController.php
touch app/Http/Controllers/Admin/TenantController.php
touch app/Http/Controllers/Admin/PaymentController.php
touch app/Http/Controllers/Admin/MaintenanceController.php

echo "✅ Controller files created!"
echo ""

# Display created structure
echo "📊 Directory Structure Created:"
echo ""
echo "resources/"
echo "├── views/"
echo "│   ├── layouts/"
echo "│   │   ├── app.blade.php"
echo "│   │   ├── guest.blade.php"
echo "│   │   └── partials/"
echo "│   │       ├── header.blade.php"
echo "│   │       ├── sidebar.blade.php"
echo "│   │       └── footer.blade.php"
echo "│   ├── admin/"
echo "│   │   ├── dashboard.blade.php"
echo "│   │   ├── estates/"
echo "│   │   │   ├── index.blade.php"
echo "│   │   │   ├── create.blade.php"
echo "│   │   │   ├── edit.blade.php"
echo "│   │   │   └── show.blade.php"
echo "│   │   ├── properties/"
echo "│   │   │   ├── index.blade.php"
echo "│   │   │   ├── create.blade.php"
echo "│   │   │   └── edit.blade.php"
echo "│   │   ├── users/"
echo "│   │   │   ├── index.blade.php"
echo "│   │   │   └── show.blade.php"
echo "│   │   ├── tenants/"
echo "│   │   │   └── index.blade.php"
echo "│   │   ├── payments/"
echo "│   │   │   └── index.blade.php"
echo "│   │   └── maintenance/"
echo "│   │       └── index.blade.php"
echo "│   └── auth/"
echo "│       ├── login.blade.php"
echo "│       └── register.blade.php"
echo "├── css/"
echo "│   └── app.css"
echo "└── js/"
echo "    └── app.js"
echo ""
echo "app/Http/Controllers/Admin/"
echo "├── DashboardController.php"
echo "├── EstateController.php"
echo "├── PropertyController.php"
echo "├── UserController.php"
echo "├── TenantController.php"
echo "├── PaymentController.php"
echo "└── MaintenanceController.php"
echo ""
echo "✅ All directories and files created successfully!"
echo ""
echo "💡 Next steps:"
echo "   1. Copy the code content into each file"
echo "   2. Update routes/web.php with your routes"
echo "   3. Run: composer install"
echo "   4. Run: npm install && npm run dev"
