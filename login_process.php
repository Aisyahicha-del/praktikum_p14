<?php
// login_process.php - API Endpoint Login Pengguna (versi Fetch API)

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once 'db.php';

// Batasi hanya untuk request ber-metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Metode request tidak didukung."
    ]);
    exit;
}

// Menangkap data input secara aman
$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// 1. Validasi Sisi Server (Wajib Diisi)
if (empty($login) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Username/Email dan Password wajib diisi."
    ]);
    exit;
}

try {
    // 2. Cari user berdasarkan username ATAU email
    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE username = ?
        OR email = ?
    ");

    $stmt->execute([
        $login,
        $login
    ]);

    $user = $stmt->fetch();

    // 3. Verifikasi keberadaan user dan kecocokan password (hash)
    if ($user && password_verify($password, $user['password'])) {

        // Login berhasil, buat sesi user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil! Mengalihkan ke dashboard...",
            "redirect" => "dashboard.php"
        ]);

    } else {

        // Username/email tidak ditemukan ATAU password salah
        http_response_code(401); // Unauthorized
        echo json_encode([
            "status" => "error",
            "message" => "Username/Email atau Password salah!"
        ]);
    }

} catch (PDOException $e) {
    // Log kesalahan SQL internal untuk keperluan debugging developer
    error_log("Database Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Kegagalan sistem internal saat memproses login."
    ]);
}
?>