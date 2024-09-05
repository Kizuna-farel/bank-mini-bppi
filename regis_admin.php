<?php
session_start();
require "function.php";

// Generate a CSRF token and store it in the session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = 'CSRF token validation failed';
    } else {
        $nama = trim($_POST['nama']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($password !== $confirm_password) {
            $error_message = 'Password dan konfirmasi password tidak cocok';
        } elseif (strlen($password) < 8) {
            $error_message = 'Password harus minimal 8 karakter';
        } else {
            $result = register_admin($conn, $nama, $username, $password);
            if ($result) {
                $success_message = 'Admin berhasil terdaftar';
                // Redirect after a short delay
                header("refresh:2;url=login.php");
            } else {
                $error_message = 'Terjadi kesalahan saat pendaftaran';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color:  #4a90e2;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 4px;
        }

        button:hover {
            background-color:  #4a90e2;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .password-toggle {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }

        .password-toggle input[type="checkbox"] {
            margin-right: 5px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4a90e2;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Ubah Admin</h2>
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <form id="register-form" method="post">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <div class="password-toggle">
                <input type="checkbox" id="show-password" onclick="togglePasswordVisibility('password')">
                <label for="show-password">Tampilkan password</label>
            </div>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <div class="password-toggle">
                <input type="checkbox" id="show-confirm-password"
                    onclick="togglePasswordVisibility('confirm_password')">
                <label for="show-confirm-password">Tampilkan password</label>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit">Ubah</button>
        </form>
        <a href="admin.php">Kembali</a>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            var passwordInput = document.getElementById(inputId);
            var checkbox = document.getElementById("show-" + inputId);
            passwordInput.type = checkbox.checked ? "text" : "password";
        }
    </script>
</body>

</html>