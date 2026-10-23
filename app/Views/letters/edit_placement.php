<div class="space-y-8 animate-fade-in-up" x-data="{
    institution: '<?= addslashes(htmlspecialchars($settings['placement_letter_institution'] ?? 'STATE UNIVERSITY OF TECHNOLOGY')) ?>',
    unit: '<?= addslashes(htmlspecialchars($settings['placement_letter_unit'] ?? 'DIRECTORATE OF SIWES & INDUSTRIAL TRAINING')) ?>',
    address: '<?= addslashes(htmlspecialchars($settings['placement_letter_address'] ?? 'P.M.B. 4000, Jericho, Ibadan, Oyo State, Nigeria')) ?>',
    refPrefix: '<?= addslashes(htmlspecialchars($settings['placement_letter_ref_prefix'] ?? 'SUT/SIWES/2026/VOL.III')) ?>',
    subject: '<?= addslashes(htmlspecialchars($settings['placement_letter_subject'] ?? 'RE: REQUEST FOR INDUSTRIAL ATTACHMENT PLACEMENT')) ?>',
    bodyText: `<?= addslashes($settings['placement_letter_body'] ?? '') ?>`,
    signatoryName: '<?= addslashes(htmlspecialchars($settings['placement_letter_signatory_name'] ?? 'Dr. Charles Koffi')) ?>',
    signatoryTitle: '<?= addslashes(htmlspecialchars($settings['placement_letter_signatory_title'] ?? 'University SIWES Coordinator')) ?>'
}">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-primary font-bold uppercase tracking-wider mb-1">
                <span>Placement Document Management</span>
                <span>&bull;</span>
                <span class="text-slate-400">Coordinator / Admin Tool</span>
            </div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Customize Official Placement Letter</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Edit the official university letter issued to companies for student industrial attachments.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/siwes/coordinator/allocations" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white rounded-xl text-xs font-semibold transition">
                &larr; Back to Allocations
            </a>
            <a href="/siwes/student/download-letter" target="_blank" class="px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary dark:text-accent rounded-xl text-xs font-bold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>Live Output View</span>
            </a>
        </div>
    </div>

    <!-- Dual Column Split: Form Editor (Left) & Real-time Letter Preview (Right) -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Form Controls (5 cols) -->
        <div class="xl:col-span-5 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-nib text-primary"></i> Letter Configuration Form
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Changes saved here apply immediately to all approved students' printable letters.</p>
            </div>

            <form action="/siwes/coordinator/letters/placement" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Institution Name (Header)</label>
                    <input type="text" name="placement_letter_institution" x-model="institution" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">SIWES Unit / Directorate</label>
                    <input type="text" name="placement_letter_unit" x-model="unit" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Official Address</label>
                    <input type="text" name="placement_letter_address" x-model="address" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Reference Prefix</label>
                        <input type="text" name="placement_letter_ref_prefix" x-model="refPrefix" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Signatory Name</label>
                        <input type="text" name="placement_letter_signatory_name" x-model="signatoryName" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Signatory Designation / Title</label>
                    <input type="text" name="placement_letter_signatory_title" x-model="signatoryTitle" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Letter Subject Line</label>
                    <input type="text" name="placement_letter_subject" x-model="subject" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Letter Body Content (Paragraphs)</label>
                    <textarea name="placement_letter_body" x-model="bodyText" rows="10" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm font-mono text-xs leading-relaxed focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Separate paragraphs with double enter. Student and Department details are dynamically formatted in preview.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save & Deploy Placement Letter Template</span>
                </button>
            </form>
        </div>

        <!-- Right: Live Real-time Visual Preview (7 cols) -->
        <div class="xl:col-span-7 bg-slate-100 dark:bg-slate-900/60 p-4 md:p-8 rounded-3xl border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success animate-ping"></span> Real-time Live Letterhead Preview
                </span>
                <span class="text-xs bg-accent/20 text-[#0A0E1A] dark:text-accent font-bold px-3 py-1 rounded-full">
                    A4 Printable Simulated Format
                </span>
            </div>

            <!-- Paper Sheet Simulation -->
            <div class="bg-white text-black p-8 md:p-12 rounded-2xl shadow-xl border border-slate-300 font-serif text-sm leading-relaxed max-w-2xl mx-auto space-y-6">
                <!-- Letterhead -->
                <div class="text-center border-b-2 border-black pb-4">
                    <h2 class="text-lg font-black uppercase font-sans tracking-wide" x-text="institution"></h2>
                    <p class="text-xs uppercase font-sans font-bold tracking-widest text-slate-600 mt-1" x-text="unit"></p>
                    <p class="text-[11px] font-sans text-slate-500 mt-0.5" x-text="address"></p>
                </div>

                <!-- Date & Ref -->
                <div class="flex justify-between text-xs font-sans text-slate-700">
                    <div>Ref: <span class="font-mono font-bold" x-text="refPrefix"></span></div>
                    <div>Date: <span class="font-bold"><?= date('F d, Y') ?></span></div>
                </div>

                <!-- Recipient -->
                <div class="font-sans text-xs space-y-0.5">
                    <p class="font-bold">The Managing Director / HR Manager,</p>
                    <p class="font-bold text-primary text-sm"><?= htmlspecialchars($sampleStudent['company_name']) ?></p>
                    <p class="text-slate-600 italic"><?= htmlspecialchars($sampleStudent['company_address']) ?></p>
                </div>

                <p class="text-xs font-sans">Dear Sir/Ma,</p>

                <!-- Subject -->
                <div class="text-center font-sans font-bold border-b border-black pb-2 text-xs uppercase leading-snug">
                    <span x-text="subject"></span> FOR <br>
                    <span><?= htmlspecialchars($sampleStudent['first_name'] . ' ' . $sampleStudent['last_name']) ?> (<?= htmlspecialchars($sampleStudent['matric_no']) ?>)</span>
                </div>

                <!-- Dynamic Body Preview -->
                <div class="text-xs font-sans text-justify text-slate-800 space-y-3 leading-relaxed">
                    <template x-for="(paragraph, index) in bodyText.split('\n\n')" :key="index">
                        <p x-show="paragraph.trim().length > 0" x-text="paragraph"></p>
                    </template>
                </div>

                <!-- Signatories -->
                <div class="mt-8 pt-4 grid grid-cols-2 gap-8 text-xs font-sans">
                    <div class="space-y-3">
                        <p>Yours faithfully,</p>
                        <div class="h-8 border-b border-slate-400 w-32"></div>
                        <div>
                            <p class="font-bold" x-text="signatoryName"></p>
                            <p class="text-[10px] text-slate-500" x-text="signatoryTitle"></p>
                        </div>
                    </div>
                    <div class="space-y-3 text-right">
                        <p class="opacity-0">HR Acceptance</p>
                        <div class="h-8 border-b border-slate-400 w-32 ml-auto"></div>
                        <div>
                            <p class="font-bold">Company Acceptance Stamp</p>
                            <p class="text-[10px] text-slate-500">Official Endorsement & Date</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
