<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Apartments & Tenant Directory</h1>
            <p class="text-sm text-slate-500">Registry of 18 apartment units, assigned meter dials, occupancy status, and balances.</p>
        </div>
        <div>
            <a href="/register" class="inline-flex items-center space-x-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Register New Tenant</span>
            </a>
        </div>
    </div>

    <!-- Units Grid (1 to 18) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($units as $u): ?>
            <?php 
                $isOccupied = ($u['status'] === 'OCCUPIED' && !empty($u['consumer_id']));
                $arrears = (float)($u['current_arrears'] ?? 0);
            ?>
            <div class="bg-white rounded-2xl border <?= $isOccupied ? 'border-slate-200/90' : 'border-dashed border-slate-300 bg-slate-50/50' ?> p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Apartment</span>
                        <h3 class="text-xl font-extrabold text-slate-900">House <?= htmlspecialchars($u['house_number']) ?></h3>
                    </div>
                    <?php if ($isOccupied): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Occupied
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                            Vacant
                        </span>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Meter Number:</span>
                        <span class="font-mono font-semibold text-slate-700"><?= htmlspecialchars($u['meter_number']) ?></span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Tenant Name:</span>
                        <span class="font-bold text-slate-800">
                            <?= $isOccupied ? htmlspecialchars($u['firstname'] . ' ' . $u['lastname']) : '<span class="text-slate-400 italic font-normal">None Assigned</span>' ?>
                        </span>
                    </div>

                    <?php if ($isOccupied): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Contact:</span>
                            <span class="font-mono text-slate-700"><?= htmlspecialchars($u['phonenumber']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Last Recorded Dial:</span>
                        <span class="font-mono font-semibold text-slate-800"><?= number_format((float)($u['last_reading'] ?? 0), 2) ?> m&sup3;</span>
                    </div>

                    <div class="flex justify-between items-center pt-1 border-t border-slate-100">
                        <span class="text-slate-500 font-medium">Unpaid Balance:</span>
                        <span class="font-mono font-bold <?= $arrears > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                            KSh <?= number_format($arrears, 2) ?>
                        </span>
                    </div>

                    <?php if ($isOccupied): ?>
                        <div class="pt-3">
                            <a href="/tenant/payments?tenant_id=<?= (int)$u['consumer_id'] ?>"
                                class="w-full inline-flex items-center justify-center space-x-1.5 py-2 px-3 rounded-xl bg-slate-100 hover:bg-sky-50 text-slate-700 hover:text-sky-700 border border-slate-200/80 hover:border-sky-200 font-semibold text-xs transition">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-sky-600"></i>
                                <span>Payment &amp; Activity Log</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
