<?php
require 'function.php';

$errorMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = validateInput($_POST['nama']);
    $newUsername = validateInput($_POST['new_username']);
    $oldPassword = validateInput($_POST['old_password']);
    $newPassword = validateInput($_POST['new_password']);
    $newNIS = validateInput($_POST['new_nis']);
    $newJurusan = validateInput($_POST['new_jurusan']);

    if (!empty($newUsername)) {
        $result = changeUsername($conn, $nama, $newUsername);
        if ($result !== "Username berhasil diubah!") {
            $errorMessages[] = $result;
        }
    }

    if (!empty($oldPassword) && !empty($newPassword)) {
        $result = changePassword($conn, $nama, $oldPassword, $newPassword);
        if ($result !== "Password berhasil diubah!") {
            $errorMessages[] = $result;
        }
    }

    if (!empty($newNIS)) {
        $result = changeNIS($conn, $nama, $newNIS);
        if ($result !== "NIS berhasil diubah!") {
            $errorMessages[] = $result;
        }
    }

    if (!empty($newJurusan)) {
        $result = changeJurusan($conn, $nama, $newJurusan);
        if ($result !== "Jurusan berhasil diubah!") {
            $errorMessages[] = $result;
        }
    }

    if (empty($errorMessages)) {
        echo "<script>alert('Data berhasil diubah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Siswa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f4f4f4, #e0e0e0);
            margin: 0;
            padding: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 24px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #03aed2;
            outline: none;
            box-shadow: 0 0 6px rgba(3, 174, 210, 0.5);
        }

        input[type="submit"] {
            background-color: #03aed2;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s, transform 0.2s;
        }

        input[type="submit"]:hover {
            background-color: #0383d2;
            transform: scale(1.02);
        }

        .error-message {
            color: #d9534f;
            margin-bottom: 20px;
            border: 1px solid #d9534f;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9d6d5;
            font-size: 16px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #03aed2;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {

            input[type="text"],
            input[type="password"],
            input[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Formulir Perubahan Data Siswa</h2>

        <?php if (!empty($errorMessages)): ?>
            <div class="error-message">
                <?php foreach ($errorMessages as $message): ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <label for="nama">Nama:</label>
            <input type="text" name="nama" id="nama" required>

            <label for="new_username">Username Baru:</label>
            <input type="text" name="new_username" id="new_username">

            <label for="old_password">Password Lama:</label>
            <input type="password" name="old_password" id="old_password">

            <label for="new_password">Password Baru:</label>
            <input type="password" name="new_password" id="new_password">

            <input type="submit" name="submit" value="Ubah">
        </form>

        <a href="index.php">Kembali</a>
    </div>
</body>

</html>