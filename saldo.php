<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header('location:login.php');
    exit();
}

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "bank") or die('Database tidak terhubung');

// Include file function.php yang berisi fungsi tambahSaldo dan kurangiSaldo
require "function.php";

$message = ''; // Variabel untuk menyimpan pesan

// Cek jika form dikirim dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = validateInput($_POST['username']);
    $jumlah = validateInput($_POST['jumlah']);
    $teler = validateInput($_POST['teler']);
    $tanggal = validateInput($_POST['tanggal']);

    // Validasi input jumlah harus berupa angka
    if (!is_numeric($jumlah)) {
        $message = "Input jumlah tidak valid!";
    } else {
        // Cek apakah username siswa ada di database
        $query = "SELECT id FROM siswa WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $namaId = $row['id'];

            // Jika tombol tambah saldo diklik
            if (isset($_POST['tambahSaldo'])) {
                $message = tambahSaldo($namaId, $jumlah, $teler, $tanggal);
            }
            // Jika tombol kurangi saldo diklik
            elseif (isset($_POST['kurangiSaldo'])) {
                $message = kurangiSaldo($namaId, $jumlah, $teler, $tanggal);
            }
        } else {
            $message = "Username tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Saldo</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #34495e;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #3498db;
            color: #ffffff;
            border: none;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[name="tambahSaldo"] {
            background-color: #2ecc71;
        }

        button[name="kurangiSaldo"] {
            background-color: #e74c3c;
        }

        button:hover {
            opacity: 0.9;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
            text-align: center;
        }

        .links {
            margin-top: 20px;
            text-align: center;
        }

        .links a {
            color: #3498db;
            text-decoration: none;
            margin: 0 10px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <form id="add-balance-form" method="post">
            <h2>Manajemen Saldo</h2>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="jumlah">Jumlah:</label>
            <input type="number" id="jumlah" name="jumlah" required>

            <label for="teler">Teler:</label>
            <input type="text" id="teler" name="teler" required>

            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal">

            <button type="submit" name="tambahSaldo">Tambah Saldo</button>
            <button type="submit" name="kurangiSaldo">Kurangi Saldo</button>
        </form>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="links">
            <a href="riwayat.php">Lihat Riwayat Transaksi</a>
            <a href="admin.php">Kembali ke Halaman Admin</a>
        </div>
    </div>
</body>

</html>