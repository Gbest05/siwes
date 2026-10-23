<div class="space-y-8 animate-fade-in-up" x-data="{
    companyName: '<?= addslashes(htmlspecialchars($company['name'] ?? 'Corporate Partner')) ?>',
    companyAddress: '<?= addslashes(htmlspecialchars($company['address'] ?? 'Official Company Address')) ?>',
    companyEmail: '<?= addslashes(htmlspecialchars($company['email'] ?? '')) ?>',
    letterTitle: '<?= addslashes(htmlspecialchars($company['application_letter_title'] ?? 'OFFICIAL INDUSTRIAL ATTACHMENT ACCEPTANCE & CONDITIONS')) ?>',
    letterContent: `<?= addslashes($company['application_letter_content'] ?? '') ?>`,
    letterSignatory: '<?= addslashes(htmlspecialchars($company['application_letter_signatory'] ?? 'Head of Human Resources & Talent Development')) ?>'
}">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-primary font-bold uppercase tracking-wider mb-1">
                <span>Corporate Partner Document Studio</span>
                <span>&bull;</span>
                <span class="text-slate-400">Custom Company Application & Acceptance Letter</span>
            </div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($company['name'] ?? 'Company') ?> Application Letter</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Customize company-specific acceptance terms, internship guidelines, and onboarding instructions.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/siwes/supervisor/dashboard" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white rounded-xl text-xs font-semibold transition">
                &larr; Back to Dashboard
            </a>
            <?php if (!empty($allCompanies)): ?>
                <!-- Company Switcher for Admin/Coordinator -->
                <form action="/siwes/company/application-letter/edit" method="GET" class="flex items-center gap-2">
                    <select name="company_id" onchange="this.form.submit()" class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-white">
                        <?php foreach ($allCompanies as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $company['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Dual Column Split -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- Form Editor Column (5 cols) -->
        <div class="xl:col-span-5 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-pen text-primary"></i> Company Letter Customization
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Students allocated to <?= htmlspecialchars($company['name']) ?> will receive these exact terms.</p>
            </div>

            <form action="/siwes/company/application-letter/edit" method="POST" class="space-y-4">
                <input type="hidden" name="company_id" value="<?= $company['id'] ?>">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company Letter Title / Header</label>
                    <input type="text" name="application_letter_title" x-model="letterTitle" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Official Signatory Designation</label>
                    <input type="text" name="application_letter_signatory" x-model="letterSignatory" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. Lead Technical Supervisor & Talent Director">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Acceptance Terms & Application Instructions</label>
                    <textarea name="application_letter_content" x-model="letterContent" rows="10" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm font-mono text-xs leading-relaxed focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Specify reporting date, required materials, team assignment, code of conduct..."></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Specify onboarding instructions, required tools, department assignment, and reporting protocols.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Save Company Application Letter</span>
                </button>
            </form>
        </div>

        <!-- Right: Live Preview Column (7 cols) -->
        <div class="xl:col-span-7 bg-slate-100 dark:bg-slate-900/60 p-4 md:p-8 rounded-3xl border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success animate-ping"></span> Live Company Acceptance Sheet Preview
                </span>
                <span class="text-xs bg-primary text-white font-bold px-3 py-1 rounded-full">
                    <?= htmlspecialchars($company['name']) ?>
                </span>
            </div>

            <!-- Paper Sheet Simulation -->
            <div class="bg-white text-black p-8 md:p-12 rounded-2xl shadow-xl border border-slate-300 font-serif text-sm leading-relaxed max-w-2xl mx-auto space-y-6">
                <!-- Company Header -->
                <div class="text-center border-b-2 border-primary pb-4">
                    <h2 class="text-lg font-black uppercase font-sans tracking-wide text-primary" x-text="companyName"></h2>
                    <p class="text-xs font-sans text-slate-600 mt-1" x-text="companyAddress"></p>
                    <p class="text-[11px] font-sans text-slate-500 mt-0.5"><?= htmlspecialchars($company['email']) ?> &bull; <?= htmlspecialchars($company['phone'] ?? '+234 800 SIWES PARTNER') ?></p>
                </div>

                <!-- Date & Ref -->
                <div class="flex justify-between text-xs font-sans text-slate-700">
                    <div>Ref: <span class="font-mono font-bold"><?= strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $company['name']), 0, 4)) ?>/SIWES/<?= date('Y') ?></span></div>
                    <div>Date: <span class="font-bold"><?= date('F d, Y') ?></span></div>
                </div>

                <!-- Recipient / Candidate -->
                <div class="font-sans text-xs space-y-0.5">
                    <p class="font-bold">To: <span class="font-bold text-slate-850"><?= htmlspecialchars($sampleStudent['first_name'] . ' ' . $sampleStudent['last_name']) ?></span> (<?= htmlspecialchars($sampleStudent['matric_no']) ?>)</p>
                    <p class="text-slate-600">Department of Computer Science &bull; State University of Technology</p>
                </div>

                <!-- Subject -->
                <div class="text-center font-sans font-bold border-b border-black pb-2 text-xs uppercase leading-snug text-slate-900">
                    <span x-text="letterTitle"></span>
                </div>

                <!-- Dynamic Content Paragraphs -->
                <div class="text-xs font-sans text-justify text-slate-800 space-y-3 leading-relaxed">
                    <template x-for="(paragraph, index) in letterContent.split('\n\n')" :key="index">
                        <p x-show="paragraph.trim().length > 0" x-text="paragraph"></p>
                    </template>
                </div>

                <!-- Company Sign-off -->
                <div class="mt-8 pt-6 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs font-sans">
                    <div class="space-y-3">
                        <p>Authorized Signature,</p>
                        <div class="h-8 border-b border-slate-400 w-36"></div>
                        <div>
                            <p class="font-bold text-slate-800" x-text="letterSignatory"></p>
                            <p class="text-[10px] text-slate-500" x-text="companyName"></p>
                        </div>
                    </div>
                    <div class="space-y-3 text-right">
                        <p class="opacity-0">Seal</p>
                        <div class="w-24 h-24 border-2 border-dashed border-slate-300 rounded-2xl ml-auto flex items-center justify-center text-[10px] text-slate-400 uppercase font-bold">
                            Corporate Seal
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
