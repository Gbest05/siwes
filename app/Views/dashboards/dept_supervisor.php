<div class="space-y-8 animate-fade-in-up" x-data="{
    activeTab: 'students',
    reviewModalOpen: false,
    complaintModalOpen: false,
    selectedStudent: null,
    complaintTitle: 'Unclear Company Stamp on Acceptance Letter',
    complaintDesc: 'The uploaded SIWES Acceptance Letter lacks an official stamped endorsement from the Human Resources department of your placement firm. Please re-upload a clear scanned copy showing the official seal.',
    templates: {
        'stamp': {
            title: 'Unclear Company Stamp on Acceptance Letter',
            desc: 'The uploaded SIWES Acceptance Letter lacks an official stamped endorsement from the Human Resources department of your placement firm. Please re-upload a clear scanned copy showing the official seal.'
        },
        'mentor': {
            title: 'Incomplete Industry Mentor Information',
            desc: 'Your submitted CV and profile do not specify the industry supervisor designation and technical unit you will be reporting to. Please update your details.'
        },
        'firm': {
            title: 'Non-accredited / Mismatched Placement Organization',
            desc: 'The company placement selected does not meet required curriculum criteria for Computer Science industrial work. Please consult the departmental SIWES coordinator.'
        },
        'photo': {
            title: 'Passport Photo Does Not Meet Specifications',
            desc: 'Please re-upload a clear portrait photograph with a plain white background for the official departmental SIWES identity registry.'
        },
        'custom': {
            title: '',
            desc: ''
        }
    },
    selectTemplate(key) {
        if (this.templates[key]) {
            this.complaintTitle = this.templates[key].title;
            this.complaintDesc = this.templates[key].desc;
        }
    },
    openComplaintModal(student) {
        this.selectedStudent = student;
        this.complaintModalOpen = true;
    },
    openReviewModal(student) {
        this.selectedStudent = student;
        this.reviewModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-primary dark:text-accent font-bold uppercase tracking-wider mb-1">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Department of Computer Science &bull; SIWES Unit</span>
            </div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">
                Departmental Supervisor Portal
            </h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">
                Welcome, <span class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']) ?></span>. Review student SIWES credentials, assign complaints/queries, and unlock logbook permissions.
            </p>
        </div>
        <div class="flex items-center space-x-3 no-print">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-primary text-white dark:bg-accent dark:text-black">
                <i class="fa-solid fa-user-shield mr-1.5"></i> Departmental Supervisor
            </span>
            <a href="/siwes/coordinator/complaints" class="px-4 py-2 bg-amber-500/15 hover:bg-amber-500 text-amber-700 dark:text-amber-400 hover:text-white text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i> <span>All Queries (<?= count($complaints) ?>)</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Supervised Students -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary dark:text-accent flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Assigned Students</span>
                <span class="text-2xl font-bold font-poppins text-slate-800 dark:text-white"><?= count($students) ?></span>
                <span class="text-[11px] text-slate-500 block">Computer Science dept</span>
            </div>
        </div>

        <!-- Card 2: Pending Document Verification -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-file-circle-exclamation"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Pending Review</span>
                <span class="text-2xl font-bold font-poppins text-amber-500"><?= $metrics['pending_review'] ?? 0 ?></span>
                <span class="text-[11px] text-slate-500 block">Awaiting your approval</span>
            </div>
        </div>

        <!-- Card 3: Cleared & Logbook Unlocked -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-success/10 text-success flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Approved / Unlocked</span>
                <span class="text-2xl font-bold font-poppins text-success"><?= $metrics['approved'] ?? 0 ?></span>
                <span class="text-[11px] text-slate-500 block">Logbooks enabled</span>
            </div>
        </div>

        <!-- Card 4: Active Complaints / Queries -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-danger/10 text-danger flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Queried Students</span>
                <span class="text-2xl font-bold font-poppins text-danger"><?= $metrics['queried'] ?? 0 ?></span>
                <span class="text-[11px] text-slate-500 block">Corrections needed</span>
            </div>
        </div>
    </div>

    <!-- Tab navigation -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-px">
        <button @click="activeTab = 'students'" :class="activeTab === 'students' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none flex items-center gap-2">
            <i class="fa-solid fa-user-graduate"></i>
            <span>Departmental Students & Verification</span>
            <span class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full font-bold"><?= count($students) ?></span>
        </button>
        <button @click="activeTab = 'complaints'" :class="activeTab === 'complaints' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Departmental Complaints & Queries</span>
            <span class="text-xs bg-amber-500/15 text-amber-600 px-2 py-0.5 rounded-full font-bold"><?= count($complaints) ?></span>
        </button>
    </div>

    <!-- TAB 1: Assigned Students List & Document Verification -->
    <div x-show="activeTab === 'students'" class="space-y-6">
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary"></i> Students Assigned Under Your Supervision
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Approve uploaded credentials to unlock the student's daily logbook and placement letter, or assign a complaint query for defective submissions.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200 text-xs uppercase tracking-wider">
                            <th class="py-3 font-semibold">Student</th>
                            <th class="py-3 font-semibold">Matric / Level</th>
                            <th class="py-3 font-semibold">Corporate Partner</th>
                            <th class="py-3 font-semibold">Uploaded Credentials</th>
                            <th class="py-3 font-semibold">Verification Status</th>
                            <th class="py-3 font-semibold text-right">Review Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-user-clock text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-medium">No students currently assigned to your departmental supervision.</p>
                                    <p class="text-xs">The Coordinator will allocate Computer Science students to you.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition">
                                    <!-- Student Name & Avatar -->
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0 flex items-center justify-center font-bold text-primary dark:text-accent shadow-sm">
                                                <?php if (!empty($s['passport_path'])): ?>
                                                    <img src="<?= asset($s['passport_path']) ?>" alt="Passport" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <?= substr($s['first_name'], 0, 1) . substr($s['last_name'], 0, 1) ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-white leading-tight">
                                                    <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
                                                </p>
                                                <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($s['phone'] ?? 'No phone') ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Matric / Level -->
                                    <td class="py-4 text-xs">
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300"><?= htmlspecialchars($s['matric_no']) ?></span>
                                        <span class="block text-slate-400"><?= htmlspecialchars($s['year_of_study'] ?? 4) ?>00 Level</span>
                                    </td>

                                    <!-- Allocated Company -->
                                    <td class="py-4 text-xs">
                                        <?php if (!empty($s['company_name'])): ?>
                                            <span class="font-bold text-primary dark:text-accent block"><?= htmlspecialchars($s['company_name']) ?></span>
                                            <?php if (!empty($s['company_supervisor_name'])): ?>
                                                <span class="text-[11px] text-slate-400">Mentor: <?= htmlspecialchars($s['company_supervisor_name']) ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-amber-500 italic">Unallocated Company</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Documents Links -->
                                    <td class="py-4 text-xs">
                                        <div class="flex flex-col space-y-1">
                                            <?php if (!empty($s['acceptance_letter_path'])): ?>
                                                <a href="<?= asset($s['acceptance_letter_path']) ?>" target="_blank" class="text-primary dark:text-accent hover:underline flex items-center space-x-1 font-semibold">
                                                    <i class="fa-solid fa-file-contract"></i> <span>Acceptance Letter</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic"><i class="fa-regular fa-file"></i> No acceptance letter</span>
                                            <?php endif; ?>

                                            <?php if (!empty($s['passport_path'])): ?>
                                                <a href="<?= asset($s['passport_path']) ?>" target="_blank" class="text-slate-600 dark:text-slate-300 hover:underline flex items-center space-x-1">
                                                    <i class="fa-solid fa-image"></i> <span>Passport Photo</span>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (!empty($s['cv_path'])): ?>
                                                <a href="<?= asset($s['cv_path']) ?>" target="_blank" class="text-slate-600 dark:text-slate-300 hover:underline flex items-center space-x-1">
                                                    <i class="fa-solid fa-file-pdf"></i> <span>Student CV</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-4">
                                        <?php if (($s['document_status'] ?? 'pending') === 'approved'): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-success/15 text-success">
                                                <i class="fa-solid fa-circle-check mr-1.5"></i> Approved
                                            </span>
                                            <span class="block text-[10px] text-slate-400 mt-1">Logbook Unlocked</span>
                                        <?php elseif (($s['document_status'] ?? 'pending') === 'queried'): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-danger/15 text-danger">
                                                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Queried
                                            </span>
                                            <span class="block text-[10px] text-danger mt-1 truncate max-w-[150px]" title="<?= htmlspecialchars($s['supervisor_feedback'] ?? '') ?>">
                                                <?= htmlspecialchars($s['supervisor_feedback'] ?? 'Correction required') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-600">
                                                <i class="fa-solid fa-clock mr-1.5"></i> Pending Review
                                            </span>
                                            <span class="block text-[10px] text-slate-400 mt-1">Logbook Locked</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Review Actions -->
                                    <td class="py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Approve Button -->
                                            <?php if (($s['document_status'] ?? 'pending') !== 'approved'): ?>
                                                <form action="/siwes/supervisor/student/review" method="POST" class="inline">
                                                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                                    <input type="hidden" name="review_action" value="approve">
                                                    <input type="hidden" name="feedback" value="SIWES credentials reviewed and officially approved by Departmental Supervisor. Logbook entries and Placement Letter unlocked.">
                                                    <button type="submit" class="bg-success hover:bg-success/90 text-white text-xs px-3 py-1.5 rounded-xl font-bold transition flex items-center space-x-1" title="Approve credentials & unlock logbook">
                                                        <i class="fa-solid fa-check"></i> <span>Approve</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <!-- Assign Complaint / Query Button -->
                                            <button type="button" @click="openComplaintModal(<?= htmlspecialchars(json_encode([
                                                'id' => $s['id'],
                                                'name' => $s['first_name'] . ' ' . $s['last_name'],
                                                'matric' => $s['matric_no'],
                                                'status' => $s['document_status'] ?? 'pending'
                                            ])) ?>)" class="bg-amber-500/15 hover:bg-amber-500 text-amber-700 dark:text-amber-300 hover:text-white text-xs px-3 py-1.5 rounded-xl font-bold transition flex items-center space-x-1" title="Assign complaint or query to student">
                                                <i class="fa-solid fa-triangle-exclamation"></i> <span>Assign Complaint</span>
                                            </button>

                                            <!-- View Modal Details -->
                                            <button type="button" @click="openReviewModal(<?= htmlspecialchars(json_encode([
                                                'id' => $s['id'],
                                                'name' => $s['first_name'] . ' ' . $s['last_name'],
                                                'matric' => $s['matric_no'],
                                                'status' => $s['document_status'] ?? 'pending',
                                                'feedback' => $s['supervisor_feedback'] ?? '',
                                                'company_id' => $s['allocated_company_id'] ?? ''
                                            ])) ?>)" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-gray-300 text-xs p-2 rounded-xl transition" title="More Options">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Departmental Complaints & Queries Management -->
    <div x-show="activeTab === 'complaints'" class="space-y-6" style="display: none;">
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-danger"></i> Complaints & Queries Log
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Complaints assigned to students under your supervision. Review status and resolve upon satisfactory student rectification.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200 text-xs uppercase tracking-wider">
                            <th class="py-3 font-semibold">Date Filed</th>
                            <th class="py-3 font-semibold">Student</th>
                            <th class="py-3 font-semibold">Query Title</th>
                            <th class="py-3 font-semibold">Description / Instruction</th>
                            <th class="py-3 font-semibold">Status</th>
                            <th class="py-3 font-semibold text-right">Update Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($complaints)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-shield-heart text-3xl mb-2 text-success/60"></i>
                                    <p class="font-medium">No complaints or queries currently pending.</p>
                                    <p class="text-xs">All assigned students have submitted valid documentation.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($complaints as $c): ?>
                                <tr>
                                    <td class="py-3 text-xs text-slate-400">
                                        <?= date('M d, Y', strtotime($c['created_at'])) ?>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($c['student_name']) ?></p>
                                        <p class="text-xs font-mono text-slate-400"><?= htmlspecialchars($c['matric_no']) ?></p>
                                    </td>
                                    <td class="py-3 font-semibold text-xs text-slate-800 dark:text-white">
                                        <?= htmlspecialchars($c['title']) ?>
                                    </td>
                                    <td class="py-3 text-xs text-slate-600 dark:text-gray-300 max-w-sm">
                                        <?= htmlspecialchars($c['description']) ?>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($c['status'] === 'resolved'): ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-success/15 text-success">Resolved</span>
                                        <?php elseif ($c['status'] === 'in_progress'): ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-600">In Progress</span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-danger/15 text-danger">Open</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-right">
                                        <form action="/siwes/coordinator/complaints/status" method="POST" class="inline-flex items-center space-x-1">
                                            <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                                            <?php if ($c['status'] !== 'resolved'): ?>
                                                <button type="submit" name="status" value="resolved" class="bg-success/15 hover:bg-success text-success hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition">
                                                    Mark Resolved
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="status" value="open" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-gray-300 px-2.5 py-1 rounded-lg text-xs font-bold transition">
                                                    Re-open
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 1: Assign Complaint / Query to Student -->
    <div x-show="complaintModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="complaintModalOpen = false" class="bg-white dark:bg-darkCard rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-danger"></i> Assign Complaint / Query
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-show="selectedStudent">
                        Target Student: <span class="font-bold text-slate-800 dark:text-white" x-text="selectedStudent?.name"></span> (<span x-text="selectedStudent?.matric"></span>)
                    </p>
                </div>
                <button @click="complaintModalOpen = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Predefined Template Quick Buttons -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Predefined Departmental Complaint Templates</label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" @click="selectTemplate('stamp')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white dark:hover:bg-accent dark:hover:text-black font-semibold text-left transition">
                        &bull; Unclear Company Stamp
                    </button>
                    <button type="button" @click="selectTemplate('mentor')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white dark:hover:bg-accent dark:hover:text-black font-semibold text-left transition">
                        &bull; Incomplete Mentor Info
                    </button>
                    <button type="button" @click="selectTemplate('firm')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white dark:hover:bg-accent dark:hover:text-black font-semibold text-left transition">
                        &bull; Mismatched / Unaccredited Firm
                    </button>
                    <button type="button" @click="selectTemplate('photo')" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white dark:hover:bg-accent dark:hover:text-black font-semibold text-left transition">
                        &bull; Passport Photo Defect
                    </button>
                </div>
            </div>

            <form action="/siwes/supervisor/student/review" method="POST" class="space-y-4">
                <input type="hidden" name="student_id" :value="selectedStudent?.id">
                <input type="hidden" name="review_action" value="query">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Complaint / Query Title</label>
                    <input type="text" name="complaint_title" x-model="complaintTitle" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Issue title...">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Detailed Explanation & Correction Instructions</label>
                    <textarea name="feedback" x-model="complaintDesc" rows="4" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Explain what the student needs to rectify..."></textarea>
                </div>

                <div class="bg-danger/10 p-3 rounded-xl text-xs text-danger flex items-start space-x-2">
                    <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                    <span>Assigning this complaint will lock the student's logbook access and send an instant notification alerting them to re-upload rectified documents.</span>
                </div>

                <button type="submit" class="w-full bg-danger hover:bg-danger/90 text-white py-3 rounded-xl font-bold shadow-md shadow-danger/25 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Issue Complaint & Notify Student</span>
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Full Review & Placement Management -->
    <div x-show="reviewModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="reviewModalOpen = false" class="bg-white dark:bg-darkCard rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base">Review Student & Actions</h3>
                <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/supervisor/student/review" method="POST" class="space-y-4">
                <input type="hidden" name="student_id" :value="selectedStudent?.id">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Action to Take</label>
                    <select name="review_action" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="approve">Approve Documents (Unlocks Logbook & Placement Letter)</option>
                        <option value="allocate_company">Assign / Reallocate Placement Company</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company Allocation (If Reassigning)</label>
                    <select name="company_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="">-- Keep Current Allocation --</option>
                        <?php if (!empty($companies)): ?>
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= $comp['id'] ?>"><?= htmlspecialchars($comp['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Supervisor Feedback Note</label>
                    <textarea name="feedback" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Optional comments for student..."></textarea>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-bold shadow-md shadow-primary/25 transition">
                    Submit Review Action
                </button>
            </form>
        </div>
    </div>
</div>
