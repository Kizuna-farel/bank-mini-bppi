<?php
require 'function.php';

$id = intval($_GET['id']);
$student = getStudentById($conn, $id);

if ($student) {
    echo json_encode($student);
} else {
    echo json_encode([]);
}
?>