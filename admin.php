<?php
require_once 'db.php';

// কেউ যদি অলরেডি লগইন থাকে তাকে সরাসরি ড্যাশবোর্ডে পাঠানো
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$login_error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $pass = trim($_POST['password']);
    
    // ইউজারনেম ও পাসওয়ার্ড চেক
    if ($username === 'rakib' && $pass === 'rakib123') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $login_error = "❌ ইউজারনেম বা পাসওয়ার্ড সঠিক নয়!";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>অ্যাডমিন লগইন - সেবা পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; }
    </style>
</head>
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
