function validateForm(event) {
  event.preventDefault(); // Prevent form submission

  const form = event.target;
  const password = form.querySelector('input[name="password"]').value;
  const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
  const messageContainer = document.getElementById('messageContainer');

  // Clear previous messages
  messageContainer.innerHTML = '';

  // Check if passwords match
  if (password !== confirmPassword) {
    const errorMessage = document.createElement('div');
    errorMessage.className = 'error';
    errorMessage.textContent = 'Passwords do not match';
    messageContainer.appendChild(errorMessage);
    return false; // Stop form submission
  }

  // If all validations pass, submit the form
  form.submit();
}