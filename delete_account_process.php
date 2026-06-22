<?php
// delete_account_process.php - API Endpoint Penghapusan Akun Pengguna

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once 'db.php';

// 1. Pastikan user sudah login (proteksi endpoint)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Sesi Anda telah berakhir. Silakan login kembali."
    ]);
    exit;
}

// 2. Batasi hanya untuk request ber-metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Metode request tidak didukung."
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Menangkap data input secara aman
$password = isset($_POST['password']) ? $_POST['password'] : '';

// 3. Validasi Wajib Diisi
if (empty($password)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Konfirmasi password wajib diisi."
    ]);
    exit;
}

try {
    // 4. Ambil data user saat ini untuk verifikasi password
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Data pengguna tidak ditemukan."
        ]);
        exit;
    }

    // 5. Verifikasi Password Konfirmasi
    if (!password_verify($password, $user['password'])) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            "status" => "error",
            "message" => "Password salah! Akun tidak dihapus."
        ]);
        exit;
    }

    // 6. Hapus Data Menggunakan Prepared Statement
    $hapus = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $hapus->execute([$userId]);

    // 7. Hancurkan sesi setelah akun berhasil dihapus
    $_SESSION = [];
    session_destroy();

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Akun Anda telah berhasil dihapus. Mengalihkan ke halaman login...",
        "redirect" => "login.html"
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Kegagalan sistem internal saat menghapus akun."
    ]);
}
?>