# Architecture Documentation - Attendance Management Portal

## Project Overview

The Attendance Management Portal is built using a custom PHP MVC (Model-View-Controller) architecture without any external frameworks. This ensures lightweight performance, full control over the codebase, and easy customization.

## Directory Structure

```
attendance-portal/
├── index.php                 # Front controller (entry point)
├── .htaccess                 # Apache URL rewriting rules
├── database.sql              # Database schema and sample data
├── README.md                 # Project documentation
├── DEPLOYMENT.md             # Deployment instructions
├── ARCHITECTURE.md           # This file
│
├── app/                      # Application logic
│   ├── controllers/          # Controller classes
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── AdminController.php
│   │   ├── TeacherController.php
│   │   ├── StudentController.php
│   │   └── ApiController.php
│   │
│   ├── models/               # Model classes (database layer)
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── Attendance.php
│   │   ├── ClassModel.php
│   │   └── Subject.php
│   │
│   ├── views/                # View templates
│   │   ├── layout/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   ├── auth/
│   │   │   └── login.php
│   │   ├── dashboard/
│   │   │   ├── admin.php
│   │   │   ├── teacher.php
│   │   │   └── student.php
│   │   ├── admin/
│   │   │   ├── users.php
│   │   │   └── reports.php
│   │   ├── teacher/
│   │   │   └── mark-attendance.php
│   │   └── student/
│   │       └── attendance.php
│   │
│   └── core/                 # Core framework classes
│       ├── Router.php        # URL routing
│       └── Controller.php    # Base controller
│
└── config/                   # Configuration files
    └── database.php          # Database connection and configuration
```

## MVC Architecture Explanation

### Model Layer
**Location**: `app/models/`

Models handle all database operations and business logic:

- **User.php**: Authentication, user management
- **Student.php**: Student profile management, attendance statistics
- **Teacher.php**: Teacher profile, class assignments
- **Attendance.php**: Attendance marking, reporting, statistics
- **ClassModel.php**: Class management
- **Subject.php**: Subject management

**Key Features**:
- PDO prepared statements for SQL injection prevention
- Centralized database connection through singleton pattern
- Business logic encapsulation
- Data validation and sanitization

### View Layer
**Location**: `app/views/`

Views handle presentation logic:

- **Layout system**: Common header/footer templates
- **Role-based views**: Different interfaces for admin, teacher, student
- **Responsive design**: Bootstrap 5 for mobile-friendly UI
- **Security**: XSS prevention through proper escaping

**Key Features**:
- Template inheritance with header/footer
- Clean separation of HTML and PHP logic
- Bootstrap components for consistent UI
- JavaScript for enhanced user experience

### Controller Layer
**Location**: `app/controllers/`

Controllers handle request processing and coordinate between models and views:

- **AuthController**: Login/logout functionality
- **DashboardController**: Role-based dashboard routing
- **AdminController**: User, class, subject management
- **TeacherController**: Attendance marking interface
- **StudentController**: Student attendance viewing
- **ApiController**: AJAX endpoints and CSV exports

**Key Features**:
- Role-based access control
- CSRF protection
- Input validation and sanitization
- Session management

## Core Components

### 1. Front Controller Pattern
**File**: `index.php`

Single entry point for all requests:
- Autoloading of classes
- Route definition and dispatching
- Global configuration loading
- Error handling

### 2. Router
**File**: `app/core/Router.php`

Simple URL routing system:
- Clean URL support via .htaccess
- Controller@method syntax
- Parameter extraction
- 404 handling

### 3. Base Controller
**File**: `app/core/Controller.php`

Common functionality for all controllers:
- Authentication checks
- Role-based access control
- View rendering
- CSRF protection
- URL generation helpers

### 4. Database Layer
**File**: `config/database.php`

Centralized database management:
- Singleton pattern for connection reuse
- PDO with prepared statements
- Error handling and logging
- Query helper methods

## Security Features

### 1. Authentication & Authorization
- Password hashing using PHP's `password_hash()`
- Session-based authentication
- Role-based access control (RBAC)
- Automatic session timeout

