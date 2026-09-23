# Build scripts

These PHP scripts keep the gallery up to date. GitHub runs them for you on every push to `main`, so you normally don't need to run anything yourself.

| File | What it does |
|---|---|
| `themes.json` | The list of categories and templates. The gallery, category pages, README files and sitemap are all made from this file. |
| `build.php` | Writes `index.html`, every category page, every template README, the main README, `sitemap.xml` and `LICENSE`. |
| `screenshot.php` | Takes the 1440 x 900 `screenshot.png` for new or changed templates with headless Chrome. |

## Add a new template

1. Make a folder inside the right category, for example `restaurant/pizza-place/`, and put the finished `index.html` in it.
2. Add an entry for it to the `themes` list in `themes.json`: `cat`, `slug`, `name`, `type`, `desc`, `sections`, `fonts`, `colors`, `keywords` and `color`.
3. Push to `main`. GitHub builds the pages, takes the screenshot and commits both.

## Run it on your own computer

You need PHP 8.1 or newer with the `mbstring` and `gd` extensions, and Google Chrome or Chromium.

```
php scripts/build.php
php scripts/screenshot.php restaurant/pizza-place/index.html
php scripts/screenshot.php --all
```

## No Python

This repo is HTML, CSS and JavaScript only. Python files are listed in `.gitignore`. If one still reaches `main`, GitHub removes it automatically, and pull requests that add Python fail the check.
