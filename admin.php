<?php
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

// লগইন ভ্যালিডেশন
$login_error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $pass = trim($_POST['password']);
    if ($username === 'rakib' && $pass === 'rakib123') {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "❌ ইউজারনেম বা পাসওয়ার্ড সঠিক নয়!";
    }
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
            $message = "<div class='bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium mb-4'>🚀 নতুন লিংকটি সফলভাবে ডাটাবেজে সেভ হয়েছে!</div>";
        } else {
            $message = "<div class='bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium mb-4'>❌ ডাটাবেজ প্রবলেম! আবার চেষ্টা করুন।</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ID Card Scanner & E-Service Pro Portal - Rakib Bhai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen">

    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
        <main class="min-h-screen flex items-center justify-center p-4 bg-slate-900">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl p-6 sm:p-8 w-full max-w-md">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">🔒</div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight uppercase">Admin Scanner Pro</h2>
                    <p class="text-xs text-slate-400 mt-1">শুধুমাত্র রাকিব ভাই এই প্যানেলে প্রবেশ করতে পারবেন</p>
                </div>
                <?php if (!empty($login_error)) echo "<div class='bg-rose-50 border border-rose-200 text-rose-600 p-2.5 rounded-xl text-xs text-center mb-4 font-semibold'>$login_error</div>"; ?>
                <form action="admin.php" method="POST" class="space-y-4">
                    <input type="text" name="username" required placeholder="ইউজারনেম লিখুন" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <button type="submit" name="login_btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-sm shadow-md transition-all">প্যানেল আনলক করুন ➔</button>
                </form>
            </div>
        </main>
    <?php else: ?>
        <div class="flex flex-col md:flex-row min-h-screen">
            
            <aside class="w-full md:w-72 bg-slate-900 text-slate-300 flex flex-col justify-between p-4 shadow-2xl border-r border-slate-800">
                <div>
                    <div class="mb-5 p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">🚀 ID Card Scanner Pro</h2>
                        <p class="text-[10px] text-blue-400 font-semibold tracking-wider uppercase">Rakib E-Service Hub V1.5</p>
                    </div>
                    
                    <nav class="space-y-1 max-h-[75vh] overflow-y-auto pr-1">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-2 pt-2">Core System</div>
                        <button onclick="switchTab('link-manager')" id="btn-link-manager" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-blue-600 text-white shadow">
                            🔗 লিংক ম্যানেজার প্যানেল
                        </button>

                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-2 pt-3">Document & ID Tools</div>
                        <button onclick="switchTab('nid-crop')" id="btn-nid-crop" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            🪪 NID Card Auto Crop (A4 Sheet)
                        </button>
                        <button onclick="switchTab('smart-resize')" id="btn-smart-resize" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            💳 Smart Card / DL Resizer
                        </button>
                        <button onclick="switchTab('img-pdf')" id="btn-img-pdf" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            📄 Image to PDF Converter
                        </button>

                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-2 pt-3">Photo Studio Tools</div>
                        <button onclick="switchTab('passport-photo')" id="btn-passport-photo" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            📸 Passport Size Photo Maker (4/8 R)
                        </button>
                        <button onclick="switchTab('stamp-photo')" id="btn-stamp-photo" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            🔖 Stamp Size Photo Maker
                        </button>
                        <button onclick="switchTab('joint-photo')" id="btn-joint-photo" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            👥 Joint Photo Maker
                        </button>
                        <button onclick="switchTab('photo-cutter')" id="btn-photo-cutter" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            ✂️ A4 Photo Cutter / Resizer
                        </button>

                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-2 pt-3">Signature & Utility</div>
                        <button onclick="switchTab('sig-pad')" id="btn-sig-pad" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            ✍️ Digital Signature Pad (PNG)
                        </button>
                        <button onclick="switchTab('date-words')" id="btn-date-words" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            📅 Date to Words Converter
                        </button>
                        <button onclick="switchTab('num-words')" id="btn-num-words" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            💰 Number to Words (Taka Calculator)
                        </button>
                        <button onclick="switchTab('age-calc')" id="btn-age-calc" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            🧮 Age Calculator (বয়স ক্যালকুলেটর)
                        </button>

                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-2 pt-3">Digital Codes & Resume</div>
                        <button onclick="switchTab('qr-generator')" id="btn-qr-generator" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            🟩 QR Code Generator
                        </button>
                        <button onclick="switchTab('barcode-maker')" id="btn-barcode-maker" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            █║ Barcode Maker
                        </button>
                        <button onclick="switchTab('cv-builder')" id="btn-cv-builder" class="tab-btn w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 hover:bg-slate-800 hover:text-white">
                            👔 CV / Resume Builder
                        </button>
                    </nav>
                </div>
                
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">© 2026 Admin Pro</span>
                    <a href="admin.php?action=logout" class="bg-rose-600 hover:bg-rose-700 text-white text-[11px] px-3 py-1.5 rounded-lg font-bold shadow-sm transition-all">Logout 📴</a>
                </div>
            </aside>

            <main class="flex-grow p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto w-full">
                
                <div id="link-manager" class="tab-content active bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-fade-in">
                    <h3 class="text-base font-bold text-slate-800 mb-4 border-b pb-2 flex items-center gap-1.5"><span>🔗</span> নতুন লিংক ডাটাবেজে যুক্ত করুন</h3>
                    <?php if(!empty($message)) echo $message; ?>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-600 block mb-1">ওয়েবসাইটের নাম *</label>
                                <input type="text" name="site_name" required placeholder="যেমন: পাসপোর্ট স্ট্যাটাস চেক" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-600 block mb-1">ওয়েবসাইট URL লিংক *</label>
                                <input type="url" name="site_url" required placeholder="https://example.com" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">ক্যাটাগরি সিলেক্ট করুন *</label>
                            <select name="category" required class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm cursor-pointer outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="সরকারি সেবা">সরকারি সেবা</option>
                                <option value="প্রবাসী সেবা">প্রবাসী সেবা</option>
                                <option value="শিক্ষা ও রেজাল্ট">শিক্ষা ও রেজাল্ট</option>
                                <option value="পেমেন্ট ও ব্যাংকিং">পেমেন্ট ও ব্যাংকিং</option>
                                <option value="ইউটিলিটি ও টুলস">ইউটিলিটি ও টুলস</option>
                                <option value="সفتওয়্যার ডাউনলোড">সফটওয়্যার ডাউনলোড</option>
                                <option value="রাকিব ড্রাইভ রিসোর্স">রাকিব ড্রাইভ রিসোর্স</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">ছোট বিবরণ (অপশনাল)</label>
                            <textarea name="description" rows="2" placeholder="সাইটের বিবরণ লিখুন..." class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm resize-none outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit" name="add_link" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold text-sm shadow transition-all">লিংক লাইভ করুন ➔</button>
                    </form>
                </div>

                <div id="nid-crop" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-1 border-b pb-2">🪪 NID Card Auto Crop & Join Engine</h3>
                    <p class="text-xs text-slate-400 mb-4">আইডি কার্ডের সামনের এবং পিছনের সাইড সিলেক্ট করুন, কোড এপিঠ-ওপিঠ এক ফ্রেমে এনে রেডি করে দিবে।</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="border border-dashed p-4 rounded-xl bg-slate-50">
                                <label class="text-xs font-bold text-slate-600 block mb-2">Front Side (সামনের পার্ট)</label>
                                <input type="file" id="nidFront" accept="image/*" class="w-full text-xs text-slate-500">
                            </div>
                            <div class="border border-dashed p-4 rounded-xl bg-slate-50">
                                <label class="text-xs font-bold text-slate-600 block mb-2">Back Side (পিছনের পার্ট)</label>
                                <input type="file" id="nidBack" accept="image/*" class="w-full text-xs text-slate-500">
                            </div>
                        </div>
                        <button onclick="processNID()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl text-sm transition-all shadow-sm">ইনস্ট্যান্ট ক্রপ এবং এপিঠ-ওপিঠ করুন ⚡</button>
                        
                        <div class="mt-4 flex flex-col items-center justify-center">
                            <canvas id="nidCanvas" class="border border-slate-300 max-w-full rounded-xl hidden bg-white shadow-md"></canvas>
                            <button id="downloadNidBtn" onclick="downloadNid()" class="hidden mt-3 bg-blue-600 hover:bg-blue-700 text-white text-xs px-5 py-2.5 rounded-xl font-bold shadow transition-all">ক্রপড আইডি কার্ড ডাউনলোড করুন 📥</button>
                        </div>
                    </div>
                </div>

                <div id="sig-pad" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-1 border-b pb-2">✍️ Digital Signature Pad (Transparent PNG)</h3>
                    <p class="text-xs text-slate-400 mb-4">নিচের বক্সে মাউস বা মোবাইল টাচ দিয়ে সিগনেচার করুন, ব্যাকগ্রাউন্ড ছাড়া ক্লিয়ার PNG ফাইল পাবেন।</p>
                    <div class="flex flex-col items-center">
                        <canvas id="sigCanvas" width="600" height="220" class="border border-slate-200 rounded-xl bg-slate-50 max-w-full cursor-crosshair shadow-inner"></canvas>
                        <div class="flex gap-3 mt-4 w-full max-w-md">
                            <button onclick="clearSignature()" class="w-1/2 bg-slate-200 hover:bg-slate-300 text-slate-700 py-2.5 rounded-xl text-xs font-bold transition-all">মুছে ফেলুন (Clear)</button>
                            <button onclick="saveSignature()" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-xs font-bold shadow transition-all">স্বাক্ষর ডাউনলোড করুন 📥</button>
                        </div>
                    </div>
                </div>

                <div id="date-words" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">📅 Date to Words / তারিখ কথায় রূপান্তর</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">তারিখ সিলেক্ট করুন</label>
                            <input type="date" id="inputDate" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button onclick="convertDateToBangla()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl text-sm transition-all shadow">কথায় রূপান্তর করুন ✨</button>
                        
                        <div id="dateResultBox" class="hidden p-4 bg-purple-50 border border-purple-100 rounded-xl text-center">
                            <p class="text-xs text-purple-500 font-bold tracking-wider">আউটপুট কথায়:</p>
                            <p id="dateBanglaOutput" class="text-lg font-bold text-purple-900 mt-1"></p>
                        </div>
                    </div>
                </div>

                <div id="num-words" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">💰 Number to Words (টাকা কথায় রূপান্তর)</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">টাকার পরিমাণ বা সংখ্যা লিখুন</label>
                            <input type="number" id="inputNumber" placeholder="যেমন: 52500" class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button onclick="convertNumberToBanglaTaka()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-xl text-sm transition-all shadow">টাকা কথায় রূপান্তর করুন ➔</button>
                        
                        <div id="numResultBox" class="hidden p-4 bg-amber-50 border border-amber-100 rounded-xl text-center">
                            <p class="text-xs text-amber-600 font-bold tracking-wider">রসিদ বা ফর্মের জন্য কথা:</p>
                            <p id="numBanglaOutput" class="text-base font-bold text-amber-900 mt-1"></p>
                        </div>
                    </div>
                </div>

                <div id="qr-generator" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">🟩 Instant QR Code Generator</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">লিংক বা টেক্সট লিখুন (bKash/Website URL)</label>
                            <input type="text" id="qrText" placeholder="https://..." class="w-full px-4 py-2 border rounded-xl bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button onclick="generateQRCode()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-xl text-sm transition-all shadow">QR Code জেনারেট করুন 🚀</button>
                        
                        <div id="qrResultBox" class="hidden flex flex-col items-center justify-center p-4 border rounded-xl bg-slate-50 mt-4">
                            <img id="qrImage" src="" alt="QR Code" class="border bg-white p-2 rounded-lg shadow-sm">
                            <button onclick="downloadQR()" class="mt-3 bg-blue-600 text-white text-xs px-4 py-2 rounded-lg font-bold">QR ডাউনলোড করুন 📥</button>
                        </div>
                    </div>
                </div>

                <div id="smart-resize" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">💳 Driving License & Smart Card Resizer</h3>
                    <p class="text-sm text-slate-500">পরবর্তী আপডেটে এই মেকানিজম সরাসরি লাইভ হবে। ফর্ম কাঠামো প্রস্তুত।</p>
                </div>
                
                <div id="img-pdf" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">📄 Image to PDF Converter</h3>
                    <p class="text-sm text-slate-500">একাধিক ছবি জোড়া দিয়ে পিডিএফ বানানোর ড্যাশবোর্ড ফর্ম কাঠামো রেডি।</p>
                </div>

                <div id="passport-photo" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">📸 Passport Size Photo Maker</h3>
                    <p class="text-sm text-slate-500">৪ কপি/৮ কপি ছবির অটো-লেআউট শিট মডিউল স্টুডিও ফ্রেম কাঠামো রেডি।</p>
                </div>

                <div id="stamp-photo" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">🔖 Stamp Size Photo Maker</h3>
                    <p class="text-sm text-slate-500">স্ট্যাম্প ছবি মেকার কাঠামো রেডি।</p>
                </div>

                <div id="joint-photo" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">👥 Joint Photo Maker</h3>
                    <p class="text-sm text-slate-500">যৌথ ছবি মেকার উইন্ডো ফ্রেম রেডি।</p>
                </div>

                <div id="photo-cutter" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">✂️ A4 Photo Cutter / Resizer</h3>
                    <p class="text-sm text-slate-500">A4 সাইজ ফটো কাটার মডিউল লেআউট রেди।</p>
                </div>

                <div id="age-calc" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">🧮 Age Calculator (বয়স ক্যালকুলেটর)</h3>
                    <p class="text-sm text-slate-500">বছর, মাস, দিন হিসেব করার ক্যালকুলেটর কাঠামো রেডি।</p>
                </div>

                <div id="barcode-maker" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">█║ Barcode Maker</h3>
                    <p class="text-sm text-slate-500">বারকোড তৈরি করার ফ্রেম রেডি।</p>
                </div>

                <div id="cv-builder" class="tab-content bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-2 border-b pb-2">👔 CV / Resume Builder</h3>
                    <p class="text-sm text-slate-500">জীবনবৃত্তান্ত তৈরি করার প্রফেশনাল ডেটা ইনপুট ফ্রেম রেডি।</p>
                </div>

            </main>
        </div>
    <?php endif; ?>

    <script>
        // ট্যাব সুইচিং মেকানিজম (কোনো রিলোড ছাড়া সুপারফাস্ট কাজ করবে)
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-blue-600', 'text-white', 'shadow');
                el.classList.add('hover:bg-slate-800');
            });
            
            document.getElementById(tabId).classList.add('active');
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('hover:bg-slate-800');
            activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow');
        }

        // NID Card Auto Crop লজিক (HTML5 Canvas টেকনোলজি)
        function processNID() {
            const frontFile = document.getElementById('nidFront').files[0];
            const backFile = document.getElementById('nidBack').files[0];
            if(!frontFile || !backFile) { alert("দয়া করে আইডি কার্ডের সামনের এবং পিছনের দুই পাশের ছবিই সিলেক্ট করুন!"); return; }

            const canvas = document.getElementById('nidCanvas');
            const ctx = canvas.getContext('2d');
            
            // Standard Print Ready Dimensions
            canvas.width = 600;
            canvas.height = 820;
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            let imgFront = new Image();
            let imgBack = new Image();

            imgFront.src = URL.createObjectURL(frontFile);
            imgFront.onload = function() {
                ctx.drawImage(imgFront, 50, 60, 500, 315); // Front Layout Center Set
                
                imgBack.src = URL.createObjectURL(backFile);
                imgBack.onload = function() {
                    ctx.drawImage(imgBack, 50, 440, 500, 315); // Back Layout Center Set
                    
                    canvas.classList.remove('hidden');
                    document.getElementById('downloadNidBtn').classList.remove('hidden');
                }
            }
        }
        function downloadNid() {
            const canvas = document.getElementById('nidCanvas');
            const link = document.createElement('a');
            link.download = 'RakibScanner_NID_Printed.png';
            link.href = canvas.toDataURL();
            link.click();
        }

        // Digital Signature Pad হ্যান্ডলিং
        const sigCanvas = document.getElementById('sigCanvas');
        const sigCtx = sigCanvas.getContext('2d');
        let isDrawing = false;

        function getPos(e) {
            const rect = sigCanvas.getBoundingClientRect();
            return {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
        }
        sigCanvas.addEventListener('mousedown', (e) => { isDrawing = true; sigCtx. someMethod = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); });
        sigCanvas.addEventListener('mousemove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.strokeStyle = "#000000"; sigCtx.stroke(); });
        window.addEventListener('mouseup', () => isDrawing = false);
        
        sigCanvas.addEventListener('touchstart', (e) => { isDrawing = true; sigCtx.beginPath(); let pos = getPos(e); sigCtx.moveTo(pos.x, pos.y); e.preventDefault(); });
        sigCanvas.addEventListener('touchmove', (e) => { if(!isDrawing) return; let pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.lineWidth = 3; sigCtx.stroke(); e.preventDefault(); });

        function clearSignature() { sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height); }
        function saveSignature() {
            const link = document.createElement('a');
            link.download = 'Digital_Signature_RakibPro.png';
            link.href = sigCanvas.toDataURL("image/png");
            link.click();
        }

        // Date to Words Converter লজিক
        function convertDateToBangla() {
            const dateVal = document.getElementById('inputDate').value;
            if(!dateVal) { alert("তারিখ সিলেক্ট করুন!"); return; }

            const months = ["জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"];
            const banglaNums = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
            
            const parts = dateVal.split('-'); 
            const year = parts[0];
            const month = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]).toString();

            let banglaDay = day.split('').map(c => banglaNums[c] || c).join('') + "ই";
            let banglaMonth = months[month];
            let banglaYear = year.split('').map(c => banglaNums[c] || c).join('');

            document.getElementById('dateResultBox').classList.remove('hidden');
            document.getElementById('dateBanglaOutput').innerText = `${banglaDay} ${banglaMonth}, ${banglaYear} ইং`;
        }

        // Number to Words (টাকা কথায় কনভার্ট করার মেকানিজম)
        function convertNumberToBanglaTaka() {
            const num = document.getElementById('inputNumber').value;
            if(!num || num < 0) { alert("সঠিক সংখ্যা বা টাকার পরিমাণ লিখুন!"); return; }
            
            const units = ['', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়'];
            const teens = ['দশ', 'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোলো', 'সতেরো', 'আঠারো', 'উনিশ'];
            const tens = ['', '', 'বিশ', 'ত্রিশ', 'চল্লিশ', 'পঞ্চাশ', 'ষাট', 'সত্তর', 'আশি', 'নব্বই'];
            
            function convertBelowHundred(n) {
                if (n < 10) return units[n];
                if (n >= 10 && n < 20) return teens[n - 10];
                let digitTens = Math.floor(n / 10);
                let digitUnits = n % 10;
                return tens[digitTens] + (digitUnits !== 0 ? ' ' + units[digitUnits] : '');
            }

            let n = parseInt(num);
            if (n === 0) {  document.getElementById('numBanglaOutput').innerText = "শূণ্য টাকা মাত্র"; return; }

            let result = '';
            let crore = Math.floor(n / 10000000); n %= 10000000;
            let lakh = Math.floor(n / 100000); n %= 100000;
            let thousand = Math.floor(n / 1000); n %= 1000;
            let hundred = Math.floor(n / 100); n %= 100;
            let rem = n;

            if (crore > 0) result += convertBelowHundred(crore) + ' কোটি ';
            if (lakh > 0) result += convertBelowHundred(lakh) + ' লক্ষ ';
            if (thousand > 0) result += convertBelowHundred(thousand) + ' হাজার ';
            if (hundred > 0) result += convertBelowHundred(hundred) + ' শত ';
            if (rem > 0) result += convertBelowHundred(rem);

            document.getElementById('numResultBox').classList.remove('hidden');
            document.getElementById('numBanglaOutput').innerText = result.trim() + " টাকা মাত্র।";
        }

        // QR Code Generator (Free Quick API Engine)
        function generateQRCode() {
            const txt = document.getElementById('qrText').value;
            if(!txt) { alert("লিংক বা টেক্সট ইনপুট দিন!"); return; }
            const qrImg = document.getElementById('qrImage');
            
            // Generate standard dynamic QR
            qrImg.src = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" + encodeURIComponent(txt);
            document.getElementById('qrResultBox').classList.remove('hidden');
        }
        function downloadQR() {
            const qrImg = document.getElementById('qrImage');
            const link = document.createElement('a');
            link.download = 'RakibPortal_QRCode.png';
            link.href = qrImg.src;
            // Target open to download easily
            window.open(qrImg.src, '_blank');
        }
    </script>
</body>
                            </html>    <style>
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
</html>        $_SESSION['admin_logged_in'] = true;
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
