<div class="bg-white py-8 px-6 shadow-2xl rounded-2xl sm:px-10 border border-slate-100">
    <div class="mb-6 text-center">
        <h3 class="text-xl font-bold text-slate-800">Tenant Registration</h3>
        <p class="text-xs text-slate-500 mt-1">Register your apartment unit to receive bills & pay via M-Pesa</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-3 rounded-r text-sm text-rose-700 flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-500"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST" class="space-y-3.5">
        <div>
            <label for="house_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                Select House Number
            </label>
            <select name="house_id" id="house_id" required
                class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                <option value="">-- Choose Your Apartment Unit --</option>
                <?php foreach ($houses as $h): ?>
                    <option value="<?= $h['id'] ?>">
                        House <?= htmlspecialchars($h['house_number']) ?> (Meter: <?= htmlspecialchars($h['meter_number']) ?>) - <?= $h['status'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="firstname" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">First Name</label>
                <input type="text" name="firstname" id="firstname" required placeholder="John"
                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label for="lastname" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Last Name</label>
                <input type="text" name="lastname" id="lastname" required placeholder="Doe"
                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="phonenumber" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Phone Number</label>
                <input type="tel" name="phonenumber" id="phonenumber" required placeholder="0712345678"
                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 font-mono">
            </div>
            <div>
                <label for="national_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">National ID</label>
                <input type="text" name="national_id" id="national_id" placeholder="32415678"
                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 font-mono">
            </div>
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
            <input type="email" name="email" id="email" required placeholder="john.doe@example.com"
                class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Password</label>
                <input type="password" name="password" id="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label for="confirm_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <div class="pt-3">
            <button type="submit"
                class="w-full flex justify-center items-center space-x-2 py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Complete Registration</span>
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <span class="text-xs text-slate-500">Already registered? </span>
        <a href="/login" class="text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline">Log in here</a>
    </div>
</div>
