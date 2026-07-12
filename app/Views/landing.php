<?php
use App\Core\Session;
?>
<!-- Dark Hero Section -->
<div class="bg-[#0A0E1A] text-white pt-10 pb-16 px-6 md:px-12 lg:px-20 relative overflow-hidden">
    <!-- Animated Blur Background Glows -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-accent/5 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-900/10 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Hero Left Content -->
        <div class="lg:col-span-7 space-y-6 animate-fade-in-up">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-poppins font-extrabold leading-tight text-white">
                <?= htmlspecialchars($settings['landing_hero_title'] ?? 'The Future of SIWES Management Starts Here.') ?>
            </h1>
            <p class="text-slate-400 text-base md:text-lg max-w-xl leading-relaxed">
                <?= htmlspecialchars($settings['landing_hero_subtitle'] ?? 'Access high-quality attachment slots from top corporate experts and secure your SIWES placement. Log daily activities, upload layouts, and track progress. Anytime, anywhere.') ?>
            </p>
            
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="/siwes/auth/register" class="bg-accent hover:bg-accent/90 text-[#0A0E1A] font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-accent/20 transition flex items-center space-x-2">
                    <span>Explore Placements</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/siwes/auth/login" class="bg-transparent hover:bg-white/5 border border-slate-700 px-8 py-3.5 rounded-xl font-bold transition flex items-center space-x-2 text-white">
                    <span>SIGN IN</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </a>
            </div>

            <!-- Student placement counts overlay -->
            <div class="flex items-center space-x-4 pt-6 border-t border-slate-800">
                <div class="flex -space-x-3">
                    <div class="w-10 h-10 rounded-full bg-slate-700 border-2 border-[#0A0E1A] flex items-center justify-center font-bold text-xs uppercase text-white">SA</div>
                    <div class="w-10 h-10 rounded-full bg-[#EAB308] border-2 border-[#0A0E1A] flex items-center justify-center font-bold text-xs uppercase text-[#0A0E1A]">CO</div>
                    <div class="w-10 h-10 rounded-full bg-slate-600 border-2 border-[#0A0E1A] flex items-center justify-center font-bold text-xs uppercase text-white">FB</div>
                    <div class="w-10 h-10 rounded-full bg-accent border-2 border-[#0A0E1A] flex items-center justify-center font-bold text-xs uppercase text-[#0A0E1A]">5K+</div>
                </div>
                <div class="text-xs text-slate-400">
                    <span class="font-bold text-white block text-sm">5,000+ Students</span>
                    are already placed and tracking logs with SIWES.
                </div>
            </div>
        </div>

        <!-- Hero Right Illustration with floating overlays -->
        <div class="lg:col-span-5 relative mt-6 lg:mt-0 animate-fade-in-up">
            <!-- Student main image -->
            <div class="relative w-full max-w-md mx-auto rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                <img src="<?= asset('assets/images/hero_student.png') ?>" alt="University student using laptop" class="w-full object-cover aspect-[4/3] lg:aspect-auto">
            </div>

            <!-- Floating Card 1: Your Progress -->
            <div class="hidden sm:block absolute -top-6 -right-4 bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-slate-850 w-52 space-y-2 text-white">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Your Progress</span>
                    <span class="text-accent font-bold">78%</span>
                </div>
                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-accent h-full rounded-full" style="width: 78%"></div>
                </div>
                <span class="text-[10px] text-slate-400 block">Keep going! You're doing great.</span>
            </div>

            <!-- Floating Card 2: Active Task -->
            <div class="hidden sm:block absolute -bottom-6 -left-4 bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-slate-850 w-60 flex items-center space-x-3 text-white">
                <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-play text-sm"></i>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Current Attachment</span>
                    <h4 class="font-bold text-xs truncate">UI/UX Design attachment</h4>
                    <span class="text-[10px] text-slate-400 block mt-0.5">Week 4 logs checked</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Horizontal Feature Bar Ribbon -->
<div class="bg-[#080B13] border-b border-slate-800 py-6 px-6 md:px-12 text-white">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 items-center">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-slate-850 text-accent flex items-center justify-center shrink-0">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div>
                <h4 class="font-bold text-xs">Intelligent Matcher</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Matches student specialties</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-slate-850 text-accent flex items-center justify-center shrink-0">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <h4 class="font-bold text-xs">Daily Logbooks</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Log attachment work hours</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-slate-850 text-accent flex items-center justify-center shrink-0">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <h4 class="font-bold text-xs">Verify Signatures</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Direct supervisor reviews</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-slate-850 text-accent flex items-center justify-center shrink-0">
                <i class="fa-solid fa-file-csv"></i>
            </div>
            <div>
                <h4 class="font-bold text-xs">Reports Exporter</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Download placement sheets</p>
            </div>
        </div>
    </div>
