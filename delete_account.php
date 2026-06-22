<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Hapus Akun</title>

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

<h2 class="text-center text-danger mb-4">
Hapus Akun
</h2>

<!-- Alert Box Dinamis untuk Menampilkan Pesan Sukses / Error -->
<div id="statusAlert" class="alert d-none" role="alert"></div>

<div class="alert alert-warning">

Akun yang dihapus tidak dapat
dikembalikan kembali.

</div>

<form id="formDeleteAccount" novalidate>

<div class="mb-3">

<label>
Konfirmasi Password
</label>

<input
type="password"
name="password"
id="password"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-danger w-100"
id="submitBtn">

<span id="btnText">Hapus Akun Permanen</span>
<span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>

</button>

</form>

<a
href="dashboard.php"
class="btn btn-secondary w-100 mt-3">

Batal

</a>

</div>

</div>

</div>

</div>

</div>

<!-- JavaScript Integrasi Fetch API -->
<script>
document.getElementById('formDeleteAccount').addEventListener('submit', async function (e) {
    // Mencegah browser melakukan submit form tradisional (reload halaman)
    e.preventDefault();

    const form = e.target;
    const alertElement = document.getElementById('statusAlert');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    // Konfirmasi tambahan sebelum mengirim request (pengaman ekstra di sisi UX)
    const yakin = confirm("Apakah Anda benar-benar yakin ingin menghapus akun ini secara permanen?");
    if (!yakin) {
        return;
    }

    const formData = new FormData(form);

    submitBtn.disabled = true;
    btnText.textContent = "Sedang Menghapus...";
    btnSpinner.classList.remove('d-none');
    alertElement.classList.add('d-none');

    try {
        const response = await fetch('delete_account_process.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        alertElement.classList.remove('d-none', 'alert-success', 'alert-danger');

        if (response.ok) {
            // Akun berhasil dihapus, sesi sudah dihancurkan oleh backend
            alertElement.classList.add('alert-success');
            alertElement.textContent = data.message;

            // Redirect ke login setelah sebentar, supaya pesan sukses terlihat
            setTimeout(function () {
                window.location.href = data.redirect || 'login.html';
            }, 1200);

        } else {
            alertElement.classList.add('alert-danger');
            alertElement.textContent = data.message || "Gagal menghapus akun.";

            submitBtn.disabled = false;
            btnText.textContent = "Hapus Akun Permanen";
            btnSpinner.classList.add('d-none');
        }

    } catch (error) {
        alertElement.classList.remove('d-none', 'alert-success');
        alertElement.classList.add('alert-danger');
        alertElement.textContent = "Gagal menghubungi server. Periksa koneksi lokal Anda.";
        console.error("Fetch error:", error);

        submitBtn.disabled = false;
        btnText.textContent = "Hapus Akun Permanen";
        btnSpinner.classList.add('d-none');
    }
});
</script>

</body>
</html>