<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easy Buy BD - Link Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen">

    <header class="bg-blue-600 text-white text-center py-8 shadow-md mb-8">
        <h1 class="text-4xl font-extrabold tracking-wide">Easy Buy BD</h1>
        <p class="text-blue-100 mt-2 text-sm md:text-base">আপনার প্রয়োজনীয় সব লিংক এবং ড্যাশবোর্ড একসাথে</p>
    </header>

    <main class="max-w-6xl mx-auto px-4 pb-16">

        <?php
        // আপনার সবগুলো লিংক এখানে অ্যারে আকারে দেওয়া হলো
        $site_links = [
            [
                "name" => "Easy Buy BD - Main Site",
                "url" => "https://easybuybd.top",
                "category" => "Main Business",
                "desc" => "আমাদের মূল ই-কমার্স প্ল্যাটফর্মের হোমপেজ।"
            ],
            [
                "name" => "Render Dashboard",
                "url" => "https://dashboard.render.com",
                "category" => "Hosting & Server",
                "desc" => "ওয়েব সার্ভিস, ডেপ্লয়মেন্ট লগ এবং অ্যাপ ম্যানেজমেন্ট ড্যাশবোর্ড।"
            ],
            [
                "name" => "Cloudflare Dashboard",
                "url" => "https://dash.cloudflare.com",
                "category" => "Domain & DNS",
                "desc" => "ডোমেন DNS রেকর্ড, SSL/TLS সিকিউরিটি এবং প্রক্সি ম্যানেজমেন্ট।"
            ],
            [
                "name" => "GitHub",
                "url" => "https://github.com",
                "category" => "Development",
                "desc" => "কোড রিপোজিটরি, ব্যাকআপ এবং ভার্সন কন্ট্রোল সিস্টেম।"
            ],
            [
                "name" => "Vercel Dashboard",
                "url" => "https://vercel.com/dashboard",
                "category" => "Hosting & Server",
                "desc" => "হাই-স্পিড ফ্রন্টএন্ড এবং পিএইচপি প্রজেক্ট ডেপ্লয়মেন্ট প্ল্যাটফর্ম।"
            ]
        ];
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($site_links as $link): ?>
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-md mb-3 border border-blue-100">
                            <?php echo htmlspecialchars($link['category']); ?>
                        </span>
                        
                        <h2 class="text-xl font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($link['name']); ?>
                        </h2>
                        
                        <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                            <?php echo htmlspecialchars($link['desc']); ?>
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" 
                           target="_blank" 
                           class="inline-flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm py-2.5 px-4 rounded-lg transition-colors shadow-sm">
                            ওপেন লিংক 
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </main>

    <footer class="bg-white border-t border-gray-200 text-center py-6 text-sm text-gray-500 mt-auto">
        &copy; <?php echo date('Y'); ?> Easy Buy BD. All rights reserved.
    </footer>

</body>
</html>
