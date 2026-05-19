<?php
$host = "dpg-d85q0mnavr4c73d62fog-a.oregon-postgres.render.com"; 
$user = "perwebb"; 
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3"; 
$dbname = "perweb"; 
$port = "5432"; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($connection_string);

if (!$conn) { die("Database Connection Failed!"); }

// ফর্ম সাবমিট হলে ডেটা ইনসার্ট করা
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
            $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm mb-4 font-medium'>🚀 নতুন লিংকটি সফলভাবে ডাটাবেজে সেভ হয়েছে!</div>";
        } else {
            $message = "<div class='bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm mb-4 font-medium'>❌ লিংক সেভ করতে সমস্যা হয়েছে।</div>";
        }
    } else {
        $message = "<div class='bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm mb-4 font-medium'>⚠️ দয়া করে সব জরুরি ঘরগুলো পূরণ করুন।</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সেবা পোর্টাল - অ্যাডমিন ড্যাশবোর্ড</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Hind Siliguri', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-blue-600 flex items-center gap-2">
                    🛠️ <span>অ্যাডমিন ড্যাশবোর্ড</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">নতুন রিসোর্স ও সেবা লিংক যুক্ত করুন</p>
            </div>
            <a href="index.php" target="_blank" class="text-xs bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-3 py-2 rounded-xl transition-all font-medium flex items-center gap-1">
                লাইভ সাইট ➔
            </a>
        </div>
    </header>

    <main class="max-w-2xl w-full mx-auto px-4 py-8 flex-grow flex flex-col justify-center">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
            
            <div class="mb-6 text-center sm:text-left">
                <h2 class="text-lg font-bold text-slate-800">새 নতুন লিংক সিকিউরলি যুক্ত করুন</h2>
                <p class="text-xs text-slate-500 mt-0.5">নিচের ফর্মটি পূরণ করে ডাটাবেজে ডাটা পাঠান</p>
            </div>

            <?php echo $message; ?>

            <form action="admin.php" method="POST" class="space-y-5">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">ওয়েবসাইটের নাম <span class="text-rose-500">*</span></label>
                    <input type="text" name="site_name" required placeholder="যেমন: ই-পাসপোর্ট পোর্টাল" 
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">ওয়েবসাইট লিংক (URL) <span class="text-rose-500">*</span></label>
                    <input type="url" name="site_url" required placeholder="https://example.com" 
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">ক্যাটাগরি সিলেক্ট করুন <span class="text-rose-500">*</span></label>
                    <select name="category" required 
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all shadow-sm cursor-pointer">
                        <option value="সরকারি সেবা">সরকারি সেবা</option>
                        <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                        <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                        <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং</option>
                        <option value="ইউটিলিটি ও টুলস">ইউটিলিটি ও টুলস</option>
                        <option value="স프트ওয়্যার ডাউনলোড">সফটওয়্যার ডাউনলোড</option>
                        <option value="ক্রিয়েটিভ ও এআই টুলস">ক্রিয়েটিভ ও এআই টুলস</option>
                        <option value="রাকিব ড্রাইভ রিসোর্স">রাকিব ড্রাইভ রিসোর্স</option>
                        <option value="প্রিমিয়াম ভিডিও কোর্স">প্রিমিয়াম ভিডিও কোর্স</option>
                        <option value="Annyanno">Annyanno</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">ছোট বিবরণ (অপশনাল)</label>
                    <textarea name="description" rows="3" placeholder="সাইটের কাজ সম্পর্কে সংক্ষেপে লিখুন..." 
                              class="w-full px-4 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all shadow-sm resize-none"></textarea>
                </div>

                <button type="submit" name="add_link" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform active:scale-[0.99]">
                    <span>ডাটাবেজে সেভ করুন</span> ➔
                </button>

            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4">
        <div class="max-w-4xl mx-auto px-4 text-center text-xs text-slate-400">
            <p>© <?php echo date('Y'); ?> অ্যাডমিন ম্যানেজমেন্ট প্যানেল | ক্লিন অ্যান্ড ফিক্সড লেআউট ⚡</p>
        </div>
    </footer>

</body>
</html>            $message = "<div class='bg-emerald-500/10 text-emerald-400 p-3 rounded-xl border border-emerald-500/20 text-sm font-semibold'>লিংক সফলভাবে লাইভ হয়েছে!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন প্যানেল | BD সেবা পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans flex items-center justify-center min-h-screen p-4">

    <?php if (!isset($_SESSION['is_admin'])): ?>
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-2xl">
            <h2 class="text-2xl font-black tracking-tight text-center text-blue-500 mb-2">🔒 ADMIN LOCK</h2>
            <p class="text-center text-xs text-slate-500 mb-6">শুধুমাত্র রাকিব ভাই এই প্যানেলে প্রবেশ করতে পারবেন।</p>
            
            <?php if($login_error) echo "<p class='text-red-400 text-sm text-center mb-4 font-bold'>$login_error</p>"; ?>
            
            <form action="admin.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">Username:</label>
                    <input type="text" name="username" required class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl mt-1 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">Password:</label>
                    <input type="password" name="password" required class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl mt-1 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold p-3.5 rounded-xl transition shadow-lg shadow-blue-600/20">প্যানেল আনলক করুন</button>
            </form>
        </div>

    <?php else: ?>
        <div class="w-full max-w-xl bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-100">সেবা পোর্টাল ড্যাশবোর্ড</h2>
                    <p class="text-xs text-slate-500">নতুন লিংক সিকিউরলি যুক্ত করুন</p>
                </div>
                <a href="admin.php?logout=1" class="text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-xl hover:bg-red-500 hover:text-white transition">লগআউট</a>
            </div>

            <?php echo $message; ?>

            <form action="admin.php" method="POST" class="space-y-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">ওয়েবসাইটের নাম:</label>
                    <input type="text" name="site_name" placeholder="যেমন: পাসপোর্ট পোর্টাল" required class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl mt-1 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">ওয়েবসাইট ইউআরএল (URL):</label>
                    <input type="url" name="site_url" placeholder="https://..." required class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl mt-1 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">ক্যাটাগরি:</label>
                    <select name="category" class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl mt-1 text-slate-300 focus:border-blue-500 focus:outline-none">
                        <option value="সরকারি সেবা">সরকারি সেবা (Govt)</option>
                        <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট (Education)</option>
                        <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং (Finance)</option>
                        <option value="ই-কমার্স ও শপিং">ই-কমার্স ও শপিং (E-commerce)</option>
                        <option value="অন্যান্য দরকারি লিংক">অন্যান্য দরকারি লিংক (Others)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">ছোট বিবরণ:</label>
                    <textarea name="description" rows="3" placeholder="সাইটের কাজ সম্পর্কে লিখুন..." class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl mt-1 text-white focus:border-blue-500 focus:outline-none"></textarea>
                </div>
                <button type="submit" name="add_link" class="w-full bg-blue-600 text-white p-3.5 rounded-xl font-bold hover:bg-blue-700 transition">ডাটাবেজে সেভ করুন ➔</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
        
        <?php echo $message; ?>

        <form action="admin.php" method="POST" class="space-y-4 mt-4">
            <div>
                <label class="block text-sm font-bold text-slate-700">ওয়েবসাইটের নাম:</label>
                <input type="text" name="site_name" placeholder="যেমন: বাংলাদেশ ফরম পোর্টাল" class="w-full p-3 border border-slate-200 rounded-xl mt-1 focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">ওয়েবসাইট লিংক (URL):</label>
                <input type="url" name="site_url" placeholder="https://forms.gov.bd" class="w-full p-3 border border-slate-200 rounded-xl mt-1 focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">ক্যাটাগরি সিলেক্ট করুন:</label>
                <select name="category" class="w-full p-3 border border-slate-200 rounded-xl mt-1 focus:border-blue-500 focus:outline-none">
                    <option value="সরকারি সেবা">সরকারি সেবা (Govt Services)</option>
                    <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট (Education)</option>
                    <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং (Finance)</option>
                    <option value="ই-কমার্স ও শপিং">ই-কমার্স ও শপিং (E-commerce)</option>
                    <option value="অন্যান্য দরকারি লিংক">অন্যান্য দরকারি লিংক (Others)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700">ছোট বিবরণ (অপশনাল):</label>
                <textarea name="description" rows="3" placeholder="এই সাইটে কী সেবা পাওয়া যায়..." class="w-full p-3 border border-slate-200 rounded-xl mt-1 focus:border-blue-500 focus:outline-none"></textarea>
            </div>
            <button type="submit" name="add_link" class="w-full bg-blue-600 text-white p-3.5 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/10">লিংক লাইভ করুন ➔</button>
        </form>
    </div>
</body>
</html>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Website Description:</label>
                <textarea name="site_desc" rows="4" class="w-full p-2.5 border rounded-lg mt-1"><?php echo $row['description']; ?></textarea>
            </div>
            <button type="submit" name="update_content" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition">Update Live Site</button>
        </form>
    </div>
</body>
</html>
