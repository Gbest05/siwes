<div class="space-y-8 animate-fade-in-up" x-data="{
    showAddModal: false,
    showEditModal: false,
    editForm: {
        id: '',
        name: '',
        code: ''
    },
    openEdit(dept) {
        this.editForm.id = dept.id;
        this.editForm.name = dept.name;
        this.editForm.code = dept.code;
        this.showEditModal = true;
    }
}">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Academic Departments</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Add, view, edit, or manage the list of academic departments in the institution.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary hover:bg-secondary text-white text-xs px-5 py-2.5 rounded-xl font-bold transition flex items-center space-x-2">
            <i class="fa-solid fa-plus text-xs"></i> <span>Add Department</span>
        </button>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-5 shadow-soft">
        <form action="/siwes/coordinator/departments" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Search Department Name/Code</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-9 pr-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Search by name or code (e.g. Computer Science, CSC)...">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-white py-2 rounded-xl text-xs font-bold transition">Filter List</button>
            </div>
        </form>
    </div>

    <!-- Departments Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">ID</th>
                        <th class="py-3 font-semibold">Department Code</th>
                        <th class="py-3 font-semibold">Department Name</th>
                        <th class="py-3 font-semibold">Registered Students</th>
                        <th class="py-3 font-semibold">Created Date</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($departments)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">No departments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td class="py-3 text-xs font-mono text-slate-400">#<?= $d['id'] ?></td>
                                <td class="py-3"><span class="bg-primary/10 text-primary font-bold px-2 py-0.5 rounded-lg text-xs"><?= htmlspecialchars($d['code']) ?></span></td>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($d['name']) ?></td>
                                <td class="py-3 font-bold text-slate-700 dark:text-gray-200"><?= $d['student_count'] ?> students</td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($d['created_at']) ?></td>
                                <td class="py-3 text-right">
                                    <div class="inline-flex space-x-1 justify-end">
                                        <!-- Edit button -->
                                        <button @click="openEdit(<?= htmlspecialchars(json_encode($d)) ?>)" class="px-2.5 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg text-xs font-bold transition">
                                            Edit
                                        </button>

                                        <!-- Delete button -->
                                        <form action="/siwes/coordinator/department/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                            <input type="hidden" name="department_id" value="<?= $d['id'] ?>">
                                            <button type="submit" class="px-2.5 py-1.5 bg-danger/10 text-danger hover:bg-danger hover:text-white rounded-lg text-xs font-bold transition" <?= $d['student_count'] > 0 ? 'disabled title="Cannot delete department with active students"' : '' ?>>
                                                Delete
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
    </div>

    <!-- Modal: Add Department -->
    <div x-show="showAddModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-xl space-y-6" @click.away="showAddModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base">Add New Department</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/department/add" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Name</label>
                    <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. Computer Science" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Code (Short code)</label>
                    <input type="text" name="code" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. CSC" required>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Create Department</button>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Department -->
    <div x-show="showEditModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-xl space-y-6" @click.away="showEditModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base">Edit Department Details</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/department/update" method="POST" class="space-y-4">
                <input type="hidden" name="department_id" :value="editForm.id">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Name</label>
                    <input type="text" name="name" :value="editForm.name" @input="editForm.name = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Department Code</label>
                    <input type="text" name="code" :value="editForm.code" @input="editForm.code = $event.target.value" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Save Changes</button>
            </form>
        </div>
    </div>
</div>
