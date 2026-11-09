<?php
$initials = strtoupper(substr($tenant['firstname'] ?? 'T', 0, 1) . substr($tenant['lastname'] ?? 'N', 0, 1));
$compliancePct = $kpis['total_invoices'] > 0
    ? round(($kpis['paid_invoices'] / $kpis['total_invoices']) * 100)
    : 100;
?>
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Admin Context Switcher (Visible only when Admin views a Tenant's personalized page) -->
    <?php if (!empty($isAdmin)): ?>
    <div class="bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-md border border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 no-print">
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                <i data-lucide="shield" class="w-3.5 h-3.5 mr-1.5"></i> Admin View
            </span>
            <span class="text-xs sm:text-sm text-slate-300">
                Inspecting personalized payment &amp; activity log for <strong class="text-white"><?= htmlspecialchars($tenant['firstname'] . ' ' . $tenant['lastname']) ?></strong>
                <?= $tenant['house_number'] ? '(House #' . htmlspecialchars($tenant['house_number']) . ')' : '' ?>
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <form action="/tenant/payments" method="GET" class="flex items-center space-x-2">
                <label for="tenantSwitcher" class="sr-only">Switch Tenant</label>
                <select id="tenantSwitcher" name="tenant_id" onchange="this.form.submit()"
                    class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <?php foreach ($allTenants as $t): ?>
                        <option value="<?= (int)$t['id'] ?>" <?= (int)$t['id'] === (int)$tenant['id'] ? 'selected' : '' ?>>
                            House #<?= htmlspecialchars($t['house_number'] ?? '-') ?> &mdash; <?= htmlspecialchars($t['firstname'] . ' ' . $t['lastname']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="/admin/tenants" class="inline-flex items-center space-x-1 text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 px-3 py-2 rounded-xl transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Directory</span>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Personalized Tenant Profile & Header Card -->
    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-44 h-44 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 font-black text-xl sm:text-2xl flex items-center justify-center shadow-lg shadow-sky-500/20 shrink-0">
                    <?= htmlspecialchars($initials) ?>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30 font-mono">
                            <?= $tenant['house_number'] ? 'House #' . htmlspecialchars($tenant['house_number']) : 'Unassigned Unit' ?>
                        </span>
                        <?php if (!empty($tenant['meter_number'])): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono bg-slate-800 text-slate-300 border border-slate-700">
                                Meter: <?= htmlspecialchars($tenant['meter_number']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($kpis['outstanding_balance'] <= 0): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 mr-1"></i> Good Standing
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i data-lucide="clock" class="w-3.5 h-3.5 mr-1"></i> Balance Due
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mt-1.5">
                        <?= htmlspecialchars($tenant['firstname'] . ' ' . $tenant['lastname']) ?>'s Payment &amp; Activity Log
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 mt-1">
                        <span class="flex items-center space-x-1 font-mono">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-sky-400"></i>
                            <span><?= htmlspecialchars($tenant['phonenumber']) ?></span>
                        </span>
                        <?php if (!empty($tenant['email'])): ?>
                        <span class="flex items-center space-x-1">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-sky-400"></i>
                            <span><?= htmlspecialchars($tenant['email']) ?></span>
                        </span>
                        <?php endif; ?>
                        <span class="flex items-center space-x-1">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-sky-400"></i>
                            <span>Paybill Acc: <strong class="text-slate-200 font-mono">H<?= htmlspecialchars($tenant['house_number'] ?? '0') ?></strong></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 no-print">
                <?php if ($kpis['outstanding_balance'] > 0 && $activeInvoice): ?>
                    <button onclick="openMpesaModal(<?= (int)$activeInvoice['id'] ?>, <?= (float)$kpis['outstanding_balance'] ?>, '<?= htmlspecialchars($tenant['phonenumber']) ?>')"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition">
                        <div class="w-4 h-4 rounded-full bg-white text-emerald-700 flex items-center justify-center font-black text-[10px] font-mono">M</div>
                        <span>Pay KSh <?= number_format($kpis['outstanding_balance'], 2) ?></span>
                    </button>
                <?php endif; ?>
                <button onclick="window.print()" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                    <i data-lucide="printer" class="w-4 h-4 text-sky-400"></i>
                    <span>Print Log</span>
                </button>
                <button onclick="exportPaymentsToCSV('<?= strtolower(htmlspecialchars($tenant['firstname'])) ?>_payments_log.csv')" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                    <i data-lucide="download" class="w-4 h-4 text-emerald-400"></i>
                    <span>Export CSV</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Personalized Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Total Paid Lifetime -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Paid (Lifetime)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-emerald-600 font-mono">
                    KSh <?= number_format($kpis['total_paid'], 2) ?>
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    Across <strong><?= $kpis['payment_count'] ?></strong> verified M-Pesa <?= $kpis['payment_count'] === 1 ? 'payment' : 'payments' ?>
                </div>
            </div>
        </div>

        <!-- KPI 2: Current Outstanding Balance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Current Balance Due</span>
                <div class="w-8 h-8 rounded-lg <?= $kpis['outstanding_balance'] > 0 ? 'bg-rose-100 text-rose-600' : 'bg-sky-100 text-sky-600' ?> flex items-center justify-center">
                    <i data-lucide="<?= $kpis['outstanding_balance'] > 0 ? 'alert-circle' : 'check-circle' ?>" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold <?= $kpis['outstanding_balance'] > 0 ? 'text-rose-600' : 'text-slate-900' ?> font-mono">
                    KSh <?= number_format($kpis['outstanding_balance'], 2) ?>
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    <?php if ($kpis['outstanding_balance'] > 0 && $activeInvoice): ?>
                        Due by <strong class="text-slate-700"><?= htmlspecialchars($activeInvoice['due_date']) ?></strong>
                    <?php else: ?>
                        <span class="text-emerald-600 font-medium">All bills settled</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KPI 3: Last Recorded Payment -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Last Payment</span>
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <?php if (!empty($kpis['last_payment'])): ?>
                    <div class="text-2xl font-extrabold text-slate-900 font-mono">
                        KSh <?= number_format((float)$kpis['last_payment']['amount'], 2) ?>
                    </div>
                    <div class="mt-1 text-xs text-slate-500 flex items-center justify-between">
                        <span class="font-mono font-semibold text-emerald-700"><?= htmlspecialchars($kpis['last_payment']['mpesa_receipt']) ?></span>
                        <span><?= htmlspecialchars(substr((string)$kpis['last_payment']['created_at'], 0, 10)) ?></span>
                    </div>
                <?php else: ?>
                    <div class="text-lg font-bold text-slate-400 py-1">No Payments Yet</div>
                    <div class="mt-1 text-xs text-slate-400">Awaiting first M-Pesa transaction</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- KPI 4: Billing Compliance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Invoices Settled</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900 font-mono">
                    <?= $kpis['paid_invoices'] ?> <span class="text-sm font-sans font-medium text-slate-500">of <?= $kpis['total_invoices'] ?> Paid</span>
                </div>
                <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= min(100, $compliancePct) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Activity Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm no-print">
        <form action="/tenant/payments" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <?php if (!empty($isAdmin)): ?>
                <input type="hidden" name="tenant_id" value="<?= (int)$tenant['id'] ?>">
            <?php endif; ?>

            <div class="sm:col-span-4">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Search Receipt or Invoice</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="e.g. QRT..., INV-202610, 0712..."
                        class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div class="sm:col-span-3">
                <label for="month" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Billing Period</label>
                <select name="month" id="month" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                    <option value="">All Billing Months</option>
                    <?php foreach ($availableMonths as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>" <?= $filterMonth === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Activity Feed Filter</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                    <option value="ALL" <?= $filterType === 'ALL' ? 'selected' : '' ?>>All Account Activity</option>
                    <option value="PAYMENT" <?= $filterType === 'PAYMENT' ? 'selected' : '' ?>>M-Pesa Payments Only</option>
                    <option value="INVOICE" <?= $filterType === 'INVOICE' ? 'selected' : '' ?>>Water Bills &amp; Readings</option>
                    <option value="NOTIFICATION" <?= $filterType === 'NOTIFICATION' ? 'selected' : '' ?>>SMS Alerts &amp; Confirmations</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex space-x-2">
                <button type="submit" class="w-full py-2 px-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-semibold transition">
                    Filter
                </button>
                <a href="/tenant/payments<?= !empty($isAdmin) ? '?tenant_id=' . (int)$tenant['id'] : '' ?>"
                   class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-semibold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Section 1: Verified M-Pesa Payment Transactions Ledger -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">M-Pesa Payment Transactions Ledger</h2>
                    <p class="text-xs text-slate-500">Complete history of your payments, M-Pesa receipt references, and applied invoices</p>
                </div>
            </div>
            <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full self-start sm:self-auto">
                Showing <?= count($payments) ?> <?= count($payments) === 1 ? 'record' : 'records' ?>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="paymentsLedgerTable">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">M-Pesa Receipt</th>
                        <th class="px-5 py-3.5">Date &amp; Time</th>
                        <th class="px-5 py-3.5">Invoice &amp; Period</th>
                        <th class="px-5 py-3.5">Phone Number</th>
                        <th class="px-5 py-3.5">Channel</th>
                        <th class="px-5 py-3.5 text-right">Amount Paid</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center no-print">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">
                                <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                No payment transactions found matching your filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $p): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-emerald-700">
                                    <span class="inline-flex items-center space-x-1.5 bg-emerald-50 border border-emerald-200/70 px-2.5 py-1 rounded-lg">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span><?= htmlspecialchars($p['mpesa_receipt'] ?: 'N/A') ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-600">
                                    <?= htmlspecialchars($p['created_at']) ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="/tenant/statement?id=<?= (int)$p['invoice_id'] ?>" class="font-mono text-xs font-bold text-sky-600 hover:text-sky-800 hover:underline block">
                                        <?= htmlspecialchars($p['invoice_number']) ?>
                                    </a>
                                    <span class="text-[11px] text-slate-400 font-mono">Billing Month: <?= htmlspecialchars($p['billing_month']) ?></span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-700">
                                    <?= htmlspecialchars($p['phone_number']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-600 font-mono">
                                    <?= htmlspecialchars($p['payment_method']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                    KSh <?= number_format((float)$p['amount'], 2) ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <?= htmlspecialchars($p['status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center no-print">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button type="button"
                                            onclick='showReceiptModal(<?= json_encode([
                                                "receipt" => $p["mpesa_receipt"],
                                                "amount" => number_format((float)$p["amount"], 2),
                                                "date" => $p["created_at"],
                                                "phone" => $p["phone_number"],
                                                "method" => $p["payment_method"],
                                                "invoice_number" => $p["invoice_number"],
                                                "billing_month" => $p["billing_month"],
                                                "house_number" => $tenant["house_number"] ?? "",
                                                "tenant_name" => $tenant["firstname"] . " " . $tenant["lastname"],
                                            ]) ?>)'
                                            class="inline-flex items-center space-x-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-lg transition">
                                            <i data-lucide="file-check" class="w-3.5 h-3.5"></i>
                                            <span>E-Receipt</span>
                                        </button>
                                        <a href="/tenant/statement?id=<?= (int)$p['invoice_id'] ?>" title="View Full Statement"
                                            class="p-1.5 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
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

    <!-- Section 2: Chronological Account Activity Timeline -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Chronological Account Activity Log</h2>
                    <p class="text-xs text-slate-500">Audit trail of all payments, monthly water meter bills, and SMS notifications</p>
                </div>
            </div>
            <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                <?= count($activityLog) ?> <?= count($activityLog) === 1 ? 'event' : 'events' ?>
            </span>
        </div>

        <?php if (empty($activityLog)): ?>
            <div class="text-center py-10 text-slate-400 text-sm">
                <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                No activity events recorded for this filter.
            </div>
        <?php else: ?>
            <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 ml-3">
                <?php foreach ($activityLog as $event): ?>
                    <?php
                        $isPayment = ($event['type'] === 'PAYMENT');
                        $isInvoice = ($event['type'] === 'INVOICE');
                        $dotClasses = $isPayment
                            ? 'bg-emerald-500 text-white ring-4 ring-emerald-50'
                            : ($isInvoice ? 'bg-sky-600 text-white ring-4 ring-sky-50' : 'bg-amber-500 text-white ring-4 ring-amber-50');
                        $iconName = $isPayment ? 'check' : ($isInvoice ? 'droplet' : 'message-square');
                        $badgeColor = $event['badge_color'] === 'emerald'
                            ? 'bg-emerald-100 text-emerald-800'
                            : ($event['badge_color'] === 'amber'
                                ? 'bg-amber-100 text-amber-800'
                                : ($event['badge_color'] === 'rose' ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800'));
                    ?>
                    <div class="relative group">
                        <!-- Timeline Node Icon -->
                        <div class="absolute -left-[37px] top-1 w-7 h-7 rounded-full <?= $dotClasses ?> flex items-center justify-center shadow-sm">
                            <i data-lucide="<?= $iconName ?>" class="w-3.5 h-3.5"></i>
                        </div>

                        <!-- Event Card -->
                        <div class="bg-slate-50/80 hover:bg-slate-50 rounded-2xl p-4 border border-slate-200/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md <?= $badgeColor ?>">
                                        <?= htmlspecialchars($event['type']) ?> &bull; <?= htmlspecialchars($event['badge']) ?>
                                    </span>
                                    <span class="text-xs font-mono text-slate-400">
                                        <?= htmlspecialchars($event['timestamp']) ?>
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    <?= htmlspecialchars($event['title']) ?>
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    <?= htmlspecialchars($event['subtitle']) ?>
                                </p>
                            </div>

                            <div class="flex items-center space-x-2 shrink-0 no-print">
                                <?php if (!empty($event['invoice_id'])): ?>
                                    <a href="/tenant/statement?id=<?= (int)$event['invoice_id'] ?>"
                                        class="inline-flex items-center space-x-1 text-xs font-semibold text-slate-700 hover:text-sky-700 bg-white border border-slate-200/90 px-3 py-1.5 rounded-xl shadow-sm transition">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-sky-600"></i>
                                        <span>Statement</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Official M-Pesa E-Receipt Modal -->
<div id="receiptModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 relative">
        <button onclick="closeReceiptModal()" class="absolute top-4 right-4 p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <div id="printableReceiptArea" class="space-y-5">
            <div class="text-center border-b border-dashed border-slate-200 pb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-2">
                    <i data-lucide="badge-check" class="w-7 h-7"></i>
                </div>
                <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-600">Official Payment E-Receipt</span>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">Classic Heights Apartments</h3>
                <p class="text-xs text-slate-400">Paybill 174379 &bull; Water Utility Management</p>
            </div>

            <div class="bg-emerald-50/70 border border-emerald-200/60 rounded-2xl p-4 text-center">
                <span class="text-xs uppercase font-semibold text-emerald-700 block">Amount Received</span>
                <span id="rcptAmount" class="text-3xl font-black font-mono text-emerald-700">KSh 0.00</span>
                <span id="rcptCode" class="block mt-1 text-xs font-mono font-bold text-slate-700">Ref: QRT0000000</span>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tenant Name</span>
                    <span id="rcptTenant" class="font-bold text-slate-800"></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Apartment Unit</span>
                    <span id="rcptHouse" class="font-bold text-slate-800 font-mono"></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">M-Pesa Phone Number</span>
                    <span id="rcptPhone" class="font-mono font-semibold text-slate-800"></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Applied Invoice</span>
                    <span id="rcptInvoice" class="font-mono font-bold text-sky-700"></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Billing Month</span>
                    <span id="rcptMonth" class="font-mono text-slate-800"></span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Payment Channel</span>
                    <span id="rcptMethod" class="font-mono font-semibold text-emerald-700"></span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-500">Transaction Timestamp</span>
                    <span id="rcptDate" class="font-mono text-slate-700"></span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center space-x-3">
            <button onclick="window.print()" class="flex-1 py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs flex items-center justify-center space-x-1.5 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Receipt</span>
            </button>
            <button onclick="closeReceiptModal()" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Interactive M-Pesa STK Push Modal -->
<div id="mpesaModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 relative">
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
    function showReceiptModal(data) {
        document.getElementById('rcptAmount').textContent = 'KSh ' + data.amount;
        document.getElementById('rcptCode').textContent = 'M-Pesa Ref: ' + (data.receipt || 'N/A');
        document.getElementById('rcptTenant').textContent = data.tenant_name;
        document.getElementById('rcptHouse').textContent = 'House #' + data.house_number;
        document.getElementById('rcptPhone').textContent = data.phone;
        document.getElementById('rcptInvoice').textContent = data.invoice_number;
        document.getElementById('rcptMonth').textContent = data.billing_month;
        document.getElementById('rcptMethod').textContent = data.method;
        document.getElementById('rcptDate').textContent = data.date;
        document.getElementById('receiptModal').classList.remove('hidden');
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.add('hidden');
    }

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
                            title: 'Payment Recorded!',
                            html: `<b>M-Pesa Receipt:</b> ${data.mpesa_receipt}<br><b>Amount:</b> KSh ${Number(data.amount).toLocaleString()}<br><small class="text-slate-500">Your payment activity log has been updated.</small>`,
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

    function exportPaymentsToCSV(filename) {
        const rows = document.querySelectorAll('#paymentsLedgerTable tr');
        const csv = [];
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            if (cols.length < 2) continue;
            // Exclude last column (Action buttons)
            for (let j = 0; j < cols.length - 1; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(','));
        }

        const csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>

