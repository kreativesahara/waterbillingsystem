<div class="max-w-md mx-auto text-center py-16">
    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-4">
        <i data-lucide="compass" class="w-8 h-8"></i>
    </div>
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">404 - Page Not Found</h1>
    <p class="mt-2 text-sm text-slate-500">The requested route <code class="bg-slate-100 text-sky-600 px-1 py-0.5 rounded font-mono"><?= htmlspecialchars($uri ?? '') ?></code> does not exist.</p>
    <div class="mt-6">
        <a href="/" class="inline-flex items-center space-x-2 px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Return to Safety</span>
        </a>
    </div>
</div>
