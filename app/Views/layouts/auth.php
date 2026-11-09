<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Login | Classic Heights Water') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 items-center justify-center text-white shadow-xl shadow-sky-500/25 mb-4">
            <i data-lucide="droplet" class="w-8 h-8 fill-white"></i>
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-white">Classic Heights</h2>
        <p class="mt-1 text-sm text-sky-400 font-medium">Water Billing & Management System</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <?= $content ?>
    </div>

    <div class="mt-8 text-center text-xs text-slate-500">
        &copy; <?= date('Y') ?> Classic Heights Apartments &bull; Powered by Lensmen Technologies
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
