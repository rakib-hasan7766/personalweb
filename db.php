<?php
// Render থেকে পাওয়া আপনার External Database URL টি এখানে বসাবেন
$db_url = "postgres://your_user:your_password@your_host.render.com/your_dbname";

$db_config = parse_url($db_url);
$host = $db_config['dpg-d85q0mnavr4c73d62fog-a'];
$user = $db_config['perwebb'];
$password = $db_config['NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3'];
$dbname = ltrim($db_config['perweb'], '/');
$port = $db_config['port'];

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($connection_string);

if (!$conn) {
    die("সার্ভার কানেকশন সাময়িকভাবে ব্যাহত হচ্ছে।");
}
?>
