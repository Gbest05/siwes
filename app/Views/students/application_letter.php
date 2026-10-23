<div class="space-y-6 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4 no-print">
        <div>
            <h1 class="text-2xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Application Letter</h1>
            <p class="text-xs text-slate-500">Official student application / introductory letter for industrial training attachment.</p>
        </div>
        <div class="flex items-center space-x-3">
            <?php if (!empty($role) && in_array($role, ['supervisor', 'admin', 'coordinator'])): ?>
                <a href="/siwes/company/application-letter/edit" class="px-3.5 py-2 bg-amber-500/15 hover:bg-amber-500 text-amber-700 dark:text-amber-300 hover:text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-building-circle-check"></i> <span>Edit Company Letter Template</span>
                </a>
            <?php endif; ?>
            <a href="/siwes/student/dashboard" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white rounded-xl text-xs font-semibold transition">
                &larr; Back to Dashboard
            </a>
            <button onclick="window.print()" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i> <span>Print / Save as PDF</span>
            </button>
        </div>
    </div>

    <!-- Letter Shell Print Area -->
    <div class="bg-white text-black p-8 md:p-12 rounded-3xl border border-slate-200 shadow-soft max-w-3xl mx-auto font-serif relative overflow-hidden print-card" id="letter-content">
        <!-- Letterhead -->
        <div class="text-center border-b-2 border-slate-800 pb-4 mb-6">
            <h2 class="text-xl font-extrabold tracking-wide uppercase font-sans"><?= htmlspecialchars($settings['institution_name'] ?? 'STATE UNIVERSITY OF TECHNOLOGY') ?></h2>
            <p class="text-xs uppercase font-sans font-semibold tracking-widest text-slate-500 mt-1"><?= htmlspecialchars($settings['system_name'] ?? 'DIRECTORATE OF SIWES & INDUSTRIAL ATTACHMENT') ?></p>
            <p class="text-xs font-sans mt-0.5">Faculty of Applied Sciences & Technology | Department of Computer Science</p>
        </div>

        <!-- Date & Ref -->
        <div class="flex justify-between text-xs font-sans mb-8">
            <span>Ref: SIWES/APP/<?= date('Y') ?>/<?= htmlspecialchars($student['matric_no'] ?? 'STU') ?></span>
            <span>Date: <?= date('F d, Y') ?></span>
        </div>

        <!-- Addressee -->
        <div class="text-sm space-y-1 mb-6">
            <p class="font-bold">The Managing Director / Human Resources Manager,</p>
            <p class="font-bold text-primary"><?= htmlspecialchars($student['company_name'] ?? 'To Whom It May Concern') ?></p>
            <p class="text-slate-600 max-w-sm font-sans text-xs italic"><?= htmlspecialchars($student['company_address'] ?? 'Official Company Address') ?></p>
        </div>

        <p class="text-sm mb-6">Dear Sir/Madam,</p>

        <!-- Subject -->
        <h3 class="text-sm font-bold uppercase text-center border-b border-black pb-1.5 mb-6 leading-relaxed font-sans">
            APPLICATION FOR STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) ATTACHMENT <br>
            CANDIDATE: <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> | MATRIC NO: <?= htmlspecialchars($student['matric_no']) ?>
        </h3>

        <!-- Body -->
        <div class="text-sm leading-relaxed space-y-4 text-justify font-sans text-slate-800">
            <p>
                I hereby formally write to request an Industrial Training attachment placement for <span class="font-bold"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></span>, a bonafide student of this institution pursuing a degree in <span class="font-bold">Computer Science</span>.
            </p>
            <p>
                The Students Industrial Work Experience Scheme (SIWES) is a mandatory academic requirement instituted by the Industrial Training Fund (ITF) and the National Universities Commission (NUC). The scheme is designed to expose students to practical work methods, industry setups, and modern equipment relevant to their academic discipline.
            </p>
            <p>
                The student has fulfilled all departmental academic prerequisites, submitted their verified credentials, and has been officially cleared and approved by their supervisor to undertake their industrial attachment with your organization.
            </p>
            <p>
                We would be most grateful if you could kindly consider this application and offer the student placement for the designated six (6) months attachment period. An acceptance form is attached for your completion and endorsement.
            </p>
            <p>
                Thank you for your anticipated co-operation and valuable contribution towards manpower training in Nigeria.
            </p>
        </div>

        <!-- Sign-off -->
        <div class="mt-8 flex justify-between items-end font-sans">
            <div>
                <div class="w-32 border-b border-black mb-1"></div>
                <p class="text-xs font-bold uppercase"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></p>
                <p class="text-[11px] text-slate-500">Student / Applicant</p>
            </div>

            <div class="text-right">
                <div class="w-40 border-b border-black mb-1 ml-auto"></div>
                <p class="text-xs font-bold uppercase">SIWES Co-ordinator</p>
                <p class="text-[11px] text-slate-500">Department of Computer Science</p>
            </div>
        </div>

        <!-- Section 2: Corporate Partner Acceptance Endorsement & Specific Terms -->
        <div class="mt-10 pt-6 border-t-2 border-dashed border-slate-300 font-sans">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex justify-between items-start border-b border-slate-200 pb-3">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Corporate Attachment Terms & Conditions</span>
                        <h4 class="text-sm font-bold text-primary uppercase"><?= htmlspecialchars($student['application_letter_title'] ?? 'OFFICIAL INDUSTRIAL ATTACHMENT ACCEPTANCE & CONDITIONS') ?></h4>
                        <p class="text-xs text-slate-600 font-semibold"><?= htmlspecialchars($student['company_name'] ?? 'Assigned Organization') ?></p>
                    </div>
                    <span class="text-[10px] bg-primary/10 text-primary font-bold px-2.5 py-1 rounded-full">Company Endorsement Section</span>
                </div>

                <div class="text-xs text-slate-700 leading-relaxed text-justify space-y-2">
                    <?php 
                        $companyTerms = $student['application_letter_content'] ?? 'We are pleased to accept your application for the SIWES Industrial Attachment Programme. Please report to the Human Resources department on your assigned start date with your introductory letter and identity credentials.';
                        $compParagraphs = explode("\n\n", $companyTerms);
                        foreach ($compParagraphs as $cp):
                            if (!empty(trim($cp))):
                    ?>
                        <p><?= nl2br(htmlspecialchars(trim($cp))) ?></p>
                    <?php 
                            endif;
                        endforeach; 
                    ?>
                </div>

                <div class="pt-4 flex justify-between items-end text-xs">
                    <div>
                        <div class="w-36 border-b border-slate-400 mb-1"></div>
                        <p class="font-bold text-slate-800"><?= htmlspecialchars($student['application_letter_signatory'] ?? 'Head of Human Resources & Talent Development') ?></p>
                        <p class="text-[10px] text-slate-500"><?= htmlspecialchars($student['company_name'] ?? 'Placement Partner') ?></p>
                    </div>
                    <div class="text-right">
                        <div class="w-20 h-20 border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center text-[9px] text-slate-400 uppercase font-bold ml-auto">
                            Company Stamp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
