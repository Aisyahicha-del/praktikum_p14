<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once 'db.php';

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Profil</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    min-height:100vh;

    background:
    linear-gradient(
        rgba(15,23,42,.45),
        rgba(37,99,235,.35)
    ),
    url('assets/img/bg.jpg');

    background-size:cover;
    background-position:center;
}

.card-box{
    background:rgba(255,255,255,.95);
    border-radius:25px;
    border:none;
}

</style>
</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card card-box shadow">

<div class="card-body p-4">

<h2 class="text-center mb-4">
Edit Profil
</h2>

<!-- Alert Box Dinamis untuk Menampilkan Pesan Sukses / Error -->
<div id="statusAlert" class="alert d-none" role="alert"></div>

<form id="formUpdateProfile" novalidate>

<div class="mb-3">
<label>Email Baru</label>
<input
type="email"
name="email"
id="email"
class="form-control"
value="<?= htmlspecialchars($user['email']); ?>"
required>
</div>

<div class="mb-3">
<label>Password Lama</label>
<input
type="password"
name="password_lama"
id="password_lama"
class="form-control"
placeholder="Masukkan password Anda saat ini"
required>
</div>

<div class="mb-3">
<label>Password Baru</label>
<input
type="password"
name="password_baru"
id="password_baru"
class="form-control"
placeholder="Min. 6 karakter"
required>
</div>

<button
type="submit"
class="btn btn-primary w-100"
id="submitBtn">

<span id="btnText">Simpan Perubahan</span>
<span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>

</button>

</form>

<a
href="dashboard.php"
class="btn btn-secondary w-100 mt-3">

Kembali

</a>

</div>

</div>

</div>

</div>

</div>

<!-- JavaScript Integrasi Fetch API -->
<script>
document.getElementById('formUpdateProfile').addEventListener('submit', async function (e) {
    // Mencegah browser melakukan submit form tradisional (reload halaman)
    e.preventDefault();

    const form = e.target;
    const alertElement = document.getElementById('statusAlert');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    const formData = new FormData(form);

    // Atur Status UI: Mengubah tombol ke mode Loading
    submitBtn.disabled = true;
    btnText.textContent = "Sedang Menyimpan...";
    btnSpinner.classList.remove('d-none');
    alertElement.classList.add('d-none');

    try {
        const response = await fetch('update_profile_process.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        alertElement.classList.remove('d-none', 'alert-success', 'alert-danger');

        if (response.ok) {
            alertElement.classList.add('alert-success');
            alertElement.textContent = data.message;

            // Kosongkan kolom password agar tidak tersisa di form
            document.getElementById('password_lama').value = '';
            document.getElementById('password_baru').value = '';

        } else {
            alertElement.classList.add('alert-danger');
            alertElement.textContent = data.message || "Gagal memperbarui profil.";
        }

    } catch (error) {
        alertElement.classList.remove('d-none', 'alert-success');
        alertElement.classList.add('alert-danger');
        alertElement.textContent = "Gagal menghubungi server. Periksa koneksi lokal Anda.";
        console.error("Fetch error:", error);

    } finally {
        submitBtn.disabled = false;
        btnText.textContent = "Simpan Perubahan";
        btnSpinner.classList.add('d-none');
    }
});
</script>

</body>
</html>
?>