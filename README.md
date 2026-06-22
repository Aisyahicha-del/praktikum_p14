# Sistem Autentikasi Pengguna

## Identitas Mahasiswa

Nama Kelompok & NIM : 
1. Syifa Salma (124005006)
2. Silvi Rusmiati (124005010)
3. Salma Ikhtifama (124005011)
4. Siti Aisyah (124005020)

## Deskripsi Aplikasi

Sistem Autentikasi Pengguna merupakan aplikasi berbasis web yang dibuat menggunakan PHP, MySQL, HTML, CSS, Bootstrap, dan JavaScript (Fetch API).

Aplikasi ini memiliki fitur:

* Registrasi Akun
* Login Pengguna
* Dashboard User
* Edit Profil
* Hapus Akun
* Logout
* Session Login
* AJAX (Fetch API)

## Teknologi yang Digunakan

* HTML5
* CSS3
* Bootstrap 5
* JavaScript
* Fetch API
* PHP
* MySQL
* XAMPP

## Struktur Project

praktikum_p14/

* index.html
* login.html
* dashboard.php
* update_profile.php
* delete_account.php
* logout.php
* register_process.php
* login_process.php
* db.php
* assets/img/bg.jpg

## Cara Instalasi dan Menjalankan Aplikasi

1. Install XAMPP.
2. Jalankan Apache dan MySQL.
3. Salin folder project ke direktori htdocs.
4. Buka phpMyAdmin.
5. Buat database dengan nama:

db_web_p14

6. Import file database:

db_web_p14.sql

7. Buka browser dan akses:

http://localhost/pwd_aisyah/praktikum_p14/

8. Registrasikan akun baru atau login menggunakan akun yang tersedia.

## Fitur Sistem

### Registrasi

Pengguna dapat membuat akun baru menggunakan username, email, dan password.

### Login

Pengguna dapat login menggunakan username atau email dan password.

### Dashboard

Menampilkan informasi akun pengguna yang sedang login.

### Edit Profil

Pengguna dapat mengubah username dan email.

### Hapus Akun

Pengguna dapat menghapus akun secara permanen.

### Logout

Pengguna dapat keluar dari sistem dengan aman menggunakan session PHP.

## Database

Nama Database:

db_web_p14

Tabel:

users

Kolom:

* id
* username
* email
* password
* created_at