</div>

<!-- Specialized Fields Card Grid (Popular Courses replacement) -->
<div id="specialized-fields" class="py-16 md:py-24 px-6 md:px-12 bg-white dark:bg-darkBg transition-colors">
    <div class="max-w-7xl mx-auto space-y-12">
        <div class="text-center space-y-3">
            <h2 class="text-3xl md:text-4xl font-poppins font-extrabold text-slate-800 dark:text-white">Specialized SIWES Fields</h2>
            <p class="text-slate-500 dark:text-gray-400 text-sm max-w-lg mx-auto">Explore our most popular industry categories and start your practical attachment today.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (empty($specialized_fields)): ?>
                <p class="col-span-full text-center text-slate-400 py-8">No specialized fields registered yet.</p>
            <?php else: ?>
                <?php foreach ($specialized_fields as $sf): ?>
                    <div class="bg-white dark:bg-[#111827] rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft overflow-hidden hover:shadow-lg transition duration-200">
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img src="<?= asset('assets/images/' . htmlspecialchars($sf['image_name'])) ?>" alt="<?= htmlspecialchars($sf['title']) ?>" class="w-full h-full object-cover">
                            <?php if (!empty($sf['badge'])): ?>
                                <span class="absolute top-3 left-3 bg-accent text-[#0A0E1A] text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md"><?= htmlspecialchars($sf['badge']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="p-5 space-y-3">
                            <h3 class="font-poppins font-bold text-slate-800 dark:text-white text-base"><?= htmlspecialchars($sf['title']) ?></h3>
                            <p class="text-slate-500 dark:text-gray-400 text-xs leading-relaxed">
                                <?= htmlspecialchars($sf['description']) ?>
                            </p>
                            <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-50 dark:border-slate-800">
                                <div class="flex items-center space-x-1.5">
                                    <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-[9px] font-bold"><?= htmlspecialchars($sf['company_initials'] ?? 'SI') ?></div>
                                    <span class="text-slate-400 text-[10px]"><?= htmlspecialchars($sf['company_name']) ?></span>
                                </div>
                                <span class="text-warning font-bold text-[10px]"><i class="fa-solid fa-star text-[9px]"></i> <?= number_format($sf['rating'] ?? 5.0, 1) ?></span>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-slate-400 text-[10px] font-semibold uppercase"><i class="fa-solid fa-hashtag mr-1"></i> <?= (int)($sf['slots_count']) ?> Slots</span>
                                <span class="text-primary dark:text-accent font-bold text-sm"><?= htmlspecialchars($sf['status']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>


    </div>
</div>

<!-- CTA Call-to-action Banner Section -->
<div class="px-6 md:px-12 py-10 bg-white dark:bg-darkBg">
    <div class="max-w-6xl mx-auto bg-[#0B1329] rounded-[32px] p-8 md:p-12 border border-slate-800 text-white flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden">
        <!-- background glows -->
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-accent/5 rounded-full blur-2xl"></div>

        <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-4 sm:space-y-0 sm:space-x-6">
            <div class="w-16 h-16 rounded-2xl bg-accent text-[#0B1329] flex items-center justify-center font-bold text-3xl shadow-lg shrink-0">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-2xl font-bold font-poppins text-white">Start Your SIWES Placement Today</h3>
                <p class="text-slate-400 text-sm max-w-md">Join thousands of students and secure your attachment placement at top corporate partners.</p>
            </div>
        </div>

        <div class="shrink-0 space-y-2 text-center md:text-left">
            <a href="/siwes/auth/register" class="inline-flex bg-accent hover:bg-accent/90 text-[#0B1329] font-bold px-8 py-3.5 rounded-xl transition items-center space-x-2 shadow-lg shadow-accent/20">
                <span>Get Started Now</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <span class="text-[10px] text-slate-500 block text-center">No registration fee required.</span>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div id="faq" class="py-16 md:py-24 px-6 md:px-12 bg-slate-50 dark:bg-[#0F172A] border-t border-slate-100 dark:border-slate-800 transition-colors" x-data="{ active: null }">
    <div class="max-w-3xl mx-auto space-y-12">
        <div class="text-center space-y-3">
            <h2 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Frequently Asked Questions</h2>
            <p class="text-slate-500 dark:text-gray-400 text-sm">Quick answers to common questions about the SIWES placement portal.</p>
        </div>
        
        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="bg-white dark:bg-[#1E293B] rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
                <button @click="active = active === 1 ? null : 1" class="w-full px-6 py-4.5 text-left font-semibold text-slate-800 dark:text-white flex justify-between items-center focus:outline-none">
                    <span>What is SIWES?</span>
                    <i class="fa-solid" :class="active === 1 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="active === 1" x-transition class="px-6 pb-4.5 text-xs text-slate-500 dark:text-gray-400 border-t border-slate-50 dark:border-slate-800 pt-3">
                    SIWES (Students Industrial Work Experience Scheme) is a program that gives students practical work experience in industries related to their course of study.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="bg-white dark:bg-[#1E293B] rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
                <button @click="active = active === 2 ? null : 2" class="w-full px-6 py-4.5 text-left font-semibold text-slate-800 dark:text-white flex justify-between items-center focus:outline-none">
                    <span>What is the SIWES Allocation System?</span>
                    <i class="fa-solid" :class="active === 2 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="active === 2" x-transition class="px-6 pb-4.5 text-xs text-slate-500 dark:text-gray-400 border-t border-slate-50 dark:border-slate-800 pt-3">
                    It is an online platform that helps students apply for SIWES, manages company registrations, and automatically or manually allocates students to suitable organizations.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="bg-white dark:bg-[#1E293B] rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
                <button @click="active = active === 3 ? null : 3" class="w-full px-6 py-4.5 text-left font-semibold text-slate-800 dark:text-white flex justify-between items-center focus:outline-none">
                    <span>Who can use this system?</span>
                    <i class="fa-solid" :class="active === 3 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="active === 3" x-transition class="px-6 pb-4.5 text-xs text-slate-500 dark:text-gray-400 border-t border-slate-50 dark:border-slate-800 pt-3">
                    The system is designed for students, SIWES coordinators, administrators and departmental officers.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="bg-white dark:bg-[#1E293B] rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
                <button @click="active = active === 4 ? null : 4" class="w-full px-6 py-4.5 text-left font-semibold text-slate-800 dark:text-white flex justify-between items-center focus:outline-none">
                    <span>How do I register on the platform?</span>
                    <i class="fa-solid" :class="active === 4 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="active === 4" x-transition class="px-6 pb-4.5 text-xs text-slate-500 dark:text-gray-400 border-t border-slate-50 dark:border-slate-800 pt-3">
                    Click on Register, fill in your personal and academic details, and submit the registration form.
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="bg-white dark:bg-[#1E293B] rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
                <button @click="active = active === 5 ? null : 5" class="w-full px-6 py-4.5 text-left font-semibold text-slate-800 dark:text-white flex justify-between items-center focus:outline-none">
                    <span>Is the system free to use?</span>
                    <i class="fa-solid" :class="active === 5 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="active === 5" x-transition class="px-6 pb-4.5 text-xs text-slate-500 dark:text-gray-400 border-t border-slate-50 dark:border-slate-800 pt-3">
                    Yes. The SIWES Allocation System is provided by the institution for eligible students.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div id="contact" class="py-16 md:py-24 px-6 md:px-12 bg-white dark:bg-darkBg border-t border-slate-100 dark:border-slate-800 transition-colors">
    <div class="max-w-xl mx-auto bg-white dark:bg-darkCard border border-slate-150 dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-soft">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-bold font-poppins text-slate-800 dark:text-white">Contact the SIWES Unit</h3>
            <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Have issues registering? Send us a message.</p>
        </div>
        
        <form action="/siwes/contact" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Full Name</label>
                <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="John Doe" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="john@student.edu.ng" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Message Description</label>
                <textarea name="message" rows="4" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Write details here..." required></textarea>
            </div>
            <button type="submit" class="w-full bg-[#0A0E1A] hover:bg-[#1E293B] text-white py-3 rounded-xl font-semibold shadow-md transition">Send Message</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="bg-[#050810] border-t border-slate-900 text-slate-500 text-xs py-8 px-6 md:px-12 text-center">
    <div class="max-w-7xl mx-auto space-y-4">
        <div class="flex justify-center space-x-6">
            <a href="/siwes/" class="hover:text-accent">Home</a>
            <a href="#specialized-fields" class="hover:text-accent">Specializations</a>
            <a href="#faq" class="hover:text-accent">FAQ</a>
        </div>
        <div class="text-slate-400 text-xs mt-2 space-y-1">
            <p>Support: <span class="text-accent"><?= htmlspecialchars($settings['contact_email'] ?? 'siwes@university.edu.ng') ?></span> | Hotline: <span class="text-accent"><?= htmlspecialchars($settings['contact_phone'] ?? '+234 803 123 4567') ?></span></p>
        </div>
        <p>&copy; 2026 <?= htmlspecialchars($settings['system_name'] ?? 'SIWES Allocation and Management Portal') ?>. Design & Implementation of SIWES Allocation System Using Nginx and SQLite.</p>
    </div>
</footer>
