<?php
$db_host = '';
$db_name = '';
$db_user = '';
$db_pass = '';

$feedback = $_GET['contact'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($nom !== '' && $prenom !== '' && $message !== '') {
        try {
            $pdo = new PDO(
                "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->prepare(
                'INSERT INTO messages (nom, prenom, message) VALUES (:nom, :prenom, :message)'
            );
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':message' => $message,
            ]);

            header('Location: index.php?contact=success#contact');
            exit;
        } catch (PDOException $e) {
            header('Location: index.php?contact=error#contact');
            exit;
        }
    } else {
        header('Location: index.php?contact=error#contact');
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
    <title>Nicolas Boulloud</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kings&family=Anton&display=optional"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kings&family=Anton&display=optional">
    </noscript>
    <link rel="stylesheet" href="Styles/Styles.min.css?v=<?php echo filemtime(__DIR__ . '/Styles/Styles.min.css'); ?>"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet"
            href="Styles/Styles.min.css?v=<?php echo filemtime(__DIR__ . '/Styles/Styles.min.css'); ?>">
    </noscript>
    <link rel="icon" type="image/png" sizes="32x32" href="./icones/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./icones/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="./icones/favicon-180.png">
    <link rel="shortcut icon" href="./icones/favicon.ico">
    <link rel="preload" as="image" href="./icones/fond.avif">
    <link rel="preload" as="image" href="./icones/fond2.avif">
    <link rel="preload" as="image" href="./icones/portrait2.avif" fetchpriority="high">
    <script>
        (function () {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('theme-light');
            }
        })();

        function toggleTheme() {
            var root = document.documentElement;
            root.classList.toggle('theme-light');
            var isLight = root.classList.contains('theme-light');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
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
        :root {
            --bg-color: #000;
            --text-color: #fff;
            --card-bg: #111;
            --tag-bg: #2b2b2b
        }

        html.theme-light {
            --bg-color: #f4f4f4;
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

        .p1 {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 8em;
            line-height: 1;
            font-family: "Anton", "Arial Narrow", sans-serif;
            text-align: center;
            -webkit-text-stroke: 1.5px currentColor;
            text-shadow: 0 6px 4px rgba(255, 255, 255, 0.35)
        }

        @media(max-width:900px) {
            .p1, .p1-echo {
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
            .p1, .p1-echo {
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
            background-image: url('./icones/fond.avif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat
        }

        html.theme-light header {
            background-image: url('./icones/fond2.avif')
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
                <img src="./icones/logo2.png" alt="Nicolas Boulloud" class="navbar-brand-logo" width="28" height="28">
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
                <button class="theme-toggle" onclick="toggleTheme()" type="button"
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
            <a href="https://github.com/UnsoberDriver" target="_blank" rel="noopener" aria-label="GitHub" title="GitHub">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path
                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                    </path>
                </svg>
            </a>
            <a href="mailto:boulloud.nicolas@gmail.com" aria-label="E-mail" title="E-mail">
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
                    <img src="./icones/portrait2.avif" alt="Photo de Nicolas Boulloud" fetchpriority="high">
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

                    <p class="p3">Second-year student in Multimedia and Internet Technologies at the Institute of
                        Technology of
                        Tarbes. Passionate about web development and data analysis, I am learning how to turn raw data into
                        clear, actionable
                        insights through code. I am seeking an internship for my second year to
                        further develop my
                        technical skills and contribute to real-world digital projects.
                    </p>
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

            <a href="./icones/CV.pdf" target="_blank" rel="noopener" class="cv-button">
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
                        <img class="project-media" src="./icones/image.avif" alt="Système de vote" width="800"
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
                        <img class="project-media" src="./icones/systeme_vote.avif" alt="Système de vote" width="800"
                            height="220" loading="lazy">

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

            </div>

            <div class="section-heading" id="contact">
                <span class="section-heading-title">Contact</span>
                <span class="section-heading-line"></span>
            </div>

            <div class="contact-layout">

                <aside class="contact-info">
                    <div class="contact-info-header">
                        <div class="contact-info-photo">
                            <img src="./icones/portrait2.avif" alt="Photo de Nicolas Boulloud" loading="lazy">
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

                        <a class="contact-link contact-link-email" href="mailto:boulloud.nicolas@gmail.com">
                            <svg viewBox="0 0 24 24" width="18" height="18">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22 6 12 13 2 6"></polyline>
                            </svg>
                            <span class="contact-link-email-text">boulloud.nicolas@gmail.com</span>
                        </a>
                    </div>
                </aside>

                <form
                    class="contact-form<?php echo $feedback === 'success' ? ' show-success' : ($feedback === 'error' ? ' show-error' : ''); ?>"
                    action="index.php#contact" method="post">
                    <h3 class="contact-form-title">Send me a message !</h3>

                    <p class="contact-form-feedback contact-form-feedback-success">Message envoyé, merci !</p>
                    <p class="contact-form-feedback contact-form-feedback-error">Une erreur est survenue, réessaie.</p>

                    <div class="contact-form-row">
                        <input type="text" name="prenom" placeholder="Prénom" required>
                        <input type="text" name="nom" placeholder="Nom" required>
                    </div>


                    <textarea name="message" placeholder="Message" required></textarea>

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
                        <a href="mailto:boulloud.nicolas@gmail.com">boulloud.nicolas@gmail.com</a>
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
                    <a href="./legal_notices.html" title="Link toward the legal notices">Legal Notices</a>
                    ·
                    <a href="./privacy_policy.html" title="Link toward the privacy policy">Privacy Policy</a>
                </p>
            </div>

        </nav>
    </footer>

    <script src="app.min.js?v=<?php echo filemtime(__DIR__ . '/app.min.js'); ?>" defer></script>

</body>

</html>
