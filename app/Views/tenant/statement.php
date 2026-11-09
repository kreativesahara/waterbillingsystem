<div class="max-w-2xl mx-auto space-y-4">
    <!-- Action Bar (hidden when printing) -->
    <div class="flex items-center justify-between no-print">
        <a href="/tenant/dashboard" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 px-3 py-2 rounded-xl shadow-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Dashboard</span>
        </a>
        <button onclick="window.print()" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 px-4 py-2 rounded-xl shadow-sm transition">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print / Save as PDF</span>
        </button>
    </div>

    <!-- Official Statement Canvas -->
    <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-lg text-slate-800 space-y-6">
        <!-- Letterhead Header -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center">
                        <i data-lucide="droplet" class="w-5 h-5 fill-white"></i>
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-900">Classic Heights</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Water Management & Tenancy Utility Department</p>
                <p class="text-xs text-slate-500">Kilimani, Nairobi, Kenya</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider <?= $invoice['status'] === 'PAID' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                    <?= $invoice['status'] ?>
                </span>
                <h2 class="text-lg font-black text-slate-900 mt-2 font-mono"><?= htmlspecialchars($invoice['invoice_number']) ?></h2>
                <span class="text-xs text-slate-400 block">Date: <?= htmlspecialchars($invoice['date_of_reading']) ?></span>
            </div>
        </div>

        <!-- Billed To & Meter Info -->
        <div class="grid grid-cols-2 gap-4 text-xs py-2">
            <div>
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Billed To</span>
                <span class="font-bold text-base text-slate-900 block"><?= htmlspecialchars($invoice['firstname'] . ' ' . $invoice['lastname']) ?></span>
                <span class="text-slate-600 block">House #<?= htmlspecialchars($invoice['house_number']) ?>, Classic Heights</span>
                <span class="font-mono text-slate-500 block">Phone: <?= htmlspecialchars($invoice['phonenumber']) ?></span>
            </div>
            <div class="text-right">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Meter & Period</span>
                <span class="font-mono font-bold text-slate-800 block">Meter: <?= htmlspecialchars($invoice['meter_number']) ?></span>
                <span class="text-slate-600 block">Period: <?= htmlspecialchars($invoice['billing_month']) ?></span>
                <span class="text-slate-500 block">Due Date: <strong class="text-slate-800"><?= htmlspecialchars($invoice['due_date']) ?></strong></span>
            </div>
        </div>

        <!-- Meter Dials Table -->
        <div class="rounded-xl border border-slate-200 overflow-hidden text-xs">
            <table class="w-full text-center">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3">Previous Dial</th>
                        <th class="py-2.5 px-3">Current Dial</th>
                        <th class="py-2.5 px-3 bg-sky-50 text-sky-800">Units Consumed</th>
                        <th class="py-2.5 px-3">Tariff Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-x divide-slate-100 font-mono text-sm">
                    <tr>
                        <td class="py-3 px-3 text-slate-600"><?= number_format($invoice['previous_reading'], 2) ?></td>
                        <td class="py-3 px-3 font-bold text-slate-800"><?= number_format($invoice['current_reading'], 2) ?></td>
                        <td class="py-3 px-3 font-bold text-sky-700 bg-sky-50/50"><?= number_format($invoice['units_consumed'], 2) ?> m&sup3;</td>
                        <td class="py-3 px-3 text-slate-600">KSh <?= number_format($invoice['cost_per_unit'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Itemized Charges -->
        <div class="space-y-2 text-xs pt-2">
            <div class="flex justify-between py-1.5 border-b border-slate-100">
                <span class="text-slate-600">Water Consumption Charges</span>
                <span class="font-mono font-bold text-slate-800">KSh <?= number_format($invoice['water_cost'], 2) ?></span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-100">
                <span class="text-slate-600">Monthly Meter Service Rent</span>
                <span class="font-mono font-bold text-slate-800">KSh <?= number_format($invoice['meter_rent'], 2) ?></span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-100">
                <span class="text-slate-600">Previous Arrears Carried Forward (BBD)</span>
                <span class="font-mono font-bold text-amber-600">KSh <?= number_format($invoice['bbd'], 2) ?></span>
            </div>
            <div class="flex justify-between py-2 text-base font-extrabold border-b-2 border-slate-900 text-slate-900">
                <span>Total Invoice Amount</span>
                <span class="font-mono">KSh <?= number_format($invoice['total_bill'], 2) ?></span>
            </div>
            <div class="flex justify-between py-1.5 text-xs text-emerald-700 font-bold">
                <span>Amount Paid</span>
                <span class="font-mono">KSh <?= number_format($invoice['amount_paid'], 2) ?></span>
            </div>
            <div class="flex justify-between py-1.5 text-xs text-rose-700 font-bold">
                <span>Balance Due</span>
                <span class="font-mono">KSh <?= number_format(max(0, $invoice['total_bill'] - $invoice['amount_paid']), 2) ?></span>
            </div>
        </div>

        <!-- Payment Instructions Footer -->
        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200 text-xs flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <span class="font-bold text-slate-900 block">Payment Details (M-Pesa)</span>
                <span class="text-slate-600">Paybill Number: <strong class="font-mono text-slate-800">174379</strong></span>
                <span class="text-slate-600 block">Account Number: <strong class="font-mono text-slate-800">H<?= htmlspecialchars($invoice['house_number']) ?></strong></span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 font-mono text-[11px] block">Lensmen Verified Statement</span>
                <span class="text-slate-500 font-mono text-[10px]">Security Hash: <?= substr(md5($invoice['invoice_number']), 0, 16) ?></span>
            </div>
        </div>
    </div>
</div>
