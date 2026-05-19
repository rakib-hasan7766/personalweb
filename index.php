<?php
// ১. ডাটাবেজ কানেকশন
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

// ২. ডাটাবেজে লিংক সেভ করার হ্যান্ডলার
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
            $message = "
            <div class='flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-sm font-semibold mb-6 shadow-sm animate-fade-in'>
                <div class='w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-base shadow-sm'>🚀</div>
                <div>নতুন লিংকটি সফলভাবে ডাটাবেজে সেভ এবং লাইভ হয়েছে!</div>
            </div>";
        } else {
            $message = "
            <div class='flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-sm font-semibold mb-6 shadow-sm animate-fade-in'>
                <div class='w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center text-base shadow-sm'>❌</div>
                <div>ডাটাবেজ কানেকশন সমস্যা! অনুগ্রহ করে আবার চেষ্টা করুন।</div>
            </div>";
        }
    }
}

// ৩. ডাটাবেজ থেকে লিংক নিয়ে আসা
$query = "SELECT * FROM site_links ORDER BY id DESC";
$result = pg_query($conn, $query);

$links_by_category = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $cat = !empty($row['category']) ? $row['category'] : 'অন্যান্য সেবা';
        $links_by_category[$cat][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ID Card Scanner & Rakib Utility Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08); }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <header class="bg-slate-900 text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-xl shadow-lg font-bold">🌐</div>
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight leading-tight">SCANNER PRO HUB</h1>
                    <p class="text-[10px] text-blue-400 font-bold tracking-wider uppercase">All-In-One Unified System</p>
                </div>
            </div>
            <div>
                <a href="#admin-section" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-md transition-all flex items-center gap-1.5">
                    ⚙️ <span>কন্ট্রোল প্যানেল</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <section class="bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white py-12 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="max-w-3xl mx-auto relative z-10">
                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Rakib Automated Portal</span>
                <h2 class="text-2xl sm:text-4xl font-black mt-3 tracking-tight">প্রয়োজনীয় সকল অনলাইন লিংক ও অটোমেশন টুলস</h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-xl mx-auto">নিচ থেকে যেকোনো লাইভ টুলস সিলেক্ট করে কাজ করতে পারবেন অথবা সার্চ দিয়ে প্রয়োজনীয় সরকারি লিংক খুঁজে নিতে পারেন।</p>
                
                <div class="mt-8 max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 text-base">🔍</div>
                    <input type="text" id="searchInput" onkeyup="searchLinks()" placeholder="যেকোনো লিংকের নাম বা কী-ওয়ার্ড লিখে সার্চ করুন..." class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-900 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/30 text-sm font-medium shadow-2xl transition-all">
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <?php if (!empty($links_by_category)): ?>
                <div id="linksContainer" class="space-y-12">
                    <?php foreach ($links_by_category as $category => $links): ?>
                        <div class="category-block bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                                <span class="w-2 h-5 bg-blue-600 rounded-full"></span>
                                <h3 class="text-base sm:text-lg font-black text-slate-800"><?php echo htmlspecialchars($category); ?></h3>
                                <span class="bg-slate-100 text-slate-500 text-[11px] font-bold px-2 py-0.5 rounded-lg"><?php echo count($links); ?>টি লিংক</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($links as $link): ?>
                                    <div class="link-card card-hover border border-slate-100 bg-slate-50/50 p-4 rounded-2xl flex flex-col justify-between gap-3">
                                        <div>
                                            <h4 class="link-title text-sm font-bold text-slate-800"><?php echo htmlspecialchars($link['site_name']); ?></h4>
                                            <p class="text-[11px] text-slate-400 mt-1 line-clamp-2"><?php echo !empty($link['description']) ? htmlspecialchars($link['description']) : 'কোনো বিবরণ দেওয়া নেই'; ?></p>
                                        </div>
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-100/70 mt-1">
                                            <span class="text-[9px] font-bold text-blue-500 uppercase px-2 py-0.5 bg-blue-50 rounded-md">Live Link</span>
                                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" class="bg-white hover:bg-slate-900 border border-slate-200 text-slate-700 hover:text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-sm transition-all transform active:scale-95">প্রবেশ করুন ➔</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="noResult" class="hidden text-center py-12 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-sm mx-auto">
                    <p class="text-3xl mb-2">🔍</p>
                    <h3 class="text-sm font-bold text-slate-700">দুঃখিত! কোনো লিংক পাওয়া যায়নি</h3>
                </div>
            <?php endif; ?>
        </div>

        <hr class="border-slate-200 max-w-7xl mx-auto my-6">

        <div id="admin-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="text-center mb-8">
                <span class="bg-blue-600 text-white text-xs px-4 py-1 rounded-full font-bold uppercase shadow-md shadow-blue-600/10">Control Center</span>
                <h2 class="text-2xl font-black text-slate-900 mt-2">অটোমেশন ও অ্যাডমিন ইঞ্জিন প্যানেল</h2>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 bg-slate-100 p-4 sm:p-6 rounded-3xl border border-slate-200">
                <aside class="w-full lg:w-64 bg-slate-950 text-slate-400 p-4 rounded-2xl flex flex-col justify-between shadow-xl">
                    <nav class="space-y-1">
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-widest px-2 pb-1">Core Control</div>
                        <button onclick="switchTab('link-manager')" id="btn-link-manager" class="tab-btn w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-blue-600 text-white shadow-md">🔗 লিংক ম্যানেজার</button>
                        
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-widest px-2 pt-4 pb-1">Image & Card Engine</div>
                        <button onclick="switchTab('nid-crop')" id="btn-nid-crop" class="tab-btn w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-900 hover:text-white">🪪 NID Auto Crop (A4)</button>
                        <button onclick="switchTab('sig-pad')" id="btn-sig-pad" class="tab-btn w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-900 hover:text-white">✍️ Signature Pad</button>
                        
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-widest px-2 pt-4 pb-1">Converters & Code</div>
                        <button onclick="switchTab('date-words')" id="btn-date-words" class="tab-btn w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-900 hover:text-white">📅 Date to Words</button>
                        <button onclick="switchTab('num-words')" id="btn-num-words" class="tab-btn w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-900 hover:text-white">💰 Number to Taka</button>
                        <button onclick="switchTab('qr-generator')" id="btn-qr-generator" class="tab-btn w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-900 hover:text-white">🟩 QR Generator</button>
                    </nav>
                    <div class="text-[10px] text-slate-600 font-bold mt-6 text-center border-t border-slate-900 pt-2">Rakib Engine Hub v2.0</div>
                </aside>

                <div class="flex-grow max-w-3xl w-full mx-auto">
                    
                    <div id="link-manager" class="tab-content active bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 border-b pb-2 mb-4">🔗 নতুন লিংক ডাটাবেজে যুক্ত করুন</h3>
                        <?php if(!empty($message)) echo $message; ?>
                        <form action="index.php#admin-section" method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-slate-500 block mb-1">ওয়েবসাইটের নাম *</label>
                                    <input type="text" name="site_name" required placeholder="যেমন: পাসপোর্ট চেক" class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-slate-500 block mb-1">ওয়েবসাইট URL লিংক *</label>
                                    <input type="url" name="site_url" required placeholder="https://example.com" class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 block mb-1">ক্যাটাগরি সিলেক্ট করুন *</label>
                                <select name="category" required class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="সরকারি সেবা">সরকারি সেবা</option>
                                    <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                                    <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                                    <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং</option>
                                    <option value="ইউটিলিটি ও টুলস">ইউটিলিটি ও টুলস</option>
                                    <option value="সফটওয়্যার ডাউনলোড">সফটওয়্যার ডাউনলোড</option>
                                    <option value="রাকিব ড্রাইভ রিসোর্স">রাকিব ড্রাইভ রিসোর্স</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 block mb-1">ছোট বিবরণ (অপশনাল)</label>
                                <textarea name="description" rows="2" placeholder="সাইটের কাজ সম্পর্কে বিবরণ..." class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm resize-none outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                            </div>
                            <button type="submit" name="add_link" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold text-xs shadow-md transition-all">লিংক লাইভ করুন ➔</button>
                        </form>
                    </div>

                    <div id="nid-crop" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 border-b pb-2 mb-2">🪪 NID Card Auto Crop & Join Engine</h3>
                        <p class="text-xs text-slate-400 mb-4">আইডি কার্ডের সামনের ও পিছনের সাইড সিলেক্ট করুন, এক পেজে অটো সাজিয়ে দিবে।</p>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-dashed p-4 rounded-xl bg-slate-50 text-center">
                                    <label class="text-xs font-bold text-slate-600 block mb-1">Front Side</label>
                                    <input type="file" id="nidFront" accept="image/*" class="text-xs text-slate-500 w-full">
                                </div>
                                <div class="border border-dashed p-4 rounded-xl bg-slate-50 text-center">
                                    <label class="text-xs font-bold text-slate-600 block mb-1">Back Side</label>
                                    <input type="file" id="nidBack" accept="image/*" class="text-xs text-slate-500 w-full">
                                </div>
                            </div>
                            <button onclick="processNID()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl text-xs transition-all">ইনস্ট্যান্ট ক্রপ এবং এপিঠ-ওপিঠ করুন ⚡</button>
                            <div class="mt-4 flex flex-col items-center">
                                <canvas id="nidCanvas" class="border max-w-full rounded-xl hidden bg-white shadow-md"></canvas>
                                <button id="downloadNidBtn" onclick="downloadNid()" class="hidden mt-3 bg-blue-600 text-white text-xs px-4 py-2 rounded-xl font-bold transition-all shadow-md">ডাউনলোড করুন 📥</button>
                            </div>
                        </div>
                    </div>

                    <div id="sig-pad" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 border-b pb-2 mb-2">✍️ Digital Signature Pad (Transparent)</h3>
                        <p class="text-xs text-slate-400 mb-4">নিচের বক্সে মাউস বা টাচ দিয়ে সাইন করে ব্যাকগ্রাউন্ড ছাড়া PNG ফাইলে নিন।</p>
                        <div class="flex flex-col items-center">
                            <canvas id="sigCanvas" width="550" height="200" class="border rounded-xl bg-slate-50 max-w-full cursor-crosshair shadow-inner"></canvas>
                            <div class="flex gap-4 mt-4 w-full">
                                <button onclick="clearSignature()" class="w-1/2 bg-slate-100 text-slate-700 py-2 rounded-xl text-xs font-bold transition-all">মুছে ফেলুন</button>
                                <button onclick="saveSignature()" class="w-1/2 bg-blue-600 text-white py-2 rounded-xl text-xs font-bold transition-all shadow-md">ডাউনলোড করুন 📥</button>
                            </div>
                        </div>
                    </div>

                    <div id="date-words" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 border-b pb-2 mb-4">📅 Date to Words / তারিখ কথায় রূপান্তর</h3>
                        <div class="space-y-4">
                            <input type="date" id="inputDate" class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm outline-none">
                            <button onclick="convertDateToBangla()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl text-xs transition-all">কথায় রূপান্তর করুন ✨</button>
                            <div id="dateResultBox" class="hidden p-4 bg-purple-50 border rounded-xl text-center">
                                <p id="dateBanglaOutput" class="text-base font-black text-purple-950"></p>
                            </div>
                        </div>
                    </div>

                    <div id="num-words" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 border-b pb-2 mb-4">💰 Number to Words (টাকা কথায় রূপান্তর)</h3>
                        <div class="space-y-4">
                            <input type="number" id="inputNumber" placeholder="টাকার পরিমাণ লিখুন..." class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm outline-none">
                            <button onclick="convertNumberToBanglaTaka()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-xl text-xs transition-all">টাকা কথায় রূপান্তর করুন ➔</button>
                            <div id="numResultBox" class="hidden p-4 bg-amber-50 border rounded-xl text-center">
                                <p id="numBanglaOutput" class="text-sm font-black text-amber-950"></p>
                            </div>
                        </div>
                    </div>

                    <div id="qr-generator" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 border-b pb-2 mb-4">🟩 Instant QR Code Generator</h3>
                        <div class="space-y-4">
                            <input type="text" id="qrText" placeholder="লিংক বা লেখা দিন (bKash/URL/Text)..." class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-50 text-sm outline-none">
                            <button onclick="generateQRCode()" class="w-full bg-slate-900 text-white font-bold py-2.5 rounded-xl text-xs transition-all">QR Code জেনারেট করুন 🚀</button>
                            <div id="qrResultBox" class="hidden flex flex-col items-center justify-center p-4 border rounded-xl bg-slate-50 mt-2">
                                <img id="qrImage" src="" alt="QR Code" class="border bg-white p-2 rounded-xl">
                                <button onclick="downloadQR()" class="mt-3 bg-blue-600 text-white text-xs px-4 py-2 rounded-xl font-bold">ডাউনলোড করুন 📥</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-[11px] text-slate-400 font-bold tracking-wide uppercase">© <?php echo date('Y'); ?> UNIFIED SCANNER PRO PORTAL | BY RAKIB BHAI ⚡</p>
        </div>
    </footer>

    <script>
        // লাইভ লিংক সার্চ ফিল্টার
        function searchLinks() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const categoryBlocks = document.getElementsByClassName('category-block');
            let overallFound = false;

            for (let i = 0; i < categoryBlocks.length; i++) {
                const cardsInBlock = categoryBlocks[i].getElementsByClassName('link-card');
                let cardsFoundInBlock = 0;

                for (let j = 0; j < cardsInBlock.length; j++) {
                    const title = cardsInBlock[j].getElementsByClassName('link-title')[0].innerText.toLowerCase();
                    if (title.includes(input)) {
                        cardsInBlock[j].style.display = "";
                        cardsFoundInBlock++;
                        overallFound = true;
                    } else {
                        cardsInBlock[j].style.display = "none";
                    }
                }
                categoryBlocks[i].style.display = cardsFoundInBlock > 0 ? "" : "none";
            }
            document.getElementById('noResult').style.display = overallFound ? "none" : "block";
        }

        // কন্ট্রোল প্যানেল ট্যাব সিলেকশন লজিক
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                el.classList.add('hover:bg-slate-900', 'hover:text-white');
            });
            
            document.getElementById(tabId).classList.add('active');
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('hover:bg-slate-900', 'hover:text-white');
            activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
        }

        // NID Auto Crop Engine
        function processNID() {
            const frontFile = document.getElementById('nidFront').files[0];
            const backFile = document.getElementById('nidBack').files[0];
            if(!frontFile || !backFile) { alert("দয়া করে দুই পাশের ছবিই সিলেক্ট করুন!"); return; }

            const canvas = document.getElementById('nidCanvas');
            const ctx = canvas.getContext('2d');
            canvas.width = 600; canvas.height = 840;
            ctx.fillStyle = "#ffffff"; ctx.fillRect(0, 0, canvas.width, canvas.height);

            let imgFront = new Image(); let imgBack = new Image();
            imgFront.src = URL.createObjectURL(frontFile);
            imgFront.onload = function() {
                ctx.drawImage(imgFront, 50, 60, 500, 320); 
                imgBack.src = URL.createObjectURL(backFile);
                imgBack.onload = function() {
                    ctx.drawImage(imgBack, 50, 450, 500, 320); 
                    canvas.classList.remove('hidden');
                    document.getElementById('downloadNidBtn').classList.remove('hidden');
                }
            }
        }
        function downloadNid() {
            const canvas = document.getElementById('nidCanvas');
            const link = document.createElement('a');
            link.download = 'NID_Print_Ready.png';
            link.href = canvas.toDataURL(); link.click();
        }

        // Digital Signature Pad Setup
        const sigCanvas = document.getElementById('sigCanvas');
        const sigCtx = sigCanvas.getContext('2d');
        let isDrawing = false;

        function getPos(e) {
            const rect = sigCanvas.getBoundingClientRect();
            return { x: (e.clientX || e.touches[0].clientX) - rect.left, y: (e.clientY || e.touches[0].clientY) - rect.top };
        }
        sigCanvas.addEventListener('mousedown', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); });
        sigCanvas.addEventListener('mousemove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.strokeStyle = "#000000"; sigCtx.stroke(); });
        window.addEventListener('mouseup', () => isDrawing = false);
        sigCanvas.addEventListener('touchstart', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); e.preventDefault(); });
        sigCanvas.addEventListener('touchmove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.stroke(); e.preventDefault(); });

        function clearSignature() { sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height); }
        function saveSignature() {
            const link = document.createElement('a'); link.download = 'Signature.png';
            link.href = sigCanvas.toDataURL("image/png"); link.click();
        }

        // Date Converter
        function convertDateToBangla() {
            const dateVal = document.getElementById('inputDate').value; if(!dateVal) return;
            const months = ["জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"];
            const banglaNums = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
            const parts = dateVal.split('-'); 
            let banglaDay = parseInt(parts[2]).toString().split('').map(c => banglaNums[c] || c).join('') + "ই";
            let banglaYear = parts[0].split('').map(c => banglaNums[c] || c).join('');
            document.getElementById('dateResultBox').classList.remove('hidden');
            document.getElementById('dateBanglaOutput').innerText = `${banglaDay} ${months[parseInt(parts[1]) - 1]}, ${banglaYear} ইং`;
        }

        // Taka Words Converter
        function convertNumberToBanglaTaka() {
            const num = document.getElementById('inputNumber').value; if(!num || num < 0) return;
            const units = ['', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়'];
            const teens = ['দশ', 'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোলো', 'সতেরো', 'আঠারো', 'উনিশ'];
            const tens = ['', '', 'বিশ', 'ত্রিশ', 'চল্লিশ', 'পঞ্চাশ', 'ছাট', 'সত্তর', 'আশি', 'নব্বই'];
            
            function convertBelowHundred(n) {
                if (n < 10) return units[n];
                if (n >= 10 && n < 20) return teens[n - 10];
                return tens[Math.floor(n / 10)] + (n % 10 !== 0 ? ' ' + units[n % 10] : '');
            }

            let n = parseInt(num);
            if (n === 0) { document.getElementById('numBanglaOutput').innerText = "শূণ্য টাকা মাত্র"; return; }
            let result = '';
            let crore = Math.floor(n / 10000000); n %= 10000000;
            let lakh = Math.floor(n / 100000); n %= 100000;
            let thousand = Math.floor(n / 1000); n %= 1000;
            let hundred = Math.floor(n / 100); let rem = n % 100;

            if (crore > 0) result += convertBelowHundred(crore) + ' কোটি ';
            if (lakh > 0) result += convertBelowHundred(lakh) + ' লক্ষ ';
            if (thousand > 0) result += convertBelowHundred(thousand) + ' হাজার ';
            if (hundred > 0) result += convertBelowHundred(hundred) + ' শত ';
            if (rem > 0) result += convertBelowHundred(rem);

            document.getElementById('numResultBox').classList.remove('hidden');
            document.getElementById('numBanglaOutput').innerText = result.trim() + " টাকা মাত্র।";
        }

        // QR Code
        function generateQRCode() {
            const txt = document.getElementById('qrText').value; if(!txt) return;
            document.getElementById('qrImage').src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent(txt);
            document.getElementById('qrResultBox').classList.remove('hidden');
        }
        function downloadQR() { window.open(document.getElementById('qrImage').src, '_blank'); }
    </script>
</body>
</html>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <header class="bg-slate-900 text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-xl shadow-lg font-bold">🌐</div>
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight leading-tight">SCANNER PRO</h1>
                    <p class="text-[10px] text-blue-400 font-bold tracking-wider uppercase">Rakib Digital E-Service Hub</p>
                </div>
            </div>
            <div>
                <a href="admin.php" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-md transition-all transform active:scale-95 flex items-center gap-1.5">
                    ⚙️ <span>অ্যাডমিন প্যানেল</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <section class="bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white py-12 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="max-w-3xl mx-auto relative z-10">
                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Digital Service Platform</span>
                <h2 class="text-2xl sm:text-4xl font-black mt-3 tracking-tight">প্রয়োজনীয় সকল অনলাইন লিংক ও সার্ভিস পোর্টাল</h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-xl mx-auto">বাংলাদেশ ও প্রবাসীদের প্রয়োজনীয় সরকারি সেবা, জন্মনিবন্ধন, পাসপোর্ট, স্মার্টকার্ড রিসাইজার এবং ড্রাইভের সকল লিংক এক জায়গায় পাবেন।</p>
                
                <div class="mt-8 max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 text-base">🔍</div>
                    <input type="text" id="searchInput" onkeyup="searchLinks()" placeholder="যেকোনো লিংকের নাম বা কী-ওয়ার্ড লিখে সার্চ করুন..." class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-900 rounded-2xl border-none outline-none focus:ring-4 focus:ring-blue-500/30 text-sm font-medium shadow-2xl transition-all">
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            
            <?php if (empty($links_by_category)): ?>
                <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-md mx-auto">
                    <p class="text-4xl mb-2">📥</p>
                    <h3 class="text-base font-bold text-slate-700">ডাটাবেজে কোনো লিংক নেই!</h3>
                    <p class="text-xs text-slate-400 mt-1">অনুগ্রহ করে অ্যাডমিন প্যানেল থেকে লিংক যুক্ত করুন।</p>
                </div>
            <?php else: ?>
                
                <div id="linksContainer" class="space-y-12">
                    <?php foreach ($links_by_category as $category => $links): ?>
                        <div class="category-block bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm transition-all">
                            
                            <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                                <span class="w-2 h-5 bg-blue-600 rounded-full"></span>
                                <h3 class="text-base sm:text-lg font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($category); ?></h3>
                                <span class="bg-slate-100 text-slate-500 text-[11px] font-bold px-2 py-0.5 rounded-lg"><?php echo count($links); ?>টি লিংক</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($links as $link): ?>
                                    <div class="link-card card-hover border border-slate-100 bg-slate-50/50 p-4 rounded-2xl flex flex-col justify-between gap-3">
                                        <div>
                                            <h4 class="link-title text-sm font-bold text-slate-800 tracking-tight leading-snug"><?php echo htmlspecialchars($link['site_name']); ?></h4>
                                            <?php if (!empty($link['description'])): ?>
                                                <p class="text-[11px] text-slate-400 mt-1 line-clamp-2"><?php echo htmlspecialchars($link['description']); ?></p>
                                            <?php else: ?>
                                                <p class="text-[11px] text-slate-300 italic mt-1">কোনো বিবরণ দেওয়া নেই</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-100/70 mt-1">
                                            <span class="text-[9px] font-bold text-blue-500 tracking-wide uppercase px-2 py-0.5 bg-blue-50 rounded-md">Live Link</span>
                                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" class="bg-white hover:bg-slate-900 border border-slate-200 text-slate-700 hover:text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-sm transition-all flex items-center gap-1 transform active:scale-95">
                                                <span>প্রবেশ করুন</span> ➔
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="noResult" class="hidden text-center py-12 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-sm mx-auto">
                    <p class="text-3xl mb-2">🔍</p>
                    <h3 class="text-sm font-bold text-slate-700">দুঃখিত! কোনো লিংক পাওয়া যায়নি</h3>
                    <p class="text-xs text-slate-400 mt-0.5">অন্য কোনো কি-ওয়ার্ড দিয়ে আবার চেষ্টা করুন।</p>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-[11px] text-slate-400 font-bold tracking-wide uppercase">© <?php echo date('Y'); ?> SCANNER PRO PORTAL | DEVELOPED BY RAKIB BHAI ⚡</p>
        </div>
    </footer>

    <script>
        function searchLinks() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const categoryBlocks = document.getElementsByClassName('category-block');
            let overallFound = false;

            for (let i = 0; i < categoryBlocks.length; i++) {
                const cardsInBlock = categoryBlocks[i].getElementsByClassName('link-card');
                let cardsFoundInBlock = 0;

                for (let j = 0; j < cardsInBlock.length; j++) {
                    const title = cardsInBlock[j].getElementsByClassName('link-title')[0].innerText.toLowerCase();
                    
                    if (title.includes(input)) {
                        cardsInBlock[j].style.display = "";
                        cardsFoundInBlock++;
                        overallFound = true;
                    } else {
                        cardsInBlock[j].style.display = "none";
                    }
                }

                if (cardsFoundInBlock > 0) {
                    categoryBlocks[i].style.display = "";
                } else {
                    categoryBlocks[i].style.display = "none";
                }
            }

            const noResultBox = document.getElementById('noResult');
            if (overallFound) {
                noResultBox.classList.add('hidden');
            } else {
                noResultBox.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
            </div>
            <div>
                <a href="admin.php" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-md transition-all transform active:scale-95 flex items-center gap-1.5">
                    ⚙️ <span>অ্যাডমিন প্যানেল</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <section class="bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white py-12 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="max-w-3xl mx-auto relative z-10">
                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Digital Service Platform</span>
                <h2 class="text-2xl sm:text-4xl font-black mt-3 tracking-tight">প্রয়োজনীয় সকল অনলাইন লিংক ও সার্ভিস পোর্টাল</h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-xl mx-auto">বাংলাদেশ ও প্রবাসীদের প্রয়োজনীয় সরকারি সেবা, জন্মনিবন্ধন, পাসপোর্ট, স্মার্টকার্ড রিসাইজার এবং ড্রাইভের সকল লিংক এক জায়গায় পাবেন।</p>
                
                <div class="mt-8 max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 text-base">🔍</div>
                    <input type="text" id="searchInput" onkeyup="searchLinks()" placeholder="যেকোনো লিংকের নাম বা কী-ওয়ার্ড লিখে সার্চ করুন..." class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-900 rounded-2xl border-none outline-none focus:ring-4 focus:ring-blue-500/30 text-sm font-medium shadow-2xl transition-all">
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            
            <?php if (empty($links_by_category)): ?>
                <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-md mx-auto">
                    <p class="text-4xl mb-2">📥</p>
                    <h3 class="text-base font-bold text-slate-700">ডাটাবেজে কোনো লিংক নেই!</h3>
                    <p class="text-xs text-slate-400 mt-1">অনুগ্রহ করে অ্যাডমিন প্যানেল থেকে লিংক যুক্ত করুন।</p>
                </div>
            <?php else: ?>
                
                <div id="linksContainer" class="space-y-12">
                    <?php foreach ($links_by_category as $category => $links): ?>
                        <div class="category-block bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm transition-all">
                            
                            <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                                <span class="w-2 h-5 bg-blue-600 rounded-full"></span>
                                <h3 class="text-base sm:text-lg font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($category); ?></h3>
                                <span class="bg-slate-100 text-slate-500 text-[11px] font-bold px-2 py-0.5 rounded-lg"><?php echo count($links); ?>টি লিংক</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($links as $link): ?>
                                    <div class="link-card card-hover border border-slate-100 bg-slate-50/50 p-4 rounded-2xl flex flex-col justify-between gap-3">
                                        <div>
                                            <h4 class="link-title text-sm font-bold text-slate-800 tracking-tight leading-snug"><?php echo htmlspecialchars($link['site_name']); ?></h4>
                                            <?php if (!empty($link['description'])): ?>
                                                <p class="text-[11px] text-slate-400 mt-1 line-clamp-2"><?php echo htmlspecialchars($link['description']); ?></p>
                                            <?php else: ?>
                                                <p class="text-[11px] text-slate-300 italic mt-1">কোনো বিবরণ দেওয়া নেই</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-100/70 mt-1">
                                            <span class="text-[9px] font-bold text-blue-500 tracking-wide uppercase px-2 py-0.5 bg-blue-50 rounded-md">Live Link</span>
                                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" class="bg-white hover:bg-slate-900 border border-slate-200 text-slate-700 hover:text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-sm transition-all flex items-center gap-1 transform active:scale-95">
                                                <span>প্রবেশ করুন</span> ➔
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="noResult" class="hidden text-center py-12 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-sm mx-auto">
                    <p class="text-3xl mb-2">🔍</p>
                    <h3 class="text-sm font-bold text-slate-700">দুঃখিত! কোনো লিংক পাওয়া যায়নি</h3>
                    <p class="text-xs text-slate-400 mt-0.5">অন্য কোনো কি-ওয়ার্ড দিয়ে আবার চেষ্টা করুন।</p>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-[11px] text-slate-400 font-bold tracking-wide uppercase">© <?php echo date('Y'); ?> SCANNER PRO PORTAL | DEVELOPED BY RAKIB BHAI ⚡</p>
        </div>
    </footer>

    <script>
        function searchLinks() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const categoryBlocks = document.getElementsByClassName('category-block');
            let overallFound = false;

            for (let i = 0; i < categoryBlocks.length; i++) {
                const cardsInBlock = categoryBlocks[i].getElementsByClassName('link-card');
                let cardsFoundInBlock = 0;

                for (let j = 0; j < cardsInBlock.length; j++) {
                    const title = cardsInBlock[j].getElementsByClassName('link-title')[0].innerText.toLowerCase();
                    
                    if (title.includes(input)) {
                        cardsInBlock[j].style.display = "";
                        cardsFoundInBlock++;
                        overallFound = true;
                    } else {
                        cardsInBlock[j].style.display = "none";
                    }
                }

                if (cardsFoundInBlock > 0) {
                    categoryBlocks[i].style.display = "";
                } else {
                    categoryBlocks[i].style.display = "none";
                }
            }

            const noResultBox = document.getElementById('noResult');
            if (overallFound) {
                noResultBox.classList.add('hidden');
            } else {
                noResultBox.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
                    ⚙️ <span>অ্যাডমিন প্যানেল</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <section class="bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white py-12 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="max-w-3xl mx-auto relative z-10">
                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Digital Service Platform</span>
                <h2 class="text-2xl sm:text-4xl font-black mt-3 tracking-tight">প্রয়োজনীয় সকল অনলাইন লিংক ও সার্ভিস পোর্টাল</h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-xl mx-auto">বাংলাদেশ ও প্রবাসীদের প্রয়োজনীয় সরকারি সেবা, জন্মনিবন্ধন, পাসপোর্ট, স্মার্টকার্ড রিসাইজার এবং ড্রাইভের সকল লিংক এক জায়গায় পাবেন।</p>
                
                <div class="mt-8 max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 text-base">🔍</div>
                    <input type="text" id="searchInput" onkeyup="searchLinks()" placeholder="যেকোনো লিংকের নাম বা কী-ওয়ার্ড লিখে সার্চ করুন..." class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-900 rounded-2xl border-none outline-none focus:ring-4 focus:ring-blue-500/30 text-sm font-medium shadow-2xl transition-all">
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            
            <?php if (empty($links_by_category)): ?>
                <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-md mx-auto">
                    <p class="text-4xl mb-2">📥</p>
                    <h3 class="text-base font-bold text-slate-700">ডাটাবেজে কোনো লিংক নেই!</h3>
                    <p class="text-xs text-slate-400 mt-1">অনুগ্রহ করে অ্যাডমিন প্যানেল থেকে লিংক যুক্ত করুন।</p>
                </div>
            <?php else: ?>
                
                <div id="linksContainer" class="space-y-12">
                    <?php foreach ($links_by_category as $category => $links): ?>
                        <div class="category-block bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm transition-all">
                            
                            <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                                <span class="w-2 h-5 bg-blue-600 rounded-full"></span>
                                <h3 class="text-base sm:text-lg font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($category); ?></h3>
                                <span class="bg-slate-100 text-slate-500 text-[11px] font-bold px-2 py-0.5 rounded-lg"><?php echo count($links); ?>টি লিংক</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($links as $link): ?>
                                    <div class="link-card card-hover border border-slate-100 bg-slate-50/50 p-4 rounded-2xl flex flex-col justify-between gap-3">
                                        <div>
                                            <h4 class="link-title text-sm font-bold text-slate-800 tracking-tight leading-snug"><?php echo htmlspecialchars($link['site_name']); ?></h4>
                                            <?php if (!empty($link['description'])): ?>
                                                <p class="text-[11px] text-slate-400 mt-1 line-clamp-2"><?php echo htmlspecialchars($link['description']); ?></p>
                                            <?php else: ?>
                                                <p class="text-[11px] text-slate-300 italic mt-1">কোনো বিবরণ দেওয়া নেই</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-100/70 mt-1">
                                            <span class="text-[9px] font-bold text-blue-500 tracking-wide uppercase px-2 py-0.5 bg-blue-50 rounded-md">Live Link</span>
                                            <a href="<?php echo htmlspecialchars($link['site_url']); ?>" target="_blank" class="bg-white hover:bg-slate-900 border border-slate-200 text-slate-700 hover:text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-sm transition-all flex items-center gap-1 transform active:scale-95">
                                                <span>প্রবেশ করুন</span> ➔
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="noResult" class="hidden text-center py-12 bg-white rounded-3xl border border-slate-200 shadow-sm max-w-sm mx-auto">
                    <p class="text-3xl mb-2">🔍</p>
                    <h3 class="text-sm font-bold text-slate-700">দুঃখিত! কোনো লিংক পাওয়া যায়নি</h3>
                    <p class="text-xs text-slate-400 mt-0.5">অন্য কোনো কি-ওয়ার্ড দিয়ে আবার চেষ্টা করুন।</p>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-[11px] text-slate-400 font-bold tracking-wide uppercase">© <?php echo date('Y'); ?> SCANNER PRO PORTAL | DEVELOPED BY RAKIB BHAI ⚡</p>
            <p class="text-[10px] text-slate-300 mt-1 font-medium">All Rights Reserved. 100% Secure & Fluid Interface Platform.</p>
        </div>
    </footer>

    <script>
        function searchLinks() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const linkCards = document.getElementsByClassName('link-card');
            const categoryBlocks = document.getElementsByClassName('category-block');
            let overallFound = false;

            // প্রতিটা ক্যাটাগরি ব্লক ধরে চেক করা
            for (let i = 0; i < categoryBlocks.length; i++) {
                const cardsInBlock = categoryBlocks[i].getElementsByClassName('link-card');
                let cardsFoundInBlock = 0;

                // ব্লকের ভেতরের কার্ডগুলো চেক করা
                for (let j = 0; j < cardsInBlock.length; j++) {
                    const title = cardsInBlock[j].getElementsByClassName('link-title')[0].innerText.toLowerCase();
                    
                    if (title.includes(input)) {
                        cardsInBlock[j].style.display = "";
                        cardsFoundInBlock++;
                        overallFound = true;
                    } else {
                        cardsInBlock[j].style.display = "none";
                    }
                }

                // যদি কোনো ব্লকের ভেতরে একটা কার্ডও ম্যাচ না করে, তবে পুরো ক্যাটাগরি ব্লকটাই হাইড করে দিবে
                if (cardsFoundInBlock > 0) {
                    categoryBlocks[i].style.display = "";
                } else {
                    categoryBlocks[i].style.display = "none";
                }
            }

            // যদি কোনো লিংকই ম্যাচ না করে তবে নো রেজাল্ট বক্স দেখাবে
            const noResultBox = document.getElementById('noResult');
            if (overallFound) {
                noResultBox.classList.add('hidden');
            } else {
                noResultBox.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
    }
}

// লিংক ডাটাবেজে সেভ
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
            $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium'>🚀 নতুন লিংকটি ডাটাবেজে সফলভাবে সেভ হয়েছে!</div>";
        } else {
            $message = "<div class='bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium'>❌ ডাটাবেজ প্রবলেম!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>রাকিব ভাই অল-ইন-ওয়ান ই-সার্ভিস পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f1f5f9; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen">

    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
        <main class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-6 w-full max-w-md">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">🔒</div>
                    <h2 class="text-xl font-bold text-slate-800 uppercase">Admin Lock Portal</h2>
                </div>
                <?php if (!empty($login_error)) echo "<div class='bg-rose-50 text-rose-600 p-2.5 rounded-xl text-xs text-center mb-4'>$login_error</div>"; ?>
                <form action="admin.php" method="POST" class="space-y-4">
                    <input type="text" name="username" required placeholder="ইউজারনেম লিখুন" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 text-sm">
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 text-sm">
                    <button type="submit" name="login_btn" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl text-sm shadow-md hover:bg-blue-700 transition-all">প্যানেল আনলক করুন ➔</button>
                </form>
            </div>
        </main>
    <?php else: ?>
        <div class="flex flex-col md:flex-row min-h-screen">
            
            <aside class="w-full md:w-64 bg-slate-900 text-slate-300 flex flex-col justify-between p-4 shadow-xl">
                <div>
                    <div class="mb-6 p-2 border-b border-slate-800">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">🛠️ Rakib Portal</h2>
                        <p class="text-[10px] text-slate-500">All-In-One Web Utility Tools</p>
                    </div>
                    
                    <nav class="space-y-1">
                        <button onclick="switchTab('link-manager')" id="btn-link-manager" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 bg-blue-600 text-white">
                            🔗 লিংক ম্যানেজার প্যানেল
                        </button>
                        <div class="pt-3 pb-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider px-2">Document Tools</div>
                        <button onclick="switchTab('nid-crop')" id="btn-nid-crop" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 hover:bg-slate-800">
                            🪪 NID Card Auto Crop
                        </button>
                        <div class="pt-3 pb-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider px-2">Signature & Utility</div>
                        <button onclick="switchTab('sig-pad')" id="btn-sig-pad" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 hover:bg-slate-800">
                            ✍️ Digital Signature Pad
                        </button>
                        <button onclick="switchTab('date-converter')" id="btn-date-converter" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 hover:bg-slate-800">
                            📅 Date to Words Convert
                        </button>
                    </nav>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Rakib Bhai V1.0</span>
                    <a href="admin.php?action=logout" class="bg-rose-600 text-white text-xs px-2.5 py-1.5 rounded-lg font-bold">Logout 📴</a>
                </div>
            </aside>

            <main class="flex-grow p-4 sm:p-8 max-w-4xl mx-auto w-full">
                
                <div id="link-manager" class="tab-content active bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">🔗 নতুন লিংক ডাটাবেজে যুক্ত করুন</h3>
                    <?php if(!empty($message)) echo $message; ?>
                    <form action="admin.php" method="POST" class="space-y-4 mt-2">
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ওয়েবসাইটের নাম *</label>
                            <input type="text" name="site_name" required placeholder="পাসপোর্ট চেক" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ওয়েবসাইট URL লিংক *</label>
                            <input type="url" name="site_url" required placeholder="https://..." class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ক্যাটাগরি সিলেক্ট করুন *</label>
                            <select name="category" required class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm">
                                <option value="সরকারি সেবা">সরকারি সেবা</option>
                                <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                                <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ছোট বিবরণ</label>
                            <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm resize-none"></textarea>
                        </div>
                        <button type="submit" name="add_link" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold text-sm">লিংক লাইভ করুন ➔</button>
                    </form>
                </div>

                <div id="nid-crop" class="tab-content bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-2 border-b pb-2">🪪 NID Card Auto Crop Tool</h3>
                    <p class="text-xs text-slate-500 mb-4">ভোটার আইডি কার্ডের সামনের এবং পিছনের সাইড আপলোড করুন, কোড অটোমেটিক কেটে স্ট্যান্ডার্ড সাইজে এক পাতায় সাজিয়ে দিবে।</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-600 block mb-1">Front Part (সামনের দিক)</label>
                                <input type="file" id="nidFront" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-600 block mb-1">Back Part (পিছনের দিক)</label>
                                <input type="file" id="nidBack" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                            </div>
                        </div>
                        <button onclick="processNID()" class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded-xl text-sm">এপিঠ-ওপিঠ একসাথে ক্রপ করুন ⚡</button>
                        
                        <div class="mt-4 flex flex-col items-center justify-center">
                            <canvas id="nidCanvas" class="border border-dashed border-slate-300 max-w-full rounded-xl hidden bg-white shadow-inner"></canvas>
                            <button id="downloadNidBtn" onclick="downloadNid()" class="hidden mt-3 bg-blue-600 text-white text-xs px-4 py-2 rounded-lg font-bold shadow">ক্রপড আইডি কার্ড ডাউনলোড করুন 📥</button>
                        </div>
                    </div>
                </div>

                <div id="sig-pad" class="tab-content bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-2 border-b pb-2">✍️ Digital Signature Generator</h3>
                    <p class="text-xs text-slate-500 mb-4">নিচের সাদা বক্সে মাউস বা মোবাইল স্ক্রিনে আঙুল দিয়ে স্বাক্ষর করুন, তারপর পিএনজি হিসেবে ডাউনলোড করুন।</p>
                    <div class="flex flex-col items-center">
                        <canvas id="sigCanvas" width="500" height="200" class="border rounded-xl bg-slate-50 max-w-full cursor-crosshair shadow-inner"></canvas>
                        <div class="flex gap-2 mt-4 w-full max-w-md">
                            <button onclick="clearSignature()" class="w-1/2 bg-slate-200 text-slate-700 py-2 rounded-xl text-sm font-bold">মুছে ফেলুন (Clear)</button>
                            <button onclick="saveSignature()" class="w-1/2 bg-blue-600 text-white py-2 rounded-xl text-sm font-bold shadow-md">স্বাক্ষর ডাউনলোড 📥</button>
                        </div>
                    </div>
                </div>

                <div id="date-converter" class="tab-content bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-2 border-b pb-2">📅 Date to Words / তারিখ কথায় রূপান্তর</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">তারিখ সিলেক্ট করুন</label>
                            <input type="date" id="inputDate" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button onclick="convertDateToBangla()" class="w-full bg-purple-600 text-white font-bold py-2.5 rounded-xl text-sm shadow">বাংলা কথায় রূপান্তর করুন ✨</button>
                        
                        <div id="dateResultBox" class="hidden p-4 bg-purple-50 border border-purple-200 rounded-xl text-center">
                            <p class="text-xs text-purple-500 font-bold uppercase tracking-wider">আউটপুট কথায়:</p>
                            <p id="dateBanglaOutput" class="text-lg font-bold text-purple-900 mt-1"></p>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    <?php endif; ?>

    <script>
        // ১. ট্যাব পরিবর্তন করার ফাংশন
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('bg-blue-600', 'text-white'));
            
            document.getElementById(tabId).classList.add('active');
            document.getElementById('btn-' + tabId).classList.add('bg-blue-600', 'text-white');
        }

        // ২. NID Auto Crop ইঞ্জিন (HTML5 Canvas টেকনোলজি)
        function processNID() {
            const frontFile = document.getElementById('nidFront').files[0];
            const backFile = document.getElementById('nidBack').files[0];
            if(!frontFile || !backFile) { alert("দয়া করে আইডি কার্ডের এপিঠ-ওপিঠ দুই পাশের ছবিই সিলেক্ট করুন!"); return; }

            const canvas = document.getElementById('nidCanvas');
            const ctx = canvas.getContext('2d');
            
            // Standard NID Dimension Layout 
            canvas.width = 600;
            canvas.height = 800;
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0,0, canvas.width, canvas.height);

            let imgFront = new Image();
            let imgBack = new Image();

            imgFront.src = URL.createObjectURL(frontFile);
            imgFront.onload = function() {
                // Front Side Crop Draw
                ctx.drawImage(imgFront, 50, 50, 500, 310);
                
                imgBack.src = URL.createObjectURL(backFile);
                imgBack.onload = function() {
                    // Back Side Crop Draw
                    ctx.drawImage(imgBack, 50, 420, 500, 310);
                    
                    canvas.classList.remove('hidden');
                    document.getElementById('downloadNidBtn').classList.remove('hidden');
                }
            }
        }
        function downloadNid() {
            const canvas = document.getElementById('nidCanvas');
            const link = document.createElement('a');
            link.download = 'RakibPortal_NID_Cropped.png';
            link.href = canvas.toDataURL();
            link.click();
        }

        // ৩. Digital Signature Pad ইঞ্জিন 
        const sigCanvas = document.getElementById('sigCanvas');
        const sigCtx = sigCanvas.getContext('2d');
        let isDrawing = false;

        // মাউস ও টাচ ইভেন্ট ট্র্যাকিং
        function getPos(e) {
            const rect = sigCanvas.getBoundingClientRect();
            return {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
        }
        sigCanvas.addEventListener('mousedown', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); });
        sigCanvas.addEventListener('mousemove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.strokeStyle = "#000000"; sigCtx.stroke(); });
        window.addEventListener('mouseup', () => isDrawing = false);
        
        sigCanvas.addEventListener('touchstart', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); e.preventDefault(); });
        sigCanvas.addEventListener('touchmove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.stroke(); e.preventDefault(); });

        function clearSignature() { sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height); }
        function saveSignature() {
            const link = document.createElement('a');
            link.download = 'Digital_Signature.png';
            link.href = sigCanvas.toDataURL("image/png");
            link.click();
        }

        // ৪. Date to Bangla Words Converter লজিক
        function convertDateToBangla() {
            const dateVal = document.getElementById('inputDate').value;
            if(!dateVal) { alert("তারিখ সিলেক্ট করুন!"); return; }

            const months = ["জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"];
            const banglaNums = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
            
            const parts = dateVal.split('-'); // YYYY-MM-DD
            const year = parts[0];
            const month = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]).toString();

            let banglaDay = day.split('').map(c => banglaNums[c] || c).join('') + "ই";
            let banglaMonth = months[month];
            let banglaYear = year.split('').map(c => banglaNums[c] || c).join('');

            document.getElementById('dateResultBox').classList.remove('hidden');
            document.getElementById('dateBanglaOutput').innerText = `${banglaDay} ${banglaMonth}, ${banglaYear} ইং`;
        }
    </script>
