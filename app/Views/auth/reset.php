<!-- Reset Password Box -->
<div class="max-w-md mx-auto my-12 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 md:p-8 shadow-soft animate-fade-in-up">
    <div class="text-center mb-8">
        <div class="bg-primary/10 text-primary w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-key text-xl"></i>
        </div>
        <h2 class="text-2xl font-bold font-poppins text-slate-800 dark:text-white">Reset Password</h2>
        <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Configure a new secure password for: <span class="font-semibold text-primary"><?= htmlspecialchars($email) ?></span></p>
    </div>

    <form action="/siwes/auth/reset" method="POST" class="space-y-4">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">New Password</label>
            <input type="password" id="p1" name="password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Confirm New Password</label>
            <input type="password" id="p2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
        </div>

        <button type="submit" id="reset-btn" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Save New Password</button>
    </form>

    <!-- Client validation hook -->
    <script src="/siwes/public/assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            validatePasswordMatch('p1', 'p2', 'reset-btn');
        });
    </script>
</div>
