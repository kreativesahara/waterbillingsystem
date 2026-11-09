<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Billing System | Classic Heights Apartments</title>
    <link rel="icon" type="image/png" href="/dashboard/images/favicon.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
    <!-- Custom Modern Stylesheet -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Modern Fixed Navbar -->
    <header class="wbs-navbar">
        <div class="nav-container">
            <div class="nav-left">
                <!-- Back to Dashboard Button -->
                <a href="/dashboard/index.html" class="btn-back" title="Return to XAMPP Dashboard">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="brand-group">
                    <div class="brand-icon">💧</div>
                    <div>
                        <div class="brand-name">Water Billing System</div>
                        <div class="brand-sub">Classic Heights Apartments</div>
                    </div>
                </div>
            </div>

            <nav class="nav-right">
                <a href="/dashboard/index.html" class="nav-link-custom">Dashboard</a>
                <a href="setup_database.php" class="nav-link-custom">DB Setup</a>
                <a href="admin/login.php" class="nav-link-custom">Admin Login</a>
                <a href="user/login.php" class="nav-link-custom">Tenant Login</a>
                <a href="/dashboard/phpinfo.php" class="nav-link-custom">PHP Info</a>

                <!-- Theme Toggle -->
                <button type="button" class="theme-toggle-btn" id="themeToggle" title="Toggle Dark/Light Mode">
                    <svg id="themeIconSun" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="themeIconMoon" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <main class="main-wrapper">
        <div class="content-container">

            <!-- Hero Header -->
            <div class="hero-block">
                <span class="badge-pill">Utility Management Portal</span>
                <h1 class="hero-heading">Water Management &amp; Billing</h1>
                <p class="hero-subtext">
                    Automated meter reading recording, monthly tenant statement generation, and instant mobile billing for Classic Heights Apartments.
                </p>

                <div class="feature-pills">
                    <div class="feat-pill">
                        <span class="feat-icon">📊</span>
                        <span>Accurate Meter Tracking</span>
                    </div>
                    <div class="feat-pill">
                        <span class="feat-icon">💬</span>
                        <span>SMS Notification Dispatch</span>
                    </div>
                    <div class="feat-pill">
                        <span class="feat-icon">📱</span>
                        <span>M-Pesa Mobile Payments</span>
                    </div>
                </div>
            </div>

            <!-- Profile Selection Cards -->
            <div class="profile-selection-section">
                <h2 class="selection-title">Select Your Login Profile</h2>
                <p class="selection-subtitle">Choose the appropriate portal based on your account role</p>

                <div class="portal-grid">
                    <!-- Admin Portal Card -->
                    <div class="portal-card portal-card-admin">
                        <div class="portal-card-header">
                            <div class="portal-avatar avatar-admin">
                                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="portal-tag tag-admin">Management</span>
                        </div>
                        <h3 class="portal-role">Administrator</h3>
                        <p class="portal-description">
                            Access management tools to input monthly tenant water meter readings, compute billing tariffs, register new consumers, and review payment history.
                        </p>
                        <ul class="portal-features">
                            <li>✓ Record monthly meter readings</li>
                            <li>✓ Calculate tariffs &amp; meter rent</li>
                            <li>✓ Register &amp; manage apartments</li>
                            <li>✓ SMS dispatch &amp; billing records</li>
                        </ul>
                        <a href="admin/login.php" class="btn-portal btn-portal-admin">
                            <span>Login as Administrator</span>
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Tenant Portal Card -->
                    <div class="portal-card portal-card-user">
                        <div class="portal-card-header">
                            <div class="portal-avatar avatar-user">
                                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="portal-tag tag-user">Resident</span>
                        </div>
                        <h3 class="portal-role">Tenant Portal</h3>
                        <p class="portal-description">
                            View your current monthly water bill statement, track past consumption trends, check your meter balance, and pay conveniently via M-Pesa.
                        </p>
                        <ul class="portal-features">
                            <li>✓ Real-time bill statement view</li>
                            <li>✓ Current &amp; previous meter readings</li>
                            <li>✓ Units consumed &amp; standing charges</li>
                            <li>✓ Mobile Money payment support</li>
                        </ul>
                        <a href="user/login.php" class="btn-portal btn-portal-user">
                            <span>Login as Tenant</span>
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Modern Footer -->
    <footer class="wbs-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <strong>CLASSIC HEIGHTS APARTMENTS</strong>
                <span>&bull; Water Utility Management</span>
            </div>
            <div class="footer-copy">
                &copy; <?= date('Y') ?> Lensmen Technologies. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Synchronized Theme Toggle
        const htmlEl = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('themeIconSun');
        const moonIcon = document.getElementById('themeIconMoon');

        function setTheme(theme) {
            htmlEl.setAttribute('data-theme', theme);
            localStorage.setItem('phpinfo_theme', theme);
            if (theme === 'dark') {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        }

        const savedTheme = localStorage.getItem('phpinfo_theme') || 
            (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        setTheme(savedTheme);

        themeToggle.addEventListener('click', () => {
            const current = htmlEl.getAttribute('data-theme');
            setTheme(current === 'dark' ? 'light' : 'dark');
        });
    </script>
</body>
</html>
