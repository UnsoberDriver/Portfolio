<?php
$env = parse_ini_file(dirname(__DIR__) . '/.env');
$db_host = $env['DB_HOST'];
$db_name = $env['DB_NAME'];
$db_user = $env['DB_USER'];
$db_pass = $env['DB_PASS'];

$feedback = $_GET['contact'] ?? '';

$allowedRedirects = ['index.php', 'main/contact.html'];
$redirect = $_POST['redirect'] ?? 'index.php';
if (!in_array($redirect, $allowedRedirects, true)) {
    $redirect = 'index.php';
}
$anchor = $redirect === 'index.php' ? '#contact' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Honeypot : champ invisible pour les humains, souvent rempli par les bots
    $honeypot = trim($_POST['site_web'] ?? '');

    // Anti-soumission instantanée : un humain met toujours plus de 2s à remplir le formulaire
    $formLoadedAt = (int) ($_POST['ts'] ?? 0);
    $elapsed = time() - $formLoadedAt;
    $tooFast = $formLoadedAt > 0 && $elapsed < 2;

    if ($honeypot !== '' || $tooFast) {
        // On fait croire au bot que ça a marché, sans rien enregistrer
        header("Location: {$redirect}?contact=success{$anchor}");
        exit;
    }

    // Filtrage de contenu basique : rejet si plusieurs URLs (signe de spam)
    $urlCount = preg_match_all('/https?:\/\//i', $message);
    if ($urlCount > 1) {
        header("Location: {$redirect}?contact=error{$anchor}");
        exit;
    }

    // Vérification Cloudflare Turnstile (CAPTCHA invisible)
    $turnstileSecret = $env['TURNSTILE_SECRET_KEY'] ?? '';
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';

    $turnstileOk = false;
    if ($turnstileSecret !== '' && $turnstileToken !== '') {
        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => $turnstileSecret,
                'response' => $turnstileToken,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);
        $verifyResponse = curl_exec($ch);
        curl_close($ch);

        if ($verifyResponse !== false) {
            $verifyData = json_decode($verifyResponse, true);
            $turnstileOk = !empty($verifyData['success']);
        }
    }

    if (!$turnstileOk) {
        header("Location: {$redirect}?contact=error{$anchor}");
        exit;
    }

    if ($nom !== '' && $prenom !== '' && $message !== '') {
        try {
            $pdo = new PDO(
                "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Rate limiting par IP : max 3 messages / heure
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

            $countStmt = $pdo->prepare(
                'SELECT COUNT(*) FROM contact_attempts WHERE ip = :ip AND created_at > (NOW() - INTERVAL 1 HOUR)'
            );
            $countStmt->execute([':ip' => $ip]);
            $recentAttempts = (int) $countStmt->fetchColumn();

            if ($recentAttempts >= 3) {
                header("Location: {$redirect}?contact=error{$anchor}");
                exit;
            }

            $logStmt = $pdo->prepare(
                'INSERT INTO contact_attempts (ip, created_at) VALUES (:ip, NOW())'
            );
            $logStmt->execute([':ip' => $ip]);

            $stmt = $pdo->prepare(
                'INSERT INTO messages (nom, prenom, message) VALUES (:nom, :prenom, :message)'
            );
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':message' => $message,
            ]);

            header("Location: {$redirect}?contact=success{$anchor}");
            exit;
        } catch (PDOException $e) {
            error_log('Contact form DB error: ' . $e->getMessage());
            header("Location: {$redirect}?contact=error{$anchor}");
            exit;
        }
    } else {
        header("Location: {$redirect}?contact=error{$anchor}");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="auteur" content="Nicolas Boulloud">
    <meta name="description"
        content="Portfolio de Nicolas Boulloud, étudiant en Multimédia et Technologies de l'Internet à Tarbes, passionné de développement web et de création d'expériences numériques modernes et accessibles.">
    <title>Portfolio - Nicolas Boulloud</title>
    <meta property="og:site_name" content="Nicolas Boulloud — Portfolio">
    <link rel="canonical" href="https://pt-nb.alwaysdata.net/">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Portfolio",
      "url": "https://pt-nb.alwaysdata.net/"
    }
    </script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kings&family=Anton&display=optional"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kings&family=Anton&display=optional">
    </noscript>
    <link rel="stylesheet"
        href="assets/styles/styles.css?v=<?php echo filemtime(__DIR__ . '/assets/styles/styles.css'); ?>" media="print"
        onload="this.media='all'">
    <noscript>
        <link rel="stylesheet"
            href="assets/styles/styles.css?v=<?php echo filemtime(__DIR__ . '/assets/styles/styles.css'); ?>">
    </noscript>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/icones/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/icones/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/icones/favicon-180.png">
    <link rel="shortcut icon" href="assets/icones/favicon.ico">
    <link rel="preload" as="image" href="assets/icones/fond.png">
    <link rel="preload" as="image" href="assets/icones/portrait2.png" fetchpriority="high">
    <script>
        (function () {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('theme-light');
            }
        })();

        function toggleTheme(evt) {
            var root = document.documentElement;
            var goingLight = !root.classList.contains('theme-light');

            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var supportsVT = typeof document.startViewTransition === 'function';

            if (reduceMotion || !supportsVT) {
                applyTheme(goingLight);
                return;
            }

            // Coordonnées du clic (ou centre du bouton) pour l'origine du cercle
            var x = evt && evt.clientX ? evt.clientX : window.innerWidth / 2;
            var y = evt && evt.clientY ? evt.clientY : window.innerHeight / 2;
            var endRadius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y)
            );

            var transition = document.startViewTransition(function () {
                applyTheme(goingLight);
            });

            transition.ready.then(function () {
                var clipPath = [
                    'circle(0px at ' + x + 'px ' + y + 'px)',
                    'circle(' + endRadius + 'px at ' + x + 'px ' + y + 'px)'
                ];

                document.documentElement.animate(
                    {
                        clipPath: goingLight ? clipPath : clipPath.slice().reverse()
                    },
                    {
                        duration: 650,
                        easing: 'cubic-bezier(.65, 0, .35, 1)',
                        pseudoElement: goingLight
                            ? '::view-transition-new(root)'
                            : '::view-transition-old(root)'
                    }
                );
            });
        }

        function applyTheme(goingLight) {
            var root = document.documentElement;
            root.classList.toggle('theme-light', goingLight);
            localStorage.setItem('theme', goingLight ? 'light' : 'dark');
            updateThemeIcon();
        }

        function updateThemeIcon() {
            var isLight = document.documentElement.classList.contains('theme-light');
            document.querySelectorAll('.theme-toggle').forEach(function (btn) {
                btn.textContent = isLight ? '☾' : '☀';
            });
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcon);
    </script>
    <style>
        /* On désactive le cross-fade par défaut de la View Transitions API */
        ::view-transition-group(root) {
            animation-duration: 650ms;
        }

        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none;
            mix-blend-mode: normal;
        }

        ::view-transition-old(root) {
            z-index: 1;
        }

        ::view-transition-new(root) {
            z-index: 9999;
        }

        /* Passage vers le sombre : l'ancien (clair) doit rester au-dessus pour se refermer en cercle */
        html:not(.theme-light)::view-transition-old(root) {
            z-index: 9999;
        }

        html:not(.theme-light)::view-transition-new(root) {
            z-index: 1;
        }
    </style>
    <style>
        :root {
            --bg-color: #000;
            --text-color: #fff;
            --card-bg: #111;
            --tag-bg: #2b2b2b
        }

        html.theme-light {
            --bg-color: #fff;
            --text-color: #111;
            --card-bg: #fff;
            --tag-bg: #e6e6e6
        }

        * {
            color: var(--text-color);
            font-family: sans-serif;
            text-align: center;
            user-select: none;
            font-style: normal
        }

        html,
        body {
            height: 100%;
            margin: 0
        }

        html {
            background-color: var(--bg-color);
            scroll-behavior: smooth;
            scrollbar-gutter: stable;
            overflow-y: scroll
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: var(--bg-color);
            transition: background-color .25s ease
        }

        .contact-form-honeypot {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden
        }

        .p1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 8em;
            line-height: 1;
            font-family: "Anton", "Arial Narrow", sans-serif;
            text-align: center;
            -webkit-text-stroke: 1.5px currentColor;
            text-shadow: none;
            filter: drop-shadow(0 6px 4px rgba(255, 255, 255, 0.35))
        }

        @media(max-width:900px) {

            .p1,
            .p1-echo {
                white-space: nowrap;
                font-size: 8vw
            }

            .p1-line {
                display: inline
            }

            .p1-line-name {
                font-size: 1em
            }
        }

        @media(max-width:640px) {

            .p1,
            .p1-echo {
                font-size: 9vw
            }
        }

        .hero-title {
            position: relative
        }

        .p1-echo {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            width: 100%;
            margin: 0;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1.5px var(--text-color);
            text-shadow: none;
            transform: translateY(0.09em);
            -webkit-mask-image: linear-gradient(to bottom, transparent 0, transparent 55%, rgba(0, 0, 0, 0.85) 100%);
            mask-image: linear-gradient(to bottom, transparent 0, transparent 55%, rgba(0, 0, 0, 0.85) 100%);
            pointer-events: none;
            user-select: none
        }

        .p1-echo .p1-line {
            color: transparent;
            -webkit-text-stroke: 1.5px var(--text-color)
        }

        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
            padding-top: 110px;
            background-image: url('assets/icones/fond.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat
        }

        html.theme-light header {
            background-image: url('assets/icones/fond2.png')
        }

        .hero-title-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%
        }

        .scroll-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            margin: 0 auto 32px auto;
            animation: bounce 2s infinite
        }

        .scroll-indicator svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: var(--text-color);
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(8px)
            }
        }

        .navbar,
        .navbar * {
            color: #fff
        }

        .navbar {
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 48px;
            width: fit-content;
            max-width: 90%;
            padding: 12px 32px;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(50, 50, 50, 0.85), rgba(5, 5, 5, 0.9));
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-sizing: border-box
        }

        .navbar-logo {
            display: flex;
            align-items: center
        }

        .navbar-logo img {
            height: 32px;
            width: auto;
            display: block
        }

        .navbar-links-wrap {
            position: relative;
            display: flex;
            align-items: center
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
            margin: 0;
            padding: 0
        }

        .nav-indicator {
            position: absolute;
            top: -9px;
            left: 0;
            width: 24px;
            height: 5px;
            border-radius: 999px;
            background-color: #fff;
            box-shadow: 0 0 6px 1px rgba(255, 255, 255, 0.5);
            transform: translateX(-50%);
            opacity: 0;
            pointer-events: none;
            transition: left .45s cubic-bezier(0.65, 0, 0.35, 1), opacity .25s ease
        }

        .nav-indicator.is-visible {
            opacity: 1
        }

        .navbar-links a {
            position: relative;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .01em;
            color: #e6e6e6;
            text-decoration: none;
            transition: color .25s ease
        }

        .navbar-links a:hover,
        .navbar-links a:visited:hover {
            color: #fff;
            text-decoration: none
        }

        .navbar-links a.actif {
            color: #fff
        }

        .navbar-brand {
            display: none;
            align-items: center;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            text-decoration: none;
            white-space: nowrap
        }

        .navbar-brand-logo {
            display: none;
            height: 28px;
            width: auto
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 14px
        }

        .navbar-hamburger {
            display: none;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.12);
            color: #fff;
            cursor: pointer;
            transition: background-color .25s ease
        }

        .navbar-hamburger:hover {
            background-color: rgba(255, 255, 255, 0.24)
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 1rem;
            line-height: 1;
            cursor: pointer;
            transition: background-color .25s ease
        }

        .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.24)
        }

        @media(max-width:640px) {
            .navbar {
                width: calc(100% - 24px);
                max-width: none;
                justify-content: space-between;
                gap: 0;
                padding: 10px 18px;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                left: 12px;
                right: 12px;
                transform: none
            }

            .navbar-brand {
                display: flex;
                position: relative;
                z-index: 1600
            }

            .navbar-brand-text {
                display: none
            }

            .navbar-brand-logo {
                display: block
            }

            .navbar-hamburger {
                display: flex
            }

            .navbar-actions {
                position: relative;
                z-index: 1600
            }

            .navbar-links {
                position: fixed;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 40px;
                background: rgba(10, 10, 10, 0.98);
                border-radius: 0;
                padding: 0;
                max-height: none;
                overflow: visible;
                opacity: 0;
                pointer-events: none;
                transition: opacity .25s ease;
                box-shadow: none;
                z-index: 1500
            }

            .navbar-links.open {
                opacity: 1;
                pointer-events: auto;
                padding: 0
            }

            .navbar-links a {
                display: block;
                width: auto;
                padding: 0;
                font-size: 1.8rem;
                text-align: center
            }

            .nav-indicator {
                display: none
            }

            .theme-toggle {
                width: 26px;
                height: 26px
            }
        }
    </style>
