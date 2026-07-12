<!-- Forgot Password Box -->
<div class="max-w-md mx-auto my-12 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 md:p-8 shadow-soft animate-fade-in-up">
    <div class="text-center mb-8">
        <div class="bg-primary/10 text-primary w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-circle-question text-xl"></i>
        </div>
        <h2 class="text-2xl font-bold font-poppins text-slate-800 dark:text-white">Recover Password</h2>
        <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Enter your registered email to receive reset instructions.</p>
    </div>

    <form action="/siwes/auth/forgot" method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <i class="fa-solid fa-envelope text-sm"></i>
                </span>
                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-10 pr-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="samuel@student.edu.ng" required>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Send Recovery Email</button>
    </form>

    <div class="text-center mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-gray-400">
        Remember your password? <a href="/siwes/auth/login" class="text-blue-600 dark:text-accent hover:underline font-semibold">Sign In here</a>
    </div>
</div>
