<div class="space-y-8 animate-fade-in-up" x-data="{
    activeTab: '<?= htmlspecialchars($activeTab ?? 'departments') ?>',
    showAddDeptModal: false,
    showEditDeptModal: false,
    showAddLevelModal: false,
    showEditLevelModal: false,
    editDeptForm: {
        id: '',
        name: '',
        code: '',
        status: 'active',
        show_on_signup: 1
    },
    editLevelForm: {
        id: '',
        name: '',
        code: '',
        year_value: 4,
        status: 'active',
        show_on_signup: 1
    },
    openEditDept(d) {
        this.editDeptForm.id = d.id;
        this.editDeptForm.name = d.name;
        this.editDeptForm.code = d.code;
        this.editDeptForm.status = d.status || 'active';
        this.editDeptForm.show_on_signup = (d.show_on_signup == 1 || d.show_on_signup === null) ? 1 : 0;
        this.showEditDeptModal = true;
    },
    openEditLevel(lvl) {
        this.editLevelForm.id = lvl.id;
        this.editLevelForm.name = lvl.name;
        this.editLevelForm.code = lvl.code;
        this.editLevelForm.year_value = lvl.year_value || 4;
        this.editLevelForm.status = lvl.status || 'active';
        this.editLevelForm.show_on_signup = (lvl.show_on_signup == 1 || lvl.show_on_signup === null) ? 1 : 0;
        this.showEditLevelModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-poppins font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-primary"></i>
                <span>Departments & Academic Levels</span>
            </h1>
            <p class="text-xs md:text-sm text-slate-500 dark:text-gray-400 mt-1">Manage academic departments, class levels, and control their availability on the student registration portal.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button @click="showAddDeptModal = true" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-md shadow-primary/20">
                <i class="fa-solid fa-plus"></i>
                <span>Add Department</span>
            </button>
            <button @click="showAddLevelModal = true" class="bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-md">
                <i class="fa-solid fa-layer-group"></i>
                <span>Add Level</span>
            </button>
        </div>
    </div>

    <!-- Tab navigation -->
    <div class="flex space-x-3 border-b border-slate-200 dark:border-slate-800 pb-px">
        <button @click="activeTab = 'departments'" :class="activeTab === 'departments' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none flex items-center gap-2">
            <i class="fa-solid fa-building-columns"></i>
            <span>Departments</span>
            <span class="text-xs bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold"><?= count($departments) ?></span>
        </button>
        <button @click="activeTab = 'levels'" :class="activeTab === 'levels' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none flex items-center gap-2">
            <i class="fa-solid fa-layer-group"></i>
            <span>Academic Levels</span>
            <span class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full font-bold"><?= count($levels) ?></span>
        </button>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 1: DEPARTMENTS MANAGEMENT -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'departments'" class="space-y-6">
        <!-- Search Filter -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-5 shadow-soft">
            <form action="/siwes/coordinator/departments" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="tab" value="departments">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Search Department</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-9 pr-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Search by department name or code (e.g. Computer Science, CSC)...">
                    </div>
                </div>
                <div>
                    <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-white py-2 rounded-xl text-xs font-bold transition">Filter Departments</button>
                </div>
            </form>
        </div>

        <!-- Departments Table -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200 text-xs uppercase tracking-wider">
                            <th class="py-3 font-semibold">Code</th>
                            <th class="py-3 font-semibold">Department Name</th>
                            <th class="py-3 font-semibold">Status</th>
                            <th class="py-3 font-semibold text-center">Sign-Up Visibility</th>
                            <th class="py-3 font-semibold">Students</th>
                            <th class="py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($departments)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-building-columns text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-medium">No departments found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($departments as $d): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition">
                                    <!-- Code -->
                                    <td class="py-4">
                                        <span class="bg-primary/10 text-primary dark:text-accent font-bold px-2.5 py-1 rounded-lg text-xs font-mono">
                                            <?= htmlspecialchars($d['code']) ?>
                                        </span>
                                    </td>

                                    <!-- Name -->
                                    <td class="py-4">
                                        <p class="font-bold text-slate-800 dark:text-white leading-tight">
                                            <?= htmlspecialchars($d['name']) ?>
                                        </p>
                                        <span class="text-[11px] text-slate-400">Created: <?= date('M d, Y', strtotime($d['created_at'])) ?></span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4">
                                        <?php if (($d['status'] ?? 'active') === 'active'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-success/15 text-success">
                                                <i class="fa-solid fa-circle-check mr-1"></i> Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-gray-300">
                                                <i class="fa-solid fa-circle-pause mr-1"></i> Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Sign-Up Visibility Toggle -->
                                    <td class="py-4 text-center">
                                        <form action="/siwes/coordinator/department/toggle-signup" method="POST" class="inline-block">
                                            <input type="hidden" name="department_id" value="<?= $d['id'] ?>">
                                            <?php if (($d['show_on_signup'] ?? 1) == 1): ?>
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white transition" title="Click to hide from student sign-up page">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                    <span>Visible on Sign-Up</span>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-white transition" title="Click to display on student sign-up page">
                                                    <i class="fa-solid fa-eye-slash text-xs"></i>
                                                    <span>Hidden from Sign-Up</span>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>

                                    <!-- Students Count -->
                                    <td class="py-4 text-xs font-semibold text-slate-700 dark:text-gray-300">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                                            <i class="fa-solid fa-users text-[10px] text-slate-400 mr-1"></i> <?= $d['student_count'] ?> Students
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 text-right">
                                        <div class="inline-flex items-center space-x-1.5">
                                            <!-- Edit button -->
                                            <button @click="openEditDept(<?= htmlspecialchars(json_encode($d)) ?>)" class="px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-xl text-xs font-bold transition flex items-center space-x-1">
                                                <i class="fa-solid fa-pen-to-square"></i> <span>Edit</span>
                                            </button>

                                            <!-- Delete button -->
                                            <?php if ($d['student_count'] == 0 && count($departments) > 1): ?>
                                                <form action="/siwes/coordinator/department/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete department \'<?= htmlspecialchars(addslashes($d['name'])) ?>\'?');">
                                                    <input type="hidden" name="department_id" value="<?= $d['id'] ?>">
                                                    <button type="submit" class="px-2.5 py-1.5 bg-danger/10 text-danger hover:bg-danger hover:text-white rounded-xl text-xs font-bold transition" title="Delete Department">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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

    <!-- ======================================================== -->
    <!-- TAB 2: ACADEMIC LEVELS MANAGEMENT -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'levels'" class="space-y-6" style="display: none;">
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-primary"></i> Academic Class Levels
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Control available academic levels (e.g. 300 Level, 400 Level) and choose which ones are selectable during student account creation.</p>
                </div>
                <button @click="showAddLevelModal = true" class="bg-primary hover:bg-secondary text-white px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow-sm">
                    <i class="fa-solid fa-plus"></i> <span>Add New Level</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200 text-xs uppercase tracking-wider">
                            <th class="py-3 font-semibold">Level Code</th>
                            <th class="py-3 font-semibold">Level Name</th>
                            <th class="py-3 font-semibold">Year Value</th>
                            <th class="py-3 font-semibold">Status</th>
                            <th class="py-3 font-semibold text-center">Sign-Up Visibility</th>
                            <th class="py-3 font-semibold">Enrolled Students</th>
                            <th class="py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($levels)): ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-layer-group text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-medium">No academic levels configured.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($levels as $lvl): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition">
                                    <!-- Code -->
                                    <td class="py-4">
                                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold px-2.5 py-1 rounded-lg text-xs font-mono">
                                            <?= htmlspecialchars($lvl['code']) ?>
                                        </span>
                                    </td>

                                    <!-- Name -->
                                    <td class="py-4 font-bold text-slate-800 dark:text-white">
                                        <?= htmlspecialchars($lvl['name']) ?>
                                    </td>

                                    <!-- Year Value -->
                                    <td class="py-4 text-xs font-semibold text-slate-600 dark:text-gray-300">
                                        Year <?= (int)$lvl['year_value'] ?>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4">
                                        <?php if (($lvl['status'] ?? 'active') === 'active'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-success/15 text-success">
                                                <i class="fa-solid fa-circle-check mr-1"></i> Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-gray-300">
                                                <i class="fa-solid fa-circle-pause mr-1"></i> Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Sign-Up Visibility Toggle -->
                                    <td class="py-4 text-center">
                                        <form action="/siwes/coordinator/level/toggle-signup" method="POST" class="inline-block">
                                            <input type="hidden" name="level_id" value="<?= $lvl['id'] ?>">
                                            <?php if (($lvl['show_on_signup'] ?? 1) == 1): ?>
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white transition" title="Click to hide from student sign-up page">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                    <span>Visible on Sign-Up</span>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-white transition" title="Click to display on student sign-up page">
                                                    <i class="fa-solid fa-eye-slash text-xs"></i>
                                                    <span>Hidden from Sign-Up</span>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>

                                    <!-- Student Count -->
                                    <td class="py-4 text-xs font-semibold text-slate-700 dark:text-gray-300">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                                            <?= $lvl['student_count'] ?> Students
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 text-right">
                                        <div class="inline-flex items-center space-x-1.5">
                                            <!-- Edit button -->
                                            <button @click="openEditLevel(<?= htmlspecialchars(json_encode($lvl)) ?>)" class="px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-xl text-xs font-bold transition flex items-center space-x-1">
                                                <i class="fa-solid fa-pen-to-square"></i> <span>Edit</span>
                                            </button>

                                            <!-- Delete button -->
                                            <?php if ($lvl['student_count'] == 0 && count($levels) > 1): ?>
                                                <form action="/siwes/coordinator/level/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete academic level \'<?= htmlspecialchars(addslashes($lvl['name'])) ?>\'?');">
                                                    <input type="hidden" name="level_id" value="<?= $lvl['id'] ?>">
                                                    <button type="submit" class="px-2.5 py-1.5 bg-danger/10 text-danger hover:bg-danger hover:text-white rounded-xl text-xs font-bold transition" title="Delete Academic Level">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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

    <!-- ======================================================== -->
    <!-- MODAL: Add Department -->
    <!-- ======================================================== -->
    <div x-show="showAddDeptModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-2xl space-y-5" @click.away="showAddDeptModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-primary"></i> Add New Department
                </h3>
                <button @click="showAddDeptModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/department/add" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Name</label>
                    <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. Electrical & Electronic Engineering" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Code</label>
                    <input type="text" name="code" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm uppercase focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. EEE" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700/60">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="show_on_signup" value="1" checked class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-slate-700 dark:text-gray-300">Display this Department on Student Sign-Up Page</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6.5">If checked, students will see this department in the dropdown when registering an account.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Create Department</button>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: Edit Department -->
    <!-- ======================================================== -->
    <div x-show="showEditDeptModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-2xl space-y-5" @click.away="showEditDeptModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Department Details
                </h3>
                <button @click="showEditDeptModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/department/update" method="POST" class="space-y-4">
                <input type="hidden" name="department_id" :value="editDeptForm.id">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Name</label>
                    <input type="text" name="name" :value="editDeptForm.name" @input="editDeptForm.name = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Code</label>
                    <input type="text" name="code" :value="editDeptForm.code" @input="editDeptForm.code = $event.target.value.toUpperCase()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm uppercase focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                    <select name="status" :value="editDeptForm.status" @change="editDeptForm.status = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700/60">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="show_on_signup" value="1" :checked="editDeptForm.show_on_signup == 1" @change="editDeptForm.show_on_signup = $event.target.checked ? 1 : 0" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-slate-700 dark:text-gray-300">Display this Department on Student Sign-Up Page</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6.5">Control whether prospective students can choose this department when signing up.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: Add Academic Level -->
    <!-- ======================================================== -->
    <div x-show="showAddLevelModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-2xl space-y-5" @click.away="showAddLevelModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-primary"></i> Add Academic Level
                </h3>
                <button @click="showAddLevelModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/level/add" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Level Name (Display Label)</label>
                    <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. 400 Level or ND II" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Level Code</label>
                        <input type="text" name="code" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. 400" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Year Value</label>
                        <input type="number" name="year_value" min="1" max="8" value="4" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700/60">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="show_on_signup" value="1" checked class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-slate-700 dark:text-gray-300">Display this Level on Student Sign-Up Page</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6.5">If checked, students will see this level as an option when creating their account.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Create Academic Level</button>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: Edit Academic Level -->
    <!-- ======================================================== -->
    <div x-show="showEditLevelModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-2xl space-y-5" @click.away="showEditLevelModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Academic Level
                </h3>
                <button @click="showEditLevelModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/level/update" method="POST" class="space-y-4">
                <input type="hidden" name="level_id" :value="editLevelForm.id">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Level Name</label>
                    <input type="text" name="name" :value="editLevelForm.name" @input="editLevelForm.name = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Level Code</label>
                        <input type="text" name="code" :value="editLevelForm.code" @input="editLevelForm.code = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Year Value</label>
                        <input type="number" name="year_value" min="1" max="8" :value="editLevelForm.year_value" @input="editLevelForm.year_value = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                    <select name="status" :value="editLevelForm.status" @change="editLevelForm.status = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700/60">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="show_on_signup" value="1" :checked="editLevelForm.show_on_signup == 1" @change="editLevelForm.show_on_signup = $event.target.checked ? 1 : 0" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-slate-700 dark:text-gray-300">Display this Level on Student Sign-Up Page</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1 pl-6.5">Control whether prospective students can choose this level when signing up.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Save Changes</button>
            </form>
        </div>
    </div>
</div>
