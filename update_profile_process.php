<?php
// update_profile_process.php - API Endpoint Update Profil Pengguna

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
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$passwordLama = isset($_POST['password_lama']) ? $_POST['password_lama'] : '';
$passwordBaru = isset($_POST['password_baru']) ? $_POST['password_baru'] : '';

// 3. Validasi Wajib Diisi
if (empty($email) || empty($passwordLama) || empty($passwordBaru)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Email, password lama, dan password baru wajib diisi."
    ]);
    exit;
}

// 4. Validasi Format Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Format penulisan email tidak valid."
    ]);
    exit;
}

// 5. Validasi Panjang Karakter Password Baru Min. 6 Karakter
if (strlen($passwordBaru) < 6) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Password baru minimal harus terdiri dari 6 karakter."
    ]);
    exit;
}

try {
    // 6. Ambil data user saat ini untuk verifikasi password lama
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

    // 7. Verifikasi Password Lama
    if (!password_verify($passwordLama, $user['password'])) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            "status" => "error",
            "message" => "Password lama tidak sesuai!"
        ]);
        exit;
    }

    // 8. Verifikasi Keunikan Email (tidak boleh dipakai user lain)
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
    $stmtCheck->execute([
        ':email' => $email,
        ':id' => $userId
    ]);

    if ($stmtCheck->fetchColumn() > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "status" => "error",
            "message" => "Email sudah digunakan oleh akun lain."
        ]);
        exit;
    }

    // 9. Hashing Password Baru
    $hashPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

    // 10. Update Data Menggunakan Prepared Statement
    $update = $pdo->prepare("
        UPDATE users
        SET email = ?, password = ?
        WHERE id = ?
    ");

    $update->execute([
        $email,
        $hashPassword,
        $userId
    ]);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Profil berhasil diperbarui!"
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Kegagalan sistem internal saat memperbarui profil."
    ]);
}
?>