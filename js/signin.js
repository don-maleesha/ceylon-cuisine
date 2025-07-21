document.addEventListener('DOMContentLoaded', function() {
    const signinForm = document.getElementById('signin-form');
    const emailInput = document.querySelector('input[name="email_address"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Floating label effect for form inputs
    const inputFields = document.querySelectorAll('.form-control input');
    inputFields.forEach(input => {
        if (input.value.trim() !== '') {
            input.classList.add('has-value');
        }
        
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
            if (this.value.trim() !== '') {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    });
    // Password visibility toggle
    const togglePassword = document.getElementById('toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    // Form validation and submission
    if (signinForm) {
        signinForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            if (!emailInput.value.trim()) {
                showInputError(emailInput, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(emailInput.value)) {
                showInputError(emailInput, 'Please enter a valid email');
                isValid = false;
            } else {
                removeInputError(emailInput);
            }
            
            if (!passwordInput.value.trim()) {
                showInputError(passwordInput, 'Password is required');
                isValid = false;
            } else {
                removeInputError(passwordInput);
            }
            
            if (!isValid) {
                e.preventDefault();
            } else {
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
                submitButton.disabled = true;
            }
        });
    }
    // Helper functions
    function showInputError(input, message) {
        const formControl = input.parentElement;
        const errorElement = formControl.querySelector('.error-message') || document.createElement('div');
        
        if (!formControl.querySelector('.error-message')) {
            errorElement.className = 'error-message';
            formControl.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        formControl.classList.add('error');
    }
    
    function removeInputError(input) {
        const formControl = input.parentElement;
        formControl.classList.remove('error');
        const errorElement = formControl.querySelector('.error-message');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Form animation
    const formContainer = document.querySelector('.form-container');
    if (formContainer) {
        formContainer.classList.add('fade-in');
    }
});
