<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "bank") or die('Database tidak terhubung');

// Function to login as admin
function login_admin($conn, $username, $password)
{
    $username = validateInput($username);
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($password, $data['password'])) {
            return $data;
        }
    }
    return false;
}

// Function to login as student
function login_user($conn, $username, $password)
{
    $username = validateInput($username);
    $query = "SELECT * FROM siswa WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($password, $data['password'])) {
            return $data;
        }
    }
    return false;
}

// Function to add balance
function tambahSaldo($conn, $namaId, $jumlah, $teler)
{
    $conn->begin_transaction();
    try {
        // Update balance
        $query = "UPDATE siswa SET saldo = saldo + ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("di", $jumlah, $namaId);
        $stmt->execute();

        // Insert transaction record
        $query = "INSERT INTO transaksi_riwayat (nama_id, type, amount, timestamp, teler) VALUES (?, 'Tambah Saldo', ?, NOW(), ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ids", $namaId, $jumlah, $teler);
        $stmt->execute();

        $conn->commit();
        return "Saldo berhasil ditambahkan!";
    } catch (Exception $e) {
        $conn->rollback();
        return "Error menambahkan saldo: " . $e->getMessage();
    }
}

// Function to deduct balance
function kurangiSaldo($conn, $namaId, $jumlah, $teler)
{
    $conn->begin_transaction();
    try {
        // Check balance
        $query = "SELECT saldo FROM siswa WHERE id = ? FOR UPDATE";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $namaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['saldo'] < $jumlah) {
            throw new Exception("Saldo tidak cukup!");
        }

        // Deduct balance
        $query = "UPDATE siswa SET saldo = saldo - ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("di", $jumlah, $namaId);
        $stmt->execute();

        // Insert transaction record
        $query = "INSERT INTO transaksi_riwayat (nama_id, type, amount, timestamp, teler) VALUES (?, 'Kurangi Saldo', ?, NOW(), ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ids", $namaId, $jumlah, $teler);
        $stmt->execute();

        $conn->commit();
        return "Pengurangan saldo berhasil!";
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

// Function to get transaction history with +/- signs
function getRiwayatTransaksi($conn, $namaId)
{
    $query = "SELECT type, amount, timestamp FROM transaksi_riwayat WHERE nama_id = ? ORDER BY timestamp DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $namaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transaksi = [];
        while ($row = $result->fetch_assoc()) {
            $sign = $row['type'] === 'Tambah Saldo' ? '+' : '-';
            $transaksi[] = [
                'type' => $row['type'],
                'amount' => $sign . $row['amount'],
                'timestamp' => $row['timestamp']
            ];
        }
        return $transaksi;
    } else {
        return "Tidak ada riwayat transaksi.";
    }
}

// Function to get current balance
function getSaldo($conn, $namaId)
{
    $query = "SELECT saldo FROM siswa WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $namaId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['saldo'];
    }
    return "User tidak ditemukan";
}

// Function to register a new user
function registerUser($conn, $nama, $username, $password, $nis, $jurusan, $kelas)
{
    $nama = validateInput($nama);
    $username = validateInput($username);
    $nis = validateInput($nis);

    $query = "SELECT * FROM siswa WHERE username = ? OR nama = ? OR nis = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $nama, $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessages = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['username'] === $username) {
                $errorMessages[] = "Username sudah terdaftar!";
            }
            if ($row['nama'] === $nama) {
                $errorMessages[] = "Nama sudah terdaftar!";
            }
            if ($row['nis'] === $nis) {
                $errorMessages[] = "NIS sudah terdaftar!";
            }
        }

        return implode(" ", $errorMessages);
    } else {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO siswa (nama, username, password, nis, jurusan, kelas, saldo) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $nama, $username, $passwordHash, $nis, $jurusan, $kelas);
        $result = $stmt->execute();

        if ($result) {
            return "Pendaftaran berhasil!";
        } else {
            return "Error mendaftar: " . $conn->error;
        }
    }
}

// Function to get all students
function getAllStudents($conn)
{
    $query = "SELECT id, nama, username, password, nis, jurusan, kelas, saldo FROM siswa";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    } else {
        return "Tidak ada data siswa";
    }
}

// Function to search students by name or username
function searchStudents($conn, $searchTerm)
{
    $query = "SELECT id, nama, username, password, nis, jurusan, kelas, saldo FROM siswa WHERE nama LIKE ? OR username LIKE ?";
    $searchTerm = "%$searchTerm%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    } else {
        return "Tidak ada data siswa yang ditemukan";
    }
}

// Function to change username
function changeUsername($conn, $nama, $newUsername)
{
    $newUsername = validateInput($newUsername);
    $nama = validateInput($nama);

    $query = "SELECT * FROM siswa WHERE username = ? OR nama = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $newUsername, $nama);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessages = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['username'] === $newUsername) {
                $errorMessages[] = "Username baru sudah digunakan!";
            }
            if ($row['nama'] === $nama) {
                $errorMessages[] = "Nama yang diberikan sudah terdaftar!";
            }
        }
        return implode(" ", $errorMessages);
    } else {
        $query = "UPDATE siswa SET username = ? WHERE nama = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $newUsername, $nama);
        $stmt->execute();

        return "Username berhasil diubah!";
    }
}

// Function to change password
function changePassword($conn, $username, $oldPassword, $newPassword)
{
    $username = validateInput($username);
    $newPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $query = "SELECT password FROM siswa WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($oldPassword, $row['password'])) {
            $query = "UPDATE siswa SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $newPassword, $username);
            $stmt->execute();

            return "Password berhasil diubah!";
        } else {
            return "Password lama salah!";
        }
    } else {
        return "Username tidak ditemukan!";
    }
}

// Function to update student data
function updateStudent($conn, $id, $nama, $username, $nis, $jurusan, $kelas)
{
    $nama = validateInput($nama);
    $username = validateInput($username);
    $nis = validateInput($nis);
    $jurusan = validateInput($jurusan);
    $kelas = validateInput($kelas);

    $query = "UPDATE siswa SET nama = ?, username = ?, nis = ?, jurusan = ?, kelas = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $nama, $username, $nis, $jurusan, $kelas, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return "Data siswa berhasil diperbarui!";
    } else {
        return "Tidak ada perubahan pada data siswa.";
    }
}

// Function to delete a student
function deleteStudent($conn, $id)
{
    $id = validateInput($id);

    // Check if student exists before deleting
    $query = "SELECT * FROM siswa WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Student exists, proceed to delete
        $query = "DELETE FROM siswa WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return "Data siswa berhasil dihapus!";
        } else {
            return "Gagal menghapus data siswa.";
        }
    } else {
        // Redirect to data.php with a message if no data is found
        header('Location: data.php?message=' . urlencode('Tidak ada data siswa yang ditemukan.'));
        exit();
    }
}


// Function to search students by username
function searchStudentsByUsername($conn, $searchTerm)
{
    $searchTerm = validateInput($searchTerm);

    $query = "SELECT id, nama, username, nis, jurusan, kelas, saldo FROM siswa WHERE username LIKE ?";
    $searchTerm = "%$searchTerm%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    } else {
        // Redirect to data.php with a message if no data is found
        header('Location: data.php?message=' . urlencode('Tidak ada data siswa yang ditemukan.'));
        exit();
    }
}



// Function to validate and sanitize input
function validateInput($data)
{
    return htmlspecialchars(trim($data));
}
