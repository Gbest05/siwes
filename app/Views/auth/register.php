<!-- Register Box -->
<div class="max-w-lg mx-auto my-8 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 md:p-8 shadow-soft animate-fade-in-up">
    <div class="text-center mb-6">
        <div class="bg-primary/10 text-primary w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-graduation-cap text-xl"></i>
        </div>
        <h2 class="text-2xl font-bold font-poppins text-slate-800 dark:text-white">Student Registration</h2>
        <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Register to start your SIWES placement logs & supervisor assignment.</p>
    </div>

    <form action="/siwes/auth/register" method="POST" class="space-y-4" id="register-form">
        <input type="hidden" name="role" value="student">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">First Name</label>
                <input type="text" name="first_name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Samuel" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Alao" required>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Username</label>
                <input type="text" name="username" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="samuel123" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="samuel@student.edu.ng" required>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Matric Number</label>
                <input type="text" name="matric_no" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="CSC/2021/045" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Phone Number</label>
                <input type="text" name="phone" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="+234..." required>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department</label>
                <select name="department_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <?php if (empty($departments)): ?>
                        <option value="1">Computer Science (CSC)</option>
                    <?php else: ?>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?> (<?= htmlspecialchars($dept['code']) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Academic Level</label>
                <select name="level" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <?php if (empty($levels)): ?>
                        <option value="400 Level" selected>400 Level</option>
                    <?php else: ?>
                        <?php foreach ($levels as $lvl): ?>
                            <option value="<?= htmlspecialchars($lvl['name']) ?>" <?= strpos($lvl['code'], '400') !== false ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lvl['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- Password Box with Toggle -->
        <div x-data="{ showPass: false }">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Password</label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" id="p1" name="password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 pr-11 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Enter secure password" required>
                <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-gray-200 focus:outline-none" tabindex="-1">
                    <i :class="showPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition mt-2 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-check"></i>
            <span>Create Student Account</span>
        </button>
    </form>

    <div class="text-center mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-gray-400">
        Already have an account? <a href="/siwes/auth/login" class="text-primary dark:text-accent hover:underline font-semibold">Sign In here</a>
    </div>
</div>
