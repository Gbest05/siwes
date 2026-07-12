<div class="space-y-8 animate-fade-in-up" x-data="{ 
    showImportModal: false, 
    showEditModal: false, 
    editForm: { 
        id: '', 
        matric_no: '', 
        first_name: '', 
        last_name: '', 
        email: '', 
        department_id: '', 
        course_of_study: '', 
        year_of_study: '', 
        phone: '' 
    } 
}">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Student Management</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">View student attachment profiles, load new batches, and coordinate status audits.</p>
        </div>
        <div class="flex space-x-2 no-print">
            <button @click="showImportModal = true" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-file-import"></i> <span>Bulk CSV Import</span>
            </button>
            <button onclick="exportTableToCSV('students-table', 'siwes_students_list.csv')" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 text-sm px-4 py-2.5 rounded-xl font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-file-export text-accent"></i> <span>Export CSV</span>
            </button>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-5 shadow-soft">
        <form action="/siwes/coordinator/students" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Search Students</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-9 pr-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Matric no or name...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Department</label>
                <select name="department" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= $filters['department'] == $dept['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">SIWES Status</label>
                <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">All Placements</option>
                    <option value="unallocated" <?= $filters['status'] === 'unallocated' ? 'selected' : '' ?>>Unallocated</option>
                    <option value="allocated" <?= $filters['status'] === 'allocated' ? 'selected' : '' ?>>Allocated</option>
                    <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-white py-2 rounded-xl text-xs font-bold transition">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400" id="students-table">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Matric No</th>
                        <th class="py-3 font-semibold">Student Name</th>
                        <th class="py-3 font-semibold">Department</th>
                        <th class="py-3 font-semibold">Field Course</th>
                        <th class="py-3 font-semibold">SIWES Status</th>
                        <th class="py-3 font-semibold">Account Status</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">No student records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td class="py-3 font-mono text-xs font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($s['matric_no']) ?></td>
                                <td class="py-3 font-medium"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($s['dept_code']) ?></td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($s['course_of_study']) ?></td>
                                <td class="py-3">
                                    <?php if ($s['siwes_status'] === 'unallocated'): ?>
                                        <span class="bg-warning/10 text-warning px-2.5 py-0.5 rounded-full text-xs font-semibold">Unallocated</span>
                                    <?php elseif ($s['siwes_status'] === 'allocated'): ?>
                                        <span class="bg-success/10 text-success px-2.5 py-0.5 rounded-full text-xs font-semibold">Allocated</span>
                                    <?php else: ?>
                                        <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-xs font-semibold">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-xs">
                                    <?php if ($s['account_status'] === 'active'): ?>
                                        <span class="text-success font-semibold"><i class="fa-solid fa-circle text-[8px] mr-1"></i> Active</span>
                                    <?php else: ?>
                                        <span class="text-danger font-semibold"><i class="fa-solid fa-circle text-[8px] mr-1"></i> Deactivated</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="inline-flex space-x-1 items-center">
                                        <button @click="showEditModal = true; 
                                                        editForm.id = '<?= $s['id'] ?>'; 
                                                        editForm.matric_no = '<?= htmlspecialchars($s['matric_no'], ENT_QUOTES) ?>';
                                                        editForm.first_name = '<?= htmlspecialchars($s['first_name'], ENT_QUOTES) ?>';
                                                        editForm.last_name = '<?= htmlspecialchars($s['last_name'], ENT_QUOTES) ?>';
                                                        editForm.email = '<?= htmlspecialchars($s['email'], ENT_QUOTES) ?>';
                                                        editForm.department_id = '<?= $s['department_id'] ?>';
                                                        editForm.course_of_study = '<?= htmlspecialchars($s['course_of_study'], ENT_QUOTES) ?>';
                                                        editForm.year_of_study = '<?= $s['year_of_study'] ?>';
                                                        editForm.phone = '<?= htmlspecialchars($s['phone'] ?? '', ENT_QUOTES) ?>';" 
                                                class="px-2.5 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg text-xs font-bold transition">
                                            Edit
                                        </button>
                                        <form action="/siwes/coordinator/student/deactivate" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $s['user_id'] ?>">
                                            <input type="hidden" name="status" value="<?= $s['account_status'] === 'active' ? 'inactive' : 'active' ?>">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition <?= $s['account_status'] === 'active' ? 'bg-danger/10 text-danger hover:bg-danger hover:text-white' : 'bg-success/10 text-success hover:bg-success hover:text-white' ?>">
                                                <?= $s['account_status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="flex justify-between items-center mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 no-print">
                <span class="text-xs text-slate-400">Showing page <?= $pagination['current'] ?> of <?= $pagination['total'] ?> (Total: <?= $pagination['total_items'] ?> students)</span>
                <div class="inline-flex space-x-1">
                    <a href="/siwes/coordinator/students?page=<?= $pagination['current'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&department=<?= urlencode($filters['department']) ?>&status=<?= urlencode($filters['status']) ?>" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs font-semibold hover:bg-slate-100 transition <?= $pagination['current'] <= 1 ? 'pointer-events-none opacity-50' : '' ?>">&laquo; Prev</a>
                    <a href="/siwes/coordinator/students?page=<?= $pagination['current'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&department=<?= urlencode($filters['department']) ?>&status=<?= urlencode($filters['status']) ?>" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs font-semibold hover:bg-slate-100 transition <?= $pagination['current'] >= $pagination['total'] ? 'pointer-events-none opacity-50' : '' ?>">Next &raquo;</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Import CSV Modal -->
    <div x-show="showImportModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl space-y-6" @click.away="showImportModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Bulk Import Student List</h3>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form action="/siwes/coordinator/student/import" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 text-xs text-slate-500 space-y-1">
                    <span class="font-bold block uppercase text-[10px]">CSV Template Format Requirements</span>
                    <span>Column 1: Username</span><br>
                    <span>Column 2: Email Address</span><br>
                    <span>Column 3: Matric Number (will be used as default password)</span><br>
                    <span>Column 4: First Name</span><br>
                    <span>Column 5: Last Name</span><br>
                    <span>Column 6: Department Code (e.g. CSC, EEE)</span><br>
                    <span>Column 7: Course of Study</span><br>
                    <span>Column 8: Year of Study (e.g. 4)</span><br>
                    <span>Column 9: Phone Number</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select CSV file</label>
                    <input type="file" name="csv_file" accept=".csv" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Start Import Batch</button>
            </form>
        </div>
    </div>
    <!-- Edit Student Modal -->
    <div x-show="showEditModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition style="display: none;">
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="showEditModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Student Details</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form action="/siwes/coordinator/student/update" method="POST" class="space-y-4">
                <input type="hidden" name="student_id" :value="editForm.id">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">First Name</label>
                        <input type="text" name="first_name" :value="editForm.first_name" @input="editForm.first_name = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Last Name</label>
                        <input type="text" name="last_name" :value="editForm.last_name" @input="editForm.last_name = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                        <input type="email" name="email" :value="editForm.email" @input="editForm.email = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Phone Number</label>
                        <input type="text" name="phone" :value="editForm.phone" @input="editForm.phone = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1 font-poppins text-accent">Matric Number</label>
                        <input type="text" name="matric_no" :value="editForm.matric_no" @input="editForm.matric_no = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1 font-poppins text-accent">Department</label>
                        <select name="department_id" :value="editForm.department_id" @change="editForm.department_id = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?> (<?= htmlspecialchars($dept['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1 font-poppins text-accent">Course of Study</label>
                        <input type="text" name="course_of_study" :value="editForm.course_of_study" @input="editForm.course_of_study = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1 font-poppins text-accent">Year of Study</label>
                        <select name="year_of_study" :value="editForm.year_of_study" @change="editForm.year_of_study = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Save Changes</button>
            </form>
        </div>
    </div>
</div>
<script src="/siwes/public/assets/js/app.js"></script>
