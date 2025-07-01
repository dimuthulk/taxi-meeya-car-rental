// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize booking form if exists
    if (document.getElementById('booking-form')) {
        initBookingForm();
    }
    
    // Load booking history if exists
    if (document.getElementById('booking-history')) {
        loadBookingHistory();
    }
});

// Initialize Booking Form
function initBookingForm() {
    const form = document.getElementById('booking-form');
    const vehicleId = new URLSearchParams(window.location.search).get('id');
    
    // Set vehicle ID in hidden field if exists
    if (vehicleId && document.getElementById('vehicle-id')) {
        document.getElementById('vehicle-id').value = vehicleId;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const vehicleId = document.getElementById('vehicle-id').value;
        const pickupDate = document.getElementById('pickup-date').value;
        const dropoffDate = document.getElementById('dropoff-date').value;
        const pickupLocation = document.getElementById('pickup-location').value;
        const dropoffLocation = document.getElementById('dropoff-location').value;
        const extras = [];
        
        // Get checked extras
        document.querySelectorAll('input[name="extras"]:checked').forEach(extra => {
            extras.push(extra.value);
        });
        
        // Validate form
        if (!pickupDate || !dropoffDate) {
            alert('Please select both pickup and dropoff dates!');
            return;
        }
        
        if (new Date(dropoffDate) <= new Date(pickupDate)) {
            alert('Dropoff date must be after pickup date!');
            return;
        }
        
        // Prepare booking data
        const bookingData = {
            vehicle_id: vehicleId,
            pickup_date: pickupDate,
            dropoff_date: dropoffDate,
            pickup_location: pickupLocation,
            dropoff_location: dropoffLocation,
            extras: extras
        };
        
        // Submit booking
        createBooking(bookingData);
    });
    
    // Set minimum dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('pickup-date').min = today;
    
    document.getElementById('pickup-date').addEventListener('change', function() {
        document.getElementById('dropoff-date').min = this.value;
    });
}

// Create Booking
function createBooking(bookingData) {
    const token = localStorage.getItem('authToken');
    
    fetch('php/bookings/create.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(bookingData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking successful! Your reservation ID is: ' + data.booking_id);
            window.location.href = 'bookings.html';
        } else {
            alert('Booking failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your booking.');
    });
}

// Load Booking History
function loadBookingHistory() {
    const token = localStorage.getItem('authToken');
    
    fetch('php/bookings/history.php', {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('booking-history');
        
        if (data.success && data.bookings.length > 0) {
            container.innerHTML = '';
            
            data.bookings.forEach(booking => {
                container.appendChild(createBookingCard(booking));
            });
        } else {
            container.innerHTML = '<p>You have no bookings yet.</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('booking-history').innerHTML = '<p>Error loading booking history.</p>';
    });
}

// Create Booking Card
function createBookingCard(booking) {
    const card = document.createElement('div');
    card.className = 'booking-card';
    
    const statusClass = booking.status.toLowerCase().replace(' ', '-');
    
    card.innerHTML = `
        <div class="booking-header">
            <h3>${booking.vehicle_make} ${booking.vehicle_model}</h3>
            <span class="status ${statusClass}">${booking.status}</span>
        </div>
        <div class="booking-details">
            <p><strong>Booking ID:</strong> ${booking.booking_id}</p>
            <p><strong>Dates:</strong> ${formatDate(booking.pickup_date)} to ${formatDate(booking.dropoff_date)}</p>
            <p><strong>Total:</strong> $${booking.total_price}</p>
        </div>
        <div class="booking-actions">
            ${booking.status === 'Upcoming' ? `
                <button class="modify-btn" data-id="${booking.booking_id}">Modify</button>
                <button class="cancel-btn" data-id="${booking.booking_id}">Cancel</button>
            ` : ''}
            <a href="booking-details.html?id=${booking.booking_id}" class="details-btn">Details</a>
        </div>
    `;
    
    // Add event listeners for buttons
    if (booking.status === 'Upcoming') {
        card.querySelector('.modify-btn').addEventListener('click', function() {
            modifyBooking(this.getAttribute('data-id'));
        });
        
        card.querySelector('.cancel-btn').addEventListener('click', function() {
            cancelBooking(this.getAttribute('data-id'));
        });
    }
    
    return card;
}

// Format Date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Modify Booking
function modifyBooking(bookingId) {
    // In a real implementation, this would open a modal or redirect to a modification page
    console.log('Modify booking:', bookingId);
    alert('Modify booking functionality would open here for ID: ' + bookingId);
}

// Cancel Booking
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        const token = localStorage.getItem('authToken');
        
        fetch('php/bookings/cancel.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking cancelled successfully.');
                loadBookingHistory();
            } else {
                alert('Failed to cancel booking: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the booking.');
        });
    }
}