-- Simple Taxi Booking Database
CREATE DATABASE IF NOT EXISTS taxi_meeya;
USE taxi_meeya;

-- Users table - basic user information
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL
);