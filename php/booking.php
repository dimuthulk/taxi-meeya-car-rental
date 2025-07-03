<?php
/**
 * Taxi Meeya Booking System
 * Handle ride booking requests from the frontend
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

class BookingManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Create a new booking
     */
    public function createBooking($bookingData) {
        try {
            // Generate unique booking reference
            $bookingReference = $this->generateBookingReference();
            
            // Validate input data
            $validation = $this->validateBookingData($bookingData);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message'],
                    'errors' => $validation['errors']
                ];
            }
            
            // Calculate estimated fare
            $estimatedFare = $this->calculateFare(
                $bookingData['vehicle_type'], 
                $bookingData['pickup_location'], 
                $bookingData['destination']
            );
            
            // Check for available drivers
            $availableDriver = $this->findAvailableDriver($bookingData['vehicle_type']);
            
            // Start transaction
            $this->conn->beginTransaction();
            
            // Insert booking
            $stmt = $this->conn->prepare("
                INSERT INTO bookings (
                    booking_reference, passenger_name, phone_number, pickup_location, 
                    destination, pickup_date, pickup_time, vehicle_type, estimated_fare, 
                    special_requests, booking_status, driver_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $bookingStatus = $availableDriver ? 'confirmed' : 'pending';
            $driverId = $availableDriver ? $availableDriver['id'] : null;
            
            $stmt->execute([
                $bookingReference,
                $bookingData['passenger_name'],
                $bookingData['phone_number'],
                $bookingData['pickup_location'],
                $bookingData['destination'],
                $bookingData['pickup_date'],
                $bookingData['pickup_time'],
                $bookingData['vehicle_type'],
                $estimatedFare,
                $bookingData['special_requests'] ?? '',
                $bookingStatus,
                $driverId
            ]);
            
            $bookingId = $this->conn->lastInsertId();
            
            // Update driver status if assigned
            if ($availableDriver) {
                $this->updateDriverStatus($availableDriver['id'], 'busy');
            }
            
            // Log the booking for tracking
            $this->logBookingActivity($bookingId, 'Booking created', $bookingStatus);
            
            $this->conn->commit();
            
            // Prepare response
            $response = [
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking' => [
                    'id' => $bookingId,
                    'reference' => $bookingReference,
                    'status' => $bookingStatus,
                    'estimated_fare' => $estimatedFare,
                    'driver' => $availableDriver,
                    'estimated_arrival' => $this->calculateEstimatedArrival()
                ]
            ];
            
            // Send confirmation SMS/email (implement as needed)
            $this->sendBookingConfirmation($bookingData, $bookingReference);
            
            return $response;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Booking Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to create booking. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get booking details by reference
     */
    public function getBooking($bookingReference) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    b.*,
                    d.driver_name,
                    d.phone_number as driver_phone,
                    d.vehicle_number,
                    d.vehicle_model,
                    d.rating as driver_rating
                FROM bookings b
                LEFT JOIN drivers d ON b.driver_id = d.id
                WHERE b.booking_reference = ?
            ");
            
            $stmt->execute([$bookingReference]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }
            
            return [
                'success' => true,
                'booking' => $booking
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve booking',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Update booking status
     */
    public function updateBookingStatus($bookingId, $status, $driverId = null) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE bookings 
                SET booking_status = ?, driver_id = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $stmt->execute([$status, $driverId, $bookingId]);
            
            $this->logBookingActivity($bookingId, "Status updated to: $status", $status);
            
            return ['success' => true, 'message' => 'Booking status updated'];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update booking status',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Cancel booking
     */
    public function cancelBooking($bookingId, $reason = '') {
        try {
            $this->conn->beginTransaction();
            
            // Get current booking
            $stmt = $this->conn->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                throw new Exception("Booking not found");
            }
            
            // Update booking status
            $stmt = $this->conn->prepare("
                UPDATE bookings 
                SET booking_status = 'cancelled', 
                    cancelled_at = CURRENT_TIMESTAMP,
                    cancellation_reason = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $stmt->execute([$reason, $bookingId]);
            
            // Free up driver if assigned
            if ($booking['driver_id']) {
                $this->updateDriverStatus($booking['driver_id'], 'available');
            }
            
            $this->logBookingActivity($bookingId, "Booking cancelled: $reason", 'cancelled');
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get customer booking history
     */
    public function getCustomerBookings($phoneNumber, $limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    b.*,
                    d.driver_name,
                    d.vehicle_number
                FROM bookings b
                LEFT JOIN drivers d ON b.driver_id = d.id
                WHERE b.phone_number = ?
                ORDER BY b.created_at DESC
                LIMIT ?
            ");
            
            $stmt->execute([$phoneNumber, $limit]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'bookings' => $bookings
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve booking history',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Private helper methods
     */
    private function generateBookingReference() {
        return 'TAXI' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    private function validateBookingData($data) {
        $errors = [];
        
        if (empty($data['passenger_name'])) {
            $errors[] = 'Passenger name is required';
        }
        
        if (empty($data['phone_number'])) {
            $errors[] = 'Phone number is required';
        } elseif (!preg_match('/^\+94[0-9]{9}$/', $data['phone_number'])) {
            $errors[] = 'Invalid phone number format';
        }
        
        if (empty($data['pickup_location'])) {
            $errors[] = 'Pickup location is required';
        }
        
        if (empty($data['destination'])) {
            $errors[] = 'Destination is required';
        }
        
        if (empty($data['pickup_date'])) {
            $errors[] = 'Pickup date is required';
        } elseif (strtotime($data['pickup_date']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Pickup date cannot be in the past';
        }
        
        if (empty($data['pickup_time'])) {
            $errors[] = 'Pickup time is required';
        }
        
        if (empty($data['vehicle_type']) || !in_array($data['vehicle_type'], ['motorbike', 'tuktuk', 'car'])) {
            $errors[] = 'Valid vehicle type is required';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'message' => empty($errors) ? 'Valid' : 'Validation failed'
        ];
    }
    
    private function calculateFare($vehicleType, $pickup, $destination) {
        // Get pricing from database
        $stmt = $this->conn->prepare("
            SELECT * FROM vehicle_pricing 
            WHERE vehicle_type = ? AND is_active = TRUE 
            ORDER BY effective_from DESC LIMIT 1
        ");
        
        $stmt->execute([$vehicleType]);
        $pricing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pricing) {
            // Fallback pricing
            $fallbackPricing = [
                'motorbike' => ['base_fare' => 150, 'per_km_rate' => 25],
                'tuktuk' => ['base_fare' => 200, 'per_km_rate' => 35],
                'car' => ['base_fare' => 350, 'per_km_rate' => 50]
            ];
            $pricing = $fallbackPricing[$vehicleType];
        }
        
        // Simulate distance calculation (in production, use Google Maps API)
        $estimatedDistance = $this->estimateDistance($pickup, $destination);
        
        $baseFare = $pricing['base_fare'];
        $distanceFare = $estimatedDistance * $pricing['per_km_rate'];
        $totalFare = $baseFare + $distanceFare;
        
        // Apply time-based multipliers if needed
        $currentHour = (int)date('H');
        if ($currentHour >= 22 || $currentHour <= 6) {
            $totalFare *= 1.2; // 20% night surcharge
        }
        
        return round($totalFare, 2);
    }
    
    private function estimateDistance($pickup, $destination) {
        // Simple distance estimation (replace with actual API in production)
        return mt_rand(5, 25); // 5-25 km
    }
    
    private function findAvailableDriver($vehicleType) {
        $stmt = $this->conn->prepare("
            SELECT * FROM drivers 
            WHERE vehicle_type = ? AND driver_status = 'available' AND is_active = TRUE 
            ORDER BY rating DESC, total_rides DESC 
            LIMIT 1
        ");
        
        $stmt->execute([$vehicleType]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function updateDriverStatus($driverId, $status) {
        $stmt = $this->conn->prepare("
            UPDATE drivers SET driver_status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?
        ");
        $stmt->execute([$status, $driverId]);
    }
    
    private function calculateEstimatedArrival() {
        // Estimate arrival time (5-15 minutes from now)
        $minutes = mt_rand(5, 15);
        return date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    }
    
    private function logBookingActivity($bookingId, $activity, $status) {
        // Log activity for tracking purposes
        error_log("Booking $bookingId: $activity (Status: $status)");
    }
    
    private function sendBookingConfirmation($bookingData, $bookingReference) {
        // Implement SMS/Email notification
        // This is a placeholder for actual notification service
        error_log("Booking confirmation sent for: $bookingReference");
    }
}

// Handle the request
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $bookingManager = new BookingManager($pdo);
    
    switch ($method) {
        case 'POST':
            // Create new booking
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            $result = $bookingManager->createBooking($input);
            echo json_encode($result);
            break;
            
        case 'GET':
            if (isset($_GET['reference'])) {
                // Get booking by reference
                $result = $bookingManager->getBooking($_GET['reference']);
                echo json_encode($result);
            } elseif (isset($_GET['phone'])) {
                // Get customer booking history
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $result = $bookingManager->getCustomerBookings($_GET['phone'], $limit);
                echo json_encode($result);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
