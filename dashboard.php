<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once 'db.php';

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([
    $_SESSION['user_id']
]);

$user = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Pengguna</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body{
    min-height:100vh;

    background:
    linear-gradient(
        rgba(15,23,42,.50),
        rgba(37,99,235,.35)
    ),
    url('assets/img/bg.jpg');

    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
}

.glass-card{
    background:rgba(255,255,255,.93);
    backdrop-filter:blur(10px);
    border:none;
    border-radius:25px;
    box-shadow:0 15px 40px rgba(0,0,0,.20);
}

.profile-img{
    width:120px;
    height:120px;
    border-radius:50%;
    background:#1E3A8A;
    color:white;
    font-size:50px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin:auto;
}

.stat-card{
    border:none;
    border-radius:20px;
    transition:.3s;
}

.stat-card:hover{
    transform:translateY(-5px);
}

</style>

</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-9">

            <div class="glass-card p-5">

                <div class="text-center">

                    <div class="profile-img">
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <h2 class="mt-3">
                        Halo,
                        <?= htmlspecialchars($user['username']); ?>
                        👋
                    </h2>

                    <p class="text-muted">
                        Selamat datang di Dashboard Pengguna
                    </p>

                </div>

                <hr>

                <div class="row text-center mb-4">

                    <div class="col-md-4 mb-3">

                        <div class="card stat-card shadow-sm">

                            <div class="card-body">

                                <h5>ID User</h5>

                                <h2 class="text-primary">
                                    <?= $user['id']; ?>
                                </h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 mb-3">

                        <div class="card stat-card shadow-sm">

                            <div class="card-body">

                                <h5>Status</h5>

                                <h2 class="text-success">
                                    Aktif
                                </h2>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 mb-3">

                        <div class="card stat-card shadow-sm">

                            <div class="card-body">

                                <h5>Role</h5>

                                <h2 class="text-info">
                                    User
                                </h2>

                            </div>

                        </div>

                    </div>

                </div>

                <table class="table table-bordered">

                    <tr>
                        <th width="30%">
                            Username
                        </th>
                        <td>
                            <?= htmlspecialchars($user['username']); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>
                            <?= htmlspecialchars($user['email']); ?>
                        </td>
                    </tr>

                </table>

                <div class="d-grid gap-2 mt-4">

                    <a
                        href="update_profile.php"
                        class="btn btn-primary">

                        <i class="bi bi-pencil-square"></i>
                        Edit Profil

                    </a>

                    <a
                        href="delete_account.php"
                        class="btn btn-outline-danger">

                        <i class="bi bi-trash"></i>
                        Hapus Akun

                    </a>

                    <a
                        href="logout.php"
                        id="logoutBtn"
                        class="btn btn-dark">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
// Tampilkan dialog konfirmasi sebelum benar-benar logout
document.getElementById('logoutBtn').addEventListener('click', function (e) {
    const yakin = confirm("Apakah Anda yakin ingin logout?");
    if (!yakin) {
        e.preventDefault(); // Batalkan navigasi ke logout.php
    }
});
</script>

</body>
</html>
?>