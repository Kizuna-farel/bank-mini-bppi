<?php
session_start();
require "function.php";
$error = "";
$sukses = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong";
    } else {
        $result_user = login_user($conn, $username, $password);
        if ($result_user !== false) {
            $_SESSION['siswa'] = $result_user;
            $sukses = "Selamat datang, " . $result_user['username'];
            header("Location: index.php");
            exit();
        } else {
            $result_admin = login_admin($conn, $username, $password);
            if ($result_admin !== false) {
                $_SESSION['admin'] = $result_admin;
                $sukses = "Selamat datang, " . $result_admin['nama'];
                header("Location: admin.php");
                exit();
            } else {
                $error = "Username atau password salah";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        #login-form {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            z-index: 10;
            position: relative;
        }

        h2 {
            color: #3a71b1;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color:#3a71b1;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.5s ;
        }

        button:hover {
            background-color: #0383d2;
        }

        .error {
            color: #c00;
            margin-bottom: 1rem;
        }

        .success {
            color: #0c0;
            margin-bottom: 1rem;
        }

        .show-password {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .show-password input {
            margin-right: 0.5rem;
        }

        /* Enhanced sea animation */
        .sea {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60%;
            background: linear-gradient(to top, #0077b6 0%, #90e0ef 100%);
            overflow: hidden;
        }

        .wave {
            position: absolute;
            bottom: 0;
            width: 200%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"><path d="M0,1000 C200,700 300,500 500,500 C700,500 800,700 1000,1000" fill="rgba(255,255,255,0.3)"/></svg>');
            background-size: 200% 100%;
            animation: wave-animation 20s infinite linear;
        }

        .wave:nth-child(2) {
            bottom: 10px;
            opacity: 0.5;
            animation: wave-animation 15s infinite linear;
        }

        .wave:nth-child(3) {
            bottom: 20px;
            opacity: 0.3;
            animation: wave-animation 10s infinite linear;
        }

        @keyframes wave-animation {
            0% {
                transform: translateX(0) translateZ(0) scaleY(1);
            }

            50% {
                transform: translateX(-25%) translateZ(0) scaleY(0.8);
            }

            100% {
                transform: translateX(-50%) translateZ(0) scaleY(1);
            }
        }

        /* Fish animation */
        .fish {
            position: absolute;
            width: 50px;
            height: 30px;
            background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 50 30" xmlns="http://www.w3.org/2000/svg"><path d="M0,15 Q10,5 25,15 T50,15 L40,5 L50,15 L40,25 Z" fill="%23FFA500"/></svg>');
            animation: fish-animation 15s infinite linear;
        }

        .fish:nth-child(2) {
            top: 60%;
            animation-duration: 20s;
            animation-delay: -5s;
            animation-timing-function: ease-in-out;
        }

        .fish:nth-child(3) {
            top: 75%;
            animation-duration: 25s;
            animation-delay: -10s;
            animation-timing-function: ease-in-out;
        }

        @keyframes fish-animation {
            0% {
                left: -50px;
                transform: scaleX(1);
            }

            49% {
                transform: scaleX(1);
            }

            50% {
                left: 100%;
                transform: scaleX(-1);
            }

            99% {
                transform: scaleX(-1);
            }

            100% {
                left: -50px;
                transform: scaleX(1);
            }
        }

        /* Bubbles animation */
        .bubble {
            position: absolute;
            bottom: -20px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            animation: bubble-animation 10s infinite ease-in-out;
        }

        .bubble:nth-child(2) {
            left: 20%;
            animation-delay: -5s;
        }

        .bubble:nth-child(3) {
            left: 40%;
            animation-delay: -8s;
        }

        .bubble:nth-child(4) {
            left: 60%;
            animation-delay: -2s;
        }

        .bubble:nth-child(5) {
            left: 80%;
            animation-delay: -6s;
        }

        @keyframes bubble-animation {
            0% {
                transform: translateY(0) scale(0.5);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) scale(1.5);
                opacity: 0;
            }
        }

        .cloud {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: #f0f2f5;
            overflow: hidden;
        }

        .cloud-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M0,50 C20,30 40,50 60,30" fill="#fff"/></path></svg>')
        }
    </style>
</head>

<body>
    <div class="sea">
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="fish"></div>
        <div class="fish"></div>
        <div class="fish"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <form id="login-form" method="post">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (!empty($sukses)): ?>
            <p class="success"><?= htmlspecialchars($sukses) ?></p>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <div class="show-password">
            <input type="checkbox" id="show-password" onclick="togglePasswordVisibility()">
            <label for="show-password">Show password</label>
        </div>

        <button type="submit">Login</button>
    </form>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var checkbox = document.getElementById("show-password");
            passwordInput.type = checkbox.checked ? "text" : "password";
        }
    </script>
</body>

</html>