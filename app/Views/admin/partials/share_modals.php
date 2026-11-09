<!-- ============================================================== -->
<!-- Single Invoice Quick Share Modal -->
<!-- ============================================================== -->
<div id="shareInvoiceModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-150">
        <!-- Close Button -->
        <button onclick="closeShareModal()" class="absolute top-5 right-5 p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- Header Info -->
        <div class="flex items-start space-x-3 pb-4 border-b border-slate-100">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 text-white flex items-center justify-center shadow-md shadow-sky-500/20 shrink-0">
                <i data-lucide="share-2" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-sky-600 font-mono" id="shareUnitBadge">House #</span>
                <h3 class="text-lg font-extrabold text-slate-900" id="shareTenantName">Tenant Billing Notification</h3>
                <p class="text-xs text-slate-500 flex items-center space-x-2 mt-0.5">
                    <span id="sharePhone" class="font-mono font-medium text-slate-700"></span>
                    <span>&bull;</span>
                    <span id="shareAmount" class="font-mono font-bold text-sky-600"></span>
                </p>
            </div>
        </div>

        <!-- Editable Message Content -->
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <label for="shareMessageText" class="font-semibold uppercase tracking-wider text-slate-500">
                    Message Preview & Customization
                </label>
                <span class="text-[11px] text-slate-400 font-mono"><span id="shareCharCount">0</span> chars</span>
            </div>
            <div class="relative">
                <textarea id="shareMessageText" rows="7" 
                    oninput="document.getElementById('shareCharCount').innerText = this.value.length"
                    class="w-full p-3.5 border border-slate-200 rounded-2xl text-xs sm:text-sm font-sans text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 leading-relaxed font-normal resize-y shadow-inner"></textarea>
            </div>
            <p class="text-[11px] text-slate-400 flex items-center space-x-1">
                <i data-lucide="info" class="w-3.5 h-3.5 shrink-0 text-sky-500"></i>
                <span>You can edit the message above before sharing or copying.</span>
            </p>
        </div>

        <!-- Sharing Action Buttons Grid -->
        <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2.5">
            <!-- 1. Send via WhatsApp -->
            <button type="button" onclick="sendInvoiceWhatsApp()" 
                class="col-span-1 inline-flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm shadow-emerald-600/20 transition">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Send WhatsApp</span>
            </button>

            <!-- 2. Copy Message -->
            <button type="button" id="copyBtn" onclick="copyInvoiceMessage()"
                class="col-span-1 inline-flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs shadow-sm transition">
                <i data-lucide="copy" class="w-4 h-4" id="copyIcon"></i>
                <span id="copyBtnText">Copy Message</span>
            </button>

            <!-- 3. Dispatch Gateway SMS -->
            <button type="button" onclick="sendInvoiceGatewaySms()"
                class="col-span-1 inline-flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold text-xs border border-sky-200 transition">
                <i data-lucide="send" class="w-4 h-4 text-sky-600"></i>
                <span>Gateway SMS</span>
            </button>

            <!-- 4. Open Phone SMS -->
            <button type="button" onclick="openPhoneSmsApp()"
                class="col-span-1 inline-flex items-center justify-center space-x-2 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs border border-slate-200 transition">
                <i data-lucide="smartphone" class="w-4 h-4 text-slate-600"></i>
                <span>Device SMS</span>
            </button>
        </div>

        <!-- Statement Link Footer -->
        <div class="mt-3 text-center">
            <a id="shareStatementLink" href="#" target="_blank" class="text-xs font-semibold text-sky-600 hover:text-sky-700 inline-flex items-center space-x-1 hover:underline">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Open Printable Official Statement</span>
            </a>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Building-Wide Broadcast & Summary Modal -->
