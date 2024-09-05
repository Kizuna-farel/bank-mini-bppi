<?php
session_start();
if (isset($_SESSION['admin']['siswa'])) {
    ?>
    <div id="logout-confirm">
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin logout?</p>
        <button id="logout-btn">Logout</button>
        <button id="cancel-btn">Batal</button>
    </div>

    <script type="text/javascript">
        document.getElementById('logout-btn').addEventListener('click', function () {
            <?php session_destroy(); ?>
            window.location.href = 'login.php';
        });

        document.getElementById('cancel-btn').addEventListener('click', function () {
            document.getElementById('logout-confirm').style.display = 'none';
        });
    </script>
    <?php
} else {
    header('Location: login.php');
    exit;
}
?>