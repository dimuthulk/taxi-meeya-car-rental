# 🚖 Taxi Meeya - Modern Taxi Booking System

A comprehensive, modern taxi booking website built with PHP, MySQL, and vanilla JavaScript. Features a responsive frontend, robust backend, admin dashboard, and real-time booking management.

![Taxi Meeya](images/Taxi_Meeya.png)

## 🌟 Features

### Frontend Features
- **Modern Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices
- **Interactive Booking Form** - Real-time price calculation and vehicle selection
- **Location Autocomplete** - Enhanced user experience with location suggestions
- **User Profile Integration** - Modern navbar with user avatar and dropdown
- **Live Backend Status** - Real-time connectivity indicator
- **Demo Mode Fallback** - Graceful degradation when backend is unavailable

### Backend Features
- **RESTful API** - Clean PHP endpoints for booking management
- **MySQL Database** - Comprehensive schema with proper indexing
- **PDO Integration** - Secure database operations with prepared statements
- **Error Handling** - Robust error management and logging
- **CORS Support** - Cross-origin resource sharing for API access

### Admin Dashboard
- **Real-time Statistics** - Live booking counts, revenue, and performance metrics
- **Booking Management** - View, update, and track all bookings
- **Status Updates** - Change booking status (pending → confirmed → completed)
- **Activity Logging** - Audit trail for all admin actions
- **Auto-refresh** - Automatic data updates every 30 seconds

### Vehicle Types
- 🏍️ **Motorbike** - Quick and economical for short trips
- 🛺 **Tuk-tuk** - Traditional Sri Lankan transport
- 🚗 **Car** - Comfortable rides for longer distances

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Semantic markup and modern standards
- **CSS3** - Flexbox, Grid, animations, and responsive design
- **JavaScript (ES6+)** - Modern vanilla JavaScript with async/await
- **FontAwesome** - Professional icon library

### Backend
- **PHP 8.0+** - Server-side scripting
- **MySQL 8.0+** - Relational database management
- **PDO** - PHP Data Objects for secure database access

### Development Tools
- **XAMPP** - Local development environment
- **Git** - Version control
- **VS Code** - Code editor with extensions

## 📁 Project Structure

```
taxi-meeya-website/
├── 📄 index.html              # Homepage with hero section and features
├── 📄 ride.html               # Booking form and ride request page
├── 📄 admin.html              # Admin dashboard for booking management
├── 📄 login.html              # User authentication page
├── 📄 register.html           # User registration page
├── 📄 contact-us.php          # Contact form with PHP processing
├── 📁 css/
│   ├── style1.css             # Main stylesheet
│   ├── login.css              # Login page styles
│   ├── register.css           # Registration page styles
│   └── ContactStyles.css      # Contact page styles
├── 📁 js/
│   ├── ride.js                # Booking form logic and API integration
│   ├── script.js              # Homepage interactions
│   ├── auth.js                # Authentication handling
│   ├── login.js               # Login form logic
│   └── register.js            # Registration form logic
├── 📁 php/
│   ├── config.php             # Database configuration
│   ├── booking.php            # Booking API endpoints
│   ├── admin.php              # Admin dashboard API
│   ├── test.php               # Backend connectivity test
│   └── auth/                  # Authentication modules
├── 📁 database/
│   ├── booking_system.sql     # Main database schema
│   ├── create_tables.sql      # Table creation scripts
│   └── taximeeya.sql          # Sample data
├── 📁 images/                 # Website assets and vehicle images
└── 📁 fontawesome-free-5.15.3-web/  # Icon library
```

## 🚀 Installation & Setup

