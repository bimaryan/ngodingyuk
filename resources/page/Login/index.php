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
        session_start();
        include '../koneksi.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $sql = "SELECT id, username, password FROM users WHERE username='$username'";
            $result = $koneksi->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Sukses',
                    text: 'Selamat datang, " . $row['username'] . "!',
                }).then(function() {
                    window.location.href = '../Home';
                });
            </script>";
                } else {
                    echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'Password Salah',
                });
            </script>";
                }
            } else {
                echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: 'Username tidak ditemukan',
            });
        </script>";
            }
        }

        $koneksi->close();
        ?>

        <h2 class="text-2xl font-semibold mb-6">Form Login</h2>
        <form action="" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username:</label>
                <input type="text" id="username" name="username" class="w-full p-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password:</label>
                <input type="password" id="password" name="password" class="w-full p-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer">Login</button>

            <!-- Tombol "Login" yang mengarah ke halaman login.php -->
            <p class="mt-4 text-sm text-gray-600">Kalian belum punya akun? <a href="../Register/" class="text-blue-500">Register</a></p>
        </form>
    </div>

</body>

</html>