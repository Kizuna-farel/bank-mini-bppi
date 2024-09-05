<?php
// Instalisasi session
session_start();

// Mengecek apakah admin ada session admin aktif, jika tidak arahkan ke login.php
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('location:login.php'); // Arahkan ke login.php
    exit;
}

// Validasi data admin
if (!isset($_SESSION['admin']['nama']) || empty($_SESSION['admin']['nama'])) {
    header('location:login.php'); // Arahkan ke login.php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            background-color: var(--background-color);
        }

        .admin-container {
            background-color: var(--card-color);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            height: 100%;
        }


        .header {
            background-color: #fdde55;
            color: #fff;
            padding: 1rem;
            border-radius: 60px 0 60px 0 ;
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Optional: adds depth to the main element */
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50px;
            /* Adjust positioning as needed */
            right: -50px;
            /* Adjust positioning as needed */
            width: 100px;
            /* Adjust size as needed */
            height: 100px;
            /* Adjust size as needed */
            background-color: rgba(255, 255, 255, 0.3);
            /* Light background for cloud effect */
            border-radius: 50%;
            /* Circular shape for a more cloud-like appearance */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            /* Increased shadow for more depth */
            transform: rotate(-30deg);
            /* Rotate for a natural cloud shape */
            z-index: 0;
            /* Place behind content */
            transition: all 0.5s ease;
            /* Smooth transition for hover effect */
        }


        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        h3 {
            font-weight: normal;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .menu-item {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 20px;
            border-radius: var(--button-shape);
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.5s ease;
        }

        .menu-item:hover::before {
            top: -10px;
            left: -10px;
            width: 200px;
            height: 200px;
        }

        .menu-item:hover {
            background-color: var(--hover-color);
            transform: translateY(-5px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .icon {
            margin-bottom: 10px;
            width: 24px;
            height: 24px;
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
            width: 24px;
            margin-top: 6px;
            margin-right: 0.5rem;
        }

        .logout-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .header{
                font-size: 1rem;
            }
            h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        h3 {
            font-weight: normal;
        }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <header class="header">
            <h1>Dashboard Admin</h1>
            <h3>Selamat datang, <?php echo $_SESSION['admin']['nama']; ?></h3>
        </header>

        <div class="menu-grid">
            <a href="saldo.php" class="menu-item">
                <i data-feather="dollar-sign" class="icon"></i>
                <span>Ubah Saldo</span>
            </a>

            <a href="register.php" class="menu-item">
                <i data-feather="user-plus" class="icon"></i>
                <span>Register Siswa</span>
            </a>

            <a href="data.php" class="menu-item">
                <i data-feather="database" class="icon"></i>
                <span>Data</span>
            </a>

            <a href="regis_admin.php" class="menu-item">
                <i data-feather="settings" class="icon"></i>
                <span>Kelola Admin</span>
            </a>
        </div>

        <a href="logout.php" class="logout-link">
            <i data-feather="log-out" class="icon"></i>
            Logout
        </a>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>