### Prerequisites
- **XAMPP** (or LAMP/WAMP/MAMP stack)
- **PHP 8.0+**
- **MySQL 8.0+**
- **Modern web browser**

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/taxi-meeya-website.git
cd taxi-meeya-website
```

### Step 2: Setup XAMPP
1. Install [XAMPP](https://www.apachefriends.org/)
2. Copy project folder to `C:\xampp\htdocs\`
3. Start Apache and MySQL services in XAMPP Control Panel

### Step 3: Database Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: `taxi_meeya`
3. Import schema: `database/booking_system.sql`
4. (Optional) Import sample data: `database/taximeeya.sql`

### Step 4: Configuration
1. Edit `php/config.php` if needed:
   ```php
   $host = 'localhost';
   $port = 3307; // Change if using different port
   $dbname = 'taxi_meeya';
   $username = 'root';
   $password = ''; // Set your MySQL password
   ```

### Step 5: Test Installation
1. Visit: `http://localhost/taxi-meeya-website/`
2. Test backend: `http://localhost/taxi-meeya-website/php/test.php`
3. Check admin dashboard: `http://localhost/taxi-meeya-website/admin.html`

## 📊 Database Schema

### Core Tables
- **`bookings`** - Main booking records with passenger details
- **`drivers`** - Driver information and vehicle details
- **`customers`** - Registered user accounts (optional)
- **`booking_tracking`** - Status changes and location tracking
- **`vehicle_pricing`** - Dynamic pricing configuration

### Key Features
- **Foreign Key Constraints** - Data integrity
- **Indexes** - Optimized query performance
- **Timestamps** - Automatic record tracking
- **Enum Types** - Consistent status values

## 🔌 API Endpoints

### Booking API (`php/booking.php`)
```http
POST /php/booking.php
# Create new booking

GET /php/booking.php?action=getBooking&id=123
# Retrieve specific booking

PUT /php/booking.php
# Update booking status
```

### Admin API (`php/admin.php`)
```http
GET /php/admin.php?action=bookings&limit=50
# Get all bookings

GET /php/admin.php?action=statistics
# Get dashboard statistics

GET /php/admin.php?action=activities
# Get recent activities

PUT /php/admin.php
# Update booking status
```

### Test Endpoint (`php/test.php`)
```http
GET /php/test.php
# Test backend connectivity
```

## 💻 Usage

### For Customers
1. **Browse Homepage** - View services and features
2. **Book a Ride** - Fill out the booking form on `/ride.html`
3. **Select Vehicle** - Choose from motorbike, tuk-tuk, or car
4. **Get Price Estimate** - Real-time fare calculation
5. **Submit Booking** - Receive confirmation with booking reference

### For Administrators
1. **Access Dashboard** - Visit `/admin.html`
2. **View Statistics** - Monitor bookings, revenue, and performance
3. **Manage Bookings** - Update status from pending to completed
4. **Track Activities** - View audit trail of all changes
5. **Auto-refresh** - Dashboard updates automatically

## 🔧 Configuration

