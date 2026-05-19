<?php
include('db.php');

// ডাটাবেজ থেকে সব লিংক তুলে আনা
$result = pg_query($conn, "SELECT * FROM site_links ORDER BY id DESC");
$links = pg_fetch_all($result);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BD সেবা পোর্টাল | সকল দরকারি ওয়েবসাইটের তালিকা</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased">

    <header class="bg-white border-b border-slate-100 sticky top-0 z-50 backdrop-blur-md bg-white/90">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-xl font-black text-blue-600 tracking-tight">BD<span class="text-slate-800">সেবা.পোর্টাল</span></a>
            <a href="admin.php" class="bg-slate-100 hover:bg-blue-50 hover:text-blue-600 px-4 py-2 rounded-xl text-xs font-bold text-slate-600 transition">নতুন লিংক যোগ করুন</a>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-6 pt-12 pb-6 text-center space-y-4">
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            বাংলাদেশের সকল প্রয়োজনীয় ওয়েবসাইট <br><span class="text-blue-600">এক জায়গায়</span>
        </h1>
        <p class="text-slate-500 max-w-xl mx-auto text-sm sm:text-base">
            আপনার প্রয়োজনীয় সরকারি সেবা, ই-সার্ভিস, শিক্ষা ও ব্যাংকিং সংক্রান্ত যেকোনো ওয়েবসাইটের আসল লিংকটি নিচে থেকে সহজেই খুঁজে নিন।
        </p>
    </section>

    <main class="max-w-6xl mx-auto px-6 py-8">
        <?php if (!$links): ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200">
                <i class="fas fa-link-slash text-4xl text-slate-300 mb-3"></i>
                <p class="text-slate-400 font-medium">এখনো কোনো লিংক যুক্ত করা হয়নি। অ্যাডমিন প্যানেল থেকে লিংক যুক্ত করুন।</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($links as $link): ?>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:border-blue-500 hover:shadow-md transition-all flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="inline-block bg-blue-50 text-blue-600 text-[10px] font-bold uppercase px-2.5 py-1 rounded-md">
                                <i class="fas fa-folder-open mr-1"></i> <?php echo htmlspecialchars($link['category']); ?>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight leading-snug">
                                <?php echo htmlspecialchars($link['site_name']); ?>
                            </h3>
                            <p class="text-slate-500 text-xs leading-relaxed">
                                <?php echo htmlspecialchars($link['description'] ?: 'কোনো বিবরণ দেওয়া নেই।'); ?>
                            </p>
                        </div>
                        <div class="pt-5 border-t border-slate-50 mt-4 flex justify-between items-center">
                            <span class="text-[11px] text-slate-400 font-mono">
                                <?php echo date('d M, Y', strtotime($link['created_at'])); ?>
                            </span>
                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" class="text-sm font-bold text-blue-600 hover:text-blue-800 inline-flex items-center space-x-1">
                                <span>ভিজিট করুন</span>
                                <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="text-center py-10 border-t border-slate-100 text-xs text-slate-400 font-semibold mt-20">
        <p>&copy; ২০২৬ BD সেবা পোর্টাল | আপনার ডোমেন দ্বারা চালিত।</p>
    </footer>

</body>
</html>