</body>
</html>    }
}

// লিংক ডাটাবেজে সেভ
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
            $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium'>🚀 নতুন লিংকটি ডাটাবেজে সফলভাবে সেভ হয়েছে!</div>";
        } else {
            $message = "<div class='bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium'>❌ ডাটাবেজ প্রবলেম!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>রাকিব ভাই অল-ইন-ওয়ান ই-সার্ভিস পোর্টাল</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f1f5f9; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen">

    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
        <main class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-6 w-full max-w-md">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">🔒</div>
                    <h2 class="text-xl font-bold text-slate-800 uppercase">Admin Lock Portal</h2>
                </div>
                <?php if (!empty($login_error)) echo "<div class='bg-rose-50 text-rose-600 p-2.5 rounded-xl text-xs text-center mb-4'>$login_error</div>"; ?>
                <form action="admin.php" method="POST" class="space-y-4">
                    <input type="text" name="username" required placeholder="ইউজারনেম লিখুন" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 text-sm">
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 text-sm">
                    <button type="submit" name="login_btn" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl text-sm shadow-md hover:bg-blue-700 transition-all">প্যানেল আনলক করুন ➔</button>
                </form>
            </div>
        </main>
    <?php else: ?>
        <div class="flex flex-col md:flex-row min-h-screen">
            
            <aside class="w-full md:w-64 bg-slate-900 text-slate-300 flex flex-col justify-between p-4 shadow-xl">
                <div>
                    <div class="mb-6 p-2 border-b border-slate-800">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">🛠️ Rakib Portal</h2>
                        <p class="text-[10px] text-slate-500">All-In-One Web Utility Tools</p>
                    </div>
                    
                    <nav class="space-y-1">
                        <button onclick="switchTab('link-manager')" id="btn-link-manager" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 bg-blue-600 text-white">
                            🔗 লিংক ম্যানেজার প্যানেল
                        </button>
                        <div class="pt-3 pb-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider px-2">Document Tools</div>
                        <button onclick="switchTab('nid-crop')" id="btn-nid-crop" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 hover:bg-slate-800">
                            🪪 NID Card Auto Crop
                        </button>
                        <div class="pt-3 pb-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider px-2">Signature & Utility</div>
                        <button onclick="switchTab('sig-pad')" id="btn-sig-pad" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 hover:bg-slate-800">
                            ✍️ Digital Signature Pad
                        </button>
                        <button onclick="switchTab('date-converter')" id="btn-date-converter" class="tab-btn w-full text-left px-3 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 hover:bg-slate-800">
                            📅 Date to Words Convert
                        </button>
                    </nav>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Rakib Bhai V1.0</span>
                    <a href="admin.php?action=logout" class="bg-rose-600 text-white text-xs px-2.5 py-1.5 rounded-lg font-bold">Logout 📴</a>
                </div>
            </aside>

            <main class="flex-grow p-4 sm:p-8 max-w-4xl mx-auto w-full">
                
                <div id="link-manager" class="tab-content active bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">🔗 নতুন লিংক ডাটাবেজে যুক্ত করুন</h3>
                    <?php if(!empty($message)) echo $message; ?>
                    <form action="admin.php" method="POST" class="space-y-4 mt-2">
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ওয়েবসাইটের নাম *</label>
                            <input type="text" name="site_name" required placeholder="পাসপোর্ট চেক" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ওয়েবসাইট URL লিংক *</label>
                            <input type="url" name="site_url" required placeholder="https://..." class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ক্যাটাগরি সিলেক্ট করুন *</label>
                            <select name="category" required class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm">
                                <option value="সরকারি সেবা">সরকারি সেবা</option>
                                <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                                <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase">ছোট বিবরণ</label>
                            <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm resize-none"></textarea>
                        </div>
                        <button type="submit" name="add_link" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold text-sm">লিংক লাইভ করুন ➔</button>
                    </form>
                </div>

                <div id="nid-crop" class="tab-content bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-2 border-b pb-2">🪪 NID Card Auto Crop Tool</h3>
                    <p class="text-xs text-slate-500 mb-4">ভোটার আইডি কার্ডের সামনের এবং পিছনের সাইড আপলোড করুন, কোড অটোমেটিক কেটে স্ট্যান্ডার্ড সাইজে এক পাতায় সাজিয়ে দিবে।</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-600 block mb-1">Front Part (সামনের দিক)</label>
                                <input type="file" id="nidFront" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-600 block mb-1">Back Part (পিছনের দিক)</label>
                                <input type="file" id="nidBack" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                            </div>
                        </div>
                        <button onclick="processNID()" class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded-xl text-sm">এপিঠ-ওপিঠ একসাথে ক্রপ করুন ⚡</button>
                        
                        <div class="mt-4 flex flex-col items-center justify-center">
                            <canvas id="nidCanvas" class="border border-dashed border-slate-300 max-w-full rounded-xl hidden bg-white shadow-inner"></canvas>
                            <button id="downloadNidBtn" onclick="downloadNid()" class="hidden mt-3 bg-blue-600 text-white text-xs px-4 py-2 rounded-lg font-bold shadow">ক্রপড আইডি কার্ড ডাউনলোড করুন 📥</button>
                        </div>
                    </div>
                </div>

                <div id="sig-pad" class="tab-content bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-2 border-b pb-2">✍️ Digital Signature Generator</h3>
                    <p class="text-xs text-slate-500 mb-4">নিচের সাদা বক্সে মাউস বা মোবাইল স্ক্রিনে আঙুল দিয়ে স্বাক্ষর করুন, তারপর পিএনজি হিসেবে ডাউনলোড করুন।</p>
                    <div class="flex flex-col items-center">
                        <canvas id="sigCanvas" width="500" height="200" class="border rounded-xl bg-slate-50 max-w-full cursor-crosshair shadow-inner"></canvas>
                        <div class="flex gap-2 mt-4 w-full max-w-md">
                            <button onclick="clearSignature()" class="w-1/2 bg-slate-200 text-slate-700 py-2 rounded-xl text-sm font-bold">মুছে ফেলুন (Clear)</button>
                            <button onclick="saveSignature()" class="w-1/2 bg-blue-600 text-white py-2 rounded-xl text-sm font-bold shadow-md">স্বাক্ষর ডাউনলোড 📥</button>
                        </div>
                    </div>
                </div>

                <div id="date-converter" class="tab-content bg-white p-6 rounded-2xl border shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-2 border-b pb-2">📅 Date to Words / তারিখ কথায় রূপান্তর</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">তারিখ সিলেক্ট করুন</label>
                            <input type="date" id="inputDate" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button onclick="convertDateToBangla()" class="w-full bg-purple-600 text-white font-bold py-2.5 rounded-xl text-sm shadow">বাংলা কথায় রূপান্তর করুন ✨</button>
                        
                        <div id="dateResultBox" class="hidden p-4 bg-purple-50 border border-purple-200 rounded-xl text-center">
                            <p class="text-xs text-purple-500 font-bold uppercase tracking-wider">আউটপুট কথায়:</p>
                            <p id="dateBanglaOutput" class="text-lg font-bold text-purple-900 mt-1"></p>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    <?php endif; ?>

    <script>
        // ১. ট্যাব পরিবর্তন করার ফাংশন
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('bg-blue-600', 'text-white'));
            
            document.getElementById(tabId).classList.add('active');
            document.getElementById('btn-' + tabId).classList.add('bg-blue-600', 'text-white');
        }

        // ২. NID Auto Crop ইঞ্জিন (HTML5 Canvas টেকনোলজি)
        function processNID() {
            const frontFile = document.getElementById('nidFront').files[0];
            const backFile = document.getElementById('nidBack').files[0];
            if(!frontFile || !backFile) { alert("দয়া করে আইডি কার্ডের এপিঠ-ওপিঠ দুই পাশের ছবিই সিলেক্ট করুন!"); return; }

            const canvas = document.getElementById('nidCanvas');
            const ctx = canvas.getContext('2d');
            
            // Standard NID Dimension Layout 
            canvas.width = 600;
            canvas.height = 800;
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0,0, canvas.width, canvas.height);

            let imgFront = new Image();
            let imgBack = new Image();

            imgFront.src = URL.createObjectURL(frontFile);
            imgFront.onload = function() {
                // Front Side Crop Draw
                ctx.drawImage(imgFront, 50, 50, 500, 310);
                
                imgBack.src = URL.createObjectURL(backFile);
                imgBack.onload = function() {
                    // Back Side Crop Draw
                    ctx.drawImage(imgBack, 50, 420, 500, 310);
                    
                    canvas.classList.remove('hidden');
                    document.getElementById('downloadNidBtn').classList.remove('hidden');
                }
            }
        }
        function downloadNid() {
            const canvas = document.getElementById('nidCanvas');
            const link = document.createElement('a');
            link.download = 'RakibPortal_NID_Cropped.png';
            link.href = canvas.toDataURL();
            link.click();
        }

        // ৩. Digital Signature Pad ইঞ্জিন 
        const sigCanvas = document.getElementById('sigCanvas');
        const sigCtx = sigCanvas.getContext('2d');
        let isDrawing = false;

        // মাউস ও টাচ ইভেন্ট ট্র্যাকিং
        function getPos(e) {
            const rect = sigCanvas.getBoundingClientRect();
            return {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
        }
        sigCanvas.addEventListener('mousedown', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); });
        sigCanvas.addEventListener('mousemove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.strokeStyle = "#000000"; sigCtx.stroke(); });
        window.addEventListener('mouseup', () => isDrawing = false);
        
        sigCanvas.addEventListener('touchstart', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); e.preventDefault(); });
        sigCanvas.addEventListener('touchmove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.stroke(); e.preventDefault(); });

        function clearSignature() { sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height); }
        function saveSignature() {
            const link = document.createElement('a');
            link.download = 'Digital_Signature.png';
            link.href = sigCanvas.toDataURL("image/png");
            link.click();
        }

        // ৪. Date to Bangla Words Converter লজিক
        function convertDateToBangla() {
            const dateVal = document.getElementById('inputDate').value;
            if(!dateVal) { alert("তারিখ সিলেক্ট করুন!"); return; }

            const months = ["জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"];
            const banglaNums = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
            
            const parts = dateVal.split('-'); // YYYY-MM-DD
            const year = parts[0];
            const month = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]).toString();

            let banglaDay = day.split('').map(c => banglaNums[c] || c).join('') + "ই";
            let banglaMonth = months[month];
            let banglaYear = year.split('').map(c => banglaNums[c] || c).join('');

            document.getElementById('dateResultBox').classList.remove('hidden');
            document.getElementById('dateBanglaOutput').innerText = `${banglaDay} ${banglaMonth}, ${banglaYear} ইং`;
        }
    </script>
</body>
</html>
