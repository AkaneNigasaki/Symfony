# Workspace Technical Specification

## Overview

This workspace contains a **School Management System** built with **Symfony 7/8** (PHP framework) and uses **Webpack Encore** for frontend asset management. The application manages schools, teachers, students, courses, classrooms, schedules, assignments, enrollments, and grades.

**Complexity**: Medium - Full-stack web application with database integration, user authentication, and modern frontend tooling.

---

## Technology Stack

### Backend
- **Framework**: Symfony 7.4+ with Framework Bundle, Twig Bundle, Security Bundle
- **Runtime**: PHP (version not specified in config, likely PHP 8.1+)
- **Template Engine**: Twig 2.12/3.0+
- **Database**: 
  - Doctrine ORM 3.6+ with migrations
  - Configured for MySQL (school_management database)
  - Alternative SQLite support available
- **AI Integration**: Symfony AI Bundle 0.3.2 + AI Agent 0.3.0
- **External Service**: Ollama AI (http://localhost:11434)

### Frontend
- **Build Tool**: Symfony Webpack Encore 5.1+
- **Bundler**: Webpack 5.74+
- **JavaScript**: Babel with preset-env, ES6+ with polyfills (core-js 3.38)
- **CSS Framework**: Tailwind CSS 3.4+ with plugins:
  - @tailwindcss/forms
  - @tailwindcss/container-queries
- **PostCSS**: Autoprefixer for browser compatibility

### Development Tools
- **Testing**: PHPUnit 12.5+
- **Code Generation**: Symfony Maker Bundle 1.66+
- **Package Managers**: Composer (PHP), npm (JavaScript)
- **Docker**: Docker Compose configuration present

---

## Project Structure

```
D:\ai\
├── src/                          # Application source code
│   ├── Controller/              # MVC Controllers (10 controllers)
│   ├── Entity/                  # Doctrine ORM entities (11 entities)
│   ├── Repository/              # Database repositories (10 repositories)
│   ├── Service/                 # Business logic services
│   ├── Exception/               # Custom exceptions (empty)
│   └── Kernel.php              # Symfony kernel
│
├── templates/                    # Twig templates
│   ├── base.html.twig          # Base layout template
│   ├── activity/               # Activity views
│   ├── classroom/              # Classroom CRUD views
│   ├── course/                 # Course CRUD views
│   ├── home/                   # Homepage
│   ├── membership/             # Membership management
│   ├── schedule/               # Schedule views
│   ├── school/                 # School CRUD views
│   ├── security/               # Login/registration
│   ├── student/                # Student CRUD views
│   └── teacher/                # Teacher CRUD views
│
├── assets/                       # Frontend assets
│   ├── app.js                  # Main JavaScript entry point
│   └── styles/                 # CSS/styling files
│
├── config/                       # Symfony configuration
│   ├── packages/               # Bundle configurations
│   ├── routes/                 # Routing configuration
│   ├── bundles.php             # Registered bundles
│   ├── routes.yaml             # Routes definition
│   └── services.yaml           # Service container config
│
├── migrations/                   # Database migrations (4 versions)
├── public/                       # Web root directory
│   ├── index.php               # Front controller
│   └── [various PHP utility files]
│
├── tests/                        # PHPUnit tests
├── var/                          # Runtime files (cache, logs)
├── vendor/                       # Composer dependencies
├── node_modules/                 # npm dependencies
├── qwen-code-examples/          # AI code examples (git submodule)
│
├── .zenflow/                     # Zenflow task management
└── .ai/                          # AI-related configurations
```

---

## Key Application Components

### Domain Entities (11 total)
1. **User** - Base user entity with authentication
2. **Student** - Student-specific data
3. **Teacher** - Teacher-specific data
4. **School** - School information
5. **Classroom** - Physical/virtual classrooms
6. **Course** - Course definitions
7. **Schedule** - Class scheduling
8. **Assignment** - Student assignments
9. **Enrollment** - Student-course relationships
10. **Grade** - Student grades
11. **UserActivity** - Activity logging

### Controllers (10 total)
- ActivityController
- ClassroomController
- CourseController
- HomeController
- MembershipController
- RegistrationController
- ScheduleController
- SchoolController
- SecurityController (login/auth)
- StudentController
- TeacherController

### Services
- **ActivityLogger** - Tracks user activities

---

## Dependencies & Package Management

### PHP Dependencies (composer.json)
**Production:**
- symfony/framework-bundle ^7.4
- symfony/twig-bundle ^8.0
- symfony/webpack-encore-bundle ^2.4
- symfony/security-bundle
- symfony/ai-bundle ^0.3.2
- symfony/ai-agent ^0.3.0
- symfony/form ^8.0
- twig/extra-bundle, twig/twig, twig/intl-extra

**Development:**
- symfony/maker-bundle ^1.66
- symfony/validator ^8.0
- phpunit/phpunit ^12.5
- doctrine/doctrine-bundle ^3.2
- doctrine/orm ^3.6
- doctrine/doctrine-migrations-bundle ^4.0

### JavaScript Dependencies (package.json)
All devDependencies:
- @symfony/webpack-encore ^5.1.0
- tailwindcss ^3.4.0 with plugins
- @babel/core, @babel/preset-env
- webpack ^5.74.0, webpack-cli ^5.1.0
- postcss, autoprefixer
- core-js ^3.38.0, regenerator-runtime

---

## Configuration Files

### Environment (.env)
- **APP_ENV**: dev
- **APP_SECRET**: a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
- **DATABASE_URL**: MySQL connection to school_management database
- **OLLAMA_URL**: http://localhost:11434
- **DEFAULT_URI**: http://localhost

### Frontend Build (webpack.config.js)
- **Entry Point**: ./assets/app.js
- **Output**: public/build/
- **Features**: 
  - Single runtime chunk
  - Code splitting enabled
  - Source maps in dev mode
  - Versioning in production
  - PostCSS with Tailwind processing
  - Babel transpilation with polyfills

### Tailwind Configuration
- **Dark Mode**: Class-based
- **Content Sources**: templates/**/*.twig, assets/**/*.js
- **Custom Colors**: Primary purple (#8a2ce2), dark theme colors, neon effects
- **Custom Font**: Inter (display font)

### Docker (compose.yaml)
Docker Compose configuration present with override file for local development.

---

## Build & Development Scripts

### NPM Scripts (package.json)
```bash
npm run dev          # Build assets for development
npm run watch        # Watch and rebuild on changes
npm run build        # Production build with optimization
npm run dev-server   # Development server with HMR
```

### Composer Scripts
```bash
composer auto-scripts  # Clear cache, install assets
```

---

## Additional Files & Artifacts

### Public Directory Utilities
Multiple PHP utility files for database management and user operations:
- add_user.php, add_user_direct.php
- db_check.php, fix_db.php, verify_db.php
- user_check.php
- Various .log and .txt files (likely temporary/debug files)

### Git Submodule
- **qwen-code-examples/** - Contains AI-related code examples and skills (dashboard-builder, image-generate, youtube-transcript-extractor)

### Version Control
- Standard Symfony .gitignore excluding:
  - /vendor/, /node_modules/
  - /var/, /public/build/
  - Environment overrides (.env.local)

---

## Data Model Summary

The application implements a typical school management domain:
- **User Management**: Authentication, registration, user activities
- **Academic Structure**: Schools → Teachers/Students → Courses → Classrooms
- **Scheduling**: Schedule entities linking time, location, and courses
- **Assessments**: Assignments and Grades
- **Enrollment**: Many-to-many relationship between students and courses
- **Membership**: School membership requests and management

---

## AI Integration

The workspace includes:
1. **Symfony AI Bundle** with AI Agent support
2. **Ollama integration** (local LLM service)
3. **Qwen code examples** submodule with various AI skills
4. **MCP configuration** (mcp.json)
5. **Zenflow task management** (.zenflow/)

---

## Verification Approach

### Testing
- **Framework**: PHPUnit 12.5+ configured via phpunit.dist.xml
- **Test Directory**: tests/
- **Bootstrap**: tests/bootstrap.php

### Build Verification
```bash
# Frontend
npm run build        # Should compile without errors

# Backend
php bin/phpunit      # Run PHP tests
php bin/console cache:clear  # Verify Symfony config
```

### Runtime Verification
- Access http://localhost (configured DEFAULT_URI)
- Verify database connectivity (MySQL @ 127.0.0.1:3306)
- Check Ollama service availability (http://localhost:11434)

---

## Notes

1. **TypeScript/React**: Commented out in webpack config but not currently enabled
2. **Sass/SCSS**: Support available but not currently enabled
3. **Database**: Currently configured for MySQL but SQLite alternative exists
4. **Environment**: Project runs in development mode by default
5. **Stability**: Composer set to "prefer-stable" with "minimum-stability: dev"
6. **License**: Proprietary (composer.json), UNLICENSED (package.json)
