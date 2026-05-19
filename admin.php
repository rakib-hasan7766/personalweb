<?php
// সেশন স্টার্ট করা যেন লগইন ডেটা মনে রাখতে পারে
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ডাটাবেজ কানেকশন
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

// লগআউট প্রসেস 
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: admin.php");
    exit;
}

// লগইন চেক লজিক (বাটনে ক্লিক করলে তাৎক্ষণিক সেশন চালু হবে)
$login_error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $pass = trim($_POST['password']);
    
    // ইউজারনেম ও পাসওয়ার্ড (আপনার ইচ্ছামতো পরিবর্তন করতে পারবেন)
    if ($username === 'rakib' && $pass === 'rakib123') {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "❌ ইউজারনেম বা পাসওয়ার্ড সঠিক নয়!";
    }
}

// ডাটাবেজে লিংক সেভ করার হ্যান্ডলার (আপনার আগের কোডের লজিক)
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_link'])) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        die("Unauthorized Access!");
    }
    
    $site_name = pg_escape_string($conn, $_POST['site_name']);
    $site_url = pg_escape_string($conn, $_POST['site_url']);
    $category = pg_escape_string($conn, $_POST['category']);
    $description = pg_escape_string($conn, $_POST['description']);

    if (!empty($site_name) && !empty($site_url) && !empty($category)) {
        $query = "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$site_name', '$site_url', '$category', '$description')";
        $result = pg_query($conn, $query);
        if ($result) {
            $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2'><span>🚀</span> নতুন লিংকটি সফলভাবে ডাটাবেজে সেভ হয়েছে!</div>";
        } else {
            $message = "<div class='bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2'><span>❌</span> ডাটাবেজ প্রবলেম! আবার চেষ্টা করুন।</div>";
        }
    } else {
        $message = "<div class='bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2'><span>⚠️</span> স্টার (*) চিহ্নিত ঘরগুলো পূরণ করুন।</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>অ্যাডমিন প্যানেল - সেবা পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; }
        /* মোবাইলে ইনপুট বক্সে ক্লিক করলে জুম হওয়া বন্ধ করার ট্রিক */
        @media screen and (max-width: 768px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
        <main class="flex-grow flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                        🔒
                    </div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight uppercase">Admin Lock</h2>
                    <p class="text-xs text-slate-400 mt-1">শুধুমাত্র রাকিব ভাই এই প্যানেলে প্রবেশ করতে পারবেন</p>
                </div>

                <?php if (!empty($login_error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-2.5 rounded-xl text-xs font-semibold mb-4 text-center">
                        <?php echo $login_error; ?>
                    </div>
                <?php endif; ?>

                <form action="admin.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username</label>
                        <input type="text" name="username" required placeholder="ইউজারনেম লিখুন" 
                               class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                    </div>
                    <button type="submit" name="login_btn" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md flex items-center justify-center gap-2 mt-2">
                        <span>প্যানেল আনলক করুন</span> ➔
                    </button>
                </form>
                
                <div class="text-center mt-6">
                    <a href="index.php" class="text-xs text-blue-600 hover:underline font-medium">← ব্যাক টু হোম সাইট</a>
                </div>
            </div>
        </main>

    <?php else: ?>
        <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-blue-600 flex items-center gap-2">
                        🛠️ <span>অ্যাডমিন ড্যাশবোর্ড</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">রিসোর্স ও সাইট লিংক ম্যানেজমেন্ট</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="index.php" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-xl text-xs font-bold border border-slate-200 transition-all shadow-sm">
                        লাইভ সাইট ➔
                    </a>
                    <a href="admin.php?action=logout" class="bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white px-3 py-2 rounded-xl text-xs font-bold border border-rose-100 transition-all">
                        লগআউট 📴
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-xl w-full mx-auto px-4 py-8 flex-grow flex flex-col justify-center">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
                
                <div class="mb-5 border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                        <span>➕</span> নতুন লিংক যুক্ত করুন
                    </h2>
                </div>

                <?php if(!empty($message)) echo $message; ?>

                <form action="admin.php" method="POST" class="space-y-4 mt-2">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ওয়েবসাইটের নাম <span class="text-rose-500">*</span></label>
                        <input type="text" name="site_name" required placeholder="যেমন: পাসপোর্ট স্ট্যাটাস চেক" 
                               class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ওয়েবসাইট লিংক (URL) <span class="text-rose-500">*</span></label>
                        <input type="url" name="site_url" required placeholder="https://example.com" 
                               class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ক্যাটাগরি সিলেক্ট করুন <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select name="category" required 
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm cursor-pointer appearance-none">
                                <option value="" disabled selected>ক্যাটাগরি বেছে নিন...</option>
                                <option value="সরকারি সেবা">সরকারি সেবা</option>
                                <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                                <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                                <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং</option>
                                <option value="ইউটিলিটি ও টুলস">ইউটিলিটি ও টুলস</option>
                                <option value="সফটওয়্যার ডাউনলোড">সফটওয়্যার ডাউনলোড</option>
                                <option value="ক্রিয়াティブ ও এআই টুলস">ক্রিয়াティブ ও এআই টুলস</option>
                                <option value="রাকিব ড্রাইভ রিসোর্স">রাকিব ড্রাইভ রিসোর্স</option>
                                <option value="প্রিমিয়াম ভিডিও কোর্স">প্রিমিয়াম ভিডিও কোর্স</option>
                                <option value="Annyanno">Annyanno</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 text-xs">▼</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ছোট বিবরণ (অপশনাল)</label>
                        <textarea name="description" rows="3" placeholder="সাইটের কাজ সম্পর্কে সংক্ষেপে লিখুন..." 
                                  class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm resize-none"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" name="add_link" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md flex items-center justify-center gap-2">
                        <span>লিংক লাইভ করুন</span> ➔
                    </button>
                </div>

            </form>
        </div>
    </main>
    <?php endif; ?>

    <footer class="bg-white border-t border-slate-200 py-4">
        <div class="max-w-4xl mx-auto px-4 text-center text-[11px] text-slate-400 font-medium">
            <p>© <?php echo date('Y'); ?> অ্যাডমিন ড্যাশবোর্ড | ওয়ান-পেজ ইন্টিগ্রেশন সাকসেসফুল ⚡</p>
        </div>
    </footer>

</body>
</html>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                    🔒
                </div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight uppercase">Admin Lock</h2>
                <p class="text-xs text-slate-400 mt-1">শুধুমাত্র রাকিব ভাই এই প্যানেলে প্রবেশ করতে পারবেন</p>
            </div>

            <?php if (!empty($login_error)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-2.5 rounded-xl text-xs font-semibold mb-4 text-center">
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form action="admin.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username</label>
                    <input type="text" name="username" required placeholder="ইউজারনেম লিখুন" 
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                </div>
                <button type="submit" name="login_btn" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md flex items-center justify-center gap-2 mt-2">
                    <span>প্যানেল আনলক করুন</span> ➔
                </button>
            </form>
            
            <div class="text-center mt-6">
                <a href="index.php" class="text-xs text-blue-600 hover:underline font-medium">← ব্যাক টু হোম সাইট</a>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4">
        <div class="max-w-4xl mx-auto px-4 text-center text-[11px] text-slate-400 font-medium">
            <p>© <?php echo date('Y'); ?> সিকিউর লগইন গেটওয়ে ⚡</p>
        </div>
    </footer>

</body>
</html>
