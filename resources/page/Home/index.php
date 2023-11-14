<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/highlight.min.js"></script> -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
    <title>NgodingYuk</title>
    <!-- Add CSS or stylesheets as needed -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="font-sans bg-gray-100">
    <div class="mx-auto p-5">
        <?php 
        session_start();

        if (!isset($_SESSION['user_id'])) {
            // User is not logged in, show SweetAlert and redirect to login page
            echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required!',
                    text: 'You need to login to access this page.',
                }).then(function() {
                    window.location.href = '../login'; // Adjust the login page URL
                });
            </script>";
            exit; // Stop executing the rest of the code
        }

        include '../koneksi.php';

        // Function to check if content is code or not
        function isCode($content)
        {
            return preg_match('/<code\b[^>]*>(.*?)<\/code>/is', $content) || preg_match('/<pre\b[^>]*>(.*?)<\/pre>/is', $content);
        }

        // Function to get total posts
        function getTotalPosts()
        {
            global $koneksi;
            $result = $koneksi->query("SELECT COUNT(*) as total FROM posts");
            $row = $result->fetch_assoc();
            return $row['total'];
        }

        $postsPerPage = 5;
        $totalPosts = getTotalPosts();
        $totalPages = ceil($totalPosts / $postsPerPage);
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $currentPage = max(1, min($currentPage, $totalPages));
        $startIndex = ($currentPage - 1) * $postsPerPage;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['reply_content']) && isset($_POST['post_id'])) {
                $user_id = $_SESSION['user_id'];
                $post_id = $_POST['post_id'];
                $reply_content = $_POST['reply_content'];

                $replySql = "INSERT INTO replies (user_id, post_id, content) VALUES ('$user_id', '$post_id', '$reply_content')";

                if ($koneksi->query($replySql) === TRUE) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: 'Balasan berhasil ditambahkan',
                        }).then(function() {
                            window.location.href = './';
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
            } else {
                $user_id = $_SESSION['user_id'];
                $title = $_POST['title'];
                $content = $_POST['content'];

                $sql = "INSERT INTO posts (user_id, title, content) VALUES ('$user_id', '$title', '$content')";

                if ($koneksi->query($sql) === TRUE) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: 'Post berhasil dibuat',
                        }).then(function() {
                            window.location.href = './';
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

        $sql = "SELECT posts.*, users.username 
                FROM posts 
                INNER JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC LIMIT $startIndex, $postsPerPage";
        $result = $koneksi->query($sql);

        echo "<div class='flex justify-between items-center mb-8 gap-1'>";
        echo "<h1 class='text-4xl font-extrabold text-indigo-600'>Ngoding<span class='text-gray-800'>Yuk</span></h1>";

        if (isset($_SESSION['user_id'])) {
            // User is logged in, show create button and dark mode button
            echo "<div class='flex gap-2'>";
            echo "<button onclick='toggleCreateForm()' class='bg-blue-500 text-white px-3 py-2 rounded cursor-pointer'><i class='bi bi-plus-square'></i></button>";
            echo "<button onclick='toggleDarkModePopup()' class='bg-gray-700 text-white px-3 py-2 rounded cursor-pointer'><i class='bi bi-cloud-moon'></i></button>";
            echo "<a href='../logout.php' class='bg-red-700 text-white px-3 py-2 rounded cursor-pointer'><i class='bi bi-box-arrow-right'></i></a>";
            echo "</div>";
        }

        echo "</div>";

        echo '<marquee behavior="scroll" direction="left">' . '<div class="text-3xl font-bold mb-8 text-gray-800">' . 'Selamat Datang di NgodingYuk! - Silahkan kalian untuk tanya tugas atau code error tanyakan di sini saja yak😊' . '</div>' . '</marquee>';

        while ($row = $result->fetch_assoc()) {
            $postId = $row['id'];

            if (isset($_SESSION['user_id'])) {
                // Create form
                echo "<div id='createForm' class='mb-8 p-4 bg-white rounded-lg shadow-md hidden'>";
                echo "<h2 class='text-xl font-semibold mb-2 text-gray-800'>Buat Post Baru</h2>";
                echo "<form action='' method='post'>";
                echo "<label for='title' class='block text-gray-700 text-sm font-semibold mb-2'>Judul:</label>";
                echo "<input type='text' name='title' class='w-full p-2 border rounded mb-4' required placeholder='Silakan isi judul di sini...'>";
                echo "<label for='content' class='block text-gray-700 text-sm font-semibold mb-2'>Isi Post:</label>";
                echo "<textarea name='content' rows='4' class='w-full p-2 border rounded mb-4' style='resize: none;' required placeholder='Tulis isi post di sini...'></textarea>";
                echo "<input type='submit' value='Buat Post' class='bg-green-500 text-white px-4 py-2 rounded cursor-pointer'>";
                echo "</form>";
                echo "</div>";
            }

            echo "<div class='mb-8 p-4 bg-white rounded-lg shadow-md'>";
            echo "<h2 class='text-xl font-semibold mb-2 text-gray-800'>" . $row['title'] . "</h2>";
            echo "<p class='text-gray-600'>By: " . $row['username'] . "</p>";

            if (isCode($row['content'])) {
                echo "<p class='text-gray-700 leading-relaxed'>" . htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') . "</p>";
            } else {
                echo "<div class='my-4 p-4 bg-gray-200 rounded-lg relative'>";
                echo '<pre><code class="language-html language-css language-js">' . htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') . '</code></pre>';
                echo "<button class='bg-blue-500 text-white px-2 py-1 rounded absolute top-2 right-2 cursor-pointer' onclick='copyCode(this)'>Copy</button>";
                echo "</div>";
            }

            echo "<p class='text-gray-500 mt-2'>Dibuat pada: " . $row['created_at'] . "</p>";
            echo "<button class='bg-blue-500 text-white px-4 py-2 rounded cursor-pointer' onclick='toggleReplyForm($postId)'>Reply</button>";

            $replySql = "SELECT replies.*, users.username 
                        FROM replies 
                        INNER JOIN users ON replies.user_id = users.id 
                        WHERE replies.post_id = $postId
                        ORDER BY replies.created_at ASC";
            $replyResult = $koneksi->query($replySql);

            // Paginated comments
            $commentsPerPage = 2; // Adjust as needed
            $commentSql = "SELECT replies.*, users.username 
                            FROM replies 
                            INNER JOIN users ON replies.user_id = users.id 
                            WHERE replies.post_id = $postId
                            ORDER BY replies.created_at ASC";

            $commentResult = $koneksi->query($commentSql);
            $totalComments = $commentResult->num_rows;
            $totalCommentPages = ceil($totalComments / $commentsPerPage);

            $currentCommentPage = isset($_GET['comment_page'][$postId]) ? $_GET['comment_page'][$postId] : 1;
            $currentCommentPage = max(1, min($currentCommentPage, $totalCommentPages));

            $startCommentIndex = ($currentCommentPage - 1) * $commentsPerPage;

            // Display comments for the current page
            $commentResult = $koneksi->query("$commentSql LIMIT $startCommentIndex, $commentsPerPage");
            while ($commentRow = $commentResult->fetch_assoc()) {
                echo "<div class='mt-3 mb-4 p-4 bg-gray-100 rounded-lg shadow-md'>";
                echo "<h3 class=''>" . $commentRow['username'] . "</h3>";
                echo "<p class='text-gray-700 leading-relaxed'>" . htmlspecialchars($commentRow['content'], ENT_QUOTES, 'UTF-8') . "</p>";
                echo "<p class='text-gray-500 mt-2'>Dibuat pada: " . $commentRow['created_at'] . "</p>";
                echo "</div>";
            }

            // Comment pagination links
            echo "<div class='my-8 text-center'>";
            for ($i = 1; $i <= $totalCommentPages; $i++) {
                echo "<a href='?page=$currentPage&comment_page[$postId]=$i' class='px-4 py-2 mx-2 bg-blue-500 text-white rounded'>$i</a>";
            }
            echo "</div>";

            if (isset($_SESSION['user_id'])) {
                // Reply form
                echo "<form id='replyForm_$postId' class='hidden' action='' method='post'>";
                echo "<input type='hidden' name='post_id' value='$postId'>";
                echo "<label for='reply_content' class='block text-gray-700 text-sm font-semibold mb-2'>Balas:</label>";
                echo "<textarea name='reply_content' rows='2' class='w-full p-2 border rounded mb-2' style='resize: none;' required placeholder='Tulis balasan Anda...'></textarea>";
                echo "<input type='submit' value='Balas' class='bg-blue-500 text-white px-4 py-2 rounded cursor-pointer'>";
                echo "</form>";
            }
            echo "</div>";
        }

        echo "<div class='my-8 text'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?page=$i' class='px-4 py-2 mx-2 bg-blue-500 text-white rounded'>$i</a>";
        }
        echo "</div>";
        $koneksi->close();
        ?>
    </div>

    <script>
        function toggleCreateForm() {
            const createForm = document.getElementById('createForm');
            if (createForm) {
                createForm.classList.toggle('hidden');
            }
        }

        function toggleDarkModePopup() {
            const darkModePopup = document.getElementById('darkModePopup');
            if (darkModePopup) {
                darkModePopup.classList.toggle('hidden');
            }
        }
    </script>


    <script>
        function toggleReplyForm(postId) {
            const form = document.getElementById(`replyForm_${postId}`);
            if (form) {
                form.classList.toggle('hidden');
            }

            const comments = document.getElementById(`comments_${postId}`);
            if (comments) {
                comments.classList.toggle('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });

        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });

        function copyCode(button) {
            const codeElement = button.previousElementSibling.querySelector('code');
            const textArea = document.createElement('textarea');
            textArea.value = codeElement.innerText;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            // document.body.removeChild(textArea);

            button.innerText = 'Copied!';
            button.classList.remove('bg-blue-500');
            button.classList.add('bg-green-500');

            setTimeout(() => {
                button.innerText = 'Copy';
                button.classList.remove('bg-green-500');
                button.classList.add('bg-blue-500');
            }, 2000);
        }
    </script>
    <script>
        function toggleDarkModePopup() {
            // Use SweetAlert to show a popup for Dark Mode
            Swal.fire({
                title: 'Dark Mode',
                text: 'COMING SOON',
                icon: 'info',
                confirmButtonText: 'Close'
            });
        }
    </script>
</body>

</html>