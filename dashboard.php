<?php
session_start();
require 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['message'])) {
    echo "<p>{$_SESSION['message']}</p>";
    unset($_SESSION['message']);
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil room_code user
$stmt = mysqli_prepare($conn, "SELECT room_code FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $room_code);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - File Manager</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: #003366;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .navbar h1 {
            margin: 0;
            font-size: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 10px;
        }

        .navbar img {
            height: 50px;
            margin-left: 15px;
        }

        .container {
            display: flex;
            flex: 1;
        }

        .sidebar {
            width: 250px;
            background-color: #002244;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }

        .sidebar h3 {
            color: #ccc;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin: 15px 0;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #fff;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .notification-icon {
            cursor: pointer;
            font-size: 20px;
            margin-right: 15px;
        }

        .notification-box {
            position: absolute;
            top: 60px;
            right: 30px;
            width: 340px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            padding: 15px;
            z-index: 1000;
            display: none;
            color: #000;
            font-size: 14px;
            line-height: 1.5;
        }

        .notification-box p {
            margin: 0 0 10px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .notification-box p:last-child {
            border-bottom: none;
        }

        .notification-box a {
            color: #007bff;
            text-decoration: none;
        }

        .notification-box a:hover {
            text-decoration: underline;
        }

        .notification-header {
            font-weight: bold;
            margin-bottom: 10px;
        }

        #viewer iframe {
            width: 100%;
            height: 600px;
        }

        #closePreview {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #cc0000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #closePreview:hover {
            background-color: #990000;
        }

        .copy-btn {
    margin-left: 10px;
    padding: 3px 8px;
    background-color: #eee;
    border: 1px solid #ccc;
    cursor: pointer;
    font-size: 12px;
}
.copy-btn:hover {
    background-color: #ddd;
}

    </style>
</head>
<body>
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <h1 style="margin: 0;">Sending File Manager</h1>
            <img src="logo.png" alt="Logo">
        </div>
        <div>
            <span class="notification-icon" onclick="toggleNotification()">🔔</span>
            Halo, <?= htmlspecialchars($username); ?> |
            <a href="logout.php">Logout</a>
        </div>

        <div class="notification-box" id="notificationBox">
            <div class="notification-header">Notifikasi Tim:</div>
            <?php
            $stmt = mysqli_prepare($conn, "
                SELECT a.action, a.created_at, u.username, f.file_name, f.file_path
                FROM activities a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN files f ON a.file_id = f.id
                WHERE u.room_code = ? AND a.user_id != ?
                ORDER BY a.created_at DESC
                LIMIT 5
            ");
            mysqli_stmt_bind_param($stmt, "si", $room_code, $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<p><strong>' . htmlspecialchars($row['username']) . ':</strong> '
                        . htmlspecialchars($row['action']);
                    if (!empty($row['file_name']) && !empty($row['file_path'])) {
                        echo ' - <a href="javascript:previewFile(\'' . htmlspecialchars($row['file_path']) . '\', \'' . pathinfo($row['file_path'], PATHINFO_EXTENSION) . '\')">'
                            . htmlspecialchars($row['file_name']) . '</a>';
                    }
                    echo ' (' . htmlspecialchars($row['created_at']) . ')</p>';
                }
            } else {
                echo '<p>Tidak ada notifikasi terbaru.</p>';
            }
            ?>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul>
                <li><a href="#">Dashboard</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="card" id="upload">
                <h2>Upload File</h2>
                <form action="upload_process.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="file" required>
                    <button type="submit" name="upload">Upload</button>
                </form>
            </div>

            <div class="card" id="files">
            <div class="card" id="files">
    <h2>File Tim Anda</h2>
    <?php
    $stmt = mysqli_prepare($conn, "
        SELECT f.file_name, f.file_path, f.file_type, u.username
        FROM files f
        JOIN users u ON f.user_id = u.id
        WHERE u.room_code = ?
        ORDER BY f.uploaded_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "s", $room_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            $file_path = htmlspecialchars($row['file_path']);
            $file_name = htmlspecialchars($row['file_name']);
            $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $username_file = htmlspecialchars($row['username']);

            echo "<li style='margin-bottom: 8px;'>
                    <input type='checkbox' class='file-check' value='$file_path' data-filename='$file_name'>
                    <a href=\"javascript:previewFile('$file_path', '$file_type')\">$file_name</a> 
                    <span style='color: gray; font-size: 13px;'>oleh $username_file</span>
                    <button onclick=\"deleteFile('$file_path')\" style='margin-left: 10px;'>Hapus</button>
                  </li>";
        }
        echo "</ul>";

        echo '
            <div style="margin-top: 15px;">
                <button onclick="shareWhatsApp()">Kirim ke WhatsApp</button>
                <button onclick="shareEmail()">Kirim ke Email</button>
            </div>
        ';
    } else {
        echo "<p>Belum ada file yang diunggah oleh tim Anda.</p>";
    }
    ?>
