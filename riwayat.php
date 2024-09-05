<?php
session_start();
require "function.php";

// Memastikan user telah login, jika tidak arahkan ke halaman login
if (!isset($_SESSION['siswa'])) {
    header('location:login.php');
    exit;
}

function getTransactionHistory($id)
{
    global $conn;

    $query = "SELECT s.saldo, s.nama, s.kelas, tr.timestamp, tr.type, tr.amount, tr.teler
              FROM siswa s
              LEFT JOIN transaksi_riwayat tr ON s.id = tr.nama_id
              WHERE s.id = ?
              ORDER BY tr.timestamp DESC";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Error preparing the statement: ' . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transactions = [];
        $nama = '';
        $kelas = '';
        $saldo = 0;

        while ($row = $result->fetch_assoc()) {
            if (empty($nama)) {
                $nama = $row['nama'];
                $kelas = $row['kelas'];
                $saldo = $row['saldo'];
            }
            $transactions[] = $row;
        }

        // Menampilkan informasi saldo dan identitas siswa
        echo "<div class='info-container'>";
        echo "<div class='info-saldo'>";
        echo "<div class='info-saldo-item'><p>Total Saldo<br> </p>";
        echo "<h3> Rp." . number_format($saldo, 2, ',', '.') . "</h3></div>";
        echo "</div>";
        echo "<div class='info-model'>";
        echo "<div class='info-item'><h3>Nama: " . htmlspecialchars($nama) . "</h3></div><br>";
        echo "<div class='info-item'><h3>Kelas: " . htmlspecialchars($kelas) . "</h3></div>";
        echo "</div>";
        echo "</div>";

        // Menampilkan riwayat transaksi
        echo "<div class='transaction-history'>";
        foreach ($transactions as $transaction) {
            // Menentukan warna dan tanda berdasarkan tipe transaksi
            $sign = $transaction['type'] === 'Tambah Saldo' ? '+' : '-';
            $colorClass = $transaction['type'] === 'Tambah Saldo' ? 'green' : 'red';

            echo "<div class='transaction-row'>";
            echo "<div>" . htmlspecialchars($transaction['timestamp']) . "</div>";
            echo "<div>" . htmlspecialchars($transaction['type']) . "</div>";
            echo "<div class='$colorClass'>" . $sign . " Rp " . number_format($transaction['amount'], 2, ',', '.') . "</div>";
            echo "<div>" . htmlspecialchars($transaction['teler']) . "</div>";
            echo "</div>";
        }
        echo "</div>";

    } else {
        echo "<p>Tidak ada riwayat transaksi.</p>";
    }

    $stmt->close();
}


// Ambil ID siswa dari sesi
$id = $_SESSION['siswa']['id'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            width: 100%;
            min-height: 160vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.85);
            width: 100%;
            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            color: #666;
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }

        .info-container {
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
        }

        .info-saldo {
            width: 49%;
            height: 6rem;
            background-color: #03aed2;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 5px;
            display: grid;
            justify-content: center;
            align-items: center;
            border-radius: 6px;
            color: white;
            position: relative;
            /* Menambahkan posisi relatif untuk pseudo-element */
            overflow: hidden;
            /* Menyembunyikan bagian dari pseudo-element yang meluber */
        }


        .info-saldo::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -70px;
            width: 150px;
            /* Lebar persegi panjang yang lebih besar untuk efek dekoratif */
            height: 150px;
            /* Tinggi persegi panjang yang lebih besar */
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10%;
            transform: rotate(50deg);
            transition: all 0.5s ease;

        }

        .info-saldo:hover::before {
            background-color: rgba(255, 255, 255, 0.4);
            /* Warna latar belakang saat hover */
            transform: rotate(45deg) scale(5.1);
            /* Memperbesar sedikit saat hover */
        }





        .info-saldo p {
            font-size: 15px;
        }

        .info-model {
            width: 49%;
            height: 6rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .info-item {
            font-size: 16px;
            width: 100%;
            border-radius: 8px 0 0 0;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #fdde55;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 5px;
            color: white;
            position: relative;
            /* Menambahkan posisi relatif untuk pseudo-element */
            overflow: hidden;
            /* Menyembunyikan bagian dari pseudo-element yang meluber */
        }

        .info-item::before {
            content: '';
            position: absolute;
            top: -30px;
            /* Adjust positioning as needed */
            right: -30px;
            /* Adjust positioning as needed */
            width: 100px;
            /* Adjust size as needed */
            height: 90px;
            /* Adjust size as needed */
            background-color: rgba(255, 255, 255, 0.3);
            /* Light background for cloud effect */
            border-radius: 0%;
            /* Make it a circle */
            
            /* Add shadow for depth */
            transform: rotate(-54deg);
            /* Rotate for a more natural cloud shape */
            z-index: 0;
            /* Place behind content */
            transition: all 0.5s ease;
            /* Smooth transition for hover effect */
        }


    


        .transaction-history {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
        }

        .transaction-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            text-align: left;
            margin-top: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, background 0.3s ease;
        }

        .transaction-row:hover {
            transform: translateY(-5px);
            background-color: #edf2f4;
        }

        .transaction-row>div {
            width: 24%;

        }

        /* Tambahkan kelas warna hijau dan merah */
        .green {
            color: green;
            font-weight: bold;
        }

        .red {
            color: red;
            font-weight: bold;
        }

        @media screen and (max-width: 480px) {
            .info-container {
                flex-direction: column-reverse;
                gap: 20px;

            }

            .transaction-row {
                font-size: 12px;
            }

            .info-saldo,
            .info-model {
                width: 100%;
                height: 5rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Riwayat Transaksi</h1>
        <?php
        // Menggunakan fungsi yang ada di file function.php untuk menampilkan transaksi
        getTransactionHistory($id);
        ?>
    </div>

</body>

</html><?php $conn->close(); ?>