<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        .logout-btn {
            background: #dc3545;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        .response {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            white-space: pre-wrap;
            font-family: monospace;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .section h2 {
            margin-top: 0;
            color: #333;
        }
        .token-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Laravel API Test Interface</h1>
        
        <!-- Registration Form -->
        <div class="section">
            <h2>Register User</h2>
            <form id="registerForm">
                <div class="form-group">
                    <label for="reg_name">Name:</label>
                    <input type="text" id="reg_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="reg_email">Email:</label>
                    <input type="email" id="reg_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="reg_password">Password:</label>
                    <input type="password" id="reg_password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="reg_password_confirmation">Confirm Password:</label>
                    <input type="password" id="reg_password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit">Register</button>
            </form>
            <div id="registerResponse" class="response" style="display: none;"></div>
        </div>

        <!-- Login Form -->
        <div class="section">
            <h2>Login User</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label for="login_email">Email:</label>
                    <input type="email" id="login_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login_password">Password:</label>
                    <input type="password" id="login_password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <div id="loginResponse" class="response" style="display: none;"></div>
        </div>

        <!-- Token Display -->
        <div class="section">
            <h2>Current Token</h2>
            <div id="tokenDisplay" class="token-display">No token available</div>
        </div>

        <!-- Protected Routes -->
        <div class="section">
            <h2>Protected Actions</h2>
            <button onclick="getUser()">Get User Info</button>
            <button onclick="logout()" class="logout-btn">Logout</button>
            <div id="protectedResponse" class="response" style="display: none;"></div>
        </div>
    </div>

    <script>
        let authToken = localStorage.getItem('authToken') || '';
        
        // Update token display
        function updateTokenDisplay() {
            const tokenDisplay = document.getElementById('tokenDisplay');
            tokenDisplay.textContent = authToken || 'No token available';
        }
        
        // Initialize token display
        updateTokenDisplay();

        // Register form handler
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('http://localhost:8000/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                const responseDiv = document.getElementById('registerResponse');
                
                responseDiv.style.display = 'block';
                responseDiv.className = 'response ' + (response.ok ? 'success' : 'error');
                responseDiv.textContent = JSON.stringify(result, null, 2);
                
                if (response.ok && result.token) {
                    authToken = result.token;
                    localStorage.setItem('authToken', authToken);
                    updateTokenDisplay();
                }
            } catch (error) {
                const responseDiv = document.getElementById('registerResponse');
                responseDiv.style.display = 'block';
                responseDiv.className = 'response error';
                responseDiv.textContent = 'Error: ' + error.message;
            }
        });

        // Login form handler
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('http://localhost:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                const responseDiv = document.getElementById('loginResponse');
                
                responseDiv.style.display = 'block';
                responseDiv.className = 'response ' + (response.ok ? 'success' : 'error');
                responseDiv.textContent = JSON.stringify(result, null, 2);
                
                if (response.ok && result.token) {
                    authToken = result.token;
                    localStorage.setItem('authToken', authToken);
                    updateTokenDisplay();
                }
            } catch (error) {
                const responseDiv = document.getElementById('loginResponse');
                responseDiv.style.display = 'block';
                responseDiv.className = 'response error';
                responseDiv.textContent = 'Error: ' + error.message;
            }
        });

        // Get user info
        async function getUser() {
            if (!authToken) {
                alert('Please login first to get a token');
                return;
            }
            
            try {
                const response = await fetch('http://localhost:8000/api/user', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                const responseDiv = document.getElementById('protectedResponse');
                
                responseDiv.style.display = 'block';
                responseDiv.className = 'response ' + (response.ok ? 'success' : 'error');
                responseDiv.textContent = JSON.stringify(result, null, 2);
            } catch (error) {
                const responseDiv = document.getElementById('protectedResponse');
                responseDiv.style.display = 'block';
                responseDiv.className = 'response error';
                responseDiv.textContent = 'Error: ' + error.message;
            }
        }

        // Logout
        async function logout() {
            if (!authToken) {
                alert('No token available to logout');
                return;
            }
            
            try {
                const response = await fetch('http://localhost:8000/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                const responseDiv = document.getElementById('protectedResponse');
                
                responseDiv.style.display = 'block';
                responseDiv.className = 'response ' + (response.ok ? 'success' : 'error');
                responseDiv.textContent = JSON.stringify(result, null, 2);
                
                if (response.ok) {
                    authToken = '';
                    localStorage.removeItem('authToken');
                    updateTokenDisplay();
                }
            } catch (error) {
                const responseDiv = document.getElementById('protectedResponse');
                responseDiv.style.display = 'block';
                responseDiv.className = 'response error';
                responseDiv.textContent = 'Error: ' + error.message;
            }
        }
    </script>
</body>
</html>