### Backend Settings (`php/config.php`)
```php
// Database Configuration
$host = 'localhost';
$port = 3307;
$dbname = 'taxi_meeya';
$username = 'root';
$password = '';

// Error Reporting (Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Frontend Settings (`js/ride.js`)
```javascript
// API Configuration
const API_BASE_URL = '/taxi-meeya-website/php/';
const DEMO_MODE_TIMEOUT = 5000; // 5 seconds
const AUTO_REFRESH_INTERVAL = 30000; // 30 seconds
```

## 🎨 Customization

### Styling
- Edit `css/style1.css` for main theme
- Modify color scheme in CSS variables
- Update logo in `images/` folder
- Customize vehicle images in `images/vehicles/`

### Functionality
- Add payment gateway in `php/booking.php`
- Implement SMS notifications
- Add email confirmations
- Integrate Google Maps API
- Add user authentication system

## 🔒 Security Features

- **SQL Injection Prevention** - PDO prepared statements
- **XSS Protection** - Input sanitization and escaping
- **CORS Headers** - Controlled cross-origin access
- **Error Handling** - No sensitive data exposure
- **Input Validation** - Server-side data validation

## 🌐 Browser Support

- ✅ **Chrome 90+**
- ✅ **Firefox 88+**
- ✅ **Safari 14+**
- ✅ **Edge 90+**
- ✅ **Mobile browsers** (iOS Safari, Chrome Mobile)

## 📱 Responsive Design

- **Mobile First** - Optimized for mobile devices
- **Breakpoints** - 768px (tablet), 1024px (desktop)
- **Touch Friendly** - Large buttons and touch targets
- **Fast Loading** - Optimized images and CSS

## 🚀 Performance

- **Lightweight** - Minimal dependencies
- **Fast Loading** - Optimized assets
- **Caching** - Browser caching for static assets
- **Lazy Loading** - Images load on demand
- **Minified CSS** - Compressed stylesheets

## 🐛 Troubleshooting

### Common Issues

**Backend Connection Failed**
```bash
# Check XAMPP services
# Verify database credentials in config.php
# Test: http://localhost/taxi-meeya-website/php/test.php
```

**Database Errors**
```bash
# Import database schema: database/booking_system.sql
# Check MySQL service is running
# Verify table names match code
```

**Booking Form Issues**
```bash
# Check browser console for JavaScript errors
# Verify API endpoints are accessible
# Test in demo mode first
```

## 📈 Future Enhancements

### Planned Features
- [ ] **Real-time Tracking** - GPS integration for live tracking
- [ ] **Payment Gateway** - Stripe/PayPal integration
- [ ] **Mobile App** - React Native mobile application
- [ ] **Driver App** - Separate app for drivers
- [ ] **Push Notifications** - Real-time updates
- [ ] **Multi-language** - Sinhala, Tamil, English support
- [ ] **Analytics Dashboard** - Advanced reporting
- [ ] **Route Optimization** - AI-powered route planning

### Technical Improvements
- [ ] **Unit Testing** - PHPUnit test suite
- [ ] **Docker Support** - Containerized deployment
- [ ] **CI/CD Pipeline** - Automated testing and deployment
- [ ] **API Documentation** - Swagger/OpenAPI specs
- [ ] **Caching Layer** - Redis for performance
- [ ] **Load Balancing** - Multiple server support

## 🤝 Contributing

1. **Fork the repository**
2. **Create feature branch** (`git checkout -b feature/amazing-feature`)
3. **Commit changes** (`git commit -m 'Add amazing feature'`)
4. **Push to branch** (`git push origin feature/amazing-feature`)
5. **Open Pull Request**

### Development Guidelines
- Follow PSR-4 autoloading standards
- Use meaningful commit messages
- Add comments for complex logic
- Test on multiple browsers
- Update documentation

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Taxi Meeya Development Team**

- 🌐 Website: [https://taxi-meeya.com](https://taxi-meeya.com)
- 📧 Email: contact@taxi-meeya.com
- 📱 Phone: +94 77 123 4567

## 🙏 Acknowledgments

- **FontAwesome** - For beautiful icons
- **XAMPP Team** - For the development environment
- **PHP Community** - For excellent documentation
- **Stack Overflow** - For problem-solving support

## 📞 Support

For support, email contact@taxi-meeya.com or create an issue in the GitHub repository.

---

**Made with ❤️ in Sri Lanka** 🇱🇰

---

### Quick Start Commands

```bash
# Clone and setup
git clone https://github.com/yourusername/taxi-meeya-website.git
cd taxi-meeya-website

# Start XAMPP services
# Import database/booking_system.sql

# Test installation
curl http://localhost/taxi-meeya-website/php/test.php

# Access application
open http://localhost/taxi-meeya-website/
```

### Project Status: ✅ Production Ready

- ✅ Frontend complete
- ✅ Backend APIs working
- ✅ Database schema implemented
- ✅ Admin dashboard functional
- ✅ Error handling robust
- ✅ Mobile responsive
- ✅ Documentation complete
