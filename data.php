<?php
session_start();
require "function.php";

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Proses penghapusan jika ada
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $message = deleteStudent($conn, $deleteId);
}

// Proses update data jika ada
if (isset($_POST['update_id'])) {
    $updateId = intval($_POST['update_id']);
    $name = validateInput($_POST['name']);
    $username = validateInput($_POST['username']);
    $nis = validateInput($_POST['nis']);
    $major = validateInput($_POST['major']);
    $balance = validateInput($_POST['balance']);
    $message = updateStudent($conn, $updateId, $name, $username, $nis, $major, $balance);
}

// Process search if exists
$searchResults = [];
$searchTerm = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $searchTerm = validateInput($_POST['searchTerm']);
    $searchResults = searchStudentsByUsername($conn, $searchTerm);
} else {
    // Get all students if no search
    $searchResults = getAllStudents($conn);
}

// Pagination
$resultsPerPage = 10;
$totalResults = count($searchResults);
$totalPages = ceil($totalResults / $resultsPerPage);
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $resultsPerPage;
$paginatedResults = array_slice($searchResults, $offset, $resultsPerPage);

// Get message from query string
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <style>
        /* Retained CSS styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1,
        h2 {
            color: #333;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"] {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
            width: 20rem;
        }

        input[type="submit"] {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
            width: 5rem;
        }

        input[type="text"]:focus {
            border-color: #0383d2;
            outline: none;
        }

        input[type="submit"] {
            background-color: #0383d2;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0277bd;
        }

        table {
            width: 100%;
            /* border-collapse: separate; */
            border-spacing: 0 10px;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr {
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        tr:hover {
            background-color: #f5f5f5;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            padding: 8px 16px;
            text-decoration: none;
            color: #000;
            background-color: #ddd;
            border-radius: 5px;
            margin: 0 4px;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #45a049;
            color: white;
        }

        .pagination .active {
            background-color: #4CAF50;
            color: white;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .btn-admin {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #0383d2;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 20px;
        }

        .btn-admin:hover {
            background-color: #0277bd;
            transform: translateY(-2px);
        }

        .btn-admin:active {
            background-color: #0277bd;
            transform: translateY(0);
        }

        .delete-btn {
            color: white;
            background-color: #e74c3c;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        /* Styles for modal background */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
        }

        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Styles for update button in table */
        .update-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            font-size: 14px;
            font-weight: bold;
        }

        .update-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .update-btn:active {
            background-color: #45a049;
            transform: translateY(0);
        }

        /* Styles for form inputs inside modal */
        .modal-content form input[type="text"] {
            width: calc(100% - 20px);
            /* Adjusted to fit within modal */
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
        }

        .modal-content form input[type="text"]:focus {
            border-color: #0383d2;
            outline: none;
        }

        /* Styles for submit button inside modal */
        .modal-content form input[type="submit"] {
            background-color: #0383d2;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-content form input[type="submit"]:hover {
            background-color: #0277bd;
        }

        .action {
            display: flex;
            gap: 10px;
        }

        button {
            font-size: 14px;
            font-weight: bold;
        }

        .message {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #721c24;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        @media (max-width: 600px) {
            table {
                width: 100%;
                font-size: 13px;
            }

            td {
                height: 5rem;
            }

            .action {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>

<body>
    <h1>Data Siswa</h1>

  
    
    <!-- Search Form -->
    <form method="post">
        <input type="text" name="searchTerm" placeholder="Cari siswa berdasarkan username..."
            value="<?= htmlspecialchars($searchTerm) ?>">
        <input type="submit" name="search" value="Cari">
    </form>
    
    <!-- Modal for Updating Student Data -->
    <!-- (Include your modal code here) -->
    
    <!-- Table of Students -->
    <table>
        <tr>
            <th></th>
            <th>Nama</th>
            <th>Username</th>
            <th>NIS</th>
            <th>Jurusan</th>
            <th>Kelas</th>
            <th>Saldo</th>
            <th>Action</th>
        </tr>
        <?php foreach ($paginatedResults as $student): ?>
            <tr>
                <td></td>
                <td><?= htmlspecialchars($student['nama']) ?></td>
                <td><?= htmlspecialchars($student['username']) ?></td>
                <td><?= htmlspecialchars($student['nis']) ?></td>
                <td><?= htmlspecialchars($student['jurusan']) ?></td>
                <td><?= htmlspecialchars($student['kelas']) ?></td>
                <td><?= htmlspecialchars($student['saldo']) ?></td>
                <td class="action">
                    <button class="update-btn"
                        onclick="openUpdateModal(<?= htmlspecialchars($student['id']) ?>, '<?= htmlspecialchars($student['nama']) ?>', '<?= htmlspecialchars($student['username']) ?>', '<?= htmlspecialchars($student['nis']) ?>', '<?= htmlspecialchars($student['kelas']) ?>', '<?= htmlspecialchars($student['jurusan']) ?>', '<?= htmlspecialchars($student['saldo']) ?>')">Update</button>
    
                    <button class="delete-btn" onclick="confirmDelete(<?= htmlspecialchars($student['id']) ?>)">Hapus</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <!-- Pagination -->
    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>">&laquo; Prev</a>
            <?php endif; ?>
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <a href="?page=<?= $page ?>" class="<?= $page == $currentPage ? 'active' : '' ?>"><?= $page ?></a>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>">Next &raquo;</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- <a href="logout.php" class="btn-admin">Logout</a> -->
    
    <script>
        function openUpdateModal(id, name, username, nis, kelas, major, balance) {
            document.getElementById('update_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('username').value = username;
            document.getElementById('nis').value = nis;
            document.getElementById('kelas').value = kelas;
            document.getElementById('major').value = major;
            document.getElementById('balance').value = balance;
            document.getElementById('updateModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('updateModal').style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('updateModal')) {
                closeModal();
            }
        }

        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                window.location.href = `data.php?delete_id=${id}`;
            }
        }
    </script>
    </body>
    
    </html>