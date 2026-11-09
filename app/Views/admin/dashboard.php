<div class="space-y-6">
    <!-- Welcome Header & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Admin Executive Dashboard</h1>
            <p class="text-sm text-slate-500">Overview of billing performance, collection rates, and water consumption for <?= date('F Y') ?>.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/admin/meter-entry" class="inline-flex items-center space-x-2 bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-md shadow-sky-500/20 transition">
                <i data-lucide="gauge" class="w-4 h-4"></i>
                <span>Record Meter Reading</span>
            </a>
            <a href="/admin/records" class="inline-flex items-center space-x-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-semibold text-sm px-3.5 py-2.5 rounded-xl shadow-sm transition">
                <i data-lucide="download" class="w-4 h-4 text-slate-500"></i>
                <span>Export</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Billed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Billed This Month</span>
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900 font-mono">
                    KSh <?= number_format($metrics['total_billed'], 2) ?>
                </div>
                <div class="mt-1 flex items-center text-xs text-slate-500">
                    <span>Billing cycle: <?= htmlspecialchars($metrics['current_month']) ?></span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Collected & Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Collected Revenue</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="badge-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-emerald-600 font-mono">
                    KSh <?= number_format($metrics['total_collected'], 2) ?>
                </div>
                <!-- Progress Bar -->
                <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= min(100, $metrics['collection_rate']) ?>%"></div>
                </div>
                <div class="mt-1.5 text-xs text-slate-500 flex justify-between font-medium">
                    <span>Collection Rate</span>
                    <span class="font-bold text-slate-700"><?= $metrics['collection_rate'] ?>%</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Outstanding Arrears -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Unpaid Arrears</span>
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-rose-600 font-mono">
                    KSh <?= number_format($metrics['outstanding_arrears'], 2) ?>
                </div>
                <div class="mt-1 flex items-center text-xs text-slate-500">
                    <span>Cumulative unpaid balance</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Water Consumed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Water Volume</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center">
                    <i data-lucide="droplet" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900 font-mono">
                    <?= number_format($metrics['total_units_consumed'], 1) ?> <span class="text-sm font-sans font-medium text-slate-500">m&sup3;</span>
                </div>
                <div class="mt-1 flex items-center text-xs text-slate-500">
                    <span><?= $metrics['occupied_houses'] ?> of <?= $metrics['total_houses'] ?> Units Occupied</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Billing Activity Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Invoices & Readings</h3>
                <p class="text-xs text-slate-500">Latest meter updates and billing dispatch status</p>
            </div>
            <a href="/admin/records" class="text-xs font-semibold text-sky-600 hover:text-sky-700 flex items-center space-x-1">
                <span>View Full Register</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Invoice #</th>
                        <th class="px-5 py-3.5">Unit</th>
                        <th class="px-5 py-3.5">Tenant Name</th>
                        <th class="px-5 py-3.5">Reading Date</th>
                        <th class="px-5 py-3.5 text-right">Units (m&sup3;)</th>
                        <th class="px-5 py-3.5 text-right">Total Bill</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Quick Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentInvoices)): ?>
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                No bills recorded yet. Record the first meter reading above!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentInvoices as $inv): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-700">
                                    <?= htmlspecialchars($inv['invoice_number']) ?>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    House <?= htmlspecialchars($inv['house_number']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-slate-700">
                                    <?= htmlspecialchars($inv['firstname'] . ' ' . $inv['lastname']) ?>
                                    <span class="block text-xs font-mono text-slate-400"><?= htmlspecialchars($inv['phonenumber']) ?></span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 text-xs">
                                    <?= htmlspecialchars($inv['date_of_reading']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono font-medium text-slate-700">
                                    <?= number_format($inv['units_consumed'], 1) ?>
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                    KSh <?= number_format($inv['total_bill'], 2) ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if ($inv['status'] === 'PAID'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Paid
                                        </span>
                                    <?php elseif ($inv['status'] === 'PARTIAL'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            Partial
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <button onclick="dispatchSms(<?= $inv['id'] ?>, '<?= htmlspecialchars($inv['firstname']) ?>')"
                                        class="inline-flex items-center space-x-1 text-xs font-semibold text-sky-600 hover:text-sky-800 bg-sky-50 hover:bg-sky-100 px-2.5 py-1 rounded-lg transition">
                                        <i data-lucide="send" class="w-3 h-3"></i>
                                        <span>SMS Bill</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function dispatchSms(invoiceId, tenantName) {
        Swal.fire({
            title: 'Dispatch Bill SMS',
            text: `Send official M-Pesa bill notification SMS to ${tenantName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Send SMS',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0284c7',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/api/send-sms', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({invoice_id: invoiceId})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'SMS Sent Successfully!',
                            text: data.message,
                            confirmButtonColor: '#0284c7'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Send SMS',
                            text: data.error || 'Unknown error',
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({icon: 'error', title: 'Network Error', text: err.message});
                });
            }
        });
    }
</script>
