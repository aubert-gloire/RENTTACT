# RENTTACT - Property Rental Management System

RENTTACT is a web-based property rental management system that connects landlords and tenants. It provides an easy-to-use platform for property listing, searching, and management.

## Features

- **User Authentication**
  - Separate landlord and tenant roles
  - Secure login and registration
  - Password hashing for security

- **Property Management**
  - Property listing with images
  - Property details including price, location, and amenities
  - Edit and delete property listings
  - Property search functionality

- **User Features**
  - Favorite properties system
  - Contact property owners
  - View property details
  - Search properties by location and price

## Requirements for setting up the project

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- XAMPP (recommended) or similar local development environment (DOWNLOAD)

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/aubert-gloire/RENTTACT.git
   ```

2. **Database Setup**
   - Start MySQL server
   - Create a new database:
     ```sql
     CREATE DATABASE renttact;
     ```
   - Import the database schema:
     ```bash
     mysql -u root renttact < database.sql
     ```

3. **Configure Database Connection**
   - Navigate to `includes/config.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'renttact');
     ```

4. **File Permissions**
   - Ensure the `uploads` directory is writable:
     ```bash
     chmod 755 uploads
     chmod 755 uploads/properties
     ```

5. **Web Server Setup**
   - If using XAMPP:
     - Place the project in `htdocs` directory
     - Start Apache and MySQL services
     - Access the site at `http://localhost/renttact`

## Directory Structure

```
renttact/
├── admin/              # Admin/Landlord dashboard
├── assets/            # CSS, JS, and images
├── includes/          # PHP includes and functions
├── uploads/           # User uploaded files
│   ├── properties/    # Property images
├── database.sql       # Database schema
├── index.php         # Homepage
└── README.md         # This file
```

## Usage

1. **Register an Account**
   - Choose between Landlord or Tenant role
   - Fill in required information
   - Login with your credentials

2. **For Landlords**
   - Access dashboard at `/admin/dashboard.php`
   - Add new properties with images
   - Manage existing properties
   - View tenant inquiries

3. **For Tenants**
   - Search properties
   - View property details
   - Save favorite properties
   - Contact landlords

## Security Features

- Password hashing using PHP's password_hash()
- SQL injection prevention
- XSS protection
- File upload validation
- User role verification

## Contributing

1. Fork the repository
2. Create a new branch
3. Make your changes
4. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue in the GitHub repository or contact [your-email@example.com].
