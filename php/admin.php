<?php
/**
 * Taxi Meeya Admin Dashboard API
 * Handle admin operations like fetching bookings, updating status, and getting statistics
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

class AdminManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all bookings for admin dashboard
     */
    public function getBookings($limit = 50, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    b.id,
                    b.booking_reference,
                    b.passenger_name,
                    b.phone_number,
                    b.pickup_location,
                    b.destination,
                    b.vehicle_type,
                    b.estimated_fare,
                    b.booking_status,
                    b.created_at,
                    b.updated_at
                FROM bookings b 
                ORDER BY b.created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $bookings,
                'count' => count($bookings)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch bookings: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get dashboard statistics
     */
    public function getStatistics() {
        try {
            // Total bookings
            $totalBookings = $this->conn->query("SELECT COUNT(*) as count FROM bookings")->fetch()['count'];
            
            // Today's bookings
            $todayBookings = $this->conn->query("
                SELECT COUNT(*) as count FROM bookings 
                WHERE DATE(created_at) = CURDATE()
            ")->fetch()['count'];
            
            // This month's revenue
            $monthlyRevenue = $this->conn->query("
                SELECT COALESCE(SUM(estimated_fare), 0) as revenue FROM bookings 
                WHERE YEAR(created_at) = YEAR(CURDATE()) 
                AND MONTH(created_at) = MONTH(CURDATE())
                AND booking_status IN ('confirmed', 'completed')
            ")->fetch()['revenue'];
            
            // Active drivers (simplified - just a count for demo)
            $activeDrivers = $this->conn->query("SELECT COUNT(*) as count FROM drivers WHERE driver_status = 'available'")->fetch()['count'];
            
            // Status breakdown
            $statusBreakdown = [];
            $statusQuery = $this->conn->query("
                SELECT booking_status, COUNT(*) as count 
                FROM bookings 
                GROUP BY booking_status
            ");
            
            while ($row = $statusQuery->fetch()) {
                $statusBreakdown[$row['booking_status']] = (int)$row['count'];
            }
            
            return [
                'success' => true,
                'data' => [
                    'total_bookings' => (int)$totalBookings,
                    'today_bookings' => (int)$todayBookings,
                    'monthly_revenue' => (float)$monthlyRevenue,
                    'active_drivers' => (int)$activeDrivers,
                    'status_breakdown' => $statusBreakdown
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update booking status
     */
    public function updateBookingStatus($bookingId, $newStatus) {
        try {
            // Validate status
            $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            if (!in_array($newStatus, $validStatuses)) {
                return [
                    'success' => false,
                    'message' => 'Invalid status. Must be one of: ' . implode(', ', $validStatuses)
                ];
            }
            
            // Check if booking exists
            $checkStmt = $this->conn->prepare("SELECT id, booking_reference, booking_status FROM bookings WHERE id = :id");
            $checkStmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            $booking = $checkStmt->fetch();
            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }
            
            // Update the booking status
            $updateStmt = $this->conn->prepare("
                UPDATE bookings 
                SET booking_status = :status, updated_at = NOW() 
                WHERE id = :id
            ");
            
            $updateStmt->bindValue(':status', $newStatus);
            $updateStmt->bindValue(':id', $bookingId, PDO::PARAM_INT);
            
            if ($updateStmt->execute()) {
                // Log the status change (optional)
                $this->logStatusChange($bookingId, $booking['booking_status'], $newStatus);
                
                return [
                    'success' => true,
                    'message' => "Booking {$booking['booking_reference']} status updated to {$newStatus}",
                    'data' => [
                        'booking_id' => $bookingId,
                        'old_status' => $booking['booking_status'],
                        'new_status' => $newStatus,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update booking status'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Log status changes for audit trail
     */
    private function logStatusChange($bookingId, $oldStatus, $newStatus) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO booking_tracking (booking_id, status, notes, created_at) 
                VALUES (:booking_id, :status, :notes, NOW())
            ");
            
            $notes = "Status changed from {$oldStatus} to {$newStatus} by admin";
            
            $stmt->bindValue(':booking_id', $bookingId);
            $stmt->bindValue(':status', $newStatus);
            $stmt->bindValue(':notes', $notes);
            
            $stmt->execute();
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log("Failed to log status change: " . $e->getMessage());
        }
    }
    
    /**
     * Get recent activities/changes
     */
    public function getRecentActivities($limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    bt.id,
                    bt.booking_id,
                    b.booking_reference,
                    bt.status,
                    bt.notes,
                    bt.created_at
                FROM booking_tracking bt
                JOIN bookings b ON bt.booking_id = b.id
                ORDER BY bt.created_at DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $activities
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ];
        }
    }
}

// Main request handler
try {
    $adminManager = new AdminManager($pdo);
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? null;
    
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'bookings':
                    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
                    $result = $adminManager->getBookings($limit, $offset);
                    break;
                    
                case 'statistics':
                    $result = $adminManager->getStatistics();
                    break;
                    
                case 'activities':
                    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                    $result = $adminManager->getRecentActivities($limit);
                    break;
                    
                default:
                    $result = [
                        'success' => false,
                        'message' => 'Invalid action. Supported actions: bookings, statistics, activities'
                    ];
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? null;
            
            if ($action === 'updateStatus') {
                $bookingId = $input['bookingId'] ?? null;
                $status = $input['status'] ?? null;
                
                if (!$bookingId || !$status) {
                    $result = [
                        'success' => false,
                        'message' => 'Missing bookingId or status'
                    ];
                } else {
                    $result = $adminManager->updateBookingStatus($bookingId, $status);
                }
            } else {
                $result = [
                    'success' => false,
                    'message' => 'Invalid action for PUT method. Expected: updateStatus'
                ];
            }
            break;
            
        default:
            $result = [
                'success' => false,
                'message' => 'Method not allowed'
            ];
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
