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

// লগআউট
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
