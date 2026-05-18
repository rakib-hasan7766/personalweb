<?php
include('db.php');

// ফর্মে সাবমিট করা ডাটা ডাটাবেজে আপডেট করার লজিক
if (isset($_POST['update_content'])) {
    $new_title = $_POST['site_title'];
    $new_desc = $_POST['site_desc'];

    // আইডি ১ এর ডাটা আপডেট করা হচ্ছে
    $update_query = "UPDATE site_settings SET title='$new_title', description='$new_desc' WHERE id=1";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Backend theke successfully update hoyeche!');</script>";
    }
}

// ডাটাবেজ থেকে বর্তমান কন্টেন্ট তুলে আনা
$result = mysqli_query($conn, "SELECT * FROM site_settings WHERE id=1");
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Backend Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md mt-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Backend Edit Panel</h2>
        
        <form action="admin.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Website Title:</label>
                <input type="text" name="site_title" value="<?php echo $row['title']; ?>" class="w-full p-2.5 border rounded-lg mt-1">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Website Description:</label>
                <textarea name="site_desc" rows="4" class="w-full p-2.5 border rounded-lg mt-1"><?php echo $row['description']; ?></textarea>
            </div>
            <button type="submit" name="update_content" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition">Update Live Site</button>
        </form>
    </div>
</body>
</html>