</div>



            <div class="card" id="viewer" style="display: none;">
                <h2>Pratinjau File</h2>
                <iframe id="fileViewer" frameborder="0"></iframe>
                <button id="closePreview" onclick="closePreview()">Tutup Pratinjau</button>
            </div>
        </div>
    </div>

    <script>
        function toggleNotification() {
            const box = document.getElementById('notificationBox');
            box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
        }

        document.addEventListener('click', function(event) {
            const notifIcon = event.target.closest('.notification-icon');
            const notifBox = document.getElementById('notificationBox');
            if (!notifIcon && !event.target.closest('#notificationBox')) {
                notifBox.style.display = 'none';
            }
        });

        function previewFile(path, type) {
            const viewer = document.getElementById("viewer");
            const iframe = document.getElementById("fileViewer");

            if (type === 'doc' || type === 'docx' || type === 'xls' || type === 'xlsx') {
                iframe.src = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(window.location.origin + '/' + path);
            } else if (type === 'pdf') {
                iframe.src = path;
            } else {
                iframe.src = '';
                alert("Tipe file tidak didukung untuk pratinjau.");
                return;
            }

            viewer.style.display = 'block';
        }

        function closePreview() {
            const viewer = document.getElementById("viewer");
            const iframe = document.getElementById("fileViewer");
            iframe.src = '';
            viewer.style.display = 'none';
        }

        function deleteFile(path) {
    console.log("Path dikirim: ", path);
    if (confirm('Yakin ingin menghapus file ini?')) {
        window.location.href = 'delete_file.php?file=' + encodeURIComponent(path);
    }
}

function getSelectedFiles() {
    const checkboxes = document.querySelectorAll('.file-check:checked');
    const files = [];
    checkboxes.forEach(cb => {
        files.push({
            path: cb.value,
            name: cb.getAttribute('data-filename')
        });
    });
    return files;
}

function shareWhatsApp() {
    const files = getSelectedFiles();
    if (files.length === 0) {
        alert('Pilih setidaknya satu file!');
        return;
    }

    let text = "Berikut file yang saya bagikan:\n";
    const origin = window.location.origin;

    files.forEach(f => {
        text += `${f.name}: ${origin}/${f.path}\n`;
    });

    const url = "https://wa.me/?text=" + encodeURIComponent(text);
    window.open(url, '_blank');
}

function shareEmail() {
    const files = getSelectedFiles();
    if (files.length === 0) {
        alert('Pilih setidaknya satu file!');
        return;
    }

    let body = "Berikut file yang saya bagikan:\n\n";
    const origin = window.location.origin;

    files.forEach(f => {
        body += `${f.name}: ${origin}/${f.path}\n`;
    });

    const subject = "File Sharing via File Manager";
    const mailto = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = mailto;
}

    </script>
</body>
</html>
