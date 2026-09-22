# Build tools (not published on the site)

GitHub Pages skips folders that start with an underscore, so nothing here appears on the live site.

- `assemble.py` builds one theme: `python3 assemble.py parts/x.json parts/x.css parts/x.html`
- `gen.py` rebuilds every gallery page, README, sitemap and the repo description from `themes.json`
- `shot.py` takes the screenshot for a theme: `python3 shot.py /home/claude/fht/<cat>/<slug>`
- `audit.py` checks the live site (HTTP, SEO tags, sitemap, downloads, screenshots)
- `render.py` opens live pages on phone and desktop and reports overflow, JS errors and broken images: `python3 render.py 0 30`
- `ar.py` and `distort.py` catch stretched or squashed images
- `ovf.py` finds which element causes sideways scrolling on phones
- `parts/` holds the meta JSON, CSS and body HTML for each theme

Copy this folder's files to `/home/claude/` before running them. They expect the repo at `/home/claude/fht/`.
