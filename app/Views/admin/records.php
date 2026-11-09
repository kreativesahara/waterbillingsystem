<div class="space-y-6">
    <!-- Header with Filters & Export Tools -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tenancy Water Billing Records</h1>
            <p class="text-sm text-slate-500">Historical billing ledger, payment statuses, and print registers.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="openBuildingBroadcastModal('<?= htmlspecialchars($filterMonth) ?>')" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold shadow-sm shadow-emerald-600/20 transition">
                <i data-lucide="megaphone" class="w-4 h-4"></i>
                <span>Building Broadcast</span>
            </button>
            <button onclick="window.print()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold shadow-sm transition">
                <i data-lucide="printer" class="w-4 h-4 text-slate-500"></i>
                <span>Print Register</span>
            </button>
            <button onclick="exportTableToCSV('water_billing_records.csv')" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold shadow-sm transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                <span>Export CSV</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm no-print">
        <form action="/admin/records" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-2">
                <label for="unit" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Unit</label>
                <select name="unit" id="unit" onchange="this.form.submit()" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                    <option value="">All Units</option>
                    <?php foreach ($units as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= $filterUnit === (int)$u['id'] ? 'selected' : '' ?>>House <?= htmlspecialchars($u['house_number']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search Unit, Name, Phone..."
                        class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label for="month" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Billing Month</label>
                <select name="month" id="month" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                    <option value="">All Months</option>
                    <?php foreach ($months as $m): ?>
                        <option value="<?= $m ?>" <?= $filterMonth === $m ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Payment Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                    <option value="">All Statuses</option>
                    <option value="PAID" <?= $filterStatus === 'PAID' ? 'selected' : '' ?>>Paid</option>
                    <option value="PENDING" <?= $filterStatus === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                    <option value="PARTIAL" <?= $filterStatus === 'PARTIAL' ? 'selected' : '' ?>>Partial</option>
                    <option value="OVERDUE" <?= $filterStatus === 'OVERDUE' ? 'selected' : '' ?>>Overdue</option>
                </select>
            </div>

            <div class="sm:col-span-3 flex space-x-2">
                <button type="submit" class="w-full py-2 px-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-semibold transition">
                    Filter
                </button>
                <a href="/admin/records" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-semibold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Print Title (Only shows in print preview) -->
    <div class="hidden print-only mb-6">
        <h2 class="text-xl font-bold">Classic Heights Apartments - Water Billing Schedule</h2>
        <p class="text-xs text-slate-500">Generated on <?= date('Y-m-d H:i:s') ?></p>
    </div>

    <!-- Records Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="recordsTable">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5">Invoice #</th>
                        <th class="px-4 py-3.5">Unit</th>
                        <th class="px-4 py-3.5">Tenant</th>
                        <th class="px-4 py-3.5">Month</th>
                        <th class="px-4 py-3.5 text-right">Prev Dial</th>
                        <th class="px-4 py-3.5 text-right">Curr Dial</th>
                        <th class="px-4 py-3.5 text-right">Units (m&sup3;)</th>
                        <th class="px-4 py-3.5 text-right">Water KSh</th>
                        <th class="px-4 py-3.5 text-right">Rent</th>
                        <th class="px-4 py-3.5 text-right">Arrears</th>
                        <th class="px-4 py-3.5 text-right">Total Bill</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center no-print">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="13" class="px-4 py-10 text-center text-slate-400">
                                <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                No billing records match your filter criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs font-bold text-slate-700">
                                    <?= htmlspecialchars($r['invoice_number']) ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900">
                                    House <?= htmlspecialchars($r['house_number']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <?= htmlspecialchars($r['firstname'] . ' ' . $r['lastname']) ?>
                                    <span class="block text-xs font-mono text-slate-400"><?= htmlspecialchars($r['phonenumber']) ?></span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600 font-mono">
                                    <?= htmlspecialchars($r['billing_month']) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-slate-500">
                                    <?= number_format($r['previous_reading'], 1) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-slate-700">
                                    <?= number_format($r['current_reading'], 1) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs font-semibold text-slate-900">
                                    <?= number_format($r['units_consumed'], 1) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-slate-700">
                                    <?= number_format($r['water_cost'], 2) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-slate-500">
                                    <?= number_format($r['meter_rent'], 2) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-amber-600 font-medium">
                                    <?= number_format($r['bbd'], 2) ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs font-bold text-slate-900">
                                    <?= number_format($r['total_bill'], 2) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($r['status'] === 'PAID'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            Paid
                                        </span>
                                    <?php elseif ($r['status'] === 'PARTIAL'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            Partial
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center no-print">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button onclick="openShareModal(<?= $r['id'] ?>)" title="Quick Actions: WhatsApp, SMS, Copy"
                                            class="p-1.5 text-slate-500 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition">
                                            <i data-lucide="share-2" class="w-4 h-4"></i>
                                        </button>
                                        <a href="/tenant/statement?id=<?= $r['id'] ?>" target="_blank" title="View Statement"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
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

<?php include __DIR__ . '/partials/share_modals.php'; ?>

<script>
    function exportTableToCSV(filename) {
        let csv = [];
        const rows = document.querySelectorAll("#recordsTable tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length - 1; j++) { // exclude last action column
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }

        const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        const downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
