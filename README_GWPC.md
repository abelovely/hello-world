# General Wingate Polytechnic College - Employee Management System

A comprehensive web application for managing employees, supervisors, supervision comments, and efficiency scoring.

## Features

### Admin Features
- **Employee Management**: Add, edit, delete, and view all employees
- **Supervisor Management**: Manage supervisor accounts
- **Efficiency Points**: Create and manage evaluation criteria with customizable weights
- **Semester Management**: Create and manage academic semesters
- **Comprehensive Reports**: View employee performance reports with PDF export

### Supervisor Features
- **View Assigned Employees**: See all employees under supervision
- **Supervision Comments**: Record supervision observations and comments
- **Efficiency Scoring**: Submit scores for each efficiency point per semester
- **Automated Calculations**: System automatically computes weighted scores
- **Reports**: Generate performance reports for assigned employees

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3, jQuery, Font Awesome
- **Libraries**: DataTables, html2pdf (for PDF export)

## Installation Instructions

### 1. Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- PDO PHP Extension

### 2. Database Setup

```bash
# Import the database schema
mysql -u root -p < database/schema.sql
```

Or manually create the database:
1. Open phpMyAdmin or MySQL client
2. Create a new database named `gwpc_employee_management`
3. Import the `database/schema.sql` file

### 3. Configuration

Edit `config/database.php` and update the database credentials:

```php
private $host = 'localhost';
private $db_name = 'gwpc_employee_management';
private $username = 'root';
private $password = '';
```

### 4. File Permissions

Ensure proper permissions for web server:

```bash
chmod -R 755 /path/to/hello-world
chown -R www-data:www-data /path/to/hello-world
```

### 5. Access the Application

Open your browser and navigate to:
```
http://localhost/hello-world/login.php
```

## Default Credentials

### Administrator
- **Username**: admin
- **Password**: admin123

### Supervisor (Sample)
- **Username**: supervisor1
- **Password**: supervisor123

**Important**: Change these passwords after first login!

## Directory Structure

```
hello-world/
├── admin/                  # Admin pages
│   ├── dashboard.php
│   ├── employees.php
│   ├── employee_form.php
│   ├── supervisors.php
│   ├── supervisor_form.php
│   ├── efficiency_points.php
│   ├── efficiency_point_form.php
│   ├── semesters.php
│   ├── semester_form.php
│   └── reports.php
├── supervisor/             # Supervisor pages
│   ├── dashboard.php
│   ├── my_employees.php
│   ├── supervision.php
│   ├── scoring.php
│   └── reports.php
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── config/                 # Configuration files
│   ├── config.php
│   ├── database.php
│   └── helpers.php
├── database/               # Database files
│   └── schema.sql
├── models/                 # Data models
│   ├── User.php
│   ├── Employee.php
│   ├── EfficiencyPoint.php
│   ├── Semester.php
│   ├── SupervisionComment.php
│   └── EfficiencyScore.php
├── views/                  # View templates
│   └── layouts/
│       ├── header.php
│       ├── footer.php
│       └── navbar.php
├── index.php              # Main entry point
├── login.php              # Login page
└── logout.php             # Logout handler
```

## Database Schema

### Main Tables

1. **users** - Admin and supervisor accounts
2. **employees** - Employee records
3. **efficiency_points** - Evaluation criteria with weights
4. **semesters** - Academic semesters
5. **supervision_comments** - Supervision observations
6. **efficiency_scores** - Employee scores with auto-calculated weighted scores

### Sample Data

The schema includes sample data:
- 1 Admin user
- 3 Supervisor users
- 5 Sample employees
- 8 Efficiency points (Teaching Quality, Punctuality, etc.)
- 3 Semesters
- Sample supervision comments and scores

## Usage Guide

### For Administrators

1. **Login** using admin credentials
2. **Add Employees**: Navigate to Employees > Add New
3. **Assign Supervisors**: Edit employee and select supervisor
4. **Create Efficiency Points**: Define evaluation criteria with weights
5. **Manage Semesters**: Create and activate semesters
6. **View Reports**: Access comprehensive reports for all employees

### For Supervisors

1. **Login** using supervisor credentials
2. **View My Employees**: See all assigned employees
3. **Add Supervision Comments**: Record observations during supervision
4. **Submit Efficiency Scores**: Score employees on each efficiency point
5. **View Reports**: Generate performance reports

### Efficiency Scoring System

- Each efficiency point has a **weight** (importance factor)
- Supervisors score employees on each point (0-100)
- System automatically calculates: `Weighted Score = Score × Weight`
- **Total Score** = Sum of all weighted scores / Sum of all weights
- Reports show both individual point scores and overall performance

## Security Features

- **Password Hashing**: Using PHP `password_hash()` with bcrypt
- **Prepared Statements**: All database queries use PDO prepared statements
- **CSRF Protection**: Token validation on all forms
- **Session Management**: Secure session handling with HttpOnly cookies
- **Input Sanitization**: All user inputs are sanitized
- **Role-Based Access**: Separate admin and supervisor access levels

## API/Integration

This system is designed as a standalone application but can be extended with:
- RESTful API endpoints
- JSON data export
- Integration with external systems
- Single Sign-On (SSO) support

## Troubleshooting

### Database Connection Error
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database user permissions

### Permission Denied
- Check file permissions: `chmod -R 755 /path/to/hello-world`
- Verify web server ownership: `chown -R www-data:www-data`

### Page Not Found
- Check Apache mod_rewrite is enabled
- Verify BASE_URL in `config/config.php`
- Ensure .htaccess file exists (if using Apache)

### Login Failed
- Verify database is properly set up
- Check sample data was imported correctly
- Use default credentials: admin/admin123

## Future Enhancements

Potential improvements and features:
- Email notifications for supervisors
- Multi-factor authentication
- Advanced analytics and charts
- Mobile responsive optimization
- Export to Excel functionality
- Automated report scheduling
- Document attachment support
- Performance trend analysis
- Employee self-service portal

## Support

For issues or questions:
- Check the database/schema.sql file for data structure
- Review config/helpers.php for available functions
- Examine models for database operations
- Refer to views/layouts for UI components

## License

This project is developed for General Wingate Polytechnic College.

## Version

Version 1.0.0 - Initial Release

## Credits

Developed as a comprehensive employee management and evaluation system for educational institutions.
