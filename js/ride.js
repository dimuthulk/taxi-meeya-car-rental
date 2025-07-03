// Ride Page JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Add helpful console messages for developers
    console.log('%c🚖 Taxi Meeya Booking System', 'color: #667eea; font-size: 16px; font-weight: bold;');
    console.log('%cFor developers:', 'color: #666; font-size: 14px;');
    console.log('1. Make sure XAMPP/Apache is running');
    console.log('2. Database should be accessible on localhost:3306 (or 3307)');
    console.log('3. Create database "taxi_meeya" and run the SQL from database/booking_system.sql');
    console.log('4. If backend fails, the system will use demo mode automatically');
    console.log('%c', 'color: initial;');

    // Form elements
    const rideForm = document.getElementById('rideBookingForm');
    const vehicleSelect = document.getElementById('vehicle-type');
    const estimatedPrice = document.getElementById('estimated-price');
    const pickupLocation = document.getElementById('pickup-location');
    const destination = document.getElementById('destination');
    const vehicleCards = document.querySelectorAll('.vehicle-card');
    const selectVehicleBtns = document.querySelectorAll('.select-vehicle-btn');

    // Set default date to today
    const dateInput = document.getElementById('ride-date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;
    dateInput.setAttribute('min', today);

    // Set default time to current time + 30 minutes
    const timeInput = document.getElementById('ride-time');
    const now = new Date();
    now.setMinutes(now.getMinutes() + 30);
    const timeString = now.toTimeString().slice(0, 5);
    timeInput.value = timeString;

    // Vehicle pricing (base rates in Rs.)
    const vehiclePricing = {
        motorbike: { base: 150, perKm: 25 },
        tuktuk: { base: 200, perKm: 35 },
        car: { base: 350, perKm: 50 }
    };

    // Calculate estimated price
    function calculatePrice() {
        const vehicleType = vehicleSelect.value;
        if (!vehicleType) {
            estimatedPrice.textContent = 'Rs. 0';
            return;
        }

        // Simulate distance calculation (in real app, use Google Maps API)
        const estimatedDistance = Math.floor(Math.random() * 20) + 5; // 5-25 km
        const pricing = vehiclePricing[vehicleType];
        const totalPrice = pricing.base + (estimatedDistance * pricing.perKm);
        
        estimatedPrice.textContent = `Rs. ${totalPrice.toLocaleString()}`;
    }

    // Vehicle selection from cards
    selectVehicleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const vehicleType = this.getAttribute('data-vehicle');
            vehicleSelect.value = vehicleType;
            
            // Update visual selection
            vehicleCards.forEach(card => card.classList.remove('selected'));
            this.closest('.vehicle-card').classList.add('selected');
            
            // Calculate price
            calculatePrice();
            
            // Scroll to form
            document.querySelector('.booking-form-container').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });
    });

    // Update price when vehicle type changes
    vehicleSelect.addEventListener('change', calculatePrice);

    // Auto-complete suggestions (mock data)
    const locationSuggestions = [
        'Colombo Fort Railway Station',
        'Bandaranaike International Airport',
        'University of Colombo',
        'Galle Face Green',
        'National Museum Colombo',
        'Independence Memorial Hall',
        'Viharamahadevi Park',
        'Mount Lavinia Beach',
        'Dehiwala Zoo',
        'Kelaniya Temple',
        'Negombo Beach',
        'Kandy City Center',
        'Temple of the Tooth',
        'Peradeniya Botanical Garden',
        'Nuwara Eliya',
        'Ella Railway Station',
        'Galle Fort',
        'Mirissa Beach',
        'Sigiriya Rock',
        'Anuradhapura Ancient City'
    ];

    // Simple autocomplete function
    function setupAutocomplete(input) {
        input.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            
            // Remove existing suggestions
            const existingSuggestions = document.querySelector('.suggestions');
            if (existingSuggestions) {
                existingSuggestions.remove();
            }
            
            if (value.length < 2) return;
            
            const matches = locationSuggestions.filter(location => 
                location.toLowerCase().includes(value)
            ).slice(0, 5);
            
            if (matches.length === 0) return;
            
            const suggestionDiv = document.createElement('div');
            suggestionDiv.className = 'suggestions';
            suggestionDiv.style.cssText = `
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #ddd;
                border-radius: 8px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            `;
            
            matches.forEach(match => {
                const suggestionItem = document.createElement('div');
                suggestionItem.textContent = match;
                suggestionItem.style.cssText = `
                    padding: 12px 15px;
                    cursor: pointer;
                    border-bottom: 1px solid #f0f0f0;
                    transition: background-color 0.2s;
                `;
                
                suggestionItem.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                
                suggestionItem.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'white';
                });
                
                suggestionItem.addEventListener('click', function() {
                    input.value = match;
                    suggestionDiv.remove();
                    calculatePrice();
                });
                
                suggestionDiv.appendChild(suggestionItem);
            });
            
            // Position relative to input
            const inputGroup = input.closest('.form-group');
            inputGroup.style.position = 'relative';
            inputGroup.appendChild(suggestionDiv);
        });
        
        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target)) {
                const suggestions = document.querySelector('.suggestions');
                if (suggestions) suggestions.remove();
            }
        });
    }

    // Setup autocomplete for location inputs
    setupAutocomplete(pickupLocation);
    setupAutocomplete(destination);

    // Form validation and submission
    rideForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const rideData = {
            passenger_name: document.getElementById('passenger-name').value,
            phone_number: document.getElementById('phone-number').value,
            pickup_location: pickupLocation.value,
            destination: destination.value,
            pickup_date: dateInput.value,
            pickup_time: timeInput.value,
            vehicle_type: vehicleSelect.value,
            special_requests: document.getElementById('special-requests').value
        };
        
        // Validate required fields
        if (!rideData.pickup_location || !rideData.destination || !rideData.vehicle_type || 
            !rideData.passenger_name || !rideData.phone_number) {
            showNotification('Please fill in all required fields', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('.book-now-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking Your Ride...';
        submitBtn.disabled = true;
        
        // Send booking request to backend
        // First, try to detect if we're in development mode
        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const bookingUrl = isLocalhost ? 'php/booking.php' : 'php/booking.php';
        
        // Check if backend is available first
        fetch(bookingUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(() => {
            // Backend is available, proceed with booking
            return fetch(bookingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(rideData)
            });
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message with booking details
                showBookingConfirmation(data.booking);
                
                // Reset form
                this.reset();
                dateInput.value = today;
                timeInput.value = timeString;
                estimatedPrice.textContent = 'Rs. 0';
                vehicleCards.forEach(card => card.classList.remove('selected'));
                
                // Store booking reference for tracking
                localStorage.setItem('lastBookingReference', data.booking.reference);
                
            } else {
                showNotification(data.message || 'Booking failed. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Booking error:', error);
            
            // Fallback to simulation mode for development/testing
            console.log('Backend not available, using simulation mode...');
            showNotification('Backend not available. Using demo mode...', 'info');
            
            // Simulate successful booking for testing
            setTimeout(() => {
                const simulatedBooking = {
                    id: Math.floor(Math.random() * 1000),
                    reference: 'TAXI' + Date.now().toString().slice(-8),
                    status: 'confirmed',
                    estimated_fare: parseFloat(estimatedPrice.textContent.replace('Rs. ', '').replace(',', '')) || 500,
                    driver: {
                        driver_name: 'Kamal Perera',
                        phone_number: '+94771234567',
                        vehicle_model: 'Toyota Axio',
                        vehicle_number: 'CAB-1234',
                        rating: 4.8
                    },
                    estimated_arrival: new Date(Date.now() + 10 * 60 * 1000).toISOString()
                };
                
                showBookingConfirmation(simulatedBooking);
                
                // Reset form
                this.reset();
                dateInput.value = today;
                timeInput.value = timeString;
                estimatedPrice.textContent = 'Rs. 0';
                vehicleCards.forEach(card => card.classList.remove('selected'));
                
                // Store booking reference for tracking
                localStorage.setItem('lastBookingReference', simulatedBooking.reference);
                
            }, 1500);
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            min-width: 300px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        `;
        
        // Set background color based on type
        switch(type) {
            case 'success':
                notification.style.background = 'linear-gradient(135deg, #4CAF50, #45a049)';
                break;
            case 'error':
                notification.style.background = 'linear-gradient(135deg, #f44336, #da190b)';
                break;
            default:
                notification.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
        }
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; margin-left: 15px;">×</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    // Show booking confirmation modal
    function showBookingConfirmation(booking) {
        const modal = document.createElement('div');
        modal.className = 'booking-confirmation-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        `;
        
        const driverInfo = booking.driver ? `
            <div class="driver-info">
                <h4><i class="fas fa-user"></i> Your Driver</h4>
                <p><strong>${booking.driver.driver_name}</strong></p>
                <p><i class="fas fa-phone"></i> ${booking.driver.phone_number}</p>
                <p><i class="fas fa-car"></i> ${booking.driver.vehicle_model} (${booking.driver.vehicle_number})</p>
                <p><i class="fas fa-star"></i> Rating: ${booking.driver.rating}/5.0</p>
            </div>
        ` : `
            <div class="driver-info">
                <h4><i class="fas fa-clock"></i> Driver Assignment</h4>
                <p>A driver will be assigned shortly. You'll receive a confirmation call within 2 minutes.</p>
            </div>
        `;
        
        modal.innerHTML = `
            <div class="confirmation-content" style="
                background: white;
                padding: 3rem;
                border-radius: 20px;
                max-width: 500px;
                width: 90%;
                text-align: center;
                animation: slideInUp 0.3s ease;
            ">
                <div class="success-icon" style="
                    width: 80px;
                    height: 80px;
                    background: linear-gradient(135deg, #4CAF50, #45a049);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 2rem;
                ">
                    <i class="fas fa-check" style="color: white; font-size: 2.5rem;"></i>
                </div>
                
                <h2 style="color: #333; margin-bottom: 1rem;">Booking Confirmed!</h2>
                <p style="color: #666; font-size: 1.4rem; margin-bottom: 2rem;">
                    Your ride has been successfully booked.
                </p>
                
                <div class="booking-details" style="
                    background: #f8f9fa;
                    padding: 2rem;
                    border-radius: 15px;
                    margin-bottom: 2rem;
                    text-align: left;
                ">
                    <h3 style="color: #333; margin-bottom: 1.5rem;">Booking Details</h3>
                    <p><strong>Reference:</strong> ${booking.reference}</p>
                    <p><strong>Estimated Fare:</strong> Rs. ${booking.estimated_fare.toLocaleString()}</p>
                    <p><strong>Status:</strong> <span style="color: #4CAF50; font-weight: bold;">${booking.status.toUpperCase()}</span></p>
                    ${booking.estimated_arrival ? `<p><strong>Estimated Arrival:</strong> ${new Date(booking.estimated_arrival).toLocaleTimeString()}</p>` : ''}
                    
                    ${driverInfo}
                </div>
                
                <div class="confirmation-actions" style="
                    display: flex;
                    gap: 1rem;
                    justify-content: center;
                ">
                    <button onclick="trackRide('${booking.reference}')" style="
                        padding: 1rem 2rem;
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white;
                        border: none;
                        border-radius: 10px;
                        font-size: 1.4rem;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-map-marker-alt"></i> Track Ride
                    </button>
                    
                    <button onclick="closeBookingModal()" style="
                        padding: 1rem 2rem;
                        background: #f8f9fa;
                        color: #333;
                        border: 2px solid #ddd;
                        border-radius: 10px;
                        font-size: 1.4rem;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    ">
                        Close
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Add close functionality
        window.closeBookingModal = function() {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                if (modal.parentElement) {
                    modal.remove();
                }
            }, 300);
        };
        
        // Close on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeBookingModal();
            }
        });
    }
    
    // Track ride function
    window.trackRide = function(bookingReference) {
        // Implement ride tracking (could open a new page or modal)
        showNotification(`Tracking ride ${bookingReference}...`, 'info');
        
        // Example: Open tracking page
        // window.open(`track.html?ref=${bookingReference}`, '_blank');
        
        // For now, just show a simple tracking simulation
        showTrackingModal(bookingReference);
    };
    
    // Show tracking modal (simple simulation)
    function showTrackingModal(bookingReference) {
        const modal = document.createElement('div');
        modal.className = 'tracking-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            animation: fadeIn 0.3s ease;
        `;
        
        modal.innerHTML = `
            <div class="tracking-content" style="
                background: white;
                padding: 3rem;
                border-radius: 20px;
                max-width: 400px;
                width: 90%;
                text-align: center;
            ">
                <h3 style="color: #333; margin-bottom: 2rem;">Live Tracking</h3>
                <div class="tracking-status" style="margin-bottom: 2rem;">
                    <div class="status-item" style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 1rem;
                        background: #f8f9fa;
                        border-radius: 10px;
                        margin-bottom: 1rem;
                    ">
                        <span>Driver En Route</span>
                        <i class="fas fa-check-circle" style="color: #4CAF50;"></i>
                    </div>
                    <div class="status-item" style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 1rem;
                        background: #fff3cd;
                        border-radius: 10px;
                        margin-bottom: 1rem;
                    ">
                        <span>Estimated Arrival: 8 mins</span>
                        <i class="fas fa-clock" style="color: #856404;"></i>
                    </div>
                </div>
                
                <button onclick="this.closest('.tracking-modal').remove()" style="
                    padding: 1rem 2rem;
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    border: none;
                    border-radius: 10px;
                    font-size: 1.4rem;
                    font-weight: 600;
                    cursor: pointer;
                    width: 100%;
                ">
                    Close Tracking
                </button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    // Check for existing booking and show quick access
    function checkExistingBooking() {
        const lastBookingRef = localStorage.getItem('lastBookingReference');
        if (lastBookingRef) {
            // Add quick access button to header
            const quickAccess = document.createElement('div');
            quickAccess.className = 'quick-booking-access';
            quickAccess.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                padding: 1rem;
                border-radius: 15px;
                cursor: pointer;
                box-shadow: 0 5px 20px rgba(0,0,0,0.3);
                z-index: 1000;
                animation: slideInRight 0.3s ease;
            `;
            
            quickAccess.innerHTML = `
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-taxi"></i>
                    <div>
                        <div style="font-weight: bold; font-size: 1.2rem;">Track Last Ride</div>
                        <div style="font-size: 1rem; opacity: 0.9;">${lastBookingRef}</div>
                    </div>
                </div>
            `;
            
            quickAccess.addEventListener('click', () => {
                trackRide(lastBookingRef);
            });
            
            document.body.appendChild(quickAccess);
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                if (quickAccess.parentElement) {
                    quickAccess.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => quickAccess.remove(), 300);
                }
            }, 10000);
        }
    }

    // Debug function to test backend connectivity
    function testBackendConnection() {
        const statusDiv = document.getElementById('backend-status');
        const statusIcon = document.getElementById('status-icon');
        const statusText = document.getElementById('status-text');
        
        if (statusDiv) {
            statusDiv.style.display = 'block';
            statusDiv.style.background = '#fff3cd';
            statusDiv.style.color = '#856404';
            statusIcon.style.color = '#ffc107';
            statusText.textContent = 'Testing backend connection...';
        }
        
        fetch('php/test.php')
            .then(response => response.json())
            .then(data => {
                console.log('Backend test result:', data);
                if (data.success) {
                    console.log('✅ Backend is working properly');
                    if (statusDiv) {
                        statusDiv.style.background = '#d4edda';
                        statusDiv.style.color = '#155724';
                        statusIcon.style.color = '#28a745';
                        statusText.textContent = '✅ Backend connected - Real bookings enabled';
                        
                        // Hide status after 3 seconds
                        setTimeout(() => {
                            statusDiv.style.display = 'none';
                        }, 3000);
                    }
                } else {
                    console.log('❌ Backend has issues:', data.message);
                    if (statusDiv) {
                        statusDiv.style.background = '#f8d7da';
                        statusDiv.style.color = '#721c24';
                        statusIcon.style.color = '#dc3545';
                        statusText.textContent = '⚠️ Backend issues - Demo mode active';
                    }
                }
            })
            .catch(error => {
                console.log('❌ Backend connection failed:', error);
                console.log('🔄 Will use simulation mode for bookings');
                if (statusDiv) {
                    statusDiv.style.background = '#f8d7da';
                    statusDiv.style.color = '#721c24';
                    statusIcon.style.color = '#dc3545';
                    statusText.textContent = '🔄 Backend offline - Demo mode active';
                }
            });
    }
    
    // Test backend on page load
    testBackendConnection();

    // Initial price calculation
    calculatePrice();
    
    // Check for existing bookings
    checkExistingBooking();
});

// Add CSS animations for modals and notifications
const style = document.createElement('style');
style.textContent = `
    .vehicle-card.selected {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.3);
        border: 3px solid #667eea;
    }
    
    .vehicle-card.selected .select-vehicle-btn {
        background: linear-gradient(135deg, #4CAF50, #45a049);
    }
    
    .vehicle-card.selected .select-vehicle-btn:hover {
        background: linear-gradient(135deg, #45a049, #3d8b40);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    @keyframes slideInUp {
        from { 
            transform: translateY(50px);
            opacity: 0;
        }
        to { 
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideInRight {
        from { 
            transform: translateX(100%);
            opacity: 0;
        }
        to { 
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from { 
            transform: translateX(0);
            opacity: 1;
        }
        to { 
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Enhance button hover effects */
    .confirmation-actions button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    /* Loading spinner animation */
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