<!-- ============================================================== -->
<div id="buildingBroadcastModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-150">
        <!-- Close Button -->
        <button onclick="closeBuildingModal()" class="absolute top-5 right-5 p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- Header -->
        <div class="flex items-start space-x-3 pb-4 border-b border-slate-100">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-500 to-sky-600 text-white flex items-center justify-center shadow-md shadow-sky-500/20 shrink-0">
                <i data-lucide="megaphone" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-sky-600 font-mono">Building-Wide Notice</span>
                <h3 class="text-lg font-extrabold text-slate-900">Apartments Water Billing Broadcast</h3>
                <p class="text-xs text-slate-500">Generate, customize, and broadcast monthly schedule to the building WhatsApp group or SMS list.</p>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-4 gap-2 my-4 text-center">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="block text-[10px] uppercase font-bold text-slate-400">Month</span>
                <span id="bldMonth" class="font-mono font-bold text-xs sm:text-sm text-slate-800">--</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="block text-[10px] uppercase font-bold text-slate-400">Total Billed</span>
                <span id="bldTotalBilled" class="font-mono font-bold text-xs sm:text-sm text-slate-800">KSh 0</span>
            </div>
            <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100">
                <span class="block text-[10px] uppercase font-bold text-emerald-600">Settled Units</span>
                <span id="bldPaidUnits" class="font-mono font-bold text-xs sm:text-sm text-emerald-700">0</span>
            </div>
            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-100">
                <span class="block text-[10px] uppercase font-bold text-rose-600">Pending Units</span>
                <span id="bldPendingUnits" class="font-mono font-bold text-xs sm:text-sm text-rose-700">0</span>
            </div>
        </div>

        <!-- Format Switcher Tabs -->
        <div class="flex rounded-xl bg-slate-100 p-1 mb-3 text-xs font-semibold">
            <button type="button" id="tabBldAll" onclick="switchBuildingTab('all')"
                class="flex-1 py-1.5 rounded-lg bg-white text-slate-800 shadow-sm transition">
                Full Building Schedule (All Units)
            </button>
            <button type="button" id="tabBldPending" onclick="switchBuildingTab('pending')"
                class="flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition">
                Pending Residents Reminder Only
            </button>
        </div>

        <!-- Textarea Preview -->
        <div class="space-y-1.5">
            <div class="flex justify-between text-xs text-slate-500 font-semibold uppercase tracking-wider">
                <span>Message Preview</span>
                <span class="text-[11px] font-mono text-slate-400"><span id="bldCharCount">0</span> chars</span>
            </div>
            <textarea id="buildingMessageText" rows="8"
                oninput="document.getElementById('bldCharCount').innerText = this.value.length"
                class="w-full p-3.5 border border-slate-200 rounded-2xl text-xs sm:text-sm font-sans text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 leading-relaxed font-normal resize-y shadow-inner"></textarea>
        </div>

        <!-- Building Actions Grid -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap sm:flex-nowrap gap-2.5">
            <!-- Copy Building Notice -->
            <button type="button" id="copyBldBtn" onclick="copyBuildingMessage()"
                class="flex-1 inline-flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-semibold text-xs shadow-sm transition">
                <i data-lucide="copy" class="w-4 h-4" id="copyBldIcon"></i>
                <span id="copyBldBtnText">Copy Building Message</span>
            </button>

            <!-- Share to Building WhatsApp Group -->
            <button type="button" onclick="shareBuildingWhatsApp()"
                class="flex-1 inline-flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm shadow-emerald-600/20 transition">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Share to WhatsApp Group</span>
            </button>

            <!-- Bulk SMS to Pending -->
            <button type="button" onclick="dispatchBulkSms()"
                class="inline-flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold text-xs border border-sky-200 transition">
                <i data-lucide="send" class="w-4 h-4 text-sky-600"></i>
                <span>Bulk SMS Unpaid</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- JavaScript Logic for Share Modals -->
