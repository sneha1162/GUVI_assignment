# GUVI Assignment - User Management Web App

A complete responsive User Management Web App built for the GUVI assignment. This project strictly follows the provided constraints including separation of frontend/backend languages, jQuery AJAX-only communication, secure authentication, and multi-database storage.


## Features

- User registration and login with JWT authentication
- Profile management with bio and email updates
- Responsive UI with Bootstrap 5
- Secure password hashing and token-based authentication
- Session management with Redis token blacklisting
- Data storage split between MySQL (credentials) and MongoDB (profiles)

## Tech Stack

### Frontend
- HTML5
- CSS3 (with Inter font)
- Bootstrap 5
- jQuery 3.6.0

### Backend
- PHP 7.4+
- MySQL (user credentials)
- MongoDB (user profiles)
- Redis (token blacklisting)
- JWT (custom implementation)

## Project Structure

```
guvi-assignment/
├── index.html
├── css/
│   └── styles.css
├── js/
│   └── app.js
├── PHP/
│   ├── config.php
│   ├── helpers.php
│   ├── register.php
│   ├── login.php
│   ├── profile.php
│   └── logout.php
├── sql/
│   └── schema.sql
├── mongo/
│   └── seed_profiles.json
└── README.md
```

## Local Setup Instructions

### Prerequisites

1. **PHP 7.4 or higher** with extensions:
   - pdo_mysql
   - redis
   - mongodb

2. **MySQL 5.7+**
3. **Redis Server**
4. **MongoDB 4.0+**
5. **Web Server** (Apache/Nginx) or PHP built-in server

### Installation Steps

1. **Clone or download the project files**

2. **Install PHP dependencies** (if using Composer, though not required here):
   ```bash
   # No Composer dependencies for this project
   ```

3. **Setup MySQL Database**:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
   This creates the `guvi_db` database and `users` table.

4. **Setup MongoDB**:
   - Start MongoDB service
   - Create database `guvi_mongo`
   - Import sample profiles (optional):
     ```bash
     mongoimport --db guvi_mongo --collection profiles --file mongo/seed_profiles.json --jsonArray
     ```

5. **Setup Redis**:
   - Start Redis server (default port 6379)

6. **Configure Database Connections**:
   - Edit `PHP/config.php` with your database credentials if different from defaults

7. **Start Web Server**:
   - Using PHP built-in server:
     ```bash
     cd /path/to/project
     php -S localhost:8000
     ```
   - Or configure Apache/Nginx to serve the project directory

8. **Access the Application**:
   - Open `http://localhost:8000` in your browser

## Deployment Instructions

### Heroku Deployment (Not Recommended)

Heroku has limitations with Redis and MongoDB add-ons. Consider AWS or DigitalOcean instead.

### AWS EC2 Deployment

1. **Launch EC2 Instance**:
   - Choose Ubuntu 20.04 LTS
   - Configure security groups for HTTP/HTTPS

2. **Install Dependencies**:
   ```bash
   sudo apt update
   sudo apt install php php-mysql php-redis php-mongodb mysql-server redis-server mongodb
   ```

3. **Setup Databases**:
   - Follow MySQL, Redis, MongoDB setup from local instructions

4. **Deploy Code**:
   - Upload project files to `/var/www/html/`
   - Configure Apache/Nginx

5. **Environment Variables**:
   - Set production database credentials
   - Update `PHP/config.php` or use environment variables

### AWS RDS + ElastiCache + DocumentDB

For production scalability:
- Use RDS for MySQL
- ElastiCache for Redis
- DocumentDB for MongoDB
- Update connection strings in `config.php`

## Demo Login Credentials

After setup, you can register new users or use these demo credentials (if you import the seed data):

- **Username**: demo_user
- **Password**: demo123

*Note: Create this user through the registration form or manually insert into MySQL.*

## Security Features

- Passwords hashed with bcrypt
- JWT tokens with expiration
- Token blacklisting on logout
- Prepared statements for SQL queries
- Input validation and sanitization
- CORS headers configured

## API Endpoints

- `POST /PHP/register.php` - User registration
- `POST /PHP/login.php` - User login (returns JWT)
- `GET /PHP/profile.php` - Get user profile (requires JWT)
- `POST /PHP/profile.php` - Update user profile (requires JWT)
- `POST /PHP/logout.php` - Logout (blacklists JWT)

## Submission Instructions

1. Ensure all files are included and functional
2. Test registration, login, profile update, and logout
3. Verify responsive design on mobile/desktop
4. Check that no PHP is mixed with HTML/CSS/JS
5. Confirm JWT is stored in localStorage and validated server-side
6. Submit the complete project folder or ZIP file

## Troubleshooting

- **Connection Errors**: Verify database services are running and credentials are correct
- **CORS Issues**: Check browser console for CORS errors; ensure headers are set
- **JWT Errors**: Confirm token is sent in Authorization header as "Bearer <token>"
- **Profile Not Loading**: Ensure MongoDB is running and profile collection exists

## License

This project is for educational purposes as part of GUVI assignment.