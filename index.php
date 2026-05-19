<?php
include('db.php');

// ডাটাবেজ থেকে ক্যাটাগরি অনুযায়ী সর্ট করে সব লিংক তুলে আনা
$result = pg_query($conn, "SELECT * FROM site_links ORDER BY category ASC, id DESC");
$links = pg_fetch_all($result) ?: [];
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BD সেবা পোর্টাল | বাংলাদেশের সব প্রয়োজনীয় ওয়েবসাইট লিংক</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0f172a] text-slate-200 antialiased font-sans">

    <header class="border-b border-slate-800/80 sticky top-0 z-50 backdrop-blur-lg bg-[#0f172a]/80">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-xl font-black text-blue-500 tracking-tight flex items-center space-x-2">
                <span class="bg-blue-600 text-white px-2.5 py-1 rounded-xl text-sm"><i class="fas fa-layer-group"></i></span>
                <span>BD<span class="text-white font-medium">সেবা.পোর্টাল</span></span>
            </a>
            <a href="admin.php" class="bg-slate-800 hover:bg-blue-600 border border-slate-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5">
                <i class="fas fa-lock text-[10px]"></i>
                <span>কন্ট্রোল প্যানেল</span>
            </a>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-6 pt-16 pb-12 text-center space-y-4">
        <div class="inline-flex items-center space-x-2 bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1 rounded-full text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>১০০% ভেরিফাইড এবং নিরাপদ লিংক কালেকশন</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
            বাংলাদেশের প্রয়োজনীয় ডিজিটাল সেবা <br><span class="text-transparent bg-clip-text bg-gradient-to-r Hell from-blue-400 to-indigo-500">এখন এক ক্লিকে</span>
        </h1>
        <p class="text-slate-400 max-w-xl mx-auto text-xs sm:text-sm font-medium leading-relaxed">
            সরকারি ই-সার্ভিস, শিক্ষাবোর্ডের রেজাল্ট, অনলাইন ব্যাংকিং কিংবা ই-কমার্স—সব দরকারি ওয়েবসাইটের আসল এড্রেস খুঁজুন ঝামেলা ছাড়াই।
        </p>
    </section>

    <main class="max-w-6xl mx-auto px-6 pb-24">
        <?php if (empty($links)): ?>
            <div class="text-center py-20 bg-slate-900/50 rounded-3xl border border-dashed border-slate-800">
                <i class="fas fa-link-slash text-4xl text-slate-700 mb-3"></i>
                <p class="text-slate-500 text-sm font-medium">কোনো ডেটা পাওয়া যায়নি। অনুগ্রহ করে প্রথমে `setup.php` ফাইলটি ব্রাউজারে রান করুন।</p>
            </div>
        <?php else: ?>
            
            <?php 
            $current_cat = "";
            foreach ($links as $link): 
                if ($current_cat !== $link['category']): 
                    $current_cat = $link['category'];
            ?>
                <div class="flex items-center space-x-3 mt-12 mb-6">
                    <span class="h-px w-6 bg-blue-500"></span>
                    <h2 class="text-md font-black text-white uppercase tracking-wider text-blue-400 flex items-center space-x-2">
                        <i class="fas fa-folder text-sm text-indigo-400"></i>
                        <span><?php echo htmlspecialchars($current_cat); ?></span>
                    </h2>
                    <span class="h-px flex-1 bg-slate-800/60"></span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php endif; ?>

                    <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 hover:border-blue-500/50 hover:bg-slate-900 transition-all flex flex-col justify-between group shadow-sm">
                        <div class="space-y-2.5">
                            <h3 class="text-md font-bold text-white group-hover:text-blue-400 transition-colors tracking-tight">
                                <?php echo htmlspecialchars($link['site_name']); ?>
                            </h3>
                            <p class="text-slate-400 text-xs leading-relaxed font-medium">
                                <?php echo htmlspecialchars($link['description'] ?: 'এই ওয়েবসাইটের মাধ্যমে প্রয়োজনীয় অনলাইন সেবা সরাসরি উপভোগ করুন।'); ?>
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-800/60 mt-4 flex justify-between items-center">
                            <span class="text-[10px] text-slate-600 font-mono">
                                Verified ✔
                            </span>
                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" class="text-xs font-bold text-blue-500 hover:text-blue-400 inline-flex items-center space-x-1 transition-all group-hover:translate-x-1">
                                <span>সাইটে প্রবেশ করুন</span>
                                <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>

            <?php 
                // পরবর্তী লিংকের আগে গ্রিড ডিভ ক্লোজিং কন্ডিশন চেক করা
                $next_index = array_search($link, $links) + 1;
                if ($next_index == count($links) || $links[$next_index]['category'] !== $current_cat):
                    echo '</div>'; // গ্রিড ডিভ ক্লোজ
                endif;
            endforeach; ?>

        <?php endif; ?>
    </main>

    <footer class="text-center py-8 border-t border-slate-900 text-xs text-slate-600 font-bold bg-slate-950">
        <p>&copy; ২০২৬ BD সেবা পোর্টাল | ডেভেলপ করেছেন রাকিব হাসান।</p>
    </footer>

</body>
</html>
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
