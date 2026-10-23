<div class="space-y-8 animate-fade-in-up" x-data="{ uploadModal: false, activeUploadType: 'passport', activeUploadTitle: 'Passport Photo' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div class="flex items-center space-x-4">
            <div class="relative group">
                <div class="w-16 h-16 rounded-2xl bg-accent text-[#0A0E1A] font-bold flex items-center justify-center text-2xl uppercase shadow-md overflow-hidden shrink-0">
                    <?php if (!empty($user['profile_pic'])): ?>
                        <img src="<?= asset($user['profile_pic']) ?>" alt="Profile Photo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= substr($user['username'], 0, 2) ?>
                    <?php endif; ?>
                </div>
                <button @click="activeUploadType = 'passport'; activeUploadTitle = 'Profile / Passport Photo'; uploadModal = true" class="absolute -bottom-1 -right-1 bg-primary text-white p-1.5 rounded-full text-[10px] shadow hover:bg-secondary transition" title="Upload Photo">
                    <i class="fa-solid fa-camera"></i>
                </button>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-poppins font-bold text-slate-800 dark:text-white">Welcome, <?= htmlspecialchars($user['profile']['first_name'] ?? $user['username']) ?>!</h1>
                <p class="text-xs md:text-sm text-slate-500 dark:text-gray-400">Matric: <span class="font-semibold text-slate-700 dark:text-gray-200"><?= htmlspecialchars($user['profile']['matric_no'] ?? 'N/A') ?></span> | Department: <span class="font-semibold text-primary">Computer Science (CSC)</span> | Level: <?= htmlspecialchars($studentProfile['year_of_study'] ?? 4) ?>00 Level</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <?php 
            $isApproved = ($studentProfile['document_status'] ?? 'pending') === 'approved' || ($studentProfile['siwes_status'] ?? 'unallocated') === 'allocated';
            ?>

            <?php if ($isApproved && !empty($allocation['company_name'])): ?>
                <a href="/siwes/student/download-letter" class="bg-primary hover:bg-secondary text-white text-xs md:text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-file-arrow-down"></i> <span>Placement Letter</span>
                </a>
                <a href="/siwes/student/download-application-letter" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs md:text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-emerald-600/20 transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-envelope-open-text"></i> <span>Application Letter</span>
                </a>
            <?php else: ?>
                <div class="bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3.5 py-2 rounded-xl text-xs text-slate-500 dark:text-gray-400 flex items-center space-x-2">
                    <i class="fa-solid fa-lock text-amber-500"></i>
                    <span>Letters unlock after supervisor approves documents</span>
                </div>
            <?php endif; ?>

            <a href="/siwes/student/logbook" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 text-xs md:text-sm px-4 py-2.5 rounded-xl font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-book-open text-accent"></i> <span>Daily Logbook</span>
            </a>
        </div>
    </div>

    <!-- Dual Supervision Cards: Departmental Supervisor & Corporate Placement Mentor -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Departmental Supervisor (Computer Science) -->
        <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-white dark:from-slate-900 dark:via-slate-850 dark:to-darkCard rounded-3xl p-6 border border-primary/20 shadow-soft space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center text-xl font-bold shadow-md shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-primary dark:text-accent flex items-center gap-1.5">
                        <i class="fa-solid fa-user-shield"></i> Departmental SIWES Supervisor
                    </span>
                    <h3 class="text-base font-bold font-poppins text-slate-800 dark:text-white mt-0.5">
                        <?= htmlspecialchars($allocation['dept_supervisor_name'] ?? 'Dr. Charles Koffi (Assigned)') ?>
                    </h3>
                    <p class="text-xs text-slate-500">Department of Computer Science</p>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="space-y-1">
                    <p class="text-slate-600 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-envelope text-primary text-xs"></i>
                        <span><?= htmlspecialchars($allocation['dept_supervisor_email'] ?? 'koffi@university.edu.ng') ?></span>
                    </p>
                    <p class="text-slate-600 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-primary text-xs"></i>
                        <span><?= htmlspecialchars($allocation['dept_supervisor_phone'] ?? '08031234501') ?></span>
                    </p>
                </div>
                <div>
                    <?php if (($studentProfile['document_status'] ?? 'pending') === 'approved'): ?>
                        <span class="bg-success/15 text-success font-bold px-3 py-1 rounded-full text-[11px] flex items-center gap-1">
                            <i class="fa-solid fa-check"></i> Documents Approved
                        </span>
                    <?php elseif (($studentProfile['document_status'] ?? 'pending') === 'queried'): ?>
                        <span class="bg-danger/15 text-danger font-bold px-3 py-1 rounded-full text-[11px] flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Documents Queried
                        </span>
                    <?php else: ?>
                        <span class="bg-amber-500/15 text-amber-600 font-bold px-3 py-1 rounded-full text-[11px] flex items-center gap-1">
                            <i class="fa-solid fa-clock"></i> Verification Pending
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 italic">
                * Your Departmental Supervisor reviews your Acceptance Letter and Passport Photo before your daily logbook unlocks.
            </p>
        </div>

        <!-- Card 2: Corporate Partner Placement & Industry Mentor -->
        <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-white dark:from-slate-900 dark:via-slate-850 dark:to-darkCard rounded-3xl p-6 border border-emerald-500/20 shadow-soft space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-bold shadow-md shrink-0">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-handshake"></i> Corporate Placement & Mentor
                    </span>
                    <h3 class="text-base font-bold font-poppins text-slate-800 dark:text-white mt-0.5">
                        <?= htmlspecialchars($allocation['company_name'] ?? 'Pending Placement Allocation') ?>
                    </h3>
                    <p class="text-xs text-slate-500 truncate max-w-xs"><?= htmlspecialchars($allocation['company_address'] ?? 'Official attachment address') ?></p>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="space-y-1">
                    <p class="text-slate-600 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-tie text-emerald-600 text-xs"></i>
                        <span>Mentor: <strong class="text-slate-800 dark:text-white"><?= htmlspecialchars($allocation['supervisor_name'] ?? 'Assigned by firm') ?></strong></span>
                    </p>
                    <?php if (!empty($allocation['supervisor_email'])): ?>
                        <p class="text-slate-600 dark:text-gray-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                            <span><?= htmlspecialchars($allocation['supervisor_email']) ?></span>
                        </p>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (!empty($allocation['company_name'])): ?>
                        <span class="bg-emerald-600/15 text-emerald-600 dark:text-emerald-400 font-bold px-3 py-1 rounded-full text-[11px]">
                            Placement Assigned
                        </span>
                    <?php else: ?>
                        <span class="bg-amber-500/15 text-amber-600 font-bold px-3 py-1 rounded-full text-[11px]">
                            Unallocated
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 italic">
                * Your Industry Mentor verifies your daily activities and weekly tasks inside the SIWES logbook.
            </p>
        </div>
    </div>

    <!-- Supervisor Feedback / Query Alert if any -->
    <?php if (($studentProfile['document_status'] ?? '') === 'queried' || !empty($studentProfile['supervisor_feedback'])): ?>
        <div class="bg-amber-500/10 border-l-4 border-amber-500 p-5 rounded-r-2xl space-y-2">
            <div class="flex items-center space-x-2 text-amber-600 dark:text-amber-400 font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <span>Supervisor Review Note / Query</span>
            </div>
            <p class="text-xs text-slate-700 dark:text-gray-300">
                <?= nl2br(htmlspecialchars($studentProfile['supervisor_feedback'])) ?>
            </p>
            <p class="text-[11px] text-amber-700 dark:text-amber-300 font-semibold pt-1">
                Please re-upload your corrected document below so your supervisor can review and approve your placement.
            </p>
        </div>
    <?php endif; ?>

    <!-- SIWES Document Upload Center -->
    <?php 
    $isSubmitted = !empty($studentProfile['documents_submitted']) || ($studentProfile['document_status'] ?? '') === 'approved';
    $isQueried = ($studentProfile['document_status'] ?? '') === 'queried';
    $canUpload = !$isSubmitted || $isQueried;
    ?>
    <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-bold font-poppins text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-primary"></i> SIWES Document Submission Center
                </h3>
                <p class="text-xs text-slate-500 dark:text-gray-400">Upload your Passport Photo and SIWES Acceptance Letter for departmental supervisor verification.</p>
            </div>
            <div>
                <?php 
                $docStatus = $studentProfile['document_status'] ?? 'pending';
                if ($docStatus === 'approved'): ?>
                    <span class="bg-success/15 text-success border border-success/30 px-3 py-1 rounded-xl text-xs font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> Documents Approved
                    </span>
                <?php elseif ($docStatus === 'queried'): ?>
                    <span class="bg-danger/15 text-danger border border-danger/30 px-3 py-1 rounded-xl text-xs font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-exclamation"></i> Query / Re-upload Required
                    </span>
                <?php elseif ($isSubmitted): ?>
                    <span class="bg-primary/15 text-primary border border-primary/30 px-3 py-1 rounded-xl text-xs font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-lock"></i> Submitted & Locked
                    </span>
                <?php else: ?>
                    <span class="bg-warning/15 text-warning border border-warning/30 px-3 py-1 rounded-xl text-xs font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-clock"></i> Pending Upload & Submission
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$canUpload): ?>
            <!-- Locked State: Documents Already Submitted -->
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white">SIWES Documents Submitted & Locked</h4>
                        <p class="text-xs text-slate-500 dark:text-gray-400">Your documents have been submitted to your supervisor. Once submitted, you cannot upload another document again.</p>
                    </div>
                </div>
                <span class="text-xs bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-xl shrink-0 flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fa-solid fa-shield-check"></i> Verified Submission
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Passport Photograph (Locked View) -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Passport Photo</span>
                            <span class="text-[10px] bg-success/15 text-success font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                <i class="fa-solid fa-check"></i> Uploaded & Locked
                            </span>
                        </div>
                        <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-200 dark:bg-slate-700 mx-auto flex items-center justify-center shadow-sm border border-slate-200 dark:border-slate-600">
                            <?php if (!empty($studentProfile['passport_path'])): ?>
                                <img src="<?= asset($studentProfile['passport_path']) ?>" alt="Passport" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fa-solid fa-user text-3xl text-slate-400"></i>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-slate-500 text-center">Official student identity photograph verified for SIWES documentation.</p>
                    </div>
                </div>

                <!-- 2. SIWES Acceptance Letter (Locked View) -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">2. SIWES Acceptance Letter</span>
                            <span class="text-[10px] bg-success/15 text-success font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                <i class="fa-solid fa-check"></i> Uploaded & Locked
                            </span>
                        </div>
                        <div class="h-24 rounded-2xl bg-slate-200/70 dark:bg-slate-700/60 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-file-contract text-4xl text-primary"></i>
                        </div>
                        <p class="text-[11px] text-slate-500 text-center">Company signed SIWES Acceptance / Placement Form under supervisor review.</p>
                        <?php if (!empty($studentProfile['acceptance_letter_path'])): ?>
                            <div class="text-center pt-1">
                                <a href="<?= asset($studentProfile['acceptance_letter_path']) ?>" target="_blank" class="w-full bg-primary/10 hover:bg-primary text-primary hover:text-white py-2 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> View Submitted Acceptance Letter
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Upload State: Single Form with One Submit Button -->
            <form action="/siwes/student/upload-document" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Passport Photograph Input -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-gray-400">1. Passport Photo</span>
                                <?php if (!empty($studentProfile['passport_path'])): ?>
                                    <span class="text-[10px] bg-success/15 text-success font-bold px-2 py-0.5 rounded-full">Current Photo Attached</span>
                                <?php else: ?>
                                    <span class="text-[10px] bg-rose-500/15 text-rose-600 font-bold px-2 py-0.5 rounded-full">Required</span>
                                <?php endif; ?>
                            </div>
                            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-200 dark:bg-slate-700 mx-auto flex items-center justify-center shadow-inner">
                                <?php if (!empty($studentProfile['passport_path'])): ?>
                                    <img src="<?= asset($studentProfile['passport_path']) ?>" alt="Passport" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fa-solid fa-image text-3xl text-slate-400"></i>
                                <?php endif; ?>
                            </div>
                            <p class="text-[11px] text-slate-500 text-center">Colored passport photograph in JPG or PNG format (Max 2MB).</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-gray-300">Choose Passport Image:</label>
                            <input type="file" name="passport_file" accept="image/jpeg,image/png" <?= empty($studentProfile['passport_path']) ? 'required' : '' ?> class="w-full text-[11px] text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                        </div>
                    </div>

                    <!-- 2. SIWES Acceptance Letter Input -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/60 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-gray-400">2. SIWES Acceptance Letter</span>
                                <?php if (!empty($studentProfile['acceptance_letter_path'])): ?>
                                    <span class="text-[10px] bg-success/15 text-success font-bold px-2 py-0.5 rounded-full">Current Document Attached</span>
                                <?php else: ?>
                                    <span class="text-[10px] bg-rose-500/15 text-rose-600 font-bold px-2 py-0.5 rounded-full">Required</span>
                                <?php endif; ?>
                            </div>
                            <div class="h-20 rounded-2xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-inner">
                                <i class="fa-solid fa-file-contract text-3xl text-primary"></i>
                            </div>
                            <p class="text-[11px] text-slate-500 text-center">Company signed acceptance form (PDF or clear image JPG/PNG).</p>
                            <?php if (!empty($studentProfile['acceptance_letter_path'])): ?>
                                <div class="text-center">
                                    <a href="<?= asset($studentProfile['acceptance_letter_path']) ?>" target="_blank" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> View Current Letter
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-gray-300">Choose Acceptance Letter:</label>
                            <input type="file" name="acceptance_file" accept=".pdf,image/jpeg,image/png" <?= empty($studentProfile['acceptance_letter_path']) ? 'required' : '' ?> class="w-full text-[11px] text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- One Unified Submit Button with Submission Notice -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2 text-xs text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-circle-info text-base shrink-0"></i>
                        <span><strong>Important:</strong> Once you submit your documents, they will be sent to your departmental supervisor and locked. You will not be able to upload another document again.</span>
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-secondary text-white px-8 py-3.5 rounded-2xl text-xs md:text-sm font-bold shadow-lg shadow-primary/25 transition flex items-center justify-center space-x-2 shrink-0">
                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                        <span>Submit Uploaded Documents</span>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Status Tracker & Profile Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Placement Status Card -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">SIWES Placement Status</h3>
            <div class="flex items-center space-x-4">
                <?php if ($studentProfile['siwes_status'] === 'unallocated'): ?>
                    <div class="bg-warning/10 text-warning p-4 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-question text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Awaiting Allocation</h4>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Upload your acceptance letter and documents above for supervisor review and placement allocation.</p>
                    </div>
                <?php elseif ($studentProfile['siwes_status'] === 'allocated'): ?>
                    <div class="bg-success/10 text-success p-4 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Placement Approved</h4>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">You are attached to <span class="font-bold text-primary"><?= htmlspecialchars($allocation['company_name'] ?? 'Assigned Partner') ?></span>. Your formal Placement Letter and Application Letter are available for download.</p>
                    </div>
                <?php elseif ($studentProfile['siwes_status'] === 'completed'): ?>
                    <div class="bg-primary/10 text-primary p-4 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">SIWES Completed</h4>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Congratulations! You have completed your SIWES program attachment.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Visual Timeline Tracker -->
            <div class="mt-8 border-t border-slate-100 dark:border-slate-800 pt-6">
                <div class="flex items-center justify-between relative max-w-md mx-auto">
                    <!-- Progress Bar Line -->
                    <div class="absolute h-0.5 bg-slate-200 dark:bg-slate-700 left-4 right-4 top-1/2 -translate-y-1/2 -z-10"></div>
                    <div class="absolute h-0.5 bg-primary left-4 top-1/2 -translate-y-1/2 -z-10 transition-all duration-300" style="width: <?= $studentProfile['siwes_status'] === 'unallocated' ? '0%' : ($studentProfile['siwes_status'] === 'allocated' ? '50%' : '100%') ?>"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold bg-white dark:bg-darkCard border-primary text-primary">1</div>
                        <span class="text-[10px] font-semibold mt-2 text-slate-500 dark:text-gray-400 uppercase">Enrolled</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition <?= ($studentProfile['siwes_status'] === 'allocated' || $studentProfile['siwes_status'] === 'completed') ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-darkCard border-slate-300 text-slate-400' ?>">2</div>
                        <span class="text-[10px] font-semibold mt-2 text-slate-500 dark:text-gray-400 uppercase">Allocated</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition <?= $studentProfile['siwes_status'] === 'completed' ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-darkCard border-slate-300 text-slate-400' ?>">3</div>
                        <span class="text-[10px] font-semibold mt-2 text-slate-500 dark:text-gray-400 uppercase">Completed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logbook Progression Card -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Daily Progress Tracker</h3>
                <span class="text-xs text-slate-500">Target: 120 completed workdays.</span>
            </div>
            
            <div class="my-4">
                <div class="flex justify-between items-center text-sm font-bold text-slate-700 dark:text-gray-200 mb-1">
                    <span>Approved Logs</span>
                    <span><?= $progress['approved'] ?> / 120 (<?= $progress['percentage'] ?>%)</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-accent h-full rounded-full transition-all duration-300" style="width: <?= min(100, $progress['percentage']) ?>%"></div>
                </div>
            </div>

            <div class="text-xs text-slate-400 dark:text-slate-500 border-t border-slate-50 dark:border-slate-800 pt-3">
                <i class="fa-solid fa-circle-info mr-1 text-primary"></i> Current total submitted: <span class="font-semibold text-slate-600 dark:text-gray-300"><?= $progress['total'] ?> days</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Allocated Company Details -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-industry mr-2 text-primary"></i> Company Attachment Placement</h3>
            
            <?php if (empty($allocation['company_name'])): ?>
                <div class="py-8 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-briefcase text-4xl mb-2"></i>
                    <p class="text-sm font-medium">No company placement confirmed yet.</p>
                    <p class="text-xs">Your supervisor or coordinator will assign your corporate placement once your documents are approved.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Company Name</span>
                            <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($allocation['company_name']) ?></p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Office Address</span>
                            <p class="text-sm text-slate-600 dark:text-gray-300 mt-0.5"><?= htmlspecialchars($allocation['company_address'] ?? 'Official address') ?></p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Contact Email</span>
                            <p class="text-sm text-primary hover:underline mt-0.5"><?= htmlspecialchars($allocation['company_email'] ?? 'info@partner.com') ?></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Corporate Supervisor</span>
                            <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($allocation['supervisor_name'] ?? 'Pending Assignment') ?></p>
                        </div>
                        <?php if (!empty($allocation['supervisor_phone'])): ?>
                            <div>
                                <span class="text-xs text-slate-400 uppercase font-semibold">Supervisor Phone</span>
                                <p class="text-sm text-slate-600 dark:text-gray-300 mt-0.5"><?= htmlspecialchars($allocation['supervisor_phone']) ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Placement Status</span>
                            <p class="text-xs font-bold text-success mt-0.5"><?= htmlspecialchars(ucfirst($studentProfile['document_status'] ?? 'Active')) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Grade Evaluation Status -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-graduation-cap mr-2 text-accent"></i> Final Performance Evaluation</h3>
            
            <?php if (empty($evaluation) || $evaluation['final_grade'] === 'Pending'): ?>
                <div class="py-8 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-award text-4xl mb-2"></i>
                    <p class="text-sm font-medium">Evaluation pending supervisor grading.</p>
                    <p class="text-xs">Your company supervisor will grade your logbooks and submit scores at the end of the internship.</p>
                </div>
            <?php else: ?>
                <div class="text-center space-y-4">
                    <div class="inline-flex w-20 h-20 rounded-full bg-success/10 text-success items-center justify-center font-extrabold text-4xl border border-success/20">
                        <?= htmlspecialchars($evaluation['final_grade']) ?>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase font-semibold block">Supervisor Mark</span>
                        <p class="text-xl font-bold text-slate-800 dark:text-white mt-0.5"><?= $evaluation['supervisor_score'] ?> / 100</p>
                    </div>
                    <div class="text-left bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Supervisor Remarks</span>
                        <p class="text-xs text-slate-600 dark:text-gray-300 mt-1"><?= htmlspecialchars($evaluation['supervisor_remarks']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