<!-- ============================================================== -->
<script>
    let activeShareData = null;
    let activeBuildingData = null;

    /**
     * Open Single Invoice Share Modal
     */
    function openShareModal(invoiceId) {
        Swal.fire({
            title: 'Loading Bill Details...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/api/invoice-share-details?id=${invoiceId}`)
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    activeShareData = res;
                    const inv = res.invoice;

                    document.getElementById('shareUnitBadge').innerText = `House #${inv.house_number}`;
                    document.getElementById('shareTenantName').innerText = `${inv.firstname} ${inv.lastname}`;
                    document.getElementById('sharePhone').innerText = inv.phonenumber;
                    document.getElementById('shareAmount').innerText = `KSh ${Number(inv.total_bill).toLocaleString()}`;
                    
                    const textarea = document.getElementById('shareMessageText');
                    textarea.value = res.message;
                    document.getElementById('shareCharCount').innerText = res.message.length;

                    document.getElementById('shareStatementLink').href = `/tenant/statement?id=${inv.id}`;

                    // Reset copy button state
                    resetCopyBtn();

                    document.getElementById('shareInvoiceModal').classList.remove('hidden');
                    lucide.createIcons();
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: res.error || 'Could not load bill details.'});
                }
            })
            .catch(err => {
                Swal.fire({icon: 'error', title: 'Network Error', text: err.message});
            });
    }

    function closeShareModal() {
        document.getElementById('shareInvoiceModal').classList.add('hidden');
    }

    function sendInvoiceWhatsApp() {
        if (!activeShareData) return;
        const msg = document.getElementById('shareMessageText').value;
        const intPhone = activeShareData.int_phone;
        const url = `https://wa.me/${intPhone}?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');
    }

    function openPhoneSmsApp() {
        if (!activeShareData) return;
        const msg = document.getElementById('shareMessageText').value;
        const phone = activeShareData.invoice.phonenumber;
        window.location.href = `sms:${phone}?body=${encodeURIComponent(msg)}`;
    }

    function copyInvoiceMessage() {
        const text = document.getElementById('shareMessageText').value;
        navigator.clipboard.writeText(text).then(() => {
            const btnText = document.getElementById('copyBtnText');
            btnText.innerText = 'Copied to Clipboard! ✓';
            btnText.parentElement.classList.add('bg-emerald-700');
            setTimeout(() => {
                resetCopyBtn();
            }, 2500);
        }).catch(() => {
            // Fallback
            document.getElementById('shareMessageText').select();
            document.execCommand('copy');
            alert('Copied to clipboard!');
        });
    }

    function resetCopyBtn() {
        const btnText = document.getElementById('copyBtnText');
        if (btnText) {
            btnText.innerText = 'Copy Message';
            btnText.parentElement.classList.remove('bg-emerald-700');
        }
    }

    function sendInvoiceGatewaySms() {
        if (!activeShareData) return;
        const invId = activeShareData.invoice.id;
        const tenantName = activeShareData.invoice.firstname;

        Swal.fire({
            title: 'Dispatch Gateway SMS',
            text: `Send official SMS alert to ${tenantName} via backend SMS gateway?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Send Now',
            confirmButtonColor: '#0284c7'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/api/send-sms', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({invoice_id: invId})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'SMS Sent!',
                            text: data.message,
                            confirmButtonColor: '#0284c7'
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'Failed', text: data.error});
                    }
                });
            }
        });
    }

    /**
     * Open Building Broadcast Modal
     */
    function openBuildingBroadcastModal(month = null) {
        Swal.fire({
            title: 'Generating Building Notice...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const url = month ? `/api/building-summary?month=${month}` : '/api/building-summary';

        fetch(url)
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    activeBuildingData = res.data;
                    const d = res.data;

                    document.getElementById('bldMonth').innerText = d.month;
                    document.getElementById('bldTotalBilled').innerText = `KSh ${Number(d.total_billed).toLocaleString()}`;
                    document.getElementById('bldPaidUnits').innerText = `${d.paid_count} / ${d.total_count}`;
                    document.getElementById('bldPendingUnits').innerText = `${d.pending_count} Unpaid`;

                    // Default to Full Building Schedule tab
                    switchBuildingTab('all');

                    resetBuildingCopyBtn();
                    document.getElementById('buildingBroadcastModal').classList.remove('hidden');
                    lucide.createIcons();
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: res.error || 'Could not load building summary.'});
                }
            })
            .catch(err => {
                Swal.fire({icon: 'error', title: 'Network Error', text: err.message});
            });
    }

    function closeBuildingModal() {
        document.getElementById('buildingBroadcastModal').classList.add('hidden');
    }

    function switchBuildingTab(tab) {
        if (!activeBuildingData) return;
        const tabAll = document.getElementById('tabBldAll');
        const tabPending = document.getElementById('tabBldPending');
        const textarea = document.getElementById('buildingMessageText');

        if (tab === 'all') {
            tabAll.className = "flex-1 py-1.5 rounded-lg bg-white text-slate-800 shadow-sm transition";
            tabPending.className = "flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition";
            textarea.value = activeBuildingData.building_message;
        } else {
            tabPending.className = "flex-1 py-1.5 rounded-lg bg-white text-slate-800 shadow-sm transition";
            tabAll.className = "flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition";
            textarea.value = activeBuildingData.reminder_message;
        }

        document.getElementById('bldCharCount').innerText = textarea.value.length;
    }

    function copyBuildingMessage() {
        const text = document.getElementById('buildingMessageText').value;
        navigator.clipboard.writeText(text).then(() => {
            const btnText = document.getElementById('copyBldBtnText');
            btnText.innerText = 'Building Notice Copied! ✓';
            btnText.parentElement.classList.add('bg-emerald-700');
            setTimeout(() => {
                resetBuildingCopyBtn();
            }, 2500);
        }).catch(() => {
            document.getElementById('buildingMessageText').select();
            document.execCommand('copy');
            alert('Copied to clipboard!');
        });
    }

    function resetBuildingCopyBtn() {
        const btnText = document.getElementById('copyBldBtnText');
        if (btnText) {
            btnText.innerText = 'Copy Building Message';
            btnText.parentElement.classList.remove('bg-emerald-700');
        }
    }

    function shareBuildingWhatsApp() {
        const msg = document.getElementById('buildingMessageText').value;
        const url = `https://wa.me/?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');
    }

    function dispatchBulkSms() {
        if (!activeBuildingData) return;
        const pendingCount = activeBuildingData.pending_count;
        const month = activeBuildingData.month;

        if (pendingCount === 0) {
            Swal.fire({
                icon: 'info',
                title: 'All Bills Settled!',
                text: 'There are no pending invoices to remind for this month.'
            });
            return;
        }

        Swal.fire({
            title: 'Bulk SMS Reminder',
            text: `Dispatch SMS reminders to all ${pendingCount} unpaid tenant(s) for ${month}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: `Yes, Send to ${pendingCount} Tenants`,
            confirmButtonColor: '#0284c7'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Dispatching Bulk SMS...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch('/api/bulk-sms', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({month: month})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Bulk Dispatch Completed!',
                            html: `${data.message}<br><br><small class="text-slate-500 font-mono">${data.recipients.join(', ')}</small>`,
                            confirmButtonColor: '#0284c7'
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'Bulk Dispatch Failed', text: data.error});
                    }
                });
            }
        });
    }

    // Close modals on Escape key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeShareModal();
            closeBuildingModal();
        }
    });
</script>
