<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Registrasi - NgodingYuk</title>
</head>

<body class="bg-gray-200 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded shadow-md w-96">
        <?php
        include '../koneksi.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Cek apakah username sudah ada di database
            $cek_username = "SELECT * FROM users WHERE username='$username'";
            $result = $koneksi->query($cek_username);

            if ($result->num_rows > 0) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Registrasi',
                        text: 'Nama pengguna sudah digunakan, silakan pilih nama pengguna lain',
                    });
                </script>";
            } else {
                // Jika nama pengguna belum ada, lakukan pendaftaran
                $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

                if ($koneksi->query($sql) === TRUE) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Registrasi Berhasil',
                            text: 'Anda dapat melakukan login sekarang',
                        }).then(function() {
                            window.location.href = '../Login';
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: " . $koneksi->error . "',
                        });
                    </script>";
                }
            }
        }

        $koneksi->close();
        ?>

        <h2 class="text-2xl font-semibold mb-6">Form Registrasi</h2>
        <form action="" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username:</label>
                <input type="text" id="username" name="username" class="w-full p-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password:</label>
                <input type="password" id="password" name="password" class="w-full p-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer">Registrasi</button>

            <!-- Tombol "Login" yang mengarah ke halaman login.php -->
            <p class="mt-4 text-sm text-gray-600">Sudah punya akun? <a href="../Login/" class="text-blue-500">Login</a></p>
        </form>
    </div>

</body>

</html>