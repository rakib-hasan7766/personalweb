<?php
include('db.php');

$message = "";

if (isset($_POST['add_link'])) {
    $site_name = pg_escape_string($conn, $_POST['site_name']);
    $site_url = pg_escape_string($conn, $_POST['site_url']);
    $category = pg_escape_string($conn, $_POST['category']);
    $description = pg_escape_string($conn, $_POST['description']);

    if (!empty($site_name) && !empty($site_url) && !empty($category)) {
        $query = "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$site_name', '$site_url', '$category', '$description')";
        if (pg_query($conn, $query)) {
            $message = "<div class='bg-emerald-100 text-emerald-800 p-3 rounded-xl text-sm font-semibold'>নতুন সাইট সফলভাবে যুক্ত হয়েছে!</div>";
        } else {
            $message = "<div class='bg-red-100 text-red-800 p-3 rounded-xl text-sm font-semibold'>যোগ করতে সমস্যা হয়েছে।</div>";
        }
    } else {
        $message = "<div class='bg-amber-100 text-amber-800 p-3 rounded-xl text-sm font-semibold'>দয়া করে সব ঘর পূরণ করুন।</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লিংক ম্যানেজমেন্ট ড্যাশবোর্ড</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mt-10">
        <h2 class="text-2xl font-black text-slate-800 mb-2">সেবা পোর্টাল ড্যাশবোর্ড</h2>
        <p class="text-sm text-slate-500 mb-6">এখান থেকে বাংলাদেশের যেকোনো দরকারি ওয়েবসাইট লিংক যুক্ত করুন।</p>
        
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
