document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var passwordError = document.getElementById('passwordError');
    var confirmPasswordError = document.getElementById('confirmPasswordError');
  
    var isValid = true;
  
    // Validar la contraseña
    if (password.length < 8 || !/[A-Z]/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
      passwordError.style.display = 'block';
      isValid = false;
    } else {
      passwordError.style.display = 'none';
    }
  
    // Validar la confirmación de la contraseña
    if (password !== confirmPassword) {
      confirmPasswordError.style.display = 'block';
      isValid = false;
    } else {
      confirmPasswordError.style.display = 'none';
    }
  
    if (isValid) {
      // Create a new FormData object
      var formData = new FormData(this);
  
      // Send the form data using Fetch API
      fetch('register.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('responseMessage').innerText = data;
        if (data.includes('Registro exitoso')) {
          document.getElementById('registrationForm').reset();
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }
  });
  