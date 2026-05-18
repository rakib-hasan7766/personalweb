<?php
include('db.php');
// ডাটাবেজ থেকে কন্টেন্ট রিড করা
$result = mysqli_query($conn, "SELECT * FROM site_settings WHERE id=1");
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title><?php echo $row['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="text-center space-y-4">
        <h1 class="text-4xl font-extrabold text-blue-600"><?php echo $row['title']; ?></h1>
        <p class="text-gray-600 max-w-md"><?php echo $row['description']; ?></p>
    </div>
</body>
</html>
