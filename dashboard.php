<?php
require_once 'db.php';

// লগইন করা না থাকলে জোর করে লগইন পেজে ফেরত পাঠানো
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php");
    exit;
}

// লগআউট প্রসেস
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: admin.php");
    exit;
}

// ডাটাবেজে লিংক সেভ করার হ্যান্ডলার
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_link'])) {
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
    <title>অ্যাডমিন ড্যাশবোর্ড - সেবা পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between">

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
                <a href="dashboard.php?action=logout" class="bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white px-3 py-2 rounded-xl text-xs font-bold border border-rose-100 transition-all">
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

            <form action="dashboard.php" method="POST" class="space-y-4 mt-2">
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ওয়েবসাইটের নাম <span class="text-rose-500">*</span></label>
                    <input type="text" name="site_name" required placeholder="যেমন: পাসপোর্ট স্ট্যাটাস চেক" 
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ওয়েবসাইট লিংক (URL) <span class="text-rose-500">*</span></label>
                    <input type="url" name="site_url" required placeholder="https://example.com" 
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ক্যাটাগরি সিলেক্ট করুন <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select name="category" required 
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm cursor-pointer appearance-none">
                            <option value="" disabled selected>ক্যাটাগরি বেছে নিন...</option>
                            <option value="सरकारी सेवा">সরকারি সেবা</option>
                            <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                            <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                            <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং</option>
                            <option value="ইউটিলিটি ও টুলস">ইউটিলিটি ও টুলস</option>
                            <option value="সফটওয়্যার ডাউনলোড">সফটওয়্যার ডাউনলোড</option>
                            <option value="ক্রিয়াটিভ ও এআই টুলস">ক্রিয়াটিভ ও এআই টুলস</option>
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
                              class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm transition-all shadow-sm resize-none"></textarea>
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

    <footer class="bg-white border-t border-slate-200 py-4">
        <div class="max-w-4xl mx-auto px-4 text-center text-[11px] text-slate-400 font-medium">
            <p>© <?php echo date('Y'); ?> অ্যাডমিন ড্যাশবোর্ড | মাল্টি-ফাইল ফিক্সড আর্কিটেকচার ⚡</p>
        </div>
    </footer>

</body>
</html>
