<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Smart Meter Reading Console</h1>
            <p class="text-sm text-slate-500">Record current monthly dial readings with instant calculation preview and anomaly detection.</p>
        </div>
        <a href="/admin/dashboard" class="text-sm font-semibold text-slate-600 hover:text-slate-900 flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Input Form (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center space-x-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-sky-600"></i>
                <span>Enter Reading Details</span>
            </h3>

            <form id="readingForm" onsubmit="event.preventDefault(); submitBill(false);" class="space-y-4">
                <!-- House Unit Selector -->
                <div>
                    <label for="house_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                        Select Apartment Unit
                    </label>
                    <select name="house_id" id="house_id" required onchange="onHouseChange()"
                        class="block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white font-medium">
                        <option value="">-- Choose Unit (1 to 18) --</option>
                        <?php foreach ($houses as $h): ?>
                            <option value="<?= $h['id'] ?>" 
                                data-tenant="<?= htmlspecialchars($h['firstname'] ? ($h['firstname'] . ' ' . $h['lastname']) : 'Vacant Unit') ?>"
                                data-phone="<?= htmlspecialchars($h['phonenumber'] ?? '') ?>"
                                data-meter="<?= htmlspecialchars($h['meter_number']) ?>">
                                House <?= htmlspecialchars($h['house_number']) ?> &bull; <?= htmlspecialchars($h['firstname'] ? ($h['firstname'] . ' ' . $h['lastname']) : '[VACANT]') ?> (Meter: <?= htmlspecialchars($h['meter_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Billing Month & Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="billing_month" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                            Billing Month
                        </label>
                        <input type="month" name="billing_month" id="billing_month" required value="<?= $defaultMonth ?>"
                            class="block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label for="date_of_reading" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                            Reading Date
                        </label>
                        <input type="date" name="date_of_reading" id="date_of_reading" required value="<?= $defaultDate ?>"
                            class="block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <!-- Current Reading Dial Input -->
                <div>
                    <label for="current_reading" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                        Current Meter Reading (Dial)
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="gauge" class="w-5 h-5"></i>
                        </div>
                        <input type="number" step="0.01" name="current_reading" id="current_reading" required placeholder="0.00" oninput="updateLiveCalculation()"
                            class="block w-full pl-11 pr-16 py-3 border border-slate-300 rounded-xl text-lg font-bold font-mono focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-xs font-semibold text-slate-400">
                            m&sup3;
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Live calculation automatically refreshes on keystroke.</p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="submitBill(false)" id="saveBtn"
                        class="flex-1 inline-flex justify-center items-center space-x-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Save Record Only</span>
                    </button>
                    <button type="button" onclick="submitBill(true)" id="saveSmsBtn"
                        class="flex-1 inline-flex justify-center items-center space-x-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 shadow-md shadow-sky-500/20 transition">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Save & Dispatch SMS</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Calculation Preview Card (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-xl border border-slate-700 relative">
                <div class="flex items-center justify-between pb-3 border-b border-slate-700/60">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Live Bill Preview</h4>
                    </div>
                    <span id="tenantTag" class="text-xs bg-slate-800 px-2.5 py-1 rounded-md text-sky-400 font-medium font-mono">No unit selected</span>
                </div>

                <!-- Anomaly Alert Banner -->
                <div id="anomalyBanner" class="hidden my-3 p-2.5 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center space-x-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 text-rose-400"></i>
                    <span id="anomalyText">Warning: Anomaly detected</span>
                </div>

                <!-- Readings Comparison -->
                <div class="grid grid-cols-2 gap-3 py-4 border-b border-slate-700/60 text-center">
                    <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700">
                        <span class="block text-[11px] text-slate-400 uppercase font-semibold">Previous Dial</span>
                        <span id="previewPrev" class="text-xl font-bold font-mono text-slate-200">0.00</span>
                    </div>
                    <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700">
                        <span class="block text-[11px] text-slate-400 uppercase font-semibold">Current Dial</span>
                        <span id="previewCurr" class="text-xl font-bold font-mono text-cyan-300">0.00</span>
                    </div>
                </div>

                <!-- Units Consumed -->
                <div class="flex justify-between items-center py-2 text-xs">
                    <span class="text-slate-300">Net Units Consumed</span>
                    <span class="font-mono font-bold text-base text-white"><span id="previewUnits">0.00</span> m&sup3;</span>
                </div>

                <!-- Cost Breakdown List -->
                <div class="space-y-1.5 py-3 border-t border-slate-700/60 text-xs text-slate-300">
                    <div class="flex justify-between">
                        <span>Water Charge (<span id="previewUnitsSub">0</span> &times; KSh 150)</span>
                        <span class="font-mono text-slate-200">KSh <span id="previewWaterCost">0.00</span></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fixed Meter Rent</span>
                        <span class="font-mono text-slate-200">KSh 50.00</span>
                    </div>
                    <div class="flex justify-between text-amber-300">
                        <span>Previous Unpaid Arrears</span>
                        <span class="font-mono">KSh <span id="previewArrears">0.00</span></span>
                    </div>
                </div>

                <!-- Total Bill Highlight -->
                <div class="mt-4 pt-4 border-t border-slate-700 flex justify-between items-baseline">
                    <span class="text-sm font-semibold text-slate-300">Total Payable</span>
                    <div class="text-right">
                        <div class="text-3xl font-extrabold text-cyan-300 font-mono">
                            KSh <span id="previewTotal">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Helper Box -->
            <div class="bg-sky-50 border border-sky-100 p-4 rounded-xl text-xs text-sky-800 space-y-1">
                <div class="font-bold flex items-center space-x-1 text-sky-900">
                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                    <span>Automatic Previous Balance Reconciliation</span>
                </div>
                <p>When you select a unit, any uncollected balance from previous months is dynamically retrieved from the database ledger.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCalcData = null;

    function onHouseChange() {
        const select = document.getElementById('house_id');
        const selected = select.options[select.selectedIndex];
        const tenantTag = document.getElementById('tenantTag');

        if (!select.value) {
            tenantTag.innerText = 'No unit selected';
            resetPreview();
            return;
        }

        const tenant = selected.getAttribute('data-tenant');
        tenantTag.innerText = tenant;

        updateLiveCalculation();
    }

    function updateLiveCalculation() {
        const houseId = document.getElementById('house_id').value;
        const currentReading = document.getElementById('current_reading').value || 0;

        if (!houseId) return;

        fetch(`/api/calculate?house_id=${houseId}&current_reading=${currentReading}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    currentCalcData = d;

                    document.getElementById('previewPrev').innerText = Number(d.previous_reading).toFixed(2);
                    document.getElementById('previewCurr').innerText = Number(d.current_reading).toFixed(2);
                    document.getElementById('previewUnits').innerText = Number(d.units_consumed).toFixed(2);
                    document.getElementById('previewUnitsSub').innerText = Number(d.units_consumed).toFixed(2);
                    document.getElementById('previewWaterCost').innerText = Number(d.water_cost).toLocaleString(undefined, {minimumFractionDigits: 2});
                    document.getElementById('previewArrears').innerText = Number(d.arrears).toLocaleString(undefined, {minimumFractionDigits: 2});
                    document.getElementById('previewTotal').innerText = Number(d.total_bill).toLocaleString(undefined, {minimumFractionDigits: 2});

                    const banner = document.getElementById('anomalyBanner');
                    const text = document.getElementById('anomalyText');
                    if (d.warnings.negative_reading) {
                        banner.className = "my-3 p-2.5 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center space-x-2";
                        text.innerText = "Error: Current reading cannot be lower than previous reading (" + d.previous_reading + ")!";
                    } else if (d.warnings.high_consumption) {
                        banner.className = "my-3 p-2.5 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs flex items-center space-x-2";
                        text.innerText = "Notice: Unusually high consumption (" + d.units_consumed + " m³) recorded for this unit.";
                    } else {
                        banner.className = "hidden";
                    }
                }
            });
    }

    function resetPreview() {
        document.getElementById('previewPrev').innerText = '0.00';
        document.getElementById('previewCurr').innerText = '0.00';
        document.getElementById('previewUnits').innerText = '0.00';
        document.getElementById('previewUnitsSub').innerText = '0';
        document.getElementById('previewWaterCost').innerText = '0.00';
        document.getElementById('previewArrears').innerText = '0.00';
        document.getElementById('previewTotal').innerText = '0.00';
        document.getElementById('anomalyBanner').className = 'hidden';
    }

    function submitBill(sendSmsAfter) {
        const houseId = document.getElementById('house_id').value;
        const billingMonth = document.getElementById('billing_month').value;
        const dateOfReading = document.getElementById('date_of_reading').value;
        const currentReading = document.getElementById('current_reading').value;

        if (!houseId || !currentReading || !billingMonth) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Details',
                text: 'Please select a unit and input the current dial reading.',
                confirmButtonColor: '#0284c7'
            });
            return;
        }

        const payload = {
            house_id: houseId,
            billing_month: billingMonth,
            date_of_reading: dateOfReading,
            current_reading: currentReading
        };

        Swal.fire({
            title: 'Generating Bill...',
            text: 'Saving reading and creating invoice record.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('/api/meter-reading', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (sendSmsAfter) {
                    // Chain SMS dispatch
                    fetch('/api/send-sms', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({invoice_id: data.invoice_id})
                    })
                    .then(r => r.json())
                    .then(smsRes => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Bill & SMS Dispatched!',
                            html: `<b>Invoice:</b> ${data.invoice_number}<br><b>Total:</b> KSh ${Number(data.total_bill).toLocaleString()}<br><small class="text-slate-500">SMS alert dispatched to ${data.tenant_phone}</small>`,
                            confirmButtonColor: '#0284c7'
                        }).then(() => {
                            window.location.href = '/admin/records';
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reading Recorded!',
                        html: `<b>Invoice:</b> ${data.invoice_number}<br><b>Total Payable:</b> KSh ${Number(data.total_bill).toLocaleString()}`,
                        confirmButtonColor: '#0284c7'
                    }).then(() => {
                        window.location.href = '/admin/records';
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Generating Bill',
                    text: data.error || 'Could not save record.'
                });
            }
        })
        .catch(err => {
            Swal.fire({icon: 'error', title: 'Network Error', text: err.message});
        });
    }
</script>
