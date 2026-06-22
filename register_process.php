<?php
// register_process.php - API Endpoint Registrasi Pengguna
header("Content-Type: application/json; charset=UTF-8");

require_once 'db.php';

// Batasi hanya untuk request ber-metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode request tidak didukung."]);
    exit;
}

// Menangkap data input secara aman dan membersihkan spasi tak perlu (sanitize)
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// 1. Validasi Sisi Server (Wajib Diisi)
if (empty($username) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Semua form wajib diisi."]);
    exit;
}

// 2. Validasi Format Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Format penulisan email tidak valid."]);
    exit;
}

// 3. Validasi Panjang Karakter Password Min. 6 Karakter
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Password minimal harus terdiri dari 6 karakter."]);
    exit;
}

try {
    // 4. Verifikasi Keunikan Akun (Username & Email tidak boleh ganda)
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
    $stmtCheck->execute([
        ':username' => $username,
        ':email' => $email
    ]);

    if ($stmtCheck->fetchColumn() > 0) {
        http_response_code(409); // Conflict
        echo json_encode(["status" => "error", "message" => "Username atau Email sudah terdaftar."]);
        exit;
    }

    // 5. Hashing Password Menggunakan Algoritma Bcrypt (Sangat Direkomendasikan)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 6. Menyimpan Data Menggunakan Prepared Statement Aman
    $queryInsert = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmtInsert = $pdo->prepare($queryInsert);
    $stmtInsert->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);

    // Berhasil mendaftarkan user baru
    http_response_code(201); // Created
    echo json_encode([
        "status" => "success",
        "message" => "Proses registrasi akun baru berhasil diselesaikan!"
    ]);

} catch (PDOException $e) {
    // Log kesalahan SQL internal untuk keperluan debugging developer
    error_log("Database Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Kegagalan sistem internal saat menyimpan data."
    ]);
}
?>