<?php
$host = "dpg-d85q0mnavr4c73d62fog-a.oregon-postgres.render.com"; 
$user = "perwebb"; 
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3"; 
$dbname = "perweb"; 
$port = "5432"; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($connection_string);

if (!$conn) { die("Database Connection Failed!"); }

// টেবিল তৈরির কুয়েরি
$sql_table = "
CREATE TABLE IF NOT EXISTS site_links (
    id SERIAL PRIMARY KEY,
    site_name VARCHAR(255) NOT NULL,
    site_url TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
pg_query($conn, $sql_table);

// বাংলাদেশের সকল দরকারি লিংকের অ্যারে
$default_links = [
    // সরকারি সেবা
    ['জাতীয় তথ্য বাতায়ন', 'https://bangladesh.gov.bd', 'সরকারি সেবা', 'বাংলাদেশের সব সরকারি মন্ত্রণালয় ও সেবার মেইন পোর্টাল।'],
    ['এনআইডি পোর্টাল (NID)', 'https://services.nidw.gov.bd', '商业 ও সরকারি সেবা', 'জাতীয় পরিচয়পত্র ডাউনলোড, সংশোধন ও নতুন নিবন্ধনের সাইট।'],
    ['অনলাইন পাসপোর্ট আবেদন', 'https://www.epassport.gov.bd', 'সরকারি সেবা', 'ই-পাসপোর্ট আবেদন এবং স্ট্যাটাস চেক করার অফিশিয়াল পোর্টাল।'],
    ['জন্ম ও মৃত্যু নিবন্ধন', 'https://bdris.gov.bd', 'সরকারি সেবা', 'নতুন জন্ম নিবন্ধন আবেদন ও যাচাইকরণের সরকারি সাইট।'],
    ['বাংলাদেশ ফরম পোর্টাল', 'https://forms.gov.bd', 'সরকারি সেবা', 'সব ধরনের সরকারি আবেদনের ফরম এক জায়গায় ডাউনলোড করুন।'],
    ['ই-নামজারি আবেদন', 'https://mutation.land.gov.bd', 'Annyanno', 'জমির ই-নামজারি বা মিউটেশনের অনলাইন আবেদন পোর্টাল।'],
    
    // শিক্ষা ও রেজাল্ট
    ['এডুকেশন বোর্ড রেজাল্ট', 'http://www.educationboardresults.gov.bd', 'শিক্ষা ও রেজাল্ট', 'SSC, HSC, JSC পরীক্ষার অফিশিয়াল রেজাল্ট দেখার মেইন সাইট।'],
    ['Web Based Result', 'https://eboardresults.com', 'শিক্ষা ও রেজাল্ট', 'জেলা বা প্রতিষ্ঠান ভিত্তিক বিশদ রেজাল্ট দেখার অনলাইন মাধ্যম।'],
    ['একাদশে ভর্তি পোর্টাল', 'http://xiclassadmission.gov.bd', 'শিক্ষা ও রেজাল্ট', 'কলেজে একাদশ শ্রেণিতে অনলাইনের মাধ্যমে ভর্তির আবেদন সাইট।'],
    ['মাধ্যমিক ও উচ্চশিক্ষা অধিদপ্তর', 'https://dshe.gov.bd', 'শিক্ষা ও রেজাল্ট', 'শিক্ষা সংক্রান্ত নোটিশ ও নির্দেশনাবলী দেখার সরকারি সাইট।'],

    // পেমেন্ট ও ব্যাংকিং
    ['বিকাশ (bKash)', 'https://www.bkash.com', 'পেমেন্ট ও ব্যাংকিং', 'বাংলাদেশের সবচেয়ে বড় মোবাইল ফিনান্সিয়াল সার্ভিস।'],
    ['নগদ (Nagad)', 'https://nagad.com.bd', 'পেমেন্ট ও ব্যাংকিং', 'ডাক বিভাগের ডিজিটাল লেনদেন ও মোবাইল ব্যাংকিং সেবা।'],
    ['রকেট (Rocket - DBBL)', 'https://www.dutchbanglabank.com/rocket/rocket.html', 'পেমেন্ট ও ব্যাংকিং', 'ডাচ-বাংলা ব্যাংকের মোবাইল ব্যাংকিং ও ইউটিলিটি বিল পেমেন্ট।'],
    ['সেলফিন (CellFin)', 'https://www.islamibankbd.com', 'পেমেন্ট ও ব্যাংকিং', 'ইসলামী ব্যাংকের জনপ্রিয় ডিজিটাল ওয়ালেট ও ব্যাংকিং অ্যাপ।'],

    // ই-কমার্স ও শপিং
    ['দারাজ বাংলাদেশ (Daraz)', 'https://www.daraz.com.bd', 'ই-কমার্স ও শপিং', 'বাংলাদেশের অন্যতম বৃহৎ অনলাইন শপিং মল ও মার্কেটপ্লেস।'],
    ['রকমারি ডট কম', 'https://www.rokomari.com', 'ই-কমার্স ও শপিং', 'বই, ইলেকট্রনিক্স ও নানাবিধ পণ্য কেনার জনপ্রিয় ই-কমার্স সাইট।'],
    ['চালডাল অনলাইন গ্রোসারি', 'https://chaldal.com', 'ই-কমার্স ও শপিং', 'নিত্যপ্রয়োজনীয় মুদি সামগ্রী ঘরে বসে অর্ডারের অনলাইন শপ।'],
    ['Pickaboo', 'https://www.pickaboo.com', 'ই-কমার্স ও শপিং', 'অরিজিনাল মোবাইল, গ্যাজেট ও ইলেকট্রনিক্স সামগ্রীর ই-কমার্স।'],
    
    // অন্যান্য
    ['বিআরটিএ সেবা পোর্টাল', 'https://bsp.brta.gov.bd', 'অন্যান্য দরকারি লিংক', 'ড্রাইভিং লাইসেন্স ও বাইক/গাড়ির রেজিস্ট্রেশন চেক করার পোর্টাল।'],
    ['বাংলাদেশ রেলওয়ে (টিকিট)', 'https://eticket.railway.gov.bd', 'Annyanno', 'অনলাইনে ট্রেনের সিট সিলেক্ট করে টিকিট কাটার অফিশিয়াল সাইট।'],
    ['অনলাইন জিডি (BD Police)', 'https://gd.police.gov.bd', 'অন্যান্য দরকারি লিংক', 'থানায় না গিয়ে অনলাইনে ঘরে বসে সাধারণ ডায়েরি বা GD করার অ্যাপ।']
];

// ডেটা ডুপ্লিকেট না করে ইনসার্ট করা
foreach ($default_links as $link) {
    $name = pg_escape_string($conn, $link[0]);
    $url = pg_escape_string($conn, $link[1]);
    $cat = pg_escape_string($conn, $link[2]);
    $desc = pg_escape_string($conn, $link[3]);
    
    $check = pg_query($conn, "SELECT 1 FROM site_links WHERE site_url='$url'");
    if (pg_num_rows($check) == 0) {
        pg_query($conn, "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$name', '$url', '$cat', '$desc')");
    }
}

echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
        <h1 style='color:#10b981;'>🔥 পারফেক্ট! সব লিংক ডাটাবেজে যুক্ত করা হয়েছে!</h1>
        <p>এখন আপনার মেইন সাইটে যান, সব লিংক প্রফেশনাল লুকে দেখতে পাবেন।</p>
      </div>";
?>
