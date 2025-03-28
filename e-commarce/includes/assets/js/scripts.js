/**
 * Main JavaScript file for e-commerce site
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle if needed
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('active');
        });
    }
    
    // Cart quantity updater
    const quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            form.submit();
        });
    });
    
    // Product image gallery (if applicable)
    const thumbnails = document.querySelectorAll('.thumbnail');
    if (thumbnails.length > 0) {
        const mainImage = document.querySelector('.main-image');
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const src = this.getAttribute('data-src');
                mainImage.src = src;
                
                // Toggle active class
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
    
    // Form validation for checkout
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            let valid = true;
            
            // Required fields validation
            const requiredFields = checkoutForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('error');
                    
                    // Create error message if not exists
                    let errorMessage = field.nextElementSibling;
                    if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                        errorMessage = document.createElement('div');
                        errorMessage.classList.add('error-message');
                        errorMessage.style.color = 'red';
                        errorMessage.style.fontSize = '0.8rem';
                        errorMessage.style.marginTop = '5px';
                        field.parentNode.insertBefore(errorMessage, field.nextSibling);
                    }
                    
                    errorMessage.textContent = 'This field is required';
                } else {
                    field.classList.remove('error');
                    
                    // Remove error message if exists
                    const errorMessage = field.nextElementSibling;
                    if (errorMessage && errorMessage.classList.contains('error-message')) {
                        errorMessage.remove();
                    }
                }
            });
            
            // Email validation
            const emailField = checkoutForm.querySelector('[type="email"]');
            if (emailField && emailField.value.trim()) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailField.value.trim())) {
                    valid = false;
                    emailField.classList.add('error');
                    
                    // Create error message if not exists
                    let errorMessage = emailField.nextElementSibling;
                    if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                        errorMessage = document.createElement('div');
                        errorMessage.classList.add('error-message');
                        errorMessage.style.color = 'red';
                        errorMessage.style.fontSize = '0.8rem';
                        errorMessage.style.marginTop = '5px';
                        emailField.parentNode.insertBefore(errorMessage, emailField.nextSibling);
                    }
                    
                    errorMessage.textContent = 'Please enter a valid email address';
                }
            }
            
            // Phone validation
            const phoneField = checkoutForm.querySelector('[name="phone"]');
            if (phoneField && phoneField.value.trim()) {
                const phonePattern = /^\d{10,15}$/;
                if (!phonePattern.test(phoneField.value.replace(/\D/g, ''))) {
                    valid = false;
                    phoneField.classList.add('error');
                    
                    // Create error message if not exists
                    let errorMessage = phoneField.nextElementSibling;
                    if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                        errorMessage = document.createElement('div');
                        errorMessage.classList.add('error-message');
                        errorMessage.style.color = 'red';
                        errorMessage.style.fontSize = '0.8rem';
                        errorMessage.style.marginTop = '5px';
                        phoneField.parentNode.insertBefore(errorMessage, phoneField.nextSibling);
                    }
                    
                    errorMessage.textContent = 'Please enter a valid phone number';
                }
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    }
});