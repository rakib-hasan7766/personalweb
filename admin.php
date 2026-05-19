<?php
session_start();
include('db.php');

// লগইন ক্রেডেনশিয়ালস সেটআপ
$admin_user = "rakib";
$admin_pass = "rakib7766"; 

// লগআউট লজিক
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// লগইন সাবমিট চেক
$login_error = "";
if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['is_admin'] = true;
    } else {
        $login_error = "ভুল ইউজারনেম বা পাসওয়ার্ড!";
    }
}

// নতুন লিংক অ্যাড করার লজিক (শুধুমাত্র লগইন থাকলে কাজ করবে)
$message = "";
if (isset($_POST['add_link']) && isset($_SESSION['is_admin'])) {
    $site_name = pg_escape_string($conn, $_POST['site_name']);
    $site_url = pg_escape_string($conn, $_POST['site_url']);
    $category = pg_escape_string($conn, $_POST['category']);
    $description = pg_escape_string($conn, $_POST['description']);

    if (!empty($site_name) && !empty($site_url)) {
        $query = "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$site_name', '$site_url', '$category', '$description')";
        if (pg_query($conn, $query)) {
            $message = "<div class='bg-emerald-500/10 text-emerald-400 p-3 rounded-xl border border-emerald-500/20 text-sm font-semibold'>লিংক সফলভাবে লাইভ হয়েছে!</div>";
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
