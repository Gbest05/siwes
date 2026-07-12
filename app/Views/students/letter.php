<div class="space-y-6 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4 no-print">
        <div>
            <h1 class="text-2xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Placement Letter</h1>
            <p class="text-xs text-slate-500">Official introductory placement letter for your attachment partner.</p>
        </div>
        <button onclick="window.print()" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
            <i class="fa-solid fa-print"></i> <span>Print / Save as PDF</span>
        </button>
    </div>

    <!-- Letter Shell Print Area -->
    <div class="bg-white text-black p-8 md:p-12 rounded-3xl border border-slate-200 shadow-soft max-w-3xl mx-auto font-serif relative overflow-hidden print-card" id="letter-content">
        <!-- Letterhead -->
        <div class="text-center border-b-2 border-slate-800 pb-4 mb-6">
            <h2 class="text-xl font-extrabold tracking-wide uppercase font-sans">STATE UNIVERSITY OF TECHNOLOGY</h2>
            <p class="text-xs uppercase font-sans font-semibold tracking-widest text-slate-500 mt-1">SIWES COORDINATION UNIT</p>
            <p class="text-xs font-sans mt-0.5">P.M.B. 4000, Jericho, Ibadan, Oyo State, Nigeria</p>
        </div>

        <!-- Date & Ref -->
        <div class="flex justify-between text-xs font-sans mb-8">
            <span>Ref: SUT/SIWES/2026/VOL.III</span>
            <span>Date: <?= date('F d, Y') ?></span>
        </div>

        <!-- Addressee -->
        <div class="text-sm space-y-1 mb-6">
            <p class="font-bold">The HR Manager / SIWES Supervisor,</p>
            <p class="font-bold text-primary"><?= htmlspecialchars($student['company_name']) ?></p>
            <p class="text-slate-600 max-w-sm font-sans text-xs italic"><?= htmlspecialchars($student['company_address']) ?></p>
        </div>

        <p class="text-sm mb-6">Dear Sir/Ma,</p>

        <!-- Subject -->
        <h3 class="text-sm font-bold uppercase text-center border-b border-black pb-1.5 mb-6 leading-relaxed">
            RE: REQUEST FOR INDUSTRIAL ATTACHMENT PLACEMENT FOR <br>
            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> (<?= htmlspecialchars($student['matric_no']) ?>)
        </h3>

        <!-- Body -->
        <div class="text-sm leading-relaxed space-y-4 text-justify font-sans text-slate-800">
            <p>
                We wish to introduce the above-named student who is currently a 4th-year student in our department of <span class="font-bold"><?= htmlspecialchars($student['department_name']) ?></span>.
            </p>
            <p>
                As part of the requirements for the award of the Bachelor of Technology degree, it is mandatory for students to undergo a six (6) months Students Industrial Work Experience Scheme (SIWES) in an industry matching their course of study.
            </p>
            <p>
                We would be highly grateful if you could accept the student into your organization to gain practical engineering and development experience under your supervisor guidance. The attachment period is scheduled to run for 24 weeks starting from the date of reporting.
            </p>
            <p>
                Kindly sign and stamp the duplicate copy of this letter to acknowledge acceptance, and indicate the department or section the student has been assigned to.
            </p>
            <p>
                Thank you for your anticipated co-operation and partnership in developing future technology leaders.
            </p>
        </div>

        <!-- Sign-off -->
        <div class="mt-12 grid grid-cols-2 gap-12 text-sm pt-8">
            <div class="space-y-4">
                <p>Yours faithfully,</p>
                <div class="h-10 border-b border-slate-300 w-36"></div>
                <div>
                    <p class="font-bold">Dr. Charles Koffi</p>
                    <p class="text-xs text-slate-500 font-sans">University SIWES Coordinator</p>
                </div>
            </div>
            
            <div class="space-y-4 text-right">
                <p class="opacity-0">Signature</p>
                <div class="h-10 border-b border-slate-300 w-36 ml-auto"></div>
                <div>
                    <p class="font-bold">HR Acceptance Stamp</p>
                    <p class="text-xs text-slate-500 font-sans">Signature & Date</p>
                </div>
            </div>
        </div>
    </div>
</div>
