## Portfolio

Discover my portfolio online here : **[pt-nb.alwaysdata.net](https://pt-nb.alwaysdata.net/)**

This repo contains the source code of my personal website, made with love in PHP/HTML/CSS/JS.

## Structure

```
/
├── .env                        # Environment variables (DB credentials, secrets) — never committed
└── www/
    │
    └── Portfolio/
        │
        ├── main/
        │   ├── about.html
        │   ├── contact.html
        │   ├── legal_notices.html
        │   ├── privacy_policy.html
        │   └── projects.html
        │
        ├── google071578dbdfbc3c7f.html
        ├── index.php
        ├── sitemap.xml
        │
        └── assets/
            │
            ├── icones/
            │
            ├── js/
            │   └── app.js
            │
            └── styles/
                └── styles.css
```

```
/
├── .env                        # Environment variables (DB credentials, secrets) — never committed
└── www/
    │
    └── Portfolio/
        │
        ├── main/
        │   ├── about.html              # Page "à propos"
        │   ├── contact.html            # Page de contact
        │   ├── legal_notices.html      # Mentions légales
        │   ├── privacy_policy.html     # Politique de confidentialité
        │   └── projects.html           # Page des projets
        │
        ├── google071578dbdfbc3c7f.html # Fichier de vérification Google Search Console
        ├── index.php                   # Page d'accueil
        ├── sitemap.xml                 # Plan du site pour les moteurs de recherche
        │
        └── assets/
            │
            ├── icones/                 # Icônes du site
            │
            ├── js/
            │   └── app.js              # Script JavaScript principal
            │
            └── styles/
                └── styles.css          # Feuille de style principale
```

 ## Internationalization

The site detects the browser language on first load and displays content in French or English accordingly. The logic lives in `lang.php`, static text is in `fr.php` / `en.php`, and recipes have `_en` columns in the database with automatic fallback to French if the translation hasn't been filled in yet.

## Legal notices
* [Legal notice](https://homekitchenclub.alwaysdata.net/mentions-legales)

## Author

Nicolas Boulloud — [LinkedIn](https://www.linkedin.com/in/nicolas-boulloud/)

## License

This project is proprietary — all rights reserved. See the LICENSE file for details.

See also NOTICE.md for additional usage restrictions (including AI training).

© 2026 Nicolas Boulloud.
