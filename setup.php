<?php
$host = "dpg-d85q0mnavr4c73d62fog-a.oregon-postgres.render.com"; 
$user = "perwebb"; 
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3"; 
$dbname = "perweb"; 
$port = "5432"; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($connection_string);

if (!$conn) {
    die("Database Connection Failed!");
}

// টেবিল তৈরি করার কুয়েরি (বাকি অংশ আগের মতোই থাকবে)
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
} else {
    echo "এরর হয়েছে: " . pg_last_error($conn);
}
?>
