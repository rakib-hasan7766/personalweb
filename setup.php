<?php
// Render থেকে পাওয়া আপনার External Database URL টি নিচে বসাবেন
$db_url = "postgres://your_user:your_password@your_host.render.com/your_dbname";

$db_config = parse_url($db_url);
$host = $db_config['host'];
$user = $db_config['user'];
$password = $db_config['pass'];
$dbname = ltrim($db_config['path'], '/');
$port = $db_config['port'];

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($connection_string);

if (!$conn) {
    die("Database Connection Failed!");
}

// ক্যাটাগরি এবং লিংক জমার টেবিল তৈরি করার কুয়েরি
$sql = "
CREATE TABLE IF NOT EXISTS site_links (
    id SERIAL PRIMARY KEY,
    site_name VARCHAR(255) NOT NULL,
    site_url TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if (pg_query($conn, $sql)) {
    echo "<h1>অভিনন্দন! সেবা পোর্টালের ডাটাবেজ টেবিল সফলভাবে তৈরি হয়েছে।</h1>";
    echo "<p>এখন আপনি এই ফাইলটি ব্রাউজারে একবার রান করার পর গিটহাব থেকে ডিলিট বা রিনেম করে দিতে পারেন নিরাপত্তার জন্য।</p>";
} else {
    echo "এরর হয়েছে: " . pg_last_error($conn);
}
?>
?>