### 2. Input Security
- PDO prepared statements prevent SQL injection
- Input validation and sanitization
- XSS prevention through proper output escaping
- CSRF token protection for forms

### 3. File Security
- .htaccess rules to protect sensitive files
- Proper file permissions
- No direct file access to PHP files
- Security headers

## Database Design

### Core Tables

1. **users**: Authentication and role management
2. **students**: Student profiles linked to users
3. **teachers**: Teacher profiles linked to users
4. **classes**: Class/grade definitions
5. **subjects**: Subject definitions
6. **teacher_assignments**: Many-to-many relationship for teacher-class-subject
7. **attendance**: Daily attendance records

### Key Relationships
- Users → Students/Teachers (1:1)
- Students → Classes (N:1)
- Teachers → Assignments → Classes/Subjects (N:M)
- Students → Attendance Records (1:N)

### Indexes for Performance
```sql
CREATE INDEX idx_attendance_date ON attendance(date);
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_attendance_class_subject ON attendance(class_id, subject_id);
```

## Request Flow

### 1. Request Processing
```
User Request → .htaccess → index.php → Router → Controller → Model → Database
                                                     ↓
User Response ← View ← Controller ← Model ← Database Response
```

### 2. Authentication Flow
```
Login Request → AuthController → User Model → Database Verification
                     ↓
Session Creation → Role Detection → Dashboard Redirect
```

### 3. Attendance Marking Flow
```
Teacher Request → TeacherController → Verify Assignment → Load Students
                        ↓
Form Submission → Validation → Attendance Model → Database Update
                        ↓
Success Response → Redirect with Message
```

## API Endpoints

### AJAX Endpoints
- `POST /api/save-attendance`: Save attendance via AJAX
- `GET /api/export-csv`: Export attendance reports as CSV

### Export Formats
- Student attendance reports
- Class attendance summaries
- Daily attendance reports
- Monthly statistics

## Performance Considerations

### 1. Database Optimization
- Proper indexing on frequently queried columns
- Efficient JOIN queries
- Pagination for large datasets
- Connection pooling through singleton pattern

### 2. Caching Strategy
- Static asset caching via .htaccess
- Session-based user data caching
- Query result caching for reports

### 3. Frontend Optimization
- CDN for Bootstrap and FontAwesome
- Minified CSS/JS in production
- Image optimization
- Gzip compression

## Scalability Features

### 1. Modular Design
- Easy to add new roles and permissions
- Pluggable authentication systems
- Extensible reporting system
- Configurable business rules

### 2. Database Scalability
- Normalized database design
- Efficient query patterns
- Support for read replicas
- Horizontal scaling ready

### 3. Code Maintainability
- Clear separation of concerns
- Consistent coding standards
- Comprehensive error handling
- Extensive documentation

## Testing Strategy

### 1. Manual Testing
- Role-based functionality testing
- Cross-browser compatibility
- Mobile responsiveness
- Security vulnerability testing

### 2. Data Validation
- Input sanitization testing
- SQL injection prevention
- XSS protection verification
- CSRF token validation

### 3. Performance Testing
- Database query optimization
- Page load time analysis
- Concurrent user testing
- Memory usage monitoring

## Deployment Considerations

### 1. Environment Configuration
- Separate config files for dev/staging/production
- Environment-specific database credentials
- Debug mode toggles
- Error logging configuration

### 2. Security Hardening
- File permission restrictions
- Database user privileges
- SSL certificate installation
- Security header configuration

### 3. Monitoring
- Error log monitoring
- Performance metrics tracking
- User activity logging
- Database performance monitoring

## Future Enhancements

### 1. Technical Improvements
- RESTful API development
- Real-time notifications
- Mobile app integration
- Advanced reporting dashboard

### 2. Feature Additions
- Bulk attendance import/export
- Parent portal access
- SMS/Email notifications
- Advanced analytics and insights

### 3. Integration Possibilities
- LMS integration
- Student information systems
- Biometric attendance devices
- Mobile attendance apps