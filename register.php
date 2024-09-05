<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Baru</title>
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

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #3a71b1;
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

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color:  #4a90e2;
            text-decoration: none;
            font-size: 16px;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Registrasi Akun Baru</h2>
        <?php
        $message = '';
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Include the function.php file
            require 'function.php';

            // Ambil data dari form
            $nama = $_POST['nama'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $nis = $_POST['nis'];
            $jurusan = $_POST['jurusan'];
            $kelas = $_POST['kelas'];

            // Panggil fungsi registerUser
            $message = registerUser($conn, $nama, $username, $password, $nis, $jurusan, $kelas);

            // Tampilkan pesan setelah registrasi
            if (strpos($message, 'berhasil') !== false) {
                $message = '<div class="message success">' . $message . '</div>';
            } else {
                $message = '<div class="message error">' . $message . '</div>';
            }
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="nis">NIS:</label>
            <input type="text" id="nis" name="nis" required>

            <label for="jurusan">Jurusan:</label>
            <select id="jurusan" name="jurusan" required>
                <option value="">Pilih Jurusan</option>
                <option value="PPLG">PPLG</option>
                <option value="TJKT">TJKT</option>
                <option value="ACP">ACP</option>
                <option value="AKL">AKL</option>
            </select>

            <label for="kelas">Kelas:</label>
            <input type="text" id="kelas" name="kelas" required>

            <input type="submit" value="Daftar">
        </form>
        <?php echo $message; ?>

        <a href="admin.php">Kembali ke Admin</a>
    </div>
</body>

</html>