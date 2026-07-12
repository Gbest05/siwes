<!-- Register Box -->
<div class="max-w-lg mx-auto my-8 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 md:p-8 shadow-soft animate-fade-in-up" x-data="{ role: 'student' }">
    <div class="text-center mb-6">
        <div class="bg-primary/10 text-primary w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-user-plus text-xl"></i>
        </div>
        <h2 class="text-2xl font-bold font-poppins text-slate-800 dark:text-white">Create Account</h2>
        <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Register to start your SIWES placement logs.</p>
    </div>

    <!-- Role Toggles -->
    <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl mb-6 select-none">
        <button @click="role = 'student'" :class="role === 'student' ? 'bg-white dark:bg-darkCard text-primary shadow-sm' : 'text-slate-500 dark:text-gray-400 hover:text-slate-800'" class="py-2 text-xs font-bold rounded-lg transition-all focus:outline-none">
            As Student
        </button>
        <button @click="role = 'supervisor'" :class="role === 'supervisor' ? 'bg-white dark:bg-darkCard text-primary shadow-sm' : 'text-slate-500 dark:text-gray-400 hover:text-slate-800'" class="py-2 text-xs font-bold rounded-lg transition-all focus:outline-none">
            As Company Supervisor
        </button>
    </div>

    <form action="/siwes/auth/register" method="POST" class="space-y-4" id="register-form">
        <input type="hidden" name="role" :value="role">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Username</label>
                <input type="text" name="username" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="samuel123" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="samuel@student.edu.ng" required>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Password</label>
                <input type="password" id="p1" name="password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">First Name</label>
                <input type="text" name="first_name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Samuel" required>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Alao" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Phone Number</label>
                <input type="text" name="phone" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="+234..." required>
            </div>
        </div>

        <!-- Student fields -->
        <div x-show="role === 'student'" x-transition class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Matric Number</label>
                    <input type="text" name="matric_no" ::required="role === 'student'" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="CSC/2021/045">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department</label>
                    <select name="department_id" ::required="role === 'student'" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?> (<?= htmlspecialchars($dept['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Course of Study</label>
                    <input type="text" name="course_of_study" ::required="role === 'student'" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Computer Science">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Year of Study</label>
                    <select name="year_of_study" ::required="role === 'student'" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="3">3rd Year</option>
                        <option value="4" selected>4th Year</option>
                        <option value="5">5th Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Supervisor fields -->
        <div x-show="role === 'supervisor'" x-transition class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Linked Attachment Company</label>
                <select name="company_id" ::required="role === 'supervisor'" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">-- Select Company --</option>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?= $comp['id'] ?>"><?= htmlspecialchars($comp['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition mt-4">Create Account</button>
    </form>

    <div class="text-center mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-gray-400">
        Already have an account? <a href="/siwes/auth/login" class="text-blue-600 dark:text-accent hover:underline font-semibold">Sign In here</a>
    </div>
</div>
