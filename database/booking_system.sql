-- Taxi Meeya Booking System Database Tables

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    passenger_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    pickup_location TEXT NOT NULL,
    destination TEXT NOT NULL,
    pickup_date DATE NOT NULL,
    pickup_time TIME NOT NULL,
    vehicle_type ENUM('motorbike', 'tuktuk', 'car') NOT NULL,
    estimated_fare DECIMAL(10, 2) NOT NULL,
    actual_fare DECIMAL(10, 2) DEFAULT NULL,
    special_requests TEXT,
    booking_status ENUM('pending', 'confirmed', 'driver_assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    driver_id INT DEFAULT NULL,
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cash', 'card', 'mobile') DEFAULT 'cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    rating INT DEFAULT NULL CHECK (rating >= 1 AND rating <= 5),
    feedback TEXT,
    
    INDEX idx_booking_reference (booking_reference),
    INDEX idx_passenger_phone (phone_number),
    INDEX idx_booking_date (pickup_date),
    INDEX idx_booking_status (booking_status),
    INDEX idx_driver_id (driver_id),
    INDEX idx_created_at (created_at)
);

-- Create drivers table
CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) UNIQUE NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    vehicle_type ENUM('motorbike', 'tuktuk', 'car') NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    vehicle_model VARCHAR(100),
    driver_status ENUM('available', 'busy', 'offline') DEFAULT 'available',
    rating DECIMAL(3, 2) DEFAULT 5.00,
    total_rides INT DEFAULT 0,
    profile_image VARCHAR(255),
    emergency_contact VARCHAR(20),
    address TEXT,
    date_joined DATE NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_driver_phone (phone_number),
    INDEX idx_vehicle_type (vehicle_type),
    INDEX idx_driver_status (driver_status),
    INDEX idx_rating (rating)
);

-- Create customers table (optional - for registered users)
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone_number VARCHAR(20) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    profile_image VARCHAR(255),
    total_bookings INT DEFAULT 0,
    favorite_locations TEXT,
    emergency_contact VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_customer_email (email),
    INDEX idx_customer_phone (phone_number)
);

-- Create booking_tracking table for real-time tracking and status changes
CREATE TABLE IF NOT EXISTS booking_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT,
    driver_latitude DECIMAL(10, 8),
    driver_longitude DECIMAL(11, 8),
    estimated_arrival TIME,
    distance_remaining DECIMAL(5, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_tracking (booking_id),
    INDEX idx_tracking_time (created_at),
    INDEX idx_status (status)
);

-- Create vehicle_pricing table
CREATE TABLE IF NOT EXISTS vehicle_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_type ENUM('motorbike', 'tuktuk', 'car') NOT NULL,
    base_fare DECIMAL(6, 2) NOT NULL,
    per_km_rate DECIMAL(6, 2) NOT NULL,
    per_minute_rate DECIMAL(6, 2) DEFAULT 0.00,
    minimum_fare DECIMAL(6, 2) NOT NULL,
    peak_hour_multiplier DECIMAL(3, 2) DEFAULT 1.00,
    night_time_multiplier DECIMAL(3, 2) DEFAULT 1.00,
    is_active BOOLEAN DEFAULT TRUE,
    effective_from DATE NOT NULL,
    effective_until DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_vehicle_pricing_type (vehicle_type),
    INDEX idx_pricing_dates (effective_from, effective_until)
);

-- Insert default pricing data
INSERT INTO vehicle_pricing (vehicle_type, base_fare, per_km_rate, minimum_fare, effective_from) VALUES
('motorbike', 150.00, 25.00, 150.00, CURDATE()),
('tuktuk', 200.00, 35.00, 200.00, CURDATE()),
('car', 350.00, 50.00, 350.00, CURDATE());

-- Insert sample drivers
INSERT INTO drivers (driver_name, phone_number, license_number, vehicle_type, vehicle_number, vehicle_model, date_joined, is_verified) VALUES
('Kamal Perera', '+94771234567', 'DL001234567', 'car', 'CAB-1234', 'Toyota Axio', CURDATE(), TRUE),
('Sunil Fernando', '+94779876543', 'DL002345678', 'tuktuk', 'TUK-5678', 'Bajaj Three Wheeler', CURDATE(), TRUE),
('Nimal Silva', '+94775555555', 'DL003456789', 'motorbike', 'BIKE-9012', 'Honda CB 125', CURDATE(), TRUE),
('Chaminda Rajapaksa', '+94778888888', 'DL004567890', 'car', 'CAB-3456', 'Suzuki Alto', CURDATE(), TRUE),
('Pradeep Wickrama', '+94772222222', 'DL005678901', 'tuktuk', 'TUK-7890', 'Piaggio Ape', CURDATE(), TRUE);

-- Update drivers status randomly for demo
UPDATE drivers SET driver_status = 'available' WHERE id IN (1, 3, 5);
UPDATE drivers SET driver_status = 'busy' WHERE id IN (2, 4);

-- Useful SQL Queries for the application

-- 1. Get available drivers by vehicle type
-- SELECT * FROM drivers 
-- WHERE vehicle_type = 'car' AND driver_status = 'available' AND is_active = TRUE 
-- ORDER BY rating DESC, total_rides DESC;

-- 2. Get booking details with driver info
-- SELECT 
--     b.*, 
--     d.driver_name, 
--     d.phone_number as driver_phone, 
--     d.vehicle_number,
--     d.vehicle_model,
--     d.rating as driver_rating
-- FROM bookings b
-- LEFT JOIN drivers d ON b.driver_id = d.id
-- WHERE b.booking_reference = 'BOOK001';

-- 3. Get customer booking history
-- SELECT * FROM bookings 
-- WHERE phone_number = '+94771234567' 
-- ORDER BY created_at DESC;

-- 4. Calculate total revenue by vehicle type
-- SELECT 
--     vehicle_type,
--     COUNT(*) as total_bookings,
--     SUM(actual_fare) as total_revenue,
--     AVG(actual_fare) as average_fare
-- FROM bookings 
-- WHERE booking_status = 'completed' AND actual_fare IS NOT NULL
-- GROUP BY vehicle_type;

-- 5. Get daily booking statistics
-- SELECT 
--     DATE(created_at) as booking_date,
--     COUNT(*) as total_bookings,
--     SUM(CASE WHEN booking_status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
--     SUM(CASE WHEN booking_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
--     SUM(actual_fare) as daily_revenue
-- FROM bookings 
-- WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
-- GROUP BY DATE(created_at)
-- ORDER BY booking_date DESC;

-- 6. Get driver performance metrics
-- SELECT 
--     d.id,
--     d.driver_name,
--     d.vehicle_type,
--     COUNT(b.id) as total_assigned_bookings,
--     SUM(CASE WHEN b.booking_status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
--     AVG(b.rating) as average_customer_rating,
--     SUM(b.actual_fare) as total_earnings
-- FROM drivers d
-- LEFT JOIN bookings b ON d.id = b.driver_id
-- WHERE d.is_active = TRUE
-- GROUP BY d.id
-- ORDER BY average_customer_rating DESC, completed_bookings DESC;

COMMIT;
