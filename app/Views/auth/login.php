<div class="bg-white py-8 px-6 shadow-2xl rounded-2xl sm:px-10 border border-slate-100" id="loginCard">
    <!-- Feedback Alerts -->
    <?php if (!empty($error)): ?>
        <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-3 rounded-r text-sm text-rose-700 flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-500"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded-r text-sm text-emerald-700 flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-500"></i>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <!-- Role Switcher Tabs -->
    <div class="flex rounded-xl bg-slate-100 p-1 mb-6">
        <button type="button" id="tabTenant" onclick="setRole('TENANT')" 
            class="w-1/2 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-1.5 bg-white text-slate-800 shadow-sm">
            <i data-lucide="user" class="w-4 h-4 text-sky-600"></i>
            <span>Tenant Portal</span>
        </button>
        <button type="button" id="tabAdmin" onclick="setRole('ADMIN')" 
            class="w-1/2 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-1.5 text-slate-500 hover:text-slate-800">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Admin Portal</span>
        </button>
    </div>

    <!-- Login Form -->
    <form action="/login" method="POST" class="space-y-4">
        <input type="hidden" name="role" id="roleInput" value="TENANT">

        <div>
            <label for="phonenumber" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                Phone Number
            </label>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="phone" class="w-4 h-4"></i>
                </div>
                <input type="tel" name="phonenumber" id="phonenumber" required placeholder="0712345678"
                    class="block w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-mono">
            </div>
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                Password
            </label>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </div>
                <input type="password" name="password" id="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                    class="block w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" id="submitBtn"
                class="w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span id="btnText">Sign In to Tenant Portal</span>
            </button>
        </div>
    </form>

    <!-- Quick Demo Logins Helper -->
    <div class="mt-6 pt-4 border-t border-slate-100">
        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider text-center mb-2">1-Click Demo Accounts</span>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="fillDemo('TENANT', '0712345678', 'tenant123')"
                class="text-left px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-sky-50 hover:border-sky-200 transition text-xs">
                <span class="font-bold text-slate-700 block">Tenant (House 1)</span>
                <span class="text-slate-400 font-mono text-[11px]">0712345678</span>
            </button>
            <button type="button" onclick="fillDemo('ADMIN', '0700111222', 'admin123')"
                class="text-left px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 transition text-xs">
                <span class="font-bold text-slate-700 block">System Admin</span>
                <span class="text-slate-400 font-mono text-[11px]">0700111222</span>
            </button>
        </div>
    </div>

    <!-- Onboarding Link -->
    <div class="mt-4 text-center">
        <span class="text-xs text-slate-500">New tenant without an account? </span>
        <a href="/register" class="text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline">Register House</a>
    </div>
</div>

<script>
    function setRole(role) {
        document.getElementById('roleInput').value = role;
        const tabTenant = document.getElementById('tabTenant');
        const tabAdmin = document.getElementById('tabAdmin');
        const btnText = document.getElementById('btnText');

        if (role === 'ADMIN') {
            tabAdmin.className = "w-1/2 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-1.5 bg-white text-slate-800 shadow-sm";
            tabTenant.className = "w-1/2 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-1.5 text-slate-500 hover:text-slate-800";
            btnText.innerText = "Sign In as Administrator";
        } else {
            tabTenant.className = "w-1/2 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-1.5 bg-white text-slate-800 shadow-sm";
            tabAdmin.className = "w-1/2 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 flex items-center justify-center space-x-1.5 text-slate-500 hover:text-slate-800";
            btnText.innerText = "Sign In to Tenant Portal";
        }
    }

    function fillDemo(role, phone, pass) {
        setRole(role);
        document.getElementById('phonenumber').value = phone;
        document.getElementById('password').value = pass;
    }
</script>
