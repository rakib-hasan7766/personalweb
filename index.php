<?php
$host = "dpg-d85q0mnavr4c73d62fog-a.oregon-postgres.render.com"; 
$user = "perwebb"; 
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3"; 
$dbname = "perweb"; 
$port = "5432"; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($connection_string);

if (!$conn) { die("Database Connection Failed!"); }

// সব ডেটা তুলে আনা
$result = pg_query($conn, "SELECT * FROM site_links ORDER BY category, site_name");
$categories = [];

while ($row = pg_fetch_assoc($result)) {
    $categories[$row['category']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>রিসোর্স ও লিংক পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Hind Siliguri', sans-serif;
            background-color: #f1f5f9; /* হালকা সুন্দর ব্যাকগ্রাউন্ড */
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-blue-600 tracking-tight flex items-center gap-2">
                    🌐 <span>রিসোর্স ও লিংক পোর্টাল</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">প্রয়োজনীয় সকল ওয়েবসাইট ও ড্রাইভ সোর্স এক জায়গায়</p>
            </div>
            
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-slate-400 text-sm">🔍</span>
                </div>
                <input type="text" id="searchInput" onkeyup="filterLinks()" 
                       placeholder="খুঁজুন (যেমন: ভিসা, drive, course)..." 
                       class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-xl bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all shadow-sm">
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        
        <div id="noResults" class="hidden text-center py-12">
            <span class="text-4xl">🔎</span>
            <h3 class="text-lg font-bold text-slate-700 mt-2">কোনো লিংক পাওয়া যায়নি!</h3>
            <p class="text-slate-500 text-sm">অনুগ্রহ করে সঠিক বানান দিয়ে আবার চেষ্টা করুন।</p>
        </div>

        <?php foreach ($categories as $categoryName => $links): ?>
            <div class="category-section mb-10" data-category="<?php echo htmlspecialchars($categoryName); ?>">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-200 pb-2">
                    <span class="text-xl">📁</span>
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">
                        <?php echo htmlspecialchars($categoryName); ?>
                    </h2>
                    <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-blue-100">
                        <?php echo count($links); ?>টি লিংক
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($links as $link): ?>
                        <div class="link-card bg-white border border-slate-200 p-4 rounded-xl shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 flex flex-col justify-between group"
                             data-name="<?php echo strtolower(htmlspecialchars($link['site_name'])); ?>"
                             data-desc="<?php echo strtolower(htmlspecialchars($link['description'])); ?>">
                            
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors text-base line-clamp-1">
                                        <?php echo htmlspecialchars($link['site_name']); ?>
                                    </h3>
                                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200 whitespace-nowrap">
                                        🔗 Link
                                    </span>
                                </div>
                                <p class="text-slate-600 text-xs line-clamp-2 mb-4 leading-relaxed">
                                    <?php echo htmlspecialchars($link['description'] ? $link['description'] : 'কোনো বিবরণ দেওয়া নেই।'); ?>
                                </p>
                            </div>

                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" 
                               class="w-full text-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-medium py-2 px-4 rounded-lg text-xs transition-all duration-200 flex items-center justify-center gap-1.5 border border-blue-100 group-hover:border-transparent">
                                <span>ভিজিট করুন</span> ➔
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    </main>

    <footer class="bg-white border-t border-slate-200 mt-12 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            <p>© <?php echo date('Y'); ?> রিসোর্স পোর্টাল প্ল্যাটফর্ম | ডার্কনেস রিমুভড অ্যান্ড লাইট ইউআই ফিক্সড ⚡</p>
        </div>
    </footer>

    <script>
        function filterLinks() {
            let input = document.getElementById('searchInput').value.toLowerCase().trim();
            let cards = document.getElementsByClassName('link-card');
            let sections = document.getElementsByClassName('category-section');
            let anyVisible = false;

            // প্রতিটি কার্ড লুপ করা
            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].getAttribute('data-name');
                let desc = cards[i].getAttribute('data-desc');

                if (name.includes(input) || desc.includes(input)) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }

            // যদি কোনো সেকশনের সব কার্ড হাইড হয়ে যায়, তবে সেকশনও হাইড করা
            for (let j = 0; j < sections.length; j++) {
                let sectionCards = sections[j].getElementsByClassName('link-card');
                let sectionVisible = false;

                for (let k = 0; k < sectionCards.length; k++) {
                    if (sectionCards[k].style.display !== "none") {
                        sectionVisible = true;
                        anyVisible = true;
                        break;
                    }
                }

                if (sectionVisible) {
                    sections[j].style.display = "";
                } else {
                    sections[j].style.display = "none";
                }
            }

            // নো রেজাল্ট মেসেজ হ্যান্ডলিং
            let noResults = document.getElementById('noResults');
            if (anyVisible) {
                noResults.classList.add('hidden');
            } else {
                noResults.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
