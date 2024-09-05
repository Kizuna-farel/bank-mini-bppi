<?php
// Instalisasi session
session_start();

// Mengecek apakah user ada session user aktif, jika tidak arahkan ke login.php
if (!isset($_SESSION['siswa'])) {
    header('location:login.php'); // Arahkan ke login.php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        /* CSS yang diperbarui */
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #5b2c6f;
            --background-color: #f4f7fa;
            --card-color: #ffffff;
            --text-color: #333333;
            --hover-color: #3a7bd5;
            --button-shape: 0px;
            /* Mengubah tombol menjadi kotak */
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            display: flex;
            flex-direction: column;
            width: 100%;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        .user-container {
            background-color: var(--primary-color);
            color: #fff;
            margin: 1rem auto;
            width: 96%;
            border-radius: 20px 0 0 0;
            padding: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .user-container::before {
            content: '';
            position: absolute;
            top: -30px;
            /* Adjust positioning as needed */
            right: -30px;
            /* Adjust positioning as needed */
            width: 200px;
            /* Adjust size as needed */
            height: 200px;
            /* Adjust size as needed */
            background-color: rgba(255, 255, 255, 0.3);
            /* Light background for cloud effect */
            border-radius: 80%;
            /* Make it a circle */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            /* Add shadow for depth */
            transform: rotate(-30deg);
            /* Rotate for a more natural cloud shape */
            z-index: 0;
            /* Place behind content */
            transition: all 0.5s ease;
            /* Smooth transition for hover effect */
        }


        .user p {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .user span {
            font-weight: bold;
        }

        .fitur-container {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            margin: 1rem auto;
            width: 96%;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .saldo-fitur {
            background-color: #fdde55;
            color: #fff;
            padding: 1rem;
            border-radius: 12px;
            text-align: left;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Optional: adds depth to the main element */
        }

        .saldo-fitur::before {
            content: '';
            position: absolute;
            top: -50px;
            /* Adjust positioning as needed */
            right: -50px;
            /* Adjust positioning as needed */
            width: 200px;
            /* Adjust size as needed */
            height: 200px;
            /* Adjust size as needed */
            background-color: rgba(255, 255, 255, 0.3);
            /* Light background for cloud effect */
            border-radius: 10%;
            /* Circular shape for a more cloud-like appearance */
            
            /* Increased shadow for more depth */
            transform: rotate(-30deg);
            /* Rotate for a natural cloud shape */
            z-index: 0;
            /* Place behind content */
            transition: all 0.5s ease;
            /* Smooth transition for hover effect */
        }

        .saldo-fitur:hover::before {
            background-color: rgba(255, 255, 255, 0.5);
            /* Brighter cloud effect on hover */
            top: -20px;
            /* Adjust position on hover */
            right: -20px;
            /* Adjust position on hover */
            transform: rotate(-30deg) scale(1.2);
            /* Scale up on hover */
        }

        .saldo-fitur p {
            font-size: 1.2rem;
            z-index: 1;
            /* Ensure text is above the cloud effect */
        }


        .saldo-fitur h3 {
            font-size: 2rem;
            margin-top: 0.5rem;
        }

        .con-fitur {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .menu-grid {
            display: flex;
            justify-content: ;
            align-items: center;
            gap: 10rem;
            /* grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px; */
            /* Jarak antar elemen dalam grid */
            justify-content: center;
            /* Pusatkan grid secara horizontal */
            align-items: center;
            /* Pusatkan grid secara vertikal */
            width: 100%;
        }

        .menu-item {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 20px;
            border-radius: 10px;
            /* Sudut melengkung untuk estetika */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            width: 200px;
            /* Lebar persegi panjang */
            height: 100px;
            /* Tinggi persegi panjang */
            text-align: center;
            margin: 10px;
            /* Jarak antar elemen */
        }

        .menu-item:hover {
            background-color: var(--hover-color);
            /* Warna latar belakang saat hover */
            transform: scale(1.05);
            /* Membesarkan sedikit saat hover */
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
            /* Bayangan lebih besar saat hover */
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            /* Lebar persegi panjang */
            height: 100px;
            /* Tinggi persegi panjang */
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.5s ease;
        }

        .menu-item:hover::before {
            top: -10px;
            left: -10px;
            width: 250px;
            /* Lebar saat hover */
            height: 150px;
            /* Tinggi saat hover */
        }

        .menu-item .icon {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }


        .menu-item .icon {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }

        .logout-link {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
            color: #e74c3c;
            font-weight: bold;
            font-size: 1rem;
        }

        .logout-link .icon {
            margin-right: 0.5rem;
        }

        .logout-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .user-container,
            .fitur-container {
                width: 100%;
            }

            .user-container {
                width: 85%;
                font-size: 13px;
            }

            .user p {
                font-size: 1rem;
            }

            .saldo-fitur p {
                font-size: 1rem;
            }

            .saldo-fitur h3 {
                font-size: 1.5rem;
            }

            .menu-grid {
                gap: 2rem;
            }

            .menu-item .icon {
                width: 40px;
                height: 40px;
                margin-bottom: 4px;
            }


            .menu-item {
                width: 120px;
                /* Lebar persegi panjang saat tampilan kecil */
                height: 80px;
                /* Tinggi persegi panjang saat tampilan kecil */
                font-size: 11px;
            }

            .menu-item::before {
                width: 180px;
                /* Lebar saat hover pada tampilan kecil */
                height: 120px;
                /* Tinggi saat hover pada tampilan kecil */
            }

            .menu-item:hover::before {
                width: 200px;
                /* Lebar saat hover pada tampilan kecil */
                height: 125px;
                /* Tinggi saat hover pada tampilan kecil */
            }

        }
    </style>
</head>

<body>
    <div class="user-container">
        <p>Selamat datang <span><?php echo htmlspecialchars($_SESSION['siswa']['username']); ?></span>, mau ngapain nih
            hari ini</p>
        <p>Kelas: <?php echo htmlspecialchars($_SESSION['siswa']['kelas']); ?></p>
    </div>

    <div class="fitur-container">
        <div class="saldo-fitur">
            <p>Saldo Anda</p>
            <h3>Rp.<?php echo htmlspecialchars($_SESSION['siswa']['saldo']); ?></h3>
        </div>

        <div class="con-fitur">
            <div class="menu-grid">
                <a href="riwayat.php?id=<?php echo htmlspecialchars($_SESSION['siswa']['id']); ?>" class="menu-item">
                    <i data-feather="user-plus" class="icon"></i>
                    <span>Info Tabungan</span>
                </a>

                <a href="change.php?id=<?php echo htmlspecialchars($_SESSION['siswa']['id']); ?>" class="menu-item">
                    <i data-feather="database" class="icon"></i>
                    <span>Kelola</span>
                </a>
            </div>
        </div>
        <a href="logout.php" class="logout-link">
            <i data-feather="log-out"></i> Logout
        </a>
    </div>



    <script>
        feather.replace();
    </script>
</body>

</html>