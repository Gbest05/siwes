<?php
use App\Core\Auth;
$user = Auth::user();
?>
<div class="space-y-8 animate-fade-in-up" x-data="{ 
    showEvalModal: false, 
    showReviewModal: false,
    activeStudent: null,
    activeStudentId: null, 
    activeStudentName: '',
    reviewAction: 'approve',
    openReview(s) {
        this.activeStudent = s;
        this.activeStudentId = s.id;
        this.activeStudentName = s.first_name + ' ' + s.last_name;
        this.reviewAction = 'approve';
        this.showReviewModal = true;
    },
    formatDocUrl(path) {
        if (!path) return '#';
        let clean = path.replace(/^\/?(siwes\/public\/|siwes\/|public\/)?/, '').replace(/^\/?uploads\//, 'uploads/');
        let base = '<?= asset('') ?>';
        return base.replace(/\/+$/, '') + '/' + clean;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center space-x-4">
            <a href="/siwes/settings" class="w-16 h-16 rounded-2xl bg-accent text-[#0A0E1A] font-bold flex items-center justify-center text-2xl uppercase shadow-md overflow-hidden shrink-0 hover:opacity-90 transition-opacity" title="Change Profile Picture">
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="<?= asset($user['profile_pic']) ?>" alt="Profile Photo" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= substr($user['username'], 0, 2) ?>
                <?php endif; ?>
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h1 class="text-2xl md:text-3xl font-poppins font-bold text-slate-800 dark:text-white">Company Supervisor Dashboard</h1>
                    <?php if (!empty($company['name'])): ?>
                        <span class="bg-primary/10 text-primary dark:text-accent border border-primary/25 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-building"></i> <?= htmlspecialchars($company['name']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs md:text-sm text-slate-500 dark:text-gray-400">
                    Host Organization: <strong class="text-slate-700 dark:text-white font-bold"><?= htmlspecialchars($company['name'] ?? 'Assigned Partner Organization') ?></strong>
                    <?php if (!empty($company['industry_category'])): ?> &bull; <?= htmlspecialchars($company['industry_category']) ?><?php endif; ?>
                    &bull; Review attached students, inspect documents, and sign daily logbooks.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="/siwes/company/application-letter/edit" class="bg-primary/10 hover:bg-primary/20 text-primary dark:text-accent text-xs px-3.5 py-2.5 rounded-xl font-bold transition flex items-center space-x-1.5 no-print" title="Edit Company Acceptance & Application Letter Template">
                <i class="fa-solid fa-file-pen"></i> <span>Edit Company Letter</span>
            </a>
            <a href="/siwes/coordinator/complaints" class="bg-amber-500/15 hover:bg-amber-500 text-amber-700 dark:text-amber-400 hover:text-white text-xs px-3.5 py-2.5 rounded-xl font-bold transition flex items-center space-x-1.5 no-print">
                <i class="fa-solid fa-triangle-exclamation"></i> <span>Complaints</span>
            </a>
            <a href="/siwes/supervisor/logbooks" class="bg-primary hover:bg-secondary text-white text-xs md:text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2 no-print">
                <i class="fa-solid fa-list-check"></i> <span>Logbooks (<?= count($pendingLogs) ?>)</span>
            </a>
        </div>
    </div>

    <!-- Company / Organization Information Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-primary/95 to-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden border border-white/10">
        <div class="absolute -right-8 -bottom-8 opacity-10 text-white pointer-events-none">
            <i class="fa-solid fa-building text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl text-accent font-bold shadow-inner shrink-0">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-accent font-bold flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-certificate"></i> Host Organization / Company
                    </span>
                    <h2 class="text-xl md:text-2xl font-bold font-poppins text-white">
                        <?= htmlspecialchars($company['name'] ?? 'Company / Organization') ?>
                    </h2>
                    <p class="text-xs text-white/70 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <?php if (!empty($company['industry_category'])): ?>
                            <span><i class="fa-solid fa-tag text-accent mr-1"></i> <?= htmlspecialchars($company['industry_category']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($company['email'])): ?>
                            <span><i class="fa-solid fa-envelope text-accent mr-1"></i> <?= htmlspecialchars($company['email']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($company['phone'])): ?>
                            <span><i class="fa-solid fa-phone text-accent mr-1"></i> <?= htmlspecialchars($company['phone']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($company['address'])): ?>
                            <span><i class="fa-solid fa-location-dot text-accent mr-1"></i> <?= htmlspecialchars($company['address']) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl px-4 py-2.5 border border-white/10 text-center">
                    <span class="block text-[10px] uppercase text-white/70 font-semibold tracking-wider">Supervised Students</span>
                    <span class="text-xl font-bold text-accent"><?= count($students) ?></span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl px-4 py-2.5 border border-white/10 text-center">
                    <span class="block text-[10px] uppercase text-white/70 font-semibold tracking-wider">Pending Logs</span>
                    <span class="text-xl font-bold text-amber-300"><?= count($pendingLogs) ?></span>
                </div>
                <a href="/siwes/company/application-letter/edit" class="bg-accent hover:bg-white text-[#0A0E1A] font-bold text-xs px-4 py-3 rounded-2xl shadow-lg transition flex items-center space-x-2">
                    <i class="fa-solid fa-file-signature"></i> <span>Company Letter</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-8">
        <!-- Assigned Students List with Document Reviews -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-users text-primary"></i> Assigned Students & Document Review
                    </h3>
                    <p class="text-xs text-slate-400">Check students assigned to you, inspect their uploaded SIWES documents, approve placement or issue queries.</p>
                </div>
                <span class="text-xs font-bold bg-primary/10 text-primary px-3 py-1 rounded-xl"><?= count($students) ?> Students Assigned</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                            <th class="py-3 font-semibold">Student Name & Matric</th>
                            <th class="py-3 font-semibold">Department & Level</th>
                            <th class="py-3 font-semibold">Company Placement</th>
                            <th class="py-3 font-semibold">Uploaded Documents</th>
                            <th class="py-3 font-semibold">Review Status</th>
                            <th class="py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-user-clock text-3xl mb-2 text-slate-300 dark:text-slate-700 block"></i>
                                    No students currently assigned to you by the coordinator.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td class="py-3.5">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-lg bg-accent text-[#0A0E1A] font-bold flex items-center justify-center text-xs overflow-hidden shrink-0">
                                                <?php if (!empty($s['passport_path'])): ?>
                                                    <img src="<?= asset($s['passport_path']) ?>" alt="Avatar" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <?= substr($s['first_name'], 0, 1) . substr($s['last_name'], 0, 1) ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-white leading-tight"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></p>
                                                <p class="font-mono text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($s['matric_no']) ?> &bull; <?= htmlspecialchars($s['phone'] ?? 'No phone') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 text-xs">
                                        <span class="font-semibold text-slate-700 dark:text-gray-300 block">Computer Science</span>
                                        <span class="text-slate-400"><?= htmlspecialchars($s['year_of_study'] ?? 4) ?>00 Level</span>
                                    </td>
                                    <td class="py-3.5 text-xs">
                                        <?php if (!empty($s['company_name'])): ?>
                                            <span class="font-semibold text-primary block"><?= htmlspecialchars($s['company_name']) ?></span>
                                            <span class="text-[10px] text-slate-400">Allocated</span>
                                        <?php else: ?>
                                            <span class="text-amber-500 font-semibold italic">Pending Placement</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 text-xs">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <?php if (!empty($s['acceptance_letter_path'])): ?>
                                                <a href="<?= asset($s['acceptance_letter_path']) ?>" target="_blank" class="px-2 py-0.5 bg-primary/10 hover:bg-primary/20 text-primary rounded-md font-semibold text-[10px] inline-flex items-center gap-1" title="View Acceptance Letter">
                                                    <i class="fa-solid fa-file-pdf"></i> Acceptance Form
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($s['cv_path'])): ?>
                                                <a href="<?= asset($s['cv_path']) ?>" target="_blank" class="px-2 py-0.5 bg-accent/15 hover:bg-accent/25 text-slate-800 dark:text-accent rounded-md font-semibold text-[10px] inline-flex items-center gap-1" title="View CV">
                                                    <i class="fa-solid fa-file-lines"></i> CV
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($s['passport_path'])): ?>
                                                <a href="<?= asset($s['passport_path']) ?>" target="_blank" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-gray-300 rounded-md font-semibold text-[10px] inline-flex items-center gap-1" title="View Photo">
                                                    <i class="fa-solid fa-image"></i> Photo
                                                </a>
                                            <?php endif; ?>
                                            <?php if (empty($s['acceptance_letter_path']) && empty($s['cv_path']) && empty($s['passport_path'])): ?>
                                                <span class="text-slate-400 italic text-[11px]">No docs uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3.5 text-xs">
                                        <?php 
                                        $dStatus = $s['document_status'] ?? 'pending';
                                        if ($dStatus === 'approved'): ?>
                                            <span class="bg-success/15 text-success font-bold px-2.5 py-0.5 rounded-full text-[10px]">Approved</span>
                                        <?php elseif ($dStatus === 'queried'): ?>
                                            <span class="bg-danger/15 text-danger font-bold px-2.5 py-0.5 rounded-full text-[10px]">Queried</span>
                                        <?php else: ?>
                                            <span class="bg-warning/15 text-warning font-bold px-2.5 py-0.5 rounded-full text-[10px]">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 text-right space-x-1">
                                        <button @click="openReview(<?= htmlspecialchars(json_encode($s)) ?>)" class="bg-primary/10 hover:bg-primary text-primary hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            Review Docs
                                        </button>
                                        <button @click="activeStudentId = '<?= $s['id'] ?>'; activeStudentName = '<?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>'; showEvalModal = true" class="bg-success/15 hover:bg-success text-success hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            Evaluate
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Logs Review -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-list-check mr-2 text-warning"></i> Pending Logbook Reviews (<?= count($pendingLogs) ?>)</h3>
            <?php if (empty($pendingLogs)): ?>
                <div class="py-8 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-circle-check text-4xl mb-2 text-success/75"></i>
                    <p class="text-sm font-medium">All student log entries verified!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($pendingLogs, 0, 3) as $log): ?>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                            <div>
                                <span class="text-xs bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-semibold uppercase tracking-wider">Week <?= $log['week_number'] ?></span>
                                <h4 class="font-bold text-slate-800 dark:text-white mt-2 text-sm"><?= htmlspecialchars($log['student_name']) ?> (<?= htmlspecialchars($log['matric_no']) ?>) - <?= $log['date'] ?></h4>
                                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1 max-w-xl italic">"<?= htmlspecialchars($log['activity_description']) ?>"</p>
                            </div>
                            <div class="shrink-0">
                                <a href="/siwes/supervisor/logbooks" class="bg-primary hover:bg-secondary text-white text-xs px-4 py-2 rounded-lg font-bold transition">Review Logs</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($pendingLogs) > 3): ?>
                        <div class="text-center pt-2">
                            <a href="/siwes/supervisor/logbooks" class="text-xs font-semibold text-primary hover:underline">View all pending logbooks (<?= count($pendingLogs) ?>) &rarr;</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal: Review Student Documents, Allocate Placement, or Issue Query/Complaint -->
    <div x-show="showReviewModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl space-y-6" @click.away="showReviewModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Review Student & SIWES Placement</h3>
                    <p class="text-xs text-slate-400" x-text="activeStudentName"></p>
                </div>
                <button @click="showReviewModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Student Uploaded Documents Quick Access -->
            <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60 space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Submitted SIWES Credentials</span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1 text-center">
                    <!-- Acceptance Letter -->
                    <template x-if="activeStudent && activeStudent.acceptance_letter_path">
                        <a :href="formatDocUrl(activeStudent.acceptance_letter_path)" target="_blank" rel="noopener noreferrer" class="p-3 bg-primary/10 hover:bg-primary/20 rounded-xl text-primary text-xs font-bold transition flex flex-col items-center shadow-sm">
                            <i class="fa-solid fa-file-contract text-lg mb-1"></i>
                            <span class="text-xs">Acceptance Form</span>
                        </a>
                    </template>
                    <template x-if="!activeStudent || !activeStudent.acceptance_letter_path">
                        <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 text-xs flex flex-col items-center">
                            <i class="fa-solid fa-file text-lg mb-1"></i>
                            <span class="text-xs">No Form</span>
                        </div>
                    </template>

                    <!-- Passport Photo -->
                    <template x-if="activeStudent && activeStudent.passport_path">
                        <a :href="formatDocUrl(activeStudent.passport_path)" target="_blank" rel="noopener noreferrer" class="p-3 bg-slate-200/70 hover:bg-slate-300 dark:bg-slate-700 rounded-xl text-slate-700 dark:text-white text-xs font-bold transition flex flex-col items-center shadow-sm">
                            <i class="fa-solid fa-image text-lg mb-1"></i>
                            <span class="text-xs">Passport Photo</span>
                        </a>
                    </template>
                    <template x-if="!activeStudent || !activeStudent.passport_path">
                        <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400 text-xs flex flex-col items-center">
                            <i class="fa-solid fa-image text-lg mb-1"></i>
                            <span class="text-xs">No Photo</span>
                        </div>
                    </template>

                    <!-- CV / Other Credentials (if uploaded) -->
                    <template x-if="activeStudent && activeStudent.cv_path">
                        <a :href="formatDocUrl(activeStudent.cv_path)" target="_blank" rel="noopener noreferrer" class="p-3 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl text-xs font-bold transition flex flex-col items-center shadow-sm col-span-2 sm:col-span-1">
                            <i class="fa-solid fa-file-pdf text-lg mb-1"></i>
                            <span class="text-xs">Student CV</span>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Action Tabs -->
            <div class="grid grid-cols-3 gap-1 bg-slate-100 dark:bg-slate-800 p-1.5 rounded-xl text-xs font-bold">
                <button type="button" @click="reviewAction = 'approve'" :class="reviewAction === 'approve' ? 'bg-white dark:bg-darkCard text-success shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'" class="py-2 rounded-lg transition">
                    Approve
                </button>
                <button type="button" @click="reviewAction = 'allocate_company'" :class="reviewAction === 'allocate_company' ? 'bg-white dark:bg-darkCard text-primary shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'" class="py-2 rounded-lg transition">
                    Assign Company
                </button>
                <button type="button" @click="reviewAction = 'query'" :class="reviewAction === 'query' ? 'bg-white dark:bg-darkCard text-danger shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'" class="py-2 rounded-lg transition">
                    Query / Issue
                </button>
            </div>

            <form action="/siwes/supervisor/student/review" method="POST" class="space-y-4">
                <input type="hidden" name="student_id" :value="activeStudentId">
                <input type="hidden" name="review_action" :value="reviewAction">

                <!-- 1. Approve Tab Body -->
                <div x-show="reviewAction === 'approve'" class="space-y-3">
                    <p class="text-xs text-slate-500 dark:text-gray-400">
                        Approving will verify the student's credentials, mark their SIWES placement as active, and unlock their printable official <strong class="text-primary">Placement Letter</strong> and <strong class="text-emerald-500">Application Letter</strong>.
                    </p>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Confirm Attached Company</label>
                        <select name="company_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">Keep current company allocation</option>
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= $comp['id'] ?>" :selected="activeStudent && activeStudent.allocated_company_id == '<?= $comp['id'] ?>'"><?= htmlspecialchars($comp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Approval Note (Optional)</label>
                        <input type="text" name="feedback" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="All documents verified. Ready for attachment.">
                    </div>
                    <button type="submit" class="w-full bg-success hover:bg-success-dark text-white py-3 rounded-xl font-semibold shadow-md shadow-success/20 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Approve Placement & Unlock Letters</span>
                    </button>
                </div>

                <!-- 2. Allocate Company Tab Body -->
                <div x-show="reviewAction === 'allocate_company'" class="space-y-3">
                    <p class="text-xs text-slate-500 dark:text-gray-400">
                        Assign or change the partner company where this student will perform their industrial attachment.
                    </p>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select Attachment Partner Company</label>
                        <select name="company_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= $comp['id'] ?>" :selected="activeStudent && activeStudent.allocated_company_id == '<?= $comp['id'] ?>'"><?= htmlspecialchars($comp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>Assign Company Placement</span>
                    </button>
                </div>

                <!-- 3. Query / Issue Tab Body -->
                <div x-show="reviewAction === 'query'" class="space-y-3">
                    <p class="text-xs text-slate-500 dark:text-gray-400">
                        Issue a complaint or query if the student's acceptance form is missing a stamp, passport is invalid, or details are incomplete.
                    </p>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Query Subject / Title</label>
                        <input type="text" name="complaint_title" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. Unclear Acceptance Letter Stamp">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Query Description / Feedback</label>
                        <textarea name="feedback" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Describe the issue so the student can re-upload..." required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-danger hover:bg-danger-dark text-white py-3 rounded-xl font-semibold shadow-md shadow-danger/20 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Send Query / Complaint to Student</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Evaluation Modal -->
    <div x-show="showEvalModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl space-y-6" @click.away="showEvalModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">SIWES Performance Evaluation</h3>
                <button @click="showEvalModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form action="/siwes/supervisor/evaluate" method="POST" class="space-y-4">
                <input type="hidden" name="student_id" :value="activeStudentId">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Student Name</span>
                    <p class="font-bold text-slate-800 dark:text-white text-sm" x-text="activeStudentName"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Internship Performance Score (0 - 100)</label>
                    <input type="number" name="score" min="0" max="100" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="85" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Supervisor Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Highlight technical skills, punctuality, and productivity..." required></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Recommendations</label>
                    <textarea name="recommendations" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Suggestions for the student or university unit..."></textarea>
                </div>

                <button type="submit" class="w-full bg-success hover:bg-success-dark text-white py-3 rounded-xl font-semibold shadow-md shadow-success/25 transition">Submit Evaluation Sheet</button>
            </form>
        </div>
    </div>
</div>
