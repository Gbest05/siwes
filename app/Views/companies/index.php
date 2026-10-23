<?php
use App\Core\Auth;
$role = Auth::getRole();
?>
<div class="space-y-8 animate-fade-in-up" x-data="{ showAddCompanyModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Partner Directory</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">View affiliated corporate organizations, create companies, manage slots, and verify partnerships.</p>
        </div>
        <div class="flex items-center space-x-2 no-print">
            <?php if (in_array($role, ['admin', 'coordinator'])): ?>
                <button @click="showAddCompanyModal = true" class="bg-primary hover:bg-secondary text-white text-xs px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-plus text-xs"></i> <span>Add Company</span>
                </button>
            <?php endif; ?>
            <button onclick="exportTableToCSV('companies-table', 'siwes_companies_list.csv')" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 text-sm px-4 py-2.5 rounded-xl font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-file-export text-accent"></i> <span>Export CSV</span>
            </button>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-5 shadow-soft">
        <form action="/siwes/coordinator/companies" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Search Organizations</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-9 pr-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Company name or location...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Industry Sector</label>
                <select name="category" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">All Sectors</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['industry_category'] ?>" <?= $filters['category'] === $cat['industry_category'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['industry_category']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-white py-2 rounded-xl text-xs font-bold transition">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Companies Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400" id="companies-table">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Company Name</th>
                        <th class="py-3 font-semibold">Sector Category</th>
                        <th class="py-3 font-semibold">Address</th>
                        <th class="py-3 font-semibold">Rating</th>
                        <th class="py-3 font-semibold">Slots Available</th>
                        <?php if ($role !== 'student'): ?>
                            <th class="py-3 font-semibold">Status</th>
                            <th class="py-3 font-semibold text-right">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($companies)): ?>
                        <tr>
                            <td colspan="<?= $role !== 'student' ? '7' : '5' ?>" class="py-6 text-center text-slate-400">No organizations found matching the criteria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($companies as $c): ?>
                            <tr>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($c['name']) ?></td>
                                <td class="py-3 text-xs font-semibold text-slate-600 dark:text-gray-300"><?= htmlspecialchars($c['industry_category']) ?></td>
                                <td class="py-3 text-xs max-w-xs truncate"><?= htmlspecialchars($c['address']) ?></td>
                                <td class="py-3 text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-<?= $i <= $c['rating'] ? 'solid' : 'regular' ?> fa-star text-xs"></i>
                                    <?php endfor; ?>
                                </td>
                                <td class="py-3 font-bold text-slate-700 dark:text-gray-200"><?= $c['available_slots'] ?> slots</td>
                                <?php if ($role !== 'student'): ?>
                                    <td class="py-3">
                                        <?php if ($c['status'] === 'verified'): ?>
                                            <span class="text-success font-semibold text-xs"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                        <?php elseif ($c['status'] === 'rejected'): ?>
                                            <span class="text-danger font-semibold text-xs"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                        <?php else: ?>
                                            <span class="text-warning font-semibold text-xs"><i class="fa-solid fa-circle-exclamation animate-pulse"></i> Pending Review</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="inline-flex space-x-1 justify-end">
                                            <!-- Verification/Suspend Toggle -->
                                            <?php if ($c['status'] === 'verified'): ?>
                                                <form action="/siwes/coordinator/company/verify" method="POST">
                                                    <input type="hidden" name="company_id" value="<?= $c['id'] ?>">
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="bg-warning/10 hover:bg-warning text-warning hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition">Suspend</button>
                                                </form>
                                            <?php else: ?>
                                                <form action="/siwes/coordinator/company/verify" method="POST">
                                                    <input type="hidden" name="company_id" value="<?= $c['id'] ?>">
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" class="bg-success/10 hover:bg-success text-success hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition">
                                                        <?= $c['status'] === 'rejected' ? 'Activate' : 'Verify' ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <!-- Permanent Delete -->
                                            <form action="/siwes/coordinator/company/delete" method="POST" onsubmit="return confirm('WARNING: Permanently delete this company? All linked supervisor records will be affected.');">
                                                <input type="hidden" name="company_id" value="<?= $c['id'] ?>">
                                                <button type="submit" class="bg-danger/10 hover:bg-danger text-danger hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add New Company -->
    <?php if (in_array($role, ['admin', 'coordinator'])): ?>
        <div x-show="showAddCompanyModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
            <div class="bg-white dark:bg-darkCard w-full max-w-lg rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-xl space-y-6" @click.away="showAddCompanyModal = false">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base">Add New Partner Company</h3>
                    <button @click="showAddCompanyModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <form action="/siwes/coordinator/company/add" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company Name</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Google Nigeria">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Official Email</label>
                            <input type="email" name="email" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="siwes@google.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Phone Number</label>
                            <input type="text" name="phone" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="+234...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Contact Person</label>
                            <input type="text" name="contact_person" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Mr. Babatunde Lawal">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Physical Address / Location</label>
                        <input type="text" name="address" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Kingsway Tower, Ikoyi, Lagos">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Industry Sector</label>
                            <input type="text" name="industry_category" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Software & Cloud Services">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Available Attachment Slots</label>
                            <input type="number" name="available_slots" min="1" max="100" value="5" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Specializations (Comma separated)</label>
                        <input type="text" name="specializations" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Web Development, Cyber Security, Cloud">
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Register Partner Company</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="/siwes/public/assets/js/app.js"></script>