</head>


<body>

    <header>

        <nav class="navbar">
            <a href="index.html" class="navbar-brand">
                <span class="navbar-brand-text">Nicolas Boulloud</span>
                <img src="assets/icones/logo2.png" alt="Nicolas Boulloud" class="navbar-brand-logo" width="28"
                    height="28">
            </a>

            <div class="navbar-links-wrap">
                <span class="nav-indicator" id="navIndicator" aria-hidden="true"></span>
                <ul class="navbar-links" id="navbarLinks">
                    <li><a href="index.html" class="actif">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#realisations">Projects</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="navbar-actions">
                <button class="theme-toggle" onclick="toggleTheme(event)" type="button"
                    aria-label="Basculer le thème clair/sombre" title="Thème clair/sombre">☀</button>
                <button class="navbar-hamburger" id="navbarHamburger" type="button" aria-label="Menu"
                    aria-expanded="false">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </nav>

        <div class="hero-title-wrap">
            <div class="hero-title">
                <p class="p1"><span class="p1-line">NICOLAS</span> <span class="p1-line p1-line-name">BOULLOUD</span>
                </p>
                <p class="p1 p1-echo" aria-hidden="true"><span class="p1-line">NICOLAS</span> <span
                        class="p1-line p1-line-name">BOULLOUD</span></p>
            </div>
        </div>

        <a href="#about" class="scroll-indicator" aria-label="Défiler vers le bas">
            <svg viewBox="0 0 24 24" width="28" height="28">
                <path d="M6 9l6 6 6-6"></path>
            </svg>
        </a>

        <div class="social-float">
            <a href="https://www.linkedin.com/in/nicolas-boulloud/" target="_blank" rel="noopener" aria-label="LinkedIn"
                title="LinkedIn">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                </svg>
            </a>
            <a href="https://github.com/UnsoberDriver" target="_blank" rel="noopener" aria-label="GitHub"
                title="GitHub">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path
                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                    </path>
                </svg>
            </a>
            <a href="#contact" aria-label="Me contacter" title="Me contacter">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22 6 12 13 2 6"></polyline>
                </svg>
            </a>
        </div>

    </header>

    <main>

        <section id="about" class="border">

            <div class="section-heading">
                <span class="section-heading-title">About</span>
                <span class="section-heading-line"></span>
            </div>

            <div class="profile-block">
                <div class="profile-photo">
                    <img src="assets/icones/portrait2.avif" alt="Photo de Nicolas Boulloud" fetchpriority="high">
                </div>
                <div class="profile-text">
                    <div class="profile-tags">
                        <span class="tag tag-location">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Tarbes
                        </span>
                        <span class="tag">Développement Web</span>
                        <span class="tag">Back-end</span>
                    </div>

                    <div class="reveal-wrap">
                        <p class="p3">Second-year student in Multimedia and Internet Technologies at the Institute of
                            Technology of
                            Tarbes. Passionate about web development and data analysis, I am learning how to turn raw
                            data into
                            clear, actionable
                            insights through code. I am seeking an internship for my second year to
                            further develop my
                            technical skills and contribute to real-world digital projects.
                        </p>
                        <div class="reveal-mask" aria-hidden="true"></div>
                    </div>
                </div>
            </div>

            <hr>

            <h3 class="timeline-heading">My Education</h3>

            <ul class="timeline">
                <span class="timeline-glow" aria-hidden="true"></span>
                <li class="timeline-item">
                    <span class="timeline-marker"></span>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h4 class="timeline-title">Lycée Saint-Cricq - Pau</h4>
                            <span class="timeline-date">2022 - 2025</span>
                        </div>
                        <p class="timeline-subtitle">Baccalauréat technologique - mention bien</p>
                    </div>
                </li>
                <li class="timeline-item">
                    <span class="timeline-marker"></span>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h4 class="timeline-title">IUT de Tarbes</h4>
                            <span class="timeline-date">2025 - 2027</span>
                        </div>
                        <p class="timeline-subtitle">Bac +1 - Multimedia and Internet Technologies</p>
                    </div>
                </li>
            </ul>

            <hr class="timeline-divider">

            <a href="assets/icones/CV.pdf" target="_blank" rel="noopener" class="cv-button">
                View my CV
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>

        </section>

        <section id="realisations" class="border">

            <div class="section-heading">
                <span class="section-heading-title">Projects</span>
                <span class="section-heading-line"></span>
            </div>

            <div class="projects-grid">

                <a class="project-card-link" href="https://romanbg.itch.io/maze-gate-of-hell" target="_blank"
                    rel="noopener">
                    <article class="project-card">
                        <img class="project-media" src="assets/icones/image.avif" alt="Système de vote" width="800"
                            height="220" loading="lazy">

                        <div class="project-content">
                            <h3>Scratch game</h3>

                            <p>
                                Maze: Gate of hell is a 2D escape game made for a school project.
                            </p>

                            <div class="project-tags">
                                <span>Scratch</span>
                                <span>Video game</span>
                            </div>
                        </div>
                    </article>
                </a>

                <a class="project-card-link" href="https://sae203.byethost10.com/?i=1" target="_blank" rel="noopener">
                    <article class="project-card">
                        <img class="project-media" src="assets/icones/systeme_vote.avif" alt="Système de vote"
                            width="800" height="220" loading="lazy">

                        <div class="project-content">
                            <h3>Voting system</h3>

                            <p>
                                Design of a voting system enabling the creation and management of interactive polls.
                            </p>

                            <div class="project-tags">
                                <span>HTML</span>
                                <span>CSS</span>
                                <span>PHP</span>
                                <span>JavaScript</span>
                                <span>MySQL</span>
                            </div>
                        </div>
                    </article>
                </a>

                <a class="project-card-link" href="https://homekitchenclub.alwaysdata.net/" target="_blank"
                    rel="noopener">
                    <article class="project-card">
                        <img class="project-media" src="assets/icones/fond3.png" alt="Recipes website" width="800"
                            height="220" loading="lazy">

                        <div class="project-content">
                            <h3>Recipe website</h3>

                            <p>
                                Public recipe browsing, admins can add, remove and update recipes.
                            </p>

                            <div class="project-tags">
                                <span>CSS</span>
                                <span>PHP</span>
                                <span>JavaScript</span>
                                <span>MySQL</span>
                            </div>
                        </div>
                    </article>
                </a>

                <a class="project-card-link" href="https://homekitchenclub.alwaysdata.net/" target="_blank"
                    rel="noopener">
                    <article class="project-card">
                        <img class="project-media" src="assets/icones/fond4.png" alt="Recipes website" width="800"
                            height="220" loading="lazy">

                        <div class="project-content">
                            <h3>Messaging platform</h3>

                            <p>
                                Nexus Pulse is a private instant messaging platform that lets you communicate with your friends.
                            </p>

                            <div class="project-tags">
                                <span>CSS</span>
                                <span>PHP</span>
                                <span>JavaScript</span>
                                <span>MySQL</span>
                            </div>
                        </div>
                    </article>
                </a>

            </div>

            <div class="section-heading" id="contact">
                <span class="section-heading-title">Contact</span>
                <span class="section-heading-line"></span>
            </div>

            <div class="contact-layout">

                <aside class="contact-info">
                    <div class="contact-info-header">
                        <div class="contact-info-photo">
                            <img src="assets/icones/portrait2.avif" alt="Photo de Nicolas Boulloud" loading="lazy">
                        </div>
                    </div>

                    <div class="contact-info-body">
                        <p class="contact-info-label">Useful links :</p>

                        <div class="contact-info-links">
                            <a class="contact-link contact-link-linkedin"
                                href="https://www.linkedin.com/in/nicolas-boulloud/" target="_blank" rel="noopener"
                                aria-label="LinkedIn">
                                <svg viewBox="0 0 24 24" width="18" height="18">
                                    <path
                                        d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                    </path>
                                    <rect x="2" y="9" width="4" height="12"></rect>
                                    <circle cx="4" cy="4" r="2"></circle>
                                </svg>
                                LinkedIn
                            </a>
                            <a class="contact-link contact-link-github" href="https://github.com/UnsoberDriver"
                                target="_blank" rel="noopener" aria-label="GitHub">
                                <svg viewBox="0 0 24 24" width="18" height="18">
                                    <path
                                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                                    </path>
                                </svg>
                                GitHub
                            </a>
                        </div>

                    </div>
                </aside>

                <form
                    class="contact-form<?php echo $feedback === 'success' ? ' show-success' : ($feedback === 'error' ? ' show-error' : ''); ?>"
                    action="index.php#contact" method="post">
                    <h3 class="contact-form-title">Send me a message !</h3>

                    <p class="contact-form-feedback contact-form-feedback-success">Message envoyé, merci !</p>
                    <p class="contact-form-feedback contact-form-feedback-error">Une erreur est survenue, réessaie.</p>

                    <!-- Honeypot anti-bot : champ caché, un humain ne le remplit jamais -->
                    <div class="contact-form-honeypot" aria-hidden="true">
                        <label for="site_web">Ne pas remplir ce champ</label>
                        <input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">
                    </div>
                    <input type="hidden" name="ts" value="<?php echo time(); ?>">

                    <div class="contact-form-row">
                        <input type="text" name="prenom" placeholder="Prénom" required>
                        <input type="text" name="nom" placeholder="Nom" required>
                    </div>


                    <textarea name="message" placeholder="Message" required></textarea>

                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAD8Eup0N-xa81hF2"></div>

                    <button type="submit" class="contact-form-submit">
                        Envoyer
                        <svg viewBox="0 0 24 24" width="18" height="18">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>

            </div>

        </section>

    </main>

    <footer>
        <nav>

            <div class="footer-top">

                <div class="footer-col footer-col-mobile-only">
                    <p class="footer-col-title">Contact &amp; Network</p>

                    <p class="footer-contact-item">
                        <svg viewBox="0 0 24 24" width="18" height="18">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22 6 12 13 2 6"></polyline>
                        </svg>
                        <a href="#contact">Me contacter</a>
                    </p>

                    <p class="footer-contact-item">
                        <svg viewBox="0 0 24 24" width="18" height="18">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Tarbes, France
                    </p>

                    <p class="footer-follow-label">Follow me :</p>
                    <div class="footer-socials">
                        <a href="https://www.linkedin.com/in/nicolas-boulloud/" target="_blank" rel="noopener"
                            aria-label="LinkedIn" title="LinkedIn">
                            <svg viewBox="0 0 24 24" width="18" height="18">
                                <path
                                    d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                </path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                        <a href="https://github.com/UnsoberDriver" target="_blank" rel="noopener" aria-label="GitHub"
                            title="GitHub">
                            <svg viewBox="0 0 24 24" width="18" height="18">
                                <path
                                    d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            <div class="footer-row">
                <p class="p2 copyright">© 2026 Nicolas Boulloud. All right reserved.</p>
                <p class="p2 legal-link">
                    <a href="main/legal_notices.html" title="Link toward the legal notices">Legal Notices</a>
                    ·
                    <a href="main/privacy_policy.html" title="Link toward the privacy policy">Privacy Policy</a>
                </p>
            </div>

        </nav>
    </footer>

    <script src="assets/js/app.js?v=<?php echo filemtime(__DIR__ . '/assets/js/app.js'); ?>" defer></script>

</body>

</html>