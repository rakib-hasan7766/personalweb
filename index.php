<?php
// ১. ডাটাবেজ কানেকশন (PostgreSQL)
$host = "dpg-d85q0mnavr4c73d62fog-a"; 
$user = "perwebb"; 
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3"; 
$dbname = "perweb"; 
$port = "5432"; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($connection_string);

if (!$conn) { 
    die("Database Connection Failed!"); 
}

// ২. ডাটাবেজে নতুন লিংক সেভ করার কোর লজিক
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_link'])) {
    $site_name = pg_escape_string($conn, trim($_POST['site_name']));
    $site_url = pg_escape_string($conn, trim($_POST['site_url']));
    $category = pg_escape_string($conn, $_POST['category']);
    $description = pg_escape_string($conn, trim($_POST['description']));

    if (!empty($site_name) && !empty($site_url) && !empty($category)) {
        $query = "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$site_name', '$site_url', '$category', '$description')";
        $result = pg_query($conn, $query);
        if ($result) {
            $message = "
            <div class='flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold mb-6 shadow-sm animate-fade-in'>
                <div class='w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-base shadow-sm'>🚀</div>
                <div>নতুন লিংকটি সফলভাবে ডাটাবেজে সেভ এবং লাইভ হয়েছে!</div>
            </div>";
        } else {
            $message = "
            <div class='flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-sm font-semibold mb-6 shadow-sm animate-fade-in'>
                <div class='w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center text-base shadow-sm'>❌</div>
                <div>ডাটা ইনসার্ট করতে সমস্যা হয়েছে! আবার চেষ্টা করুন।</div>
            </div>";
        }
    }
}

// ৩. ডাটাবেজ থেকে লিংক সমূহ রিড করা
$query = "SELECT * FROM site_links ORDER BY id DESC";
$fetch_result = pg_query($conn, $query);

$links_by_category = [];
if ($fetch_result) {
    while ($row = pg_fetch_assoc($fetch_result)) {
        $cat = (!empty($row['category'])) ? trim($row['category']) : 'অন্যান্য সেবা';
        $links_by_category[$cat][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
