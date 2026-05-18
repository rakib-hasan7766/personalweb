<?php
// Render থেকে পাওয়া External Database URL টি এখানে বসাবেন
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

// টেবিল তৈরি এবং ডাটা ইনসার্ট করার কুয়েরি
$sql = "
CREATE TABLE IF NOT EXISTS site_settings (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255),
    description TEXT
);
INSERT INTO site_settings (title, description) 
SELECT 'Easy Buy BD', 'Welcome to our premium e-commerce site.'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE id = 1);
";

// কুয়েরি রান করা
if (pg_query($conn, $sql)) {
    echo "<h1>চমৎকার! ডাটাবেজ টেবিল এবং ডিফল্ট ডাটা সফলভাবে তৈরি হয়েছে।</h1>";
} else {
    echo "এরর হয়েছে: " . pg_last_error($conn);
}
?>
