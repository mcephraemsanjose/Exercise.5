<?php
session_start();

define('USER_DATA_FILE', 'users.json');

$users = [];
if (file_exists(USER_DATA_FILE)) {
    $users = json_decode(file_get_contents(USER_DATA_FILE), true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['status' => 'error', 'message' => 'Invalid request'];

    if ($_POST['action'] === 'register') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (substr($username, 10) !== '@gmail.com') {
            $response['message'] = "Username must have 10 characters and end with '@gmail.com'.";
        } 
        elseif (isset($users[$username])) {
            $response['message'] = "Username is already taken.";
        } 
        else {
            $users[$username] = password_hash($password, PASSWORD_DEFAULT);
            file_put_contents(USER_DATA_FILE, json_encode($users)); // Save to file
            $response = ['status' => 'success', 'message' => "Registration successful! You can now log in."];
        }
    }

    if ($_POST['action'] === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (isset($users[$username]) && password_verify($password, $users[$username])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $response = ['status' => 'success', 'message' => 'Login successful!'];
        } else {
            $response['message'] = "Invalid username or password.";
        }
    }

    if ($_POST['action'] === 'suggest') {
        $input = $_POST['username'] ?? '';
        $suggestions = [];

        foreach ($users as $username => $passwordHash) {
            if (stripos($username, $input) === 0) { // Check if username starts with the input
                $suggestions[] = $username;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'suggestions' => $suggestions]);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration Form</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Link to the CSS -->
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#login-form').on('submit', function (e) {
            e.preventDefault(); // Prevent traditional form submission
            
            $.ajax({
                type: 'POST',
                url: '', // The same page
                data: $(this).serialize(), // Serialize form data
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.reload();
                        $('#login-message').removeClass('success').addClass('error').text(response.message);
                    }
                },
                error: function () {
                    $('#login-message').removeClass('success').addClass('error').text('An error occurred.');
                }
            });
        });

$('#register-form').on('submit', function (e) {
    e.preventDefault(); // Prevent traditional form submission
    
    $.ajax({
        type: 'POST',
        url: '', // The same page
        data: $(this).serialize(), // Serialize form data
        success: function (response) {
            if (response.status === 'success') {
                $('#register-message').removeClass('error').addClass('success').text(response.message);
              
                $('#register_username').val(''); 
                $('#register_password').val(''); 
                $('#login_suggestions').empty(); 
            } else {
                $('#register-message').removeClass('success').addClass('error').text(response.message);
            }
        },
        error: function () {
            $('#register-message').removeClass('success').addClass('error').text('An error occurred.');
        }
    });
});
        $('#username').on('input', function () {
            const input = $(this).val();
            if (input.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: '', // The same page
                    data: {
                        action: 'suggest',
                        username: input
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#login_suggestions').empty();
                            if (response.suggestions.length > 0) {
                                response.suggestions.forEach(function (username) {
                                    $('#login_suggestions').append('<li class="suggestion-item" style="cursor: pointer;">' + username + '</li>');
                                });
                            } else {
                                $('#login_suggestions').append('<li>No suggestions available</li>');
                            }
                        }
                    }
                });
            } else {
                $('#login_suggestions').empty();
            }
        });

        $(document).on('click', '#login_suggestions .suggestion-item', function () {
            const selectedUsername = $(this).text();
            $('#username').val(selectedUsername); 
            $('#login_suggestions').empty(); 
        });
    });
    </script>
</head>
<body style="background: url('Beezzz.jpg.webp'); background-size: cover; background-position: center; font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; align-items: center;">

    <div style="background-color:#ebcb65; border-radius: 15px; padding: 30px; width: 355px; margin: 50px auto; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border: 5px solid #df944f;">

<?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 50vh;">
        <h1 style="text-align: center;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p style="text-align: center;">You are now logged in to our homepage.</p>
        <div style="margin-top: 10px; display: flex; flex-direction: row; gap: 10px;">
            <a href="homepage.php" style="text-decoration: none; background-color: #df944f; color: white; padding: 10px 15px; border-radius: 5px;">Home Profile</a>
            <a href="?logout=true" style="text-decoration: none; background-color: #df944f; color: white; padding: 10px 15px; border-radius: 5px;">Logout</a>
        </div>
    </div>
<?php else: ?>
        <h2 style="text-align: center;">Login Form</h2>

        <!-- Display login error/success message -->
        <p id="login-message" class="error"></p>

        <!-- Login form -->
        <form id="login-form">
            <input type="hidden" name="action" value="login">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <ul id="login_suggestions" style="list-style: none; padding: 0; margin: 0; text-align: left;"></ul><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Login</button>
        </form>

        <h2 style="text-align: center;">Register Form</h2>

        <!-- Display register error/success message -->
        <p id="register-message" class="error"></p>

        <!-- Register form -->
        <form id="register-form">
            <input type="hidden" name="action" value="register">
            <label for="register_username">Username:</label>
            <input type="text" id="register_username" name="username" required><br>
            <label for="register_password">Password:</label>
            <input type="password" id="register_password" name="password" required><br>
            <button type="submit">Register</button>
        </form>
    <?php endif; ?>
    </div>
</body>
</html>
