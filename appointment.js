// API Configuration
const API_URL = 'http://localhost:5000/api';

// DOM Elements
const appointmentForm = document.querySelector('.appointment-form form');
const doctorSelect = document.getElementById('doctor');
const serviceSelect = document.getElementById('service');

// Load doctors and services when page loads
document.addEventListener('DOMContentLoaded', async () => {
    try {
        await loadDoctors();
        await loadServices();
    } catch (error) {
        console.error('Error loading initial data:', error);
        showNotification('Error loading data. Please refresh the page.', 'error');
    }
});

// Load doctors from the API
async function loadDoctors() {
    try {
        const response = await fetch(`${API_URL}/users?role=doctor`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        const doctors = await response.json();
        
        // Clear existing options except the first one
        doctorSelect.innerHTML = '<option value="">-- Choose a Doctor --</option>';
        
        // Add doctor options
        doctors.forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor._id;
            option.textContent = `${doctor.name} (${doctor.specialization || 'General Practitioner'})`;
            doctorSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading doctors:', error);
        showNotification('Error loading doctors. Please try again.', 'error');
    }
}

// Load services from the API
async function loadServices() {
    try {
        const response = await fetch(`${API_URL}/services`);
        const services = await response.json();
        
        // Clear existing options except the first one
        serviceSelect.innerHTML = '<option value="">-- Choose a Service --</option>';
        
        // Add service options
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service._id;
            option.textContent = `${service.name} - $${service.price}`;
            serviceSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading services:', error);
        showNotification('Error loading services. Please try again.', 'error');
    }
}

// Handle form submission
appointmentForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Check if user is logged in
    const token = localStorage.getItem('token');
    if (!token) {
        showNotification('Please login to book an appointment', 'error');
        window.location.href = '/login.html';
        return;
    }

    const formData = {
        doctor: doctorSelect.value,
        service: serviceSelect.value,
        date: document.getElementById('date').value,
        time: document.getElementById('time').value,
        notes: document.getElementById('message').value
    };

    try {
        const response = await fetch(`${API_URL}/appointments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.ok) {
            showNotification('Appointment booked successfully!', 'success');
            appointmentForm.reset();
        } else {
            showNotification(data.message || 'Error booking appointment', 'error');
        }
    } catch (error) {
        console.error('Error booking appointment:', error);
        showNotification('Error booking appointment. Please try again.', 'error');
    }
});

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
} 