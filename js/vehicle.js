document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vehicleForm');
    const messageDiv = document.getElementById('message');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic client-side validation
        const make = document.getElementById('make').value.trim();
        const model = document.getElementById('model').value.trim();
        const type = document.getElementById('type').value;
        const seats = document.getElementById('seats').value;
        const price = document.getElementById('price_per_day').value;
        const vehicleNumber = document.getElementById('vehicle_number').value.trim();
        
        if (!vehicleNumber || !make || !model || !type || !seats || !price) {
            showMessage('Please fill in all required fields.', 'error');
            return;
        }
        
        if (seats < 2 || seats > 20) {
            showMessage('Number of seats must be between 2 and 20.', 'error');
            return;
        }
        
        if (price <= 0) {
            showMessage('Price per day must be greater than 0.', 'error');
            return;
        }
        
        // If validation passes, submit the form
        const formData = new FormData(form);
        
        fetch('php/vehicle.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                form.reset();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('An error occurred while submitting the form.', 'error');
            console.error('Error:', error);
        });
    });
    
    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = 'message ' + type;
        messageDiv.style.display = 'block';
        
        // Hide message after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
});