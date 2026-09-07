## Portfolio

Découvrez mon portfolio en ligne : **[pt-nb.alwaysdata.net](https://pt-nb.alwaysdata.net/)**

Ce repo contient le code source de mon site personnel, développé en PHP/HTML/CSS/JS.

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

 ## Internationalization

The site detects the browser language on first load and displays content in French or English accordingly. The logic lives in `lang.php`, static text is in `fr.php` / `en.php`, and recipes have `_en` columns in the database with automatic fallback to French if the translation hasn't been filled in yet.

## Author
Nicolas Boulloud
