<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Analytical Reports</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Compile real-time metrics, download data tables, and print verification audits.</p>
        </div>
        <div class="flex space-x-2 no-print">
            <button onclick="exportTableToCSV('report-table', 'siwes_<?= $reportType ?>_report.csv')" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-file-csv"></i> <span>Export to Excel/CSV</span>
            </button>
            <button onclick="window.print()" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 text-sm px-4 py-2.5 rounded-xl font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-print text-accent"></i> <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- Tab navigation -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-px no-print select-none">
        <a href="/siwes/coordinator/reports?type=allocations" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all <?= $reportType === 'allocations' ? 'bg-primary text-white' : 'text-slate-400 hover:text-slate-600' ?>">Allocations Report</a>
        <a href="/siwes/coordinator/reports?type=students" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all <?= $reportType === 'students' ? 'bg-primary text-white' : 'text-slate-400 hover:text-slate-600' ?>">Students Report</a>
        <a href="/siwes/coordinator/reports?type=companies" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all <?= $reportType === 'companies' ? 'bg-primary text-white' : 'text-slate-400 hover:text-slate-600' ?>">Companies capacity</a>
        <a href="/siwes/coordinator/reports?type=attendance" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all <?= $reportType === 'attendance' ? 'bg-primary text-white' : 'text-slate-400 hover:text-slate-600' ?>">Attendance Audit</a>
        <a href="/siwes/coordinator/reports?type=performance" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-all <?= $reportType === 'performance' ? 'bg-primary text-white' : 'text-slate-400 hover:text-slate-600' ?>">Evaluations Report</a>
    </div>

    <!-- Report Contents Paper -->
    <div class="bg-white text-black p-6 md:p-8 rounded-3xl border border-slate-200 shadow-soft print-card" id="report-container">
        <!-- University Letterhead (Only visible during print/pdf export) -->
        <div class="hidden print:block text-center border-b-2 border-slate-800 pb-4 mb-6">
            <h2 class="text-xl font-extrabold tracking-wide uppercase">STATE UNIVERSITY OF TECHNOLOGY</h2>
            <p class="text-xs uppercase font-bold tracking-widest text-slate-500 mt-1">SIWES COORDINATION UNIT</p>
            <p class="text-[10px] mt-0.5">Official System Audited Report</p>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold font-poppins text-slate-800 uppercase tracking-wide border-l-4 border-primary pl-3">
                <?= ucfirst($reportType) ?> Report Sheet
            </h2>
            <span class="text-xs text-slate-400 font-sans">Compiled: <?= date('Y-m-d H:i:s') ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="report-table">
                <!-- Allocations table -->
                <?php if ($reportType === 'allocations'): ?>
                    <thead>
                        <tr class="border-b-2 border-slate-200 text-slate-800 uppercase font-sans font-bold">
                            <th class="py-2.5">Matric No</th>
                            <th class="py-2.5">Student Name</th>
                            <th class="py-2.5">Dept</th>
                            <th class="py-2.5">Assigned Company</th>
                            <th class="py-2.5">Company Supervisor</th>
                            <th class="py-2.5">Lock Status</th>
                            <th class="py-2.5">Assigned By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 font-mono font-semibold"><?= htmlspecialchars($row['matric_no']) ?></td>
                                <td class="py-2 font-medium"><?= htmlspecialchars($row['student_name']) ?></td>
                                <td class="py-2 font-semibold"><?= htmlspecialchars($row['dept_code']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['supervisor_name'] ?? 'Unassigned') ?></td>
                                <td class="py-2"><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                                <td class="py-2 text-[10px] uppercase font-bold"><?= htmlspecialchars($row['allocated_by']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                <!-- Students table -->
                <?php elseif ($reportType === 'students'): ?>
                    <thead>
                        <tr class="border-b-2 border-slate-200 text-slate-800 uppercase font-sans font-bold">
                            <th class="py-2.5">Matric No</th>
                            <th class="py-2.5">Name</th>
                            <th class="py-2.5">Department</th>
                            <th class="py-2.5">Field Course</th>
                            <th class="py-2.5">Year</th>
                            <th class="py-2.5">Placement Status</th>
                            <th class="py-2.5">Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 font-mono font-semibold"><?= htmlspecialchars($row['matric_no']) ?></td>
                                <td class="py-2 font-medium"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['department_name']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['course_of_study']) ?></td>
                                <td class="py-2 text-center"><?= $row['year_of_study'] ?></td>
                                <td class="py-2 font-semibold"><?= htmlspecialchars(ucfirst($row['siwes_status'])) ?></td>
                                <td class="py-2 text-slate-500"><?= htmlspecialchars($row['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                <!-- Companies capacity -->
                <?php elseif ($reportType === 'companies'): ?>
                    <thead>
                        <tr class="border-b-2 border-slate-200 text-slate-800 uppercase font-sans font-bold">
                            <th class="py-2.5">Company Name</th>
                            <th class="py-2.5">Sector Sector</th>
                            <th class="py-2.5">Contact Person</th>
                            <th class="py-2.5">Contact Phone</th>
                            <th class="py-2.5 text-center">Star Rating</th>
                            <th class="py-2.5 text-center">Remaining slots</th>
                            <th class="py-2.5 text-center">Filled slots</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 font-medium"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['industry_category']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['contact_person']) ?></td>
                                <td class="py-2 font-mono"><?= htmlspecialchars($row['phone']) ?></td>
                                <td class="py-2 text-center font-bold text-amber-500"><?= $row['rating'] ?> &starf;</td>
                                <td class="py-2 text-center font-bold"><?= $row['available_slots'] ?></td>
                                <td class="py-2 text-center font-bold text-slate-400"><?= $row['filled_slots'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                <!-- Attendance audit -->
                <?php elseif ($reportType === 'attendance'): ?>
                    <thead>
                        <tr class="border-b-2 border-slate-200 text-slate-800 uppercase font-sans font-bold">
                            <th class="py-2.5">Matric No</th>
                            <th class="py-2.5">Student Name</th>
                            <th class="py-2.5">Dept</th>
                            <th class="py-2.5 text-center">Present Days</th>
                            <th class="py-2.5 text-center">Absent Days</th>
                            <th class="py-2.5 text-center">Progress Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 font-mono font-semibold"><?= htmlspecialchars($row['matric_no']) ?></td>
                                <td class="py-2 font-medium"><?= htmlspecialchars($row['student_name']) ?></td>
                                <td class="py-2 font-semibold"><?= htmlspecialchars($row['dept_code']) ?></td>
                                <td class="py-2 text-center font-bold text-success"><?= $row['present_days'] ?></td>
                                <td class="py-2 text-center font-bold text-danger"><?= $row['absent_days'] ?></td>
                                <td class="py-2 text-center font-bold"><?= round(($row['present_days'] / 96) * 100, 1) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                <!-- Evaluations and Grades -->
                <?php elseif ($reportType === 'performance'): ?>
                    <thead>
                        <tr class="border-b-2 border-slate-200 text-slate-800 uppercase font-sans font-bold">
                            <th class="py-2.5">Matric No</th>
                            <th class="py-2.5">Student Name</th>
                            <th class="py-2.5">Dept</th>
                            <th class="py-2.5">Company Placed</th>
                            <th class="py-2.5 text-center">Supervisor Mark</th>
                            <th class="py-2.5 text-center">Grade Letter</th>
                            <th class="py-2.5">Remarks Comments</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 font-mono font-semibold"><?= htmlspecialchars($row['matric_no']) ?></td>
                                <td class="py-2 font-medium"><?= htmlspecialchars($row['student_name']) ?></td>
                                <td class="py-2 font-semibold"><?= htmlspecialchars($row['dept_code']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td class="py-2 text-center font-bold"><?= $row['supervisor_score'] ?> / 100</td>
                                <td class="py-2 text-center font-extrabold text-success"><?= htmlspecialchars($row['final_grade']) ?></td>
                                <td class="py-2 text-xs italic max-w-xs truncate">"<?= htmlspecialchars($row['supervisor_remarks']) ?>"</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<script src="/siwes/public/assets/js/app.js"></script>
