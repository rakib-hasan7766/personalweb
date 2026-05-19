<?php
$host = "dpg-d85q0mnavr4c73d62fog-a.oregon-postgres.render.com"; 
$user = "perwebb"; 
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3"; 
$dbname = "perweb"; 
$port = "5432"; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";
$conn = pg_connect($connection_string);

if (!$conn) { die("Database Connection Failed!"); }

// টেবিল তৈরির কুয়েরি
$sql_table = "
CREATE TABLE IF NOT EXISTS site_links (
    id SERIAL PRIMARY KEY,
    site_name VARCHAR(255) NOT NULL,
    site_url TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
pg_query($conn, $sql_table);

// পিডিএফ এবং টেক্সটের শতভাগ লিংকের কমপ্লিট অ্যারে
$default_links = [
    // === সরকারি ও নাগরিক সেবা ===
    ['জাতীয় তথ্য বাতায়ন, 'https://bangladesh.gov.bd', 'সরকারি সেবা', 'বাংলাদেশের সব সরকারি মন্ত্রণালয় ও সেবার মেইন পোর্টাল।'],
    ['এনআইডি পোর্টাল (NID)', 'https://services.nidw.gov.bd/nid-pub/', 'সরকারি সেবা', 'জাতীয় পরিচয়পত্র ডাউনলোড, সংশোধন ও নতুন নিবন্ধনের সাইট।'],
    ['অনলাইন পাসপোর্ট আবেদন', 'https://www.epassport.gov.bd/landing', 'সরকারি সেবা', 'ই-পাসপোর্ট আবেদন এবং নতুন অ্যাকাউন্ট খোলার পোর্টাল।'],
    ['পাসপোর্ট স্ট্যাটাস চেক', 'https://www.epassport.gov.bd/authorization/application-status', 'Annyanno', 'স্লিপ নম্বর দিয়ে পাসপোর্ট রেডি হয়েছে কি না চেক করার লিংক।'],
    ['জন্মনিবন্ধন অনলাইন কপি যাচাই', 'https://everify.bdris.gov.bd/', 'সরকারি সেবা', 'জন্মনিবন্ধন অনলাইন কপি চেক এবং ডাউনলোড করার লিংক।'],
    ['জন্মনিবন্ধন নতুন আবেদন', 'https://bdris.gov.bd/br/application', 'সরকারি সেবা', 'নতুন জন্মনিবন্ধনের জন্য অনলাইন আবেদন ফরম।'],
    ['কোভিড-১৯ করোনা ভ্যাক্সিন পোর্টাল', 'https://surokkha.gov.bd/', 'সরকারি সেবা', 'সুরক্ষা পোর্টাল - করোনা ভ্যাক্সিন নিবন্ধন ও সার্টিফিকেট ডাউনলোড।'],
    ['বিআরটিএ সেবা পোর্টাল', 'https://bsp.brta.gov.bd/', 'সরকারি সেবা', 'অনলাইনে ড্রাইভিং লাইসেন্স করার সঠিক নিয়ম ও ট্র্যাকিং পোর্টাল।'],
    ['ভূমি উন্নয়ন কর পোর্টাল', 'https://ldtax.gov.bd/citizen/register', 'সরকারি সেবা', 'নাগরিক ভূমি উন্নয়ন কর বা খাজনা প্রদানের অনলাইন রেজিস্ট্রেশন লিংক।'],
    ['অনলাইন জিডি (BD Police)', 'https://gd.police.gov.bd', 'Annyanno', 'থানায় না গিয়ে অনলাইনে ঘরে বসে সাধারণ ডায়েরি বা GD করার অ্যাপ।'],
    ['ই-টিন (TIN) আবেদন পোর্টাল', 'https://secure.incometax.gov.bd/TINHome', 'Annyanno', 'নতুন ই-টিন সার্টিফিকেটের জন্য অনলাইন আবেদন এর লিংক।'],
    ['বাংলাদেশ ফরম পোর্টাল', 'https://forms.gov.bd', 'সরকারি সেবা', 'সব ধরনের সরকারি আবেদনের ফরম এক জায়গায় ডাউনলোড করুন।'],
    ['ই-নামজারি আবেদন', 'https://mutation.land.gov.bd', 'Annyanno', 'জমির ই-নামজারি বা মিউটেশনের অনলাইন আবেদন পোর্টাল।'],
    ['গর্ভবতী ভাতার আবেদন', 'https://www.youtube.com/watch?v=avjheqYhmQw', 'Annyanno', 'গর্ভবতী ভাতার অনলাইন আবেদন করার নিয়ম ও গাইড ভিডিও।'],
    ['ভিডব্লিউবি কর্মসূচি (VWB)', 'http://dwavwb.gov.bd/icvgd/applicant/vgd/application-find', 'Annyanno', 'মহিলা ও শিশু বিষয়ক মন্ত্রণালয়ের ভিডব্লিউবি কর্মসূচি ট্র্যাকিং।'],
    ['৩০ কেজি চাউলের আবেদন ফরম', 'http://dwavwb.gov.bd/icvgd/applicant/vgd/form', 'সরকারি সেবা', 'ভিজিডি/ভিডব্লিউবি কার্ডের ৩০ কেজি চালের অনলাইন আবেদন লিংক।'],
    ['প্রাথমিক শিক্ষক বেতন রশিদ', 'https://ibas.finance.gov.bd/ibas2/Fixation', 'Annyanno', 'iBAS++ এর মাধ্যমে প্রাইমারি স্কুলের শিক্ষকদের বেতন ফিক্সেশন রশিদ বের করার লিংক।'],

    // === প্রবাসী সেবা ও ভিসা চেক ===
    ['আমি প্রবাসী ওয়েবসাইট', 'https://www.amiprobashi.com/', 'প্রবাসী সেবা', 'বিদেশ গমনেচ্ছু কর্মীদের জন্য "আমি প্রবাসী" অফিশিয়াল প্ল্যাটফর্ম।'],
    ['PDO ট্রেনিং সার্টিফিকেট ডাউনলোড', 'https://www.amiprobashi.com/download-certificate.html', 'প্রবাসী সেবা', 'আমি প্রবাসী পোর্টাল থেকে PDO ট্রেনিং সার্টিফিকেট ডাউনলোড লিংক।'],
    ['সব দেশের ভিসা চেক লিংক', 'https://probangla.com/visa-check/', 'প্রবাসী সেবা', 'প্রোবাংলা - বিশ্বের সব দেশের অনলাইন ভিসা চেক করার গাইড লিংক।'],
    ['সকল ই-সার্ভিস পোর্টাল', 'https://probangla.com/category/e-service/', 'প্রবাসী সেবা', 'সব ধরনের অনলাইন নাগরিক ও ই-সার্ভিসের কালেকশন।'],
    ['BMET বায়ো ফিঙ্গার চেক', 'http://www.old.bmet.gov.bd/BMET/biocheck', 'প্রবাসী সেবা', 'BMET বায়োমেট্রিক ফিঙ্গারপ্রিন্ট স্ট্যাটাস চেক করার লিংক।'],
    ['BMET জেনারেল রিপোর্ট', 'http://www.old.bmet.gov.bd/BMET/generalreports', 'Annyanno', 'BMET ক্লিয়ারেন্স এবং ম্যানপাওয়ার কার্ড স্ট্যাটাস চেক।'],
    ['সৌদি আরব ভিসা চেক (পাসপোর্ট নং)', 'https://visa.mofa.gov.sa/VisaPerson/GetApplicantData', 'প্রবাসী সেবা', 'সৌদি আরবের ই-ভিসা চেক করার লিংক (পাসপোর্ট নং দিয়ে)।'],
    ['সৌদি আরব ভিসা চেক (ভিসা নং)', 'https://visa.mofa.gov.sa/Enjaz/GetVisaInformation/Person', 'Annyanno', 'সৌদি আরবের ভিসা ইনফরমেশন চেক (ভিসা নম্বর দিয়ে)।'],
    ['দুবাই ভিসা চেক পোর্টাল', 'https://smartservices.icp.gov.ae/echannels/web/client/default.html#/fileValidity', 'প্রবাসী সেবা', 'ICP সংযুক্ত আরব আমিরাত (UAE/দুবাই) ভিসা ভ্যালিডিটি চেক করার লিংক।'],
    ['কাতার ভিসা চেক ও প্রিন্ট', 'https://portal.moi.gov.qa/wps/portal/MOIInternet/services/inquiries/visaservices/enquiryandprinting', 'প্রবাসী সেবা', 'কাতার মিনিস্ট্রি অফ ইন্টেরিয়র (MOI) ভিসা এনকোয়ারি ও প্রিন্ট।'],
    ['কাতার অ্যাপয়েন্টমেন্ট লেটার', 'https://www.qatarvisacenter.com/manage', 'প্রবাসী সেবা', 'QVC কাতার ভিসা সেন্টার অ্যাপয়েন্টমেন্ট লেটার ডাউনলোড লিংক।'],
    ['মালয়েশিয়া ভিসা চেক (টোকেন নং)', 'https://eservices.imi.gov.my/myimms/PRAStatus', 'Annyanno', 'মালয়েশিয়া ইমিগ্রেশন ভিসা স্ট্যাটাস চেক (টোকেন নম্বর দিয়ে)।'],
    ['মালয়েশিয়া ভিসা চেক (পাসপোর্ট নং)', 'https://eservices.imi.gov.my/myimms/FomemaStatus', 'Annyanno', 'মালয়েশিয়া FOMEMA মেডিকেল ও ভিসা স্ট্যাটাস চেক (পাসপোর্ট দিয়ে)।'],
    ['এয়ার এরাবিয়া টিকিট চেক', 'https://webcheckin.airarabia.com/accelaero/en/index.html', 'Annyanno', 'এয়ার এরাবিয়া এয়ারলাইন্সের অনলাইন ওয়েব চেক-ইন ও টিকিট চেক।'],

    // === শিক্ষা ও রেজাল্ট ===
    ['এডুকেশন বোর্ড রেজাল্ট', 'http://www.educationboardresults.gov.bd', 'শিক্ষা ও রেজাল্ট', 'SSC, HSC, JSC পরীক্ষার অফিশিয়াল রেজাল্ট দেখার প্রধান পোর্টাল।'],
    ['ওয়েব বেজ রেজাল্ট সিস্টেম', 'https://eboardresults.com/v2/home', 'শিক্ষা ও রেজাল্ট', 'সকল পাবলিক পরীক্ষার জেলা, বোর্ড ও প্রতিষ্ঠান ভিত্তিক বিস্তারিত রেজাল্ট।'],
    ['ডিগ্রি/অনার্স রেজাল্ট পোর্টাল', 'http://103.113.200.7/', 'শিক্ষা ও রেজাল্ট', 'জাতীয় বিশ্ববিদ্যালয়ের ডিগ্রি ও অনার্স পরীক্ষার রেজাল্ট চেক করার লিংক।'],
    ['একাদশে ভর্তি পোর্টাল', 'http://xiclassadmission.gov.bd', 'শিক্ষা ও রেজাল্ট', 'কলেজে একাদশ শ্রেণিতে অনলাইনের মাধ্যমে ভর্তির আবেদন সাইট।'],
    ['মাধ্যমিক ও উচ্চশিক্ষা অধিদপ্তর', 'https://dshe.gov.bd', 'শিক্ষা ও রেজাল্ট', 'শিক্ষা সংক্রান্ত নোটিশ ও নির্দেশনাবলী দেখার সরকারি সাইট।'],

    // === টিকিট ও পেমেন্ট ===
    ['বাংলাদেশ রেলওয়ে ই-টিকিট', 'https://eticket.railway.gov.bd/', 'পেমেন্ট ও ব্যাংকিং', 'অনলাইনে ট্রেনের সিট সিলেক্ট করে টিকিট কাটার অফিশিয়াল সাইট।'],
    ['বিকাশ (bKash)', 'https://www.bkash.com', 'পেমেন্ট ও ব্যাংকিং', 'বাংলাদেশের সবচেয়ে বড় মোবাইল ফিনান্সিয়াল সার্ভিস।'],
    ['নগদ (Nagad)', 'https://nagad.com.bd', 'পেমেন্ট ও ব্যাংকিং', 'ডাক বিভাগের ডিজিটাল লেনদেন ও মোবাইল ব্যাংকিং সেবা।'],
    ['রকেট (Rocket - DBBL)', 'https://www.dutchbanglabank.com/rocket/rocket.html', 'পেমেন্ট ও ব্যাংকিং', 'ডাচ-বাংলা ব্যাংকের মোবাইল ব্যাংকিং ও ইউটিলিটি বিল পেমেন্ট।'],
    ['সেলফিন (CellFin)', 'https://www.islamibankbd.com', 'পেমেন্ট ও ব্যাংকিং', 'ইসলামী ব্যাংকের জনপ্রিয় ডিজিটাল ওয়ালেট ও ব্যাংকিং অ্যাপ।'],

    // === ইউটিলিটি, টুলস ও টাইপিং ===
    ['ঝাপসা ছবি সোজা/ক্লিয়ার করার লিংক', 'https://www.cutout.pro/photo-enhancer-sharpener-upscaler/upload', 'ইউটিলিটি ও টুলস', 'Cutout Pro - ঝাপসা ছবিকে ক্লিয়ার এবং হাই রেজোলিউশন করার এআই টুল।'],
    ['ছবির ব্যাকগ্রাউন্ড পরিবর্তন', 'https://www.cutout.pro/remove-background/upload', 'ইউটিলিটি ও টুলস', 'অনলাইনে যেকোনো ছবির ব্যাকগ্রাউন্ড এক ক্লিকে রিমুভ ও পরিবর্তন করার লিংক।'],
    ['Canva Official Website', 'https://www.canva.com/', 'ইউটিলিটি ও টুলস', 'ক্যানভা প্রো এবং বিভিন্ন ডিজাইনিং এর অফিশিয়াল প্ল্যাটফর্ম লিংক।'],
    ['অনলাইন বয়স ক্যালকুলেটর', 'https://www.pathgriho.com/2021/06/online-age-calculator-Bangla.html', 'ইউটিলিটি ও টুলস', 'জন্ম তারিখ দিয়ে বর্তমান নিখুঁত বয়স বের করার অনলাইন ক্যালকুলেটর।'],
    ['গুগল ট্রান্সলেটর (Bangla-English)', 'https://translate.google.com/?sl=bn&tl=en&op=translate', 'ইউটিলিটি ও টুলস', 'বাংলা থেকে ইংরেজি বা যেকোনো ভাষা অনুবাদ করার অফিশিয়াল টুল।'],
    ['Typing.com', 'https://www.typing.com', 'ইউটিলিটি ও টুলস', 'অনলাইনে ফ্রি-তে কিবোর্ড টাইপিং স্পিড বাড়ানোর সেরা ওয়েবসাইট।'],
    ['The Typing Cat', 'https://thetypingcat.com', 'ইউটিলিটি ও টুলস', 'অ্যাডভান্সড টাইপিং প্র্যাকটিস এবং স্পিড বাড়ানোর প্রফেশনাল সাইট।'],
    ['Learn Typing', 'https://www.learntyping.org', 'ইউটিলিটি ও টুলস', 'নতুনদের জন্য একদম শুরু থেকে টাইপিং শেখার অনলাইন গাইড।'],
    ['অনলাইন ভয়েস টাইপিং ডক', 'https://docs.google.com/document/d/1q6mcNDT9jweMBJEv2f0KDpjRlbocQShaTU6_Yn7DRcl/edit', 'ইউটিলিটি ও টুলস', 'গুগল ডক ভয়েস টাইপিং এবং প্র্যাকটিস লিংক।'],

    // === ソフトওয়্যার ডাউনলোড উৎস ===
    ['GetIntoPC Free Utilities', 'https://getintopc.com/softwares/utilities/everything-free-download/', 'সফটওয়্যার ডাউনলোড', 'কম্পিউটারের সব ধরনের প্রয়োজনীয় ক্র্যাক সফটওয়্যার ডাউনলোডের মেইন ওয়েবসাইট।'],
    ['Ninite App Installer', 'https://ninite.com/', 'Annyanno', 'এক ক্লিকে কম্পিউটারের সব দরকারি ব্রাউজার ও রানটাইম অ্যাপস একসাথে ডাউনলোড করার সাইট।'],

    // === ক্রিয়েটিভ ও এআই (AI) টুলস ===
    ['ChatGPT (OpenAI)', 'https://chatgpt.com', 'ক্রিয়েটিভ ও এআই টুলস', 'কনটেন্ট রাইটিং, কোডিং এবং যেকোনো প্রশ্নের উত্তরের জন্য জেনারেটিভ এআই পোর্টাল।'],
    ['Gemini (Google AI)', 'https://gemini.google.com', 'ক্রিয়েটিভ ও এআই টুলস', 'গুগলের আধুনিক লার্জ ল্যাঙ্গুয়েজ মডেল যা রিয়েল-টাইম সার্চ ডাটা সাপোর্ট করে।'],
    ['Google AI Studio', 'https://aistudio.google.com', 'ক্রিয়েটিভ ও এআই টুলস', 'গুগলের এপিআই ডেভেলপমেন্ট এবং মডেল টেস্ট করার অফিশিয়াল স্টুডিও পোর্টাল।'],
    ['Google Flow', 'https://flow.google.com', 'ক্রিয়েটিভ ও এআই টুলস', 'গুগলের অটোমেশন এবং ওয়ার্কফ্লো ম্যানেজমেন্ট এআই প্ল্যাটফর্ম।'],
    ['Microsoft Copilot', 'https://copilot.microsoft.com', 'ক্রিয়েটিভ ও এআই টুলস', 'মাইক্রোসফটের সম্পূর্ণ ফ্রি ও পাওয়ারফুল এআই অ্যাসিস্ট্যান্ট।'],
    ['LMSYS Chatbot Arena', 'https://lmarena.ai', 'ক্রিয়েটিভ ও এআই টুলস', 'সব পেইড এবং টপ এআই মডেল সম্পূর্ণ ফ্রিতে তুলনা ও ব্যবহার করার প্ল্যাটফর্ম।'],
    ['Midjourney', 'https://midjourney.com', 'ক্রিয়েটিভ ও এআই টুলস', 'টেক্সট থেকে আর্টিস্টিক ও হাই-কোয়ালিটি আর্টিস্টিক ইমেজ তৈরির শীর্ষ টুল।'],
    ['Stable Diffusion', 'https://stable-diffusion.io', 'Annyanno', 'ওপেন-সোর্স টেক্সট-টু-ইমেজ মডেল, কাস্টমাইজড ট্রেইনিং ও লোকাল রান সুবিধা।'],
    ['DALL-E 3 (OpenAI)', 'https://openai.com/dall-e-3', 'Annyanno', 'টেক্সট থেকে ফটো-রিয়েলিস্টিক ইমেজ তৈরি করে, ChatGPT এর সাথেও ইন্টিগ্রেটেড।'],
    ['Adobe Firefly', 'https://adobe.com/firefly', 'Annyanno', 'ডিজাইন ও ব্র্যান্ডিং ফোকাসড জেনারেটিভ এআই, ফটোশপ ইন্টিগ্রেটেড।'],
    ['Canva AI Image Generator', 'https://canva.com/ai-image-generator', 'ক্রিয়েটিভ ও এআই টুলস', 'সোশ্যাল মিডিয়া ও মার্কেটিং ডিজাইনের জন্য দ্রুত ইমেজ জেনারেটর।'],
    ['ElevenLabs', 'https://elevenlabs.io', 'Annyanno', 'ন্যাচারাল-সাউন্ডিং ভয়েস ক্লোনিং ও প্রফেশনাল টেক্সট-টু-স্পিচ এআই।'],
    ['Murf AI', 'https://murf.ai', 'Annyanno', 'বিজ্ঞাপন, ই-লার্নিং ও প্রফেশনাল ভয়েসওভার তৈরির জন্য উপযোগী টুল।'],
    ['Play.ht', 'https://play.ht', 'Annyanno', 'আর্টিকেলকে প্রফেশনাল অডিওতে রূপান্তর করার মাল্টি-ল্যাঙ্গুয়েজ ওয়ালেট।'],
    ['AIVA Music Composer', 'https://aiva.ai', 'ক্রিয়েটিভ ও এআই টুলস', 'AI মিউজিক কম্পোজার - ২৫০+ স্টাইলে নতুন কপিরাইট-ফ্রি মিউজিক ট্র্যাকার।'],
    ['Soundraw AI', 'https://soundraw.io', 'ক্রিয়েটিভ ও এআই টুলস', 'ব্যাকগ্রাউন্ড মিউজিক, অ্যাড ও ভিডিওর জন্য কাস্টম এআই অডিও জেনারেটর।'],
    ['Runway Gen-2', 'https://runwayml.com/gen-2', 'Annyanno', 'টেক্সট ও ইমেজ থেকে প্রফেশনাল সিনেমাটিক ভিডিও জেনারেশনের এআই।'],
    ['Pika Labs AI', 'https://pikalabsai.net', 'Annyanno', 'টেক্সট থেকে ছোট ভিডিও ক্লিপ ও চমৎকার অ্যানিমেশন তৈরির টুল।'],
    ['Google Veo 3', 'https://aistudio.google.com/veo-3', 'Annyanno', 'গুগলের হাই-কোয়ালিটি টেক্সট-টু-ভিডিও জেনারেশন এবং নেটিভ অডিও সাপোর্ট।'],
    ['Luma AI Dream Machine', 'https://lumalabs.ai/dream-machine', 'Annyanno', 'রিয়েলিস্টিক ভিডিও এবং ৩ডি (3D) মোশন কনটেন্ট তৈরির পাওয়ারফুল টুল।'],
    ['Synthesia', 'https://synthesia.io', 'Annyanno', 'AI অ্যাভাটার ব্যবহার করে স্বয়ংক্রিয় প্রফেশনাল প্রেজেন্টেশন ভিডিও মেকার।'],
    ['Kling AI', 'https://klingai.com', 'Annyanno', 'সিনেমাটিক হাই-ফিডেলিটি ভিজ্যুয়াল ভিডিও জেনারেশনের আধুনিক প্ল্যাটফর্ম।'],

    // === রাকিব ভাইয়ের এক্সক্লুসিভ ড্রাইভ রিসোর্স (অ্যাসেটস ও ড্রাইভ লিংক) ===
    [' Footbal Live Match, 'https://durbinlive.live/', 'রাকিব ড্রাইভ রিসোর্স', 'এখানে ফুটবল এবং ক্রিকেট এর সকল ম্যাচ লাইভ দেখতে পারবেন সম্পন্ন ফ্রিতে।],
	[' Footbal Live Match 2, 'https://yosin-tv.org/', 'রাকিব ড্রাইভ রিসোর্স', 'এখানে ফুটবল এবং ক্রিকেট এর সকল ম্যাচ লাইভ দেখতে পারবেন সম্পন্ন ফ্রিতে।],
	[' সকল সেবা এক সঙ্গে, 'https://www.idcardscannerpro.com/', 'রাকিব ড্রাইভ রিসোর্স', 'এখানে কম্পিউটার দোকানের প্রয়োজনীয় সকল টুলস ফ্রিতে পাওয়া যায়।],
    ['Question Word File 2024', 'https://drive.google.com/file/d/1yXb5eO0nkX9XGGhcoVuo98BRw5_bq9A1/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', '২০২৪ সালের এডিটেবল কোয়েশ্চেন এমএস ওয়ার্ড ফাইল ব্যাকআপ।'],
    ['Adobe Premiere Pro CC 2019', 'https://drive.google.com/file/d/1xvPzGa7J6ksRE_zX7ONLaOn31RoSpvE1/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'ভিডিও এডিটিং সফটওয়্যার অ্যাডোবি প্রিমিয়ার প্রো ২০১৯ ইনস্টলার ফাইল।'],
    ['Adobe Premiere Pro 2022 v22.6.2', 'https://drive.google.com/file/d/1HPAUMqhgH2ktNllOjK5BPSSOpPHpOfTa/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'অ্যাডোবি প্রিমিয়ার প্রো ২০২২ ক্র্যাক ও ফুল ইনস্টলেশন ফাইল।'],
    ['Toshiba e-Studio 2523AD Printer Driver', 'https://drive.google.com/file/d/1618s-YK_pXrnyxUVjKzPceHIOKcLIeuD/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'তোশিবা ২৫২৩এডি প্রিন্টার এবং ফটোকপিয়ার অফিশিয়াল উইন্ডোজ ড্রাইভার।'],
    ['Toshiba 2523A Basic Driver', 'https://drive.google.com/file/d/16jIJjRJU_VVFTyBjn0AJd-iP-CnoHKv-/', 'রাকিব ড্রাইভ রিসোর্স', 'তোশিবা ২৫২৩এ মডেলের স্ট্যান্ডার্ড প্রিন্ট ও স্ক্যানার ড্রাইভার।'],
    ['Word 2007 PDF Converter Plugin', 'https://drive.google.com/file/d/1VbYtaVk0iCuwMd5Bkor5AG5JhZOmZqyW/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'এমএস ওয়ার্ড ২০০৭ ফাইলকে সরাসরি পিডিএফ এক্সপোর্ট করার প্লাগইন অ্যাপ।'],
    ['Halkata Design AI Vector File 1', 'https://drive.google.com/file/d/1NMQ3WzrLAzz1rZqQJFkjn9MAbvYpx-71/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'হালখাতা এডিটেবল ভেক্টর ডিজাইন ইলাস্ট্রেটর পার্ট ১।'],
    ['হালখাতা এডিটেবল ডিজাইন ফাইল (.AI) ২', 'https://drive.google.com/file/d/1lAbcMK2fP4CqQRFlw0UBYf5UYUH8SVWT/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'ইলাস্ট্রেটর এডিটেবল ডিজিটাল হালখাতা ইনভাইটেশন কার্ড ও ব্যানার ডিজাইন ২।'],
    ['বাংলা বায়োডাটা প্রফেশনাল ফরম্যাট', 'https://drive.google.com/file/d/1yy5_w464nVQeugP6jJ_-MQX4LCy6JcYQ/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'বিয়ের বা প্রফেশনাল কাজের জন্য সুন্দর বাংলা বায়োডাটা এমএস ওয়ার্ড ফাইল।'],
    ['IDM Lifetime License Key Free', 'https://drive.google.com/file/d/1KlXvQTpBrTMJ_2-rOLFUsDmg6XMjGiJF/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'ইন্টারনেট ডাউনলোড ম্যানেজার (IDM) আজীবন মেয়াদের লাইসেন্স ক্র্যাক ফাইল।'],
    ['IDM How to Install Video Guide', 'https://drive.google.com/file/d/1ovzFeYTXpUz89By0B9JcUzZVaksrLDS_/', 'রাকিব ড্রাইভ রিসোর্স', 'আইডিএম কিভাবে লাইফটাইম অ্যাক্টিভেট করবেন তার ভিডিও টিউটোরিয়াল গাইড।'],
    ['Format Factory v2.60 Original', 'https://drive.google.com/file/d/11pxdiDiFnBAUAOEUHKbGDYb4X91ZILCQ/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'অডিও, ভিডিও এবং ইমেজ ফরম্যাট কনভার্ট করার জন্য ফরমেট ফ্যাক্টরি।'],
    ['Photoshop Automation Action Files', 'https://drive.google.com/file/d/1pzOWikHCpWaLC-OS8XorH5PMgD6H8mYW/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', '১ ক্লিকে পাসপোর্ট সাইজ ছবি ও স্টুডিও কাজের রেডিমেড অ্যাকশন ফাইল।'],
    ['Photoshop Action Video Guide', 'https://drive.google.com/file/d/1irniFzlljRZp3KepifM45m5SdAwklycQ/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'ফটোশপ অ্যাকশন ফাইল ইমপোর্ট ও প্রফেশনাল কাজ করার ভিডিও গাইড।'],
    ['KMPlayer Original Video Player', 'https://drive.google.com/file/d/1TfIvhvQFShHyzjmetz_WudSdHrX7fsSn/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'উচ্চমানের মিডিয়া প্লেয়ার কেএমপ্লেয়ার ডেস্কটপ ভার্সন।'],
    ['UltraViewer v6.6 Premium Setup', 'https://drive.google.com/file/d/1o3cTq9vI11tGrs1LfKLMaWh8FMoV4LGO/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'রিমোট কম্পিউটার কন্ট্রোল ও কাস্টমার সাপোর্টের আল্ট্রাভিউয়ার সফটওয়্যার।'],
    ['Photoshop Class Premium Video', 'https://drive.google.com/file/d/1p0okQwYkyD1YE03BDH9ThF18WPE-ZlNv/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'ফটোশপ টুলস ও স্টুডিও এডিটিং কাজের ফুল প্র্যাকটিক্যাল মাস্টারক্লাস ভিডিও।'],
    ['Graphic Design AI Master Class Video', 'https://drive.google.com/file/d/1cL0TmnB3jU7ij_Ci2g0LfjQ4IGoy3i5S/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'অ্যাডোবি ইলাস্ট্রেটর ভেক্টর ও ক্রিয়েটিভ প্রিন্ট ডিজাইনের প্রিমিয়াম গাইড ভিডিও।'],
    ['Microsoft Excel Premium Class Video', 'https://drive.google.com/file/d/1tv4ZhjhCUHEvAJmliGwUk-Lg0qpDw_58/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'অফিস কাজের জন্য এক্সেল ফর্মুলা, ডাটা সর্টিং ও প্রফেশনাল ক্লাসের লিংক।'],
    ['Illustrator AI All Editable File No 1', 'https://drive.google.com/file/d/1mIXtrbPOOvpmW354huH93e4MMyyIDL3C/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'গ্রাফিক্স ডিজাইনারদের জন্য প্রয়োজনীয় সকল র-ফাইল ও ইলাস্ট্রেটর সোর্স ব্যাকআপ।'],
    ['30টি জরুরি দরখাস্ত (Word File)', 'https://drive.google.com/file/d/1qUoT3A6CBiVVr6Ldj8FZX5a5pKIqWhNW/view?usp=sharing', 'রাকিব ড্রাইভ রিসোর্স', 'চাকরি, ছুটি ও বিভিন্ন আবেদনের ৩০টি এডিটেবল বাংলা দরখাস্ত ফরম্যাট ফাইল।'],

    // === প্রিমিয়াম আইটি কোর্স কালেকশন ===
    ['গ্রাফিক্স ডিজাইন ফুল মেগা কোর্স', 'https://drive.google.com/open?id=1w8vHj7oWHpkh2UUAW59kpEG7E2wGD5aD', 'প্রিমিয়াম ভিডিও কোর্স', 'সম্পূর্ণ প্রফেশনাল গ্রাফিক্স ডিজাইন এবং ইলাস্ট্রেটর/ফটোশপ মাস্টারক্লাস।'],
    ['অ্যাফিলিয়েট মার্কেটিং (৫৩টি ক্লাস)', 'https://drive.google.com/drive/folders/12DfMygTE7T4aQBOKcKupqCl6O-s9fon5?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'প্যাসিভ ইনকামের জন্য বিস্তারিত গাইডলাইনসহ ৫৩টি ডেডিকেটেড ভিডিও ক্লাস।'],
    ['এসইও (SEO) ফুল মাস্টারকোর্স', 'https://drive.google.com/drive/u/2/folders/1BiUimpLkuwMjHwHBS_kymTKROhlpTVdC', 'প্রিমিয়াম ভিডিও কোর্স', 'সার্চ ইঞ্জিন অপ্টিমাইজেশন (On-Page, Off-Page) অ্যাডভান্সড টিউটোরিয়াল।'],
    ['ফাইভার সাকসেস সিক্রেট (৪০টি ভিডিও)', 'https://drive.google.com/drive/mobile/folders/1ZKM0pEdKskoeFOe_hTx746QvNT4B3JL7', 'প্রিমিয়াম ভিডিও কোর্স', 'Fiverr মার্কেটপ্লেসে গিগ র‍্যাঙ্কিং ও অর্ডার পাওয়ার ৪০টি প্রফেশনাল গাইড ভিডিও।'],
    ['সিপিএ মার্কেটিং ফুল কোর্স পার্ট ১', 'https://drive.google.com/open?id=1i_owh0jH8T1dwZmDSHP4sKaoc0z26jO9', 'প্রিমিয়াম ভিডিও কোর্স', 'CPA Marketing প্রফেশনাল ক্যাম্পেইন বিল্ড ও আর্নিং মেথড কোর্স ১।'],
    ['সিপিএ মার্কেটিং ফুল কোর্স পার্ট ২', 'https://drive.google.com/open?id=1AAIwLS85aecMhgLIIO1M8f8uhKPFlfU6', 'প্রিমিয়াম ভিডিও কোর্স', 'CPA Marketing লিড জেনারেশন ও ট্রাফিক সোর্স কমপ্লিট কোর্স ২।'],
    ['সিপিএ মার্কেটিং ফুল কোর্স পার্ট ৩', 'https://drive.google.com/drive/folders/1KG4-Adpmg8zYMW5qup_e9GteV3LiBg1k', 'প্রিমিয়াম ভিডিও কোর্স', 'CPA নেটওয়ার্ক অ্যাপ্রুভাল ও পেইড ট্রাফিক সিক্রেট মেথড পার্ট ৩।'],
    ['CPA Marketing Mega Folder (Mega)', 'https://mega.nz/folder/JJxjyaLC#vdizop3q5h2GbPrt7R4Tsg', 'প্রিমিয়াম ভিডিও কোর্স', 'মেগা সার্ভারে ব্যাকআপ রাখা প্রফেশনাল সিপিএ মার্কেটিং স্পেশাল ফোল্ডার।'],
    ['ফেসবুক মার্কেটিং কমপ্লিট কোর্স', 'https://drive.google.com/drive/folders/1m-V8pHb3HUACY1Aca2PmFfBembCvjDiK', 'প্রিমিয়াম ভিডিও কোর্স', 'মেটা অ্যাডস ম্যানেজার, টার্геটিং ও বুস্টিং ক্যাম্পেইনের ফুল সিক্রেট কোর্স।'],
    ['অ্যাডভান্সড ডাটা এন্ট্রি মাস্টারক্লাস', 'https://drive.google.com/drive/mobile/folders/13syccv3Pj_egBuzW6cuEaPxdxmlxfZDN', 'প্রিমিয়াম ভিডিও কোর্স', 'মাইক্রোসফট এক্সেল, ওয়ার্ড ও লিড জেনারেশন ডাটা এন্ট্রি লাইভ প্রজেক্ট কোর্স।'],
    ['মাইক্রোসফট পাওয়ারপয়েন্ট ফুল কোর্স', 'https://drive.google.com/folderview?id=1Zr3WCs2WgNfXACwWHQPGBdETt8kid13J', 'প্রিমিয়াম ভিডিও কোর্স', 'প্রফেশনাল অ্যানিমেশন ও কর্পোরেট প্রেজেন্টেশন স্লাইড বানানোর সম্পূর্ণ কোর্স।'],
    ['মাইক্রোসফট এক্সেল মাস্টার কোর্স', 'https://drive.google.com/drive/folders/1-g11Q2elvu8TsQni2psg0O01dE0Ae8Jx', 'প্রিমিয়াম ভিডিও কোর্স', 'বেসিক থেকে অ্যাডভান্সড লেভেলের স্প্রেডশিট ফর্মুলা ও ফিনান্সিয়াল অডিট কোর্স।'],
    ['লোগো ডিজাইন মাস্টারক্লাস (বাকী বিল্লাহ)', 'https://mega.nz/folder/BFBWhRSZ#kmO9D-n9ew18VWhfAw8HRw', 'প্রিমিয়াম ভিডিও কোর্স', 'ক্রিয়েটিভ ব্র্যান্ড লোগো ডিজাইন মডিউল বাই প্রফেশনাল মেন্টর বিল্লাহ ভাই।'],
    ['Mega Logo Vector Pack Collection', 'https://drive.google.com/drive/u/0/folders/1oC5NN_xJO0mW4SpP1Mzv1RZIANiBYkc5', 'প্রিমিয়াম ভিডিও কোর্স', 'হাজার হাজার এডিটেবল লোগো টেমপ্লেট ও মোশন ডিজাইন ব্যাকআপ লিংক।'],
    ['মোশন গ্রাফিক্স কোর্স (Creative IT)', 'https://drive.google.com/drive/mobile/folders/1hz81RVsa4U6o07sX4NgBjRvZMqeGgVxN?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'ক্রিয়েটিভ আইটি ইনস্টিটিউটের প্রিমিয়াম মোশন গ্রাফিক্স ও আফটার ইফেক্টস কোর্স।'],
    ['ভিডিও এডিটিং ফুল গাইড (৩২টি ক্লাস)', 'https://drive.google.com/drive/u/0/folders/1IUyCpp6NL4yxNXnfm-BjsmYAoThm3N7Z', 'প্রিমিয়াম ভিডিও কোর্স', 'প্রিমিয়ার প্রো ও ফিল্মোরা দিয়ে সিনেমাটিক এডিটিংয়ের ৩২টি মাস্টার ভিডিও।'],
    ['ইউটিউব কন্টেন্ট ক্রিয়েশন ও আর্নিং', 'https://drive.google.com/open?id=1OmDFvSnAgI9-DCF-T2OvVe0tdTzAKYUZ', 'প্রিমিয়াম ভিডিও কোর্স', 'ইউটিউব চ্যানেল গ্রোথ, এসইও এবং মনিটাইজেশন অর্জনের ৩০টি প্রফেশনাল ক্লাস।'],
    ['সাইবার সিকিউরিটি ও ইথিক্যাল হাকিং', 'https://drive.google.com/drive/folders/1QsnlqaK0nKKA_FGu-LjSlrqQHQftnByZ?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'নেটওয়ার্ক পেনিট্রেশন টেস্টিং এবং সাইবার ডিফেন্সের কমপ্লিট ইথিক্যাল হাকিং।'],
    ['ফেসবুক হ্যাকিং প্রটেকশন (২১টি ভিডিও)', 'https://drive.google.com/drive/folders/1NHtQ-qwOCbQdTWVK0Q02RDFlHsiwElQs', 'প্রিমিয়াম ভিডিও কোর্স', 'সোশ্যাল মিডিয়া সিকিউরিটি, রিকভারি ও অ্যাকাউন্ট সিকিউর করার ২১টি ভিডিও।'],
    ['ওয়াইফাই হ্যাকিং ও সিকিউরিটি কোর্স', 'https://drive.google.com/drive/folders/1tgkKt4lSpXD3GnMQRgUb4bbtlmpP9XOE', 'প্রিমিয়াম ভিডিও কোর্স', 'নেটওয়ার্ক সিকিউরিটি এবং ওয়াইফাই পাসওয়ার্ড টেস্ট পেনিট্রেশন কমপ্লিট কোর্স।'],
    ['অ্যান্ড্রয়েড হ্যাকিং ও ওএস সিকিউরিটি', 'https://drive.google.com/drive/folders/1_G6kt5leGkmzMs_hveS0oUya591gVso2', 'প্রিমিয়াম ভিডিও কোর্স', 'মোবাইল সিকিউরিটি, ম্যালওয়্যার অ্যানালাইসিস ও প্রটেকশনের ২৭টি প্রিমিয়াম ভিডিও।'],
    ['Blackhat Money Making Methods', 'https://mega.nz/folder/FlJkgBxT#DCpPhtQ3phMjuK4iHnu3jw', 'প্রিমিয়াম ভিডিও কোর্স', 'ব্ল্যাকহ্যাট মার্কেটিং ও অ্যাডভান্সড অনলাইন ট্রাফিক আর্নিং সিক্রেট ফাইল।'],
    ['অ্যান্ড্রয়েড অ্যাপ ডেভেলপমেন্ট (৫৮টি ভিডিও)', 'https://drive.google.com/drive/folders/15DZTb3kraSVCRRQnk3duOz1sPPcrK0Ti', 'প্রিমিয়াম ভিডিও কোর্স', 'জাভা/কোটলিন ব্যবহার করে অ্যান্ড্রোয়েড অ্যাপ মেকিংয়ের ৫৮টি ভিডিও ক্লাস।'],
    ['এইচটিএমএল, সিএসএস ও বুটস্ট্র্যাপ', 'https://drive.google.com/drive/folders/1EcHwyVbD1F_JQ-0y5bqO9aU-Oaxg0wax?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'ওয়েব ডিজাইনের প্রথম ধাপ - HTML5, CSS3 এবং Responsive Bootstrap কোর্স।'],
    ['জাভাস্ক্রিপ্ট (JavaScript) ফুল কোর্স', 'https://drive.google.com/drive/folders/1KxXXU8w6OcXzvIjV_T7erG1fqxJKoLss?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'লজিক্যাল প্রোগ্রামিং ও ডাইনামিক ফ্রন্টএন্ডের জন্য জাভাস্ক্রিপ্ট ভিডিও ল্যাব।'],
    ['পাইথন প্রোগ্রামিং ফান্ডামেন্টালস', 'https://drive.google.com/drive/folders/1BON3vCgFzG7bE405K6NzkHWbFDxrUmYC?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'Python এর বেসিক থেকে অবজেক্ট ওরিয়েন্টেড লজিক শেখার টিউটোরিয়াল।'],
    ['SQL Database Optimization', 'https://drive.google.com/drive/folders/1vRS2BslS5MI5c0DNMpQLzm_InNkzQT1p?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'রিলেশনাল ডাটাবেজ, কুয়েরি ডিজাইন ও স্ট্রাকচার্ড কুয়েরি ল্যাঙ্গুয়েজ কোর্স।'],
    ['Django Python Backend Framework', 'https://drive.google.com/drive/folders/1b8OFe1cmcUi4Cl-AWG2lRP4uD3sgyF3E?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'পাইথন জ্যাঙ্গো ফ্রেমওয়ার্ক দিয়ে ফুল স্ট্যাক ওয়েব ব্যাকএন্ড ডেভেলপমেন্ট।'],
    ['React For Frontend Development', 'https://drive.google.com/drive/folders/1BOS2dzJ7afiY1Wu4khECM3sKweg3bGCP?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'সিঙ্গেল পেজ অ্যাপ্লিকেশন ও মডার্ন ইউজার ইন্টারফেস তৈরির রিয়্যাক্ট জেএস কোর্স।'],
    ['ওয়ার্ডপ্রেস থিম ডেভেলপমেন্ট', 'https://drive.google.com/drive/folders/1uM1-onL3yMGJOq7yBzmqOiwPDaFTueZt?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'কাস্টম কোডিং দিয়ে থিম ও প্লাগইন বানানোর ৪০টি অ্যাডভান্সড ভিডিও টিউটোরিয়াল।'],
    ['ওয়েব ডিজাইন প্রফেশনাল (৩৩টি ভিডিও)', 'https://drive.google.com/drive/folders/1nNo3NiF6gfoCllRJlUH4JxW0vH6oYnLk?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'ইউআই টেমপ্লেট থেকে পিএসডি টু এইচটিএমএল কনভার্সনের ৩৩টি লাইভ প্রজেক্ট ক্লাস।'],
    ['গুগল টপ র‍্যাঙ্কিং এসইও মেথড', 'https://drive.google.com/open?id=1OmDFvSnAgI9-DCF-T2OvVe0tdTzAKYUZ', 'প্রিমিয়াম ভিডিও কোর্স', 'যেকোনো ওয়েবসাইটকে গুগলের প্রথম পাতায় র‍্যাঙ্ক করানোর প্রফেশনাল টেকনিক।'],
    ['সুন্দর ও দ্রুত হাতের লেখা (৬৯টি ভিডিও)', 'https://drive.google.com/drive/u/0/folders/1S31Ukn2vC71otcA7VSq2dlE9vauuxsOy', 'প্রিমিয়াম ভিডিও কোর্স', 'হাতের লেখা আকর্ষণীয় ও দ্রুত করার প্র্যাকটিস টিউটোরিয়াল কালেকশন।'],
    ['অটোক্যাড (AutoCAD) ২ডি ও ৩ডি কোর্স', 'https://drive.google.com/drive/folders/1rgrSJhAFm3X_kli1wGm4b7V20slfKo4C?usp=sharing', 'প্রিমিয়াম ভিডিও কোর্স', 'ইঞ্জিনিয়ারিং ও আর্কিটেকচারাল ফ্লোর প্ল্যানিং অটোক্যাড কমপ্লিট মডিউল।'],
    ['কনটেন্ট ক্রিয়েটর মাস্টারক্লাস', 'https://drive.google.com/drive/mobile/folders/1KtIcE9xwFwXghRUmTJdH2YyFMr5WoI1b', 'প্রিমিয়াম ভিডিও কোর্স', 'সোশ্যাল মিডিয়া ও প্রফেশনাল ভিডিও কনটেন্ট ক্রিয়েশন কমপ্লিট মেথড।']
];

// লুপ চালিয়ে ডেটাবেজে ডুপ্লিকেট চেক করে ইনসার্ট করা
$inserted = 0;
foreach ($default_links as $link) {
    $name = pg_escape_string($conn, $link[0]);
    $url = pg_escape_string($conn, $link[1]);
    $cat = pg_escape_string($conn, $link[2]);
    $desc = pg_escape_string($conn, $link[3]);
    
    // ডুপ্লিকেট এড়াতে ইউআরএল ভেরিফিকেশন কুয়েরি
    $check = pg_query($conn, "SELECT 1 FROM site_links WHERE site_url='$url'");
    if (pg_num_rows($check) == 0) {
        pg_query($conn, "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$name', '$url', '$cat', '$desc')");
        $inserted++;
    }
}

echo "<div style='text-align:center; margin-top:60px; font-family:sans-serif;'>
        <h1 style='color:#2563eb;'>🚀 ডাটাবেজ আপডেট সম্পূর্ণ সফল!</h1>
        <p style='color:#64748b; font-size:16px;'>পিডিএফ এবং টেক্সট ফাইলের শতভাগ নিখুঁত মিল রেখে মোট <b>$inserted টি</b> লিংক ডাটাবেজে রেকর্ড করা হয়েছে।</p>
        <a href='index.php' style='display:inline-block; margin-top:20px; background:#2563eb; color:#fff; padding:12px 24px; text-decoration:none; border-radius:12px; font-weight:bold;'>মেইন প্রফেশনাল সাইট দেখুন ➔</a>
      </div>";
?>
    ['Web Based Result', 'https://eboardresults.com', 'শিক্ষা ও রেজাল্ট', 'জেলা বা প্রতিষ্ঠান ভিত্তিক বিশদ রেজাল্ট দেখার অনলাইন মাধ্যম।'],
    ['একাদশে ভর্তি পোর্টাল', 'http://xiclassadmission.gov.bd', 'শিক্ষা ও রেজাল্ট', 'কলেজে একাদশ শ্রেণিতে অনলাইনের মাধ্যমে ভর্তির আবেদন সাইট।'],
    ['মাধ্যমিক ও উচ্চশিক্ষা অধিদপ্তর', 'https://dshe.gov.bd', 'শিক্ষা ও রেজাল্ট', 'শিক্ষা সংক্রান্ত নোটিশ ও নির্দেশনাবলী দেখার সরকারি সাইট।'],

    // পেমেন্ট ও ব্যাংকিং
    ['বিকাশ (bKash)', 'https://www.bkash.com', 'পেমেন্ট ও ব্যাংকিং', 'বাংলাদেশের সবচেয়ে বড় মোবাইল ফিনান্সিয়াল সার্ভিস।'],
    ['নগদ (Nagad)', 'https://nagad.com.bd', 'পেমেন্ট ও ব্যাংকিং', 'ডাক বিভাগের ডিজিটাল লেনদেন ও মোবাইল ব্যাংকিং সেবা।'],
    ['রকেট (Rocket - DBBL)', 'https://www.dutchbanglabank.com/rocket/rocket.html', 'পেমেন্ট ও ব্যাংকিং', 'ডাচ-বাংলা ব্যাংকের মোবাইল ব্যাংকিং ও ইউটিলিটি বিল পেমেন্ট।'],
    ['সেলফিন (CellFin)', 'https://www.islamibankbd.com', 'পেমেন্ট ও ব্যাংকিং', 'ইসলামী ব্যাংকের জনপ্রিয় ডিজিটাল ওয়ালেট ও ব্যাংকিং অ্যাপ।'],

    // ই-কমার্স ও শপিং
    ['দারাজ বাংলাদেশ (Daraz)', 'https://www.daraz.com.bd', 'ই-কমার্স ও শপিং', 'বাংলাদেশের অন্যতম বৃহৎ অনলাইন শপিং মল ও মার্কেটপ্লেস।'],
    ['রকমারি ডট কম', 'https://www.rokomari.com', 'ই-কমার্স ও শপিং', 'বই, ইলেকট্রনিক্স ও নানাবিধ পণ্য কেনার জনপ্রিয় ই-কমার্স সাইট।'],
    ['চালডাল অনলাইন গ্রোসারি', 'https://chaldal.com', 'ই-কমার্স ও শপিং', 'নিত্যপ্রয়োজনীয় মুদি সামগ্রী ঘরে বসে অর্ডারের অনলাইন শপ।'],
    ['Pickaboo', 'https://www.pickaboo.com', 'ই-কমার্স ও শপিং', 'অরিজিনাল মোবাইল, গ্যাজেট ও ইলেকট্রনিক্স সামগ্রীর ই-কমার্স।'],
    
    // অন্যান্য
    ['বিআরটিএ সেবা পোর্টাল', 'https://bsp.brta.gov.bd', 'অন্যান্য দরকারি লিংক', 'ড্রাইভিং লাইসেন্স ও বাইক/গাড়ির রেজিস্ট্রেশন চেক করার পোর্টাল।'],
    ['বাংলাদেশ রেলওয়ে (টিকিট)', 'https://eticket.railway.gov.bd', 'Annyanno', 'অনলাইনে ট্রেনের সিট সিলেক্ট করে টিকিট কাটার অফিশিয়াল সাইট।'],
    ['অনলাইন জিডি (BD Police)', 'https://gd.police.gov.bd', 'অন্যান্য দরকারি লিংক', 'থানায় না গিয়ে অনলাইনে ঘরে বসে সাধারণ ডায়েরি বা GD করার অ্যাপ।']
];

// ডেটা ডুপ্লিকেট না করে ইনসার্ট করা
foreach ($default_links as $link) {
    $name = pg_escape_string($conn, $link[0]);
    $url = pg_escape_string($conn, $link[1]);
    $cat = pg_escape_string($conn, $link[2]);
    $desc = pg_escape_string($conn, $link[3]);
    
    $check = pg_query($conn, "SELECT 1 FROM site_links WHERE site_url='$url'");
    if (pg_num_rows($check) == 0) {
        pg_query($conn, "INSERT INTO site_links (site_name, site_url, category, description) VALUES ('$name', '$url', '$cat', '$desc')");
    }
}

echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
        <h1 style='color:#10b981;'>🔥 পারফেক্ট! সব লিংক ডাটাবেজে যুক্ত করা হয়েছে!</h1>
        <p>এখন আপনার মেইন সাইটে যান, সব লিংক প্রফেশনাল লুকে দেখতে পাবেন।</p>
      </div>";
?>
