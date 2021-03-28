# LSN - Sismana | CodeIgniter 4 Based Web Application

## Kebutuhan Server 
1. PHP versi 7.2 keatas
2. MYSQL versi 5.1 keatas
3. Ekstensi *[intl](https://www.php.net/manual/en/intl.requirements.php), [mbstring](https://www.php.net/manual/en/mbstring.requirements.php), php-json, php-mysqlnd, php-xml* sudah terpasang di server

## Instalasi
1. Clone atau download repositori ini
2. Buka file /app/Config/Database.php
3. Ubah nilai username, password, dan database sesuai konfigurasi database yang akan dituju
4. Compile aplikasi ini 
5. Lalu kunjungi halaman [base url aplikasi ini]/public/main/install, untuk melakukan instalasi database
6. Terakhir, uji coba dengan login pada [base url aplikasi ini] menggunakan default user admin:adminganteng

### Cara Compile Aplikasi
- Pastikan PHP dengan versi sesuai kebutuhan server (PHP 7.2 keatas) sudah terinstall
- Pastikan [Composer](https://getcomposer.org/) sudah terinstall

1. Buka powershell atau terminal pada direktori aplikasi
2. Jalankan perintah *php builds release*
3. Lalu jalankan perintah *composer update*

### catatan
[base url aplikasi ini] merupakan base url tempat anda meletakkan aplikasi ini

contoh :  
1.[base url aplikasi ini]/public/main/install  == https://localhost/public/main/install, berarti [base url aplikasi ini] anda adalah https://localhost

2.[base url aplikasi ini]/public/main/install  == https://localhost:8081/app/public/main/install, berarti [base url aplikasi ini] anda adalah https://localhost:8081/app
