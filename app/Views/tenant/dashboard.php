<div class="max-w-2xl mx-auto space-y-6">
    <!-- Apartment & Tenant Greeting -->
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-sky-600 font-mono">
                <?= $house ? 'House #' . htmlspecialchars($house['house_number']) : 'Tenant' ?> &bull; <?= htmlspecialchars($house['meter_number'] ?? '') ?>
            </span>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Hello, <?= htmlspecialchars($user['firstname']) ?>! 👋
            </h1>
        </div>
        <a href="/tenant/statement" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 px-3 py-2 rounded-xl shadow-sm transition">
            <i data-lucide="file-text" class="w-4 h-4 text-sky-600"></i>
            <span>Statement</span>
        </a>
    </div>

    <!-- Hero Card: Active Outstanding Bill -->
    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-36 h-36 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center">
                    <i data-lucide="droplet" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">
                    <?= $currentInvoice ? 'Water Statement &bull; ' . htmlspecialchars($currentInvoice['billing_month']) : 'Active Statement' ?>
                </span>
            </div>

            <?php if ($totalOutstanding <= 0): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <i data-lucide="check" class="w-3.5 h-3.5 mr-1"></i> Paid in Full
                </span>
            <?php else: ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse">
                    <i data-lucide="clock" class="w-3.5 h-3.5 mr-1"></i> Payment Due
                </span>
            <?php endif; ?>
        </div>

        <!-- Big Amount Display -->
        <div class="py-5">
            <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">Total Outstanding Amount</span>
            <div class="text-4xl sm:text-5xl font-extrabold font-mono text-white tracking-tight flex items-baseline space-x-1">
                <span class="text-2xl text-cyan-400 font-sans font-bold">KSh</span>
                <span><?= number_format($totalOutstanding, 2) ?></span>
            </div>
            <?php if ($currentInvoice && $totalOutstanding > 0): ?>
                <p class="text-xs text-slate-400 mt-2 flex items-center space-x-1">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-sky-400"></i>
                    <span>Due on or before <strong class="text-slate-200"><?= htmlspecialchars($currentInvoice['due_date']) ?></strong></span>
                </p>
            <?php endif; ?>
        </div>

        <!-- Meter Readings Snapshot -->
        <?php if ($currentInvoice): ?>
            <div class="grid grid-cols-3 gap-2 py-3 px-3.5 bg-slate-800/80 rounded-2xl border border-slate-700/60 text-center text-xs">
                <div>
                    <span class="block text-slate-400 text-[10px] uppercase font-semibold">Prev Dial</span>
                    <span class="font-mono font-bold text-slate-200"><?= number_format($currentInvoice['previous_reading'], 1) ?></span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[10px] uppercase font-semibold">Current Dial</span>
                    <span class="font-mono font-bold text-cyan-300"><?= number_format($currentInvoice['current_reading'], 1) ?></span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[10px] uppercase font-semibold">Units Consumed</span>
                    <span class="font-mono font-bold text-emerald-400"><?= number_format($currentInvoice['units_consumed'], 1) ?> m&sup3;</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- 1-Click M-Pesa Action Button -->
        <div class="mt-5">
            <?php if ($totalOutstanding > 0 && $currentInvoice): ?>
                <button onclick="openMpesaModal(<?= $currentInvoice['id'] ?>, <?= $totalOutstanding ?>, '<?= htmlspecialchars($user['phonenumber']) ?>')"
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-base shadow-lg shadow-emerald-500/25 flex items-center justify-center space-x-2 transition transform active:scale-[0.98]">
                    <div class="w-5 h-5 rounded-full bg-white text-emerald-700 flex items-center justify-center font-black text-xs font-mono">M</div>
                    <span>Pay KSh <?= number_format($totalOutstanding, 2) ?> via M-Pesa</span>
                </button>
            <?php else: ?>
                <div class="text-center py-2 text-xs text-slate-400 flex items-center justify-center space-x-1">
                    <i data-lucide="smile" class="w-4 h-4 text-emerald-400"></i>
                    <span>Your account is up to date. Thank you for timely payments!</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Monthly Water Usage Trend Chart -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Water Consumption History</h3>
                <p class="text-xs text-slate-500">Your monthly water usage in cubic meters (m&sup3;)</p>
            </div>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
            </div>
        </div>

        <?php if (empty($history)): ?>
            <div class="text-center py-8 text-xs text-slate-400">
                No historical records yet.
            </div>
        <?php else: ?>
            <!-- Responsive CSS/SVG Bar Chart -->
            <?php 
                $maxUnits = 1;
                foreach ($history as $h) {
                    if ($h['units_consumed'] > $maxUnits) $maxUnits = $h['units_consumed'];
                }
            ?>
            <div class="pt-4 pb-2">
                <div class="flex items-end justify-between gap-3 h-40 border-b border-slate-100 px-2">
                    <?php foreach ($history as $item): ?>
                        <?php 
                            $heightPct = max(15, round(($item['units_consumed'] / $maxUnits) * 100)); 
                        ?>
                        <div class="flex-1 flex flex-col items-center group">
                            <!-- Tooltip on hover -->
                            <span class="text-[11px] font-mono font-bold text-slate-700 mb-1 group-hover:text-sky-600 transition">
                                <?= number_format($item['units_consumed'], 1) ?>m&sup3;
                            </span>
                            <!-- Bar -->
                            <div class="w-full max-w-[48px] bg-sky-100 group-hover:bg-sky-500 rounded-t-xl transition-all duration-300 relative" style="height: <?= $heightPct ?>%;">
                                <div class="w-full bg-gradient-to-t from-sky-600 to-cyan-400 rounded-t-xl h-full"></div>
                            </div>
                            <!-- Month Label -->
                            <span class="text-[10px] font-semibold text-slate-400 uppercase mt-2 font-mono">
                                <?= date('M', strtotime($item['billing_month'] . '-01')) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Itemized Bill Breakdown -->
    <?php if ($currentInvoice): ?>
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-3">
        <h3 class="text-base font-bold text-slate-900 mb-3 flex items-center space-x-2">
            <i data-lucide="list" class="w-4 h-4 text-sky-600"></i>
            <span>Current Invoice Itemization</span>
        </h3>

        <div class="divide-y divide-slate-100 text-sm">
            <div class="py-2.5 flex justify-between">
                <span class="text-slate-600">Water Consumption (<?= $currentInvoice['units_consumed'] ?> m&sup3; &times; KSh 150)</span>
                <span class="font-mono font-semibold text-slate-800">KSh <?= number_format($currentInvoice['water_cost'], 2) ?></span>
            </div>
            <div class="py-2.5 flex justify-between">
                <span class="text-slate-600">Monthly Meter Service Rent</span>
                <span class="font-mono font-semibold text-slate-800">KSh <?= number_format($currentInvoice['meter_rent'], 2) ?></span>
            </div>
            <div class="py-2.5 flex justify-between">
                <span class="text-slate-600">Previous Arrears Brought Down</span>
                <span class="font-mono font-semibold text-amber-600">KSh <?= number_format($currentInvoice['bbd'], 2) ?></span>
            </div>
            <div class="py-2.5 flex justify-between font-bold text-base text-slate-900">
                <span>Total Monthly Bill</span>
                <span class="font-mono text-sky-600">KSh <?= number_format($currentInvoice['total_bill'], 2) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Payments & Receipts -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center space-x-2">
            <i data-lucide="receipt" class="w-4 h-4 text-emerald-600"></i>
            <span>Recent Payment Receipts</span>
        </h3>

        <?php if (empty($payments)): ?>
            <p class="text-xs text-slate-400 py-3 text-center">No payment transactions recorded yet.</p>
        <?php else: ?>
            <div class="space-y-2.5">
                <?php foreach ($payments as $p): ?>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 block">KSh <?= number_format($p['amount'], 2) ?></span>
                                <span class="font-mono text-slate-400 text-[11px]">Ref: <?= htmlspecialchars($p['mpesa_receipt']) ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 block"><?= htmlspecialchars($p['created_at']) ?></span>
                            <span class="text-emerald-600 font-semibold font-mono"><?= htmlspecialchars($p['payment_method']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Interactive M-Pesa STK Push Modal -->
<div id="mpesaModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        <button onclick="closeMpesaModal()" class="absolute top-4 right-4 p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <div class="text-center">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white mx-auto flex items-center justify-center font-black text-2xl font-mono shadow-lg shadow-emerald-500/25 mb-3">
                M
            </div>
            <h3 class="text-lg font-extrabold text-slate-900">Lipa na M-Pesa Online</h3>
            <p class="text-xs text-slate-500 mt-1">An instant STK Push prompt will be sent directly to your phone handset.</p>
        </div>

        <form id="stkForm" onsubmit="event.preventDefault(); triggerStkPush();" class="mt-5 space-y-4">
            <input type="hidden" id="modalInvoiceId">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Paybill / Business #</label>
                <div class="p-2.5 rounded-xl bg-slate-100 font-mono font-bold text-slate-700 text-sm">
                    174379 (Classic Heights)
                </div>
            </div>

            <div>
                <label for="modalPhone" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">M-Pesa Phone Number</label>
                <input type="tel" id="modalPhone" required
                    class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl font-mono font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="modalAmount" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Amount to Pay (KSh)</label>
                <input type="number" step="0.01" id="modalAmount" required
                    class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl font-mono font-bold text-slate-800 text-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <button type="submit" id="stkSubmitBtn"
                class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 flex items-center justify-center space-x-2 transition">
                <i data-lucide="smartphone" class="w-4 h-4"></i>
                <span>Send STK Push Prompt</span>
            </button>
        </form>
    </div>
</div>

<script>
    function openMpesaModal(invoiceId, amount, phone) {
        document.getElementById('modalInvoiceId').value = invoiceId;
        document.getElementById('modalAmount').value = amount;
        document.getElementById('modalPhone').value = phone;
        document.getElementById('mpesaModal').classList.remove('hidden');
    }

    function closeMpesaModal() {
        document.getElementById('mpesaModal').classList.add('hidden');
    }

    function triggerStkPush() {
        const invoiceId = document.getElementById('modalInvoiceId').value;
        const phone = document.getElementById('modalPhone').value;
        const amount = document.getElementById('modalAmount').value;

        closeMpesaModal();

        // 1. Show simulated PIN prompt
        Swal.fire({
            title: 'M-Pesa STK Push Sent!',
            html: `Prompt sent to <b>${phone}</b>.<br><br><i>Please enter your M-Pesa PIN on your phone handset to authorize payment of KSh ${Number(amount).toLocaleString()} to Classic Heights Apartments.</i>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Confirm PIN Entered',
            cancelButtonText: 'Cancel Payment',
            confirmButtonColor: '#059669',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Post to API to complete settlement
                fetch('/api/pay/stk-push', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        invoice_id: invoiceId,
                        phone: phone,
                        amount: amount
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Received!',
                            html: `<b>M-Pesa Receipt:</b> ${data.mpesa_receipt}<br><b>Amount:</b> KSh ${Number(data.amount).toLocaleString()}<br><small class="text-slate-500">Your account has been credited.</small>`,
                            confirmButtonColor: '#059669'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'Payment Failed', text: data.error});
                    }
                });
            }
        });
    }
</script>
