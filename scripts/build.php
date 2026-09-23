<?php
// Builds the gallery (index.html), category pages, README files, sitemap.xml and LICENSE
// from scripts/themes.json. Run: php scripts/build.php
declare(strict_types=1);
mb_internal_encoding('UTF-8');
$MMCSS = <<<'MMCSS'
/* Shared look with mmrahmanbappi.github.io */
:root{--ink:#171518;--ink2:#403b42;--mu:#5f5d61;--bg:#eeeeea;--card:#fff;--ln:#d9d8d2;--ac:#b23a0a;--ac2:#8f2f08;--yl:#ff7b4f;--h:"Inter",system-ui,sans-serif;--b:"Inter",system-ui,sans-serif;color-scheme:light}
@media (prefers-color-scheme:dark){:root{--ink:#f2f1ed;--ink2:#d7d5d9;--mu:#a3a1a6;--bg:#141316;--card:#222126;--ln:#302f35;--ac:#ff7b4f;--ac2:#ff9670;color-scheme:dark}}
h1,h2,h3{letter-spacing:-.035em}h1{font-weight:560!important}h2,h3{font-weight:600!important}
header.top{position:sticky;top:0;z-index:5;background:color-mix(in srgb,var(--bg) 88%,transparent);backdrop-filter:saturate(1.4) blur(10px)}
.brand i{border-radius:50%!important}
.top nav a.gh{border:1.5px solid var(--ink);border-radius:999px;padding:.35rem 1rem;color:var(--ink);font-weight:600}
.btn{border-radius:999px!important}
.idx{background:rgba(0,0,0,.6);color:#fff!important;padding:1px 7px;border-radius:6px}
@media (prefers-color-scheme:dark){:root{--ac:#ff8a63}}
.brand i{color:var(--bg)!important}.kick{background:var(--card)}.kick b{color:#fff}
.btn{color:var(--bg)}.btn.l{background:var(--card)}.col a{background:var(--card)}.srch input{background:var(--card);color:var(--ink)}
.chips button{background:var(--card);color:var(--ink)}.chips button[aria-pressed=true]{color:var(--bg)}.links a:first-child{color:var(--bg)}
.none{background:var(--card)}.how div{color:var(--bg)}.faq details{background:var(--card)}
.bar{background:color-mix(in srgb,var(--bg) 92%,transparent)!important}.chips button span{color:inherit!important;background:color-mix(in srgb,currentColor 14%,transparent)!important}
.kick b{background:#b23a0a!important;color:#fff!important}
@media (max-width:700px){.top nav a:not(.gh){display:none}}
MMCSS;
$root = dirname(__DIR__) . '/';
$BASE = 'https://mmrahmanbappi.github.io/100-free-html-templates/';
$REPO = 'https://github.com/mmrahmanbappi/100-free-html-templates';
$D = json_decode(file_get_contents(__DIR__ . '/themes.json'), true, 512, JSON_THROW_ON_ERROR);
$T = $D['themes']; $C = $D['categories'];
$N = count($T); $YEAR = (int)date('Y');
$MONTH = date('F Y'); $TODAY = date('Y-m-d');

function e(string $s): string { return str_replace(['&', '<', '>', '"', "'"], ['&amp;', '&lt;', '&gt;', '&quot;', '&#x27;'], $s); }
function low(string $s): string { return mb_strtolower($s); }
function isup(string $w): bool { return (bool)preg_match('/\p{Lu}/u', $w) && !preg_match('/\p{Ll}/u', $w); }
function words(string $s): array { return preg_split('/\s+/u', trim($s), -1, PREG_SPLIT_NO_EMPTY); }
function jstr(string $s): string {
    $out = '"';
    foreach (preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
        $o = mb_ord($ch);
        if ($ch === '"') $out .= '\\"'; elseif ($ch === '\\') $out .= '\\\\';
        elseif ($ch === "\n") $out .= '\\n'; elseif ($ch === "\r") $out .= '\\r'; elseif ($ch === "\t") $out .= '\\t';
        elseif ($ch === "\x08") $out .= '\\b'; elseif ($ch === "\x0c") $out .= '\\f';
        elseif ($o < 0x20) $out .= sprintf('\\u%04x', $o); else $out .= $ch;
    }
    return $out . '"';
}
// Same layout as JSON with a one-space indent, which is what the pages have always used.
function jdump($v, int $lvl = 0): string {
    $pad = str_repeat(' ', $lvl + 1); $end = str_repeat(' ', $lvl);
    if (is_array($v)) {
        if ($v === []) return '[]';
        if (array_is_list($v)) return "[\n" . implode(",\n", array_map(fn($x) => $pad . jdump($x, $lvl + 1), $v)) . "\n" . $end . ']';
        $parts = []; foreach ($v as $k => $x) $parts[] = $pad . jstr((string)$k) . ': ' . jdump($x, $lvl + 1);
        return "{\n" . implode(",\n", $parts) . "\n" . $end . '}';
    }
    if (is_string($v)) return jstr($v);
    if (is_bool($v)) return $v ? 'true' : 'false';
    if ($v === null) return 'null';
    return (string)$v;
}
function ld($obj): string { return "<script type=\"application/ld+json\">\n" . jdump($obj) . "\n</script>"; }
function fit(array $parts, string $prefix, string $suffix, int $limit = 160): string {
    $out = $prefix . implode(', ', $parts) . $suffix; $k = count($parts);
    while (mb_strlen(e($out)) > $limit && $k > 1) { $k--; $out = $prefix . implode(', ', array_slice($parts, 0, $k)) . ' and more' . $suffix; }
    return $out;
}
function inCat(array $T, string $k): array { return array_values(array_filter($T, fn($t) => $t['cat'] === $k)); }

foreach ($C as $ck => &$cv) {
    $ts = inCat($T, $ck); $n = count($ts);
    $types = array_map(fn($t) => implode(' ', array_map(fn($w) => (isup($w) || $w === 'SaaS') ? $w : low($w), words($t['type']))), $ts);
    $cname = implode(' ', array_map(fn($w) => (isup($w) || in_array($w, ['SaaS', '&'], true)) ? $w : low($w), words($cv['name'])));
    $noun = $cv['noun'] ?? 'Website Templates';
    $cv['title'] = "$n Free {$cv['name']} $noun (HTML) $YEAR";
    $cv['desc'] = fit($types, "$n free $cname " . low($noun) . ' in one HTML file: ', '. Mobile friendly and SEO ready.');
}
unset($cv);

$FAQ = json_decode(<<<'J'
[["Are these HTML templates really free?", "Yes. Every template is free for personal and commercial use under the MIT license. You don't need to sign up, pay or add a credit link."], ["Do I need to know coding to use them?", "No. Basic editing is enough. Open the index.html file in a text editor such as VS Code or Notepad, change the words, phone numbers and image links, then save the file."], ["How do I put a template online for free?", "Upload the index.html file to GitHub Pages, Netlify or Cloudflare Pages. All three have free plans and take about ten minutes to set up."], ["Can I use a template for a client's website?", "Yes. The MIT license lets you use, change and sell websites built with these templates, including work you do for clients."], ["Are the templates good for SEO?", "Yes. Each template has a page title and description, social sharing tags, schema markup, alt text on every image and a clear heading order, so Google can read and rank the page."], ["Will the templates work on mobile phones?", "Yes. Every template adjusts to phones, tablets and desktop screens, and has been checked at common screen sizes."]]
J, true);

// Per-theme README
foreach ($T as $t) {
    $url = "{$BASE}{$t['cat']}/{$t['slug']}/";
    $sections = implode("\n", array_map(fn($s) => '- ' . $s, $t['sections']));
    $typeLow = low($t['type']);
    $md = <<<MD
# {$t['name']}: Free {$t['type']} Website Template (HTML)

{$t['desc']}

![{$t['name']}, a free {$typeLow} website template](screenshot.png)

**[See the live demo and download it free]($url)** &nbsp;|&nbsp; [More free templates]($BASE)

## What you get

- One `index.html` file. The CSS and JavaScript are inside it, so there is nothing to install.
- Works on phones, tablets and desktops.
- SEO ready: page title, meta description, canonical link, social sharing tags and schema markup.
- Easy to read for everyone: clear headings, alt text on every image and keyboard-friendly menus.
- Loads fast: images load only when needed and the page uses just two Google Fonts.
- Full example text, written like a real business, so you can see how your finished site will read.

## Sections on the page

$sections

## Fonts and colours

- **Fonts:** {$t['fonts']}, both free from Google Fonts.
- **Colours:** {$t['colors']}. You can change every colour in the `:root` section at the top of the `<style>` block.

## How to make it yours

1. Open the [live demo]($url) and click **Download this template (free)** in the black bar at the top.
2. Open the file in a text editor (VS Code, Sublime Text or Notepad++).
3. Delete the black notice bar at the top. Look for the comment `Template notice bar` and remove that block.
4. Change the business name, text, phone number, email and address.
5. Swap the photo links (they start with `https://images.unsplash.com/`) for your own photos.
6. In the `<head>`, update the title, description and the `canonical`, `og:url` and `og:image` links to your own website address.
7. Replace the schema markup with details of your own business (example below).
8. Upload the file to your web host, GitHub Pages or Netlify.

### Contact form

The form sends messages through Formspree. Make a free account at [formspree.io](https://formspree.io), create a form, and replace `your-form-id` in the form's `action` with your own form ID.

### Schema markup for your business

Replace the structured data block in the `<head>` with your own details. For a local business it looks like this:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Your Business Name",
  "url": "https://www.yourwebsite.com/",
  "telephone": "+1-000-000-0000",
  "email": "hello@yourwebsite.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "123 Main Street",
    "addressLocality": "Your City",
    "postalCode": "00000",
    "addressCountry": "US"
  },
  "openingHours": "Mo-Fr 09:00-17:00"
}
</script>
```

## Credits

- Photos from [Unsplash](https://unsplash.com), free to use under the Unsplash License.
- Fonts from [Google Fonts](https://fonts.google.com).

## License

MIT. Use it for personal or commercial projects. A link back is nice but not required. If this template saved you time, please give the repository a star so other people can find it.

<sub>Search terms: {$t['keywords']}, free HTML template, one page website template, responsive website template download</sub>

MD;
    file_put_contents("{$root}{$t['cat']}/{$t['slug']}/README.md", $md);
}

$CSS = <<<'EOT'
*,*::before,*::after{box-sizing:border-box}[hidden]{display:none!important}
:root{--ink:#16181F;--ink2:#2A2D38;--mu:#5E6372;--bg:#EDEFF3;--card:#fff;--ln:#DADDE5;--ac:#3D5AFE;--ac2:#2C45D6;--yl:#FFC53D;--h:"Bricolage Grotesque",system-ui,sans-serif;--b:"Hanken Grotesk",system-ui,sans-serif}
html{scroll-behavior:smooth}body{margin:0;font-family:var(--b);color:var(--ink);background:var(--bg);line-height:1.6;font-size:17px}
img{max-width:100%;height:auto;display:block}a{color:inherit}.wrap{max-width:1280px;margin:0 auto;padding:0 24px}
.skip{position:absolute;left:-999px}.skip:focus{left:10px;top:10px;background:var(--yl);padding:8px;z-index:99}:focus-visible{outline:3px solid var(--yl);outline-offset:2px}
h1,h2,h3{font-family:var(--h);margin:0;letter-spacing:-.02em}
.top{display:flex;justify-content:space-between;align-items:center;height:70px;gap:16px}.brand{display:flex;align-items:center;gap:10px;font-family:var(--h);font-weight:800;font-size:20px;text-decoration:none}
.brand i{display:grid;place-items:center;width:34px;height:34px;border-radius:10px;background:var(--ink);color:var(--yl);font-style:normal;font-size:15px}
.top nav{display:flex;gap:22px;font-weight:600;font-size:15px}.top nav a{text-decoration:none}.top nav a:hover{color:var(--ac)}
.hero{padding:26px 0 70px;overflow:hidden}.hg{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center}.hg>*{min-width:0}
.crumb{font-size:15px;color:var(--mu);margin-bottom:14px}.crumb a{color:var(--ac);font-weight:600}
.kick{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--ln);border-radius:40px;padding:6px 14px 6px 8px;font-size:14px;font-weight:600;margin-bottom:22px}.kick b{background:var(--yl);border-radius:20px;padding:2px 10px}
.hero h1{font-size:clamp(46px,6.6vw,92px);line-height:.95;font-weight:800}.hero h1 .num{display:block;font-size:1.55em;line-height:.85;color:transparent;-webkit-text-stroke:2.5px var(--ink);letter-spacing:-.04em}
.hero h1 .hl{background:linear-gradient(transparent 62%,var(--yl) 62% 92%,transparent 92%)}
.hero p.lead{font-size:20px;color:var(--mu);max-width:34em;margin:22px 0 28px}
.btns{display:flex;gap:12px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;gap:8px;background:var(--ink);color:#fff;text-decoration:none;font:700 16px var(--b);padding:14px 22px;border-radius:12px}.btn:hover{background:var(--ac)}.btn.l{background:#fff;color:var(--ink);box-shadow:inset 0 0 0 1.5px var(--ln)}.btn.l:hover{box-shadow:inset 0 0 0 1.5px var(--ink)}
.stats{display:flex;gap:30px;flex-wrap:wrap;margin-top:34px;padding:0;list-style:none}.stats li{font-size:14px;color:var(--mu)}.stats b{display:block;font-family:var(--h);font-size:30px;color:var(--ink);line-height:1.1}
.wall{height:560px;display:grid;grid-template-columns:1fr 1fr;gap:16px;-webkit-mask-image:linear-gradient(transparent,#000 12%,#000 88%,transparent);mask-image:linear-gradient(transparent,#000 12%,#000 88%,transparent);transform:rotate(-4deg)}
.col{display:flex;flex-direction:column;gap:16px;animation:up 40s linear infinite}.col.b{animation-direction:reverse;animation-duration:46s}
.col a{display:block;border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 14px 30px rgba(22,24,31,.14);border:4px solid #fff;transition:transform .25s}.col a:hover{transform:scale(1.03)}.col img{aspect-ratio:16/10;object-fit:cover;object-position:top}
@keyframes up{to{transform:translateY(-50%)}}
.bar{position:sticky;top:0;z-index:40;background:rgba(237,239,243,.92);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border-bottom:1px solid var(--ln)}
.bar .wrap{display:flex;gap:14px;align-items:center;padding-top:12px;padding-bottom:12px;flex-wrap:wrap}
.srch{position:relative;flex:1 1 280px;max-width:420px}.srch input{width:100%;font:16px var(--b);padding:11px 14px 11px 40px;border:1.5px solid var(--ln);border-radius:12px;background:#fff}.srch svg{position:absolute;left:13px;top:50%;transform:translateY(-50%)}
.chips{display:flex;gap:6px;flex-wrap:wrap;order:3;flex:1 1 100%;padding:2px}.chips::-webkit-scrollbar{display:none}
.chips button{flex:none;font:600 14px var(--b);background:#fff;border:1.5px solid var(--ln);border-radius:40px;padding:8px 14px;cursor:pointer;color:var(--ink);white-space:nowrap}.chips button span{color:var(--mu);font-weight:500;margin-left:4px}
.chips button[aria-pressed=true]{background:var(--ink);border-color:var(--ink);color:#fff}.chips button[aria-pressed=true] span{color:#B9BDC9}
.count{font-size:14px;color:var(--mu);white-space:nowrap;margin-left:auto}
main{padding:40px 0 30px}.sec{margin-bottom:64px;scroll-margin-top:90px}.sh{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.sh h2{font-size:clamp(28px,3.4vw,42px);font-weight:800}.sh p{color:var(--mu);margin:6px 0 0;max-width:56em;font-size:16px}.sh a.all{font-weight:700;color:var(--ac);text-decoration:none;white-space:nowrap}.sh a.all:hover{text-decoration:underline}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:22px}
.card{background:var(--card);border-radius:18px;overflow:hidden;display:flex;flex-direction:column;border:1px solid var(--ln);transition:transform .2s,box-shadow .2s}
.card:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(22,24,31,.12)}
.shot{position:relative;background:var(--c);padding:12px 12px 0}.shot .chrome{display:flex;gap:5px;padding:0 4px 8px}.shot .chrome i{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.55)}
.shot img{border-radius:8px 8px 0 0;aspect-ratio:16/10;object-fit:cover;object-position:top;width:100%}
.idx{position:absolute;right:14px;top:8px;font:700 12px var(--b);color:rgba(255,255,255,.85)}
.card .body{padding:16px 18px 18px;display:flex;flex-direction:column;flex:1}.card .ty{font-size:13px;font-weight:700;color:var(--mu);display:flex;align-items:center;gap:6px}.card .ty i{width:10px;height:10px;border-radius:50%;background:var(--c)}
.card h3{font-size:21px;margin:4px 0 6px}.card p{color:var(--mu);font-size:15px;margin:0 0 14px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.card .fn{font-size:13px;color:var(--mu);margin:-6px 0 14px}.links{margin-top:auto;display:flex;gap:8px}.links a{flex:1;text-align:center;text-decoration:none;font-weight:700;padding:10px;border-radius:10px;font-size:14px;border:1.5px solid var(--ln)}
.links a:first-child{background:var(--ink);color:#fff;border-color:var(--ink)}.links a:first-child:hover{background:var(--ac);border-color:var(--ac)}.links a:last-child:hover{border-color:var(--ink)}
.none{background:#fff;border-radius:18px;padding:40px;text-align:center;color:var(--mu)}
.how{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:64px}.how div{background:var(--ink);color:#fff;border-radius:18px;padding:24px;position:relative;overflow:hidden}
.how b{display:block;font-family:var(--h);font-size:22px;margin:8px 0 6px}.how p{margin:0;color:#B9BDC9;font-size:15px}.how span{font-family:var(--h);font-weight:800;font-size:54px;line-height:1;color:var(--yl)}
.faq{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:40px}.faq details{background:#fff;border:1px solid var(--ln);border-radius:14px;padding:16px 20px}.faq summary{font-weight:700;cursor:pointer}.faq p{margin:10px 0 0;color:var(--mu);font-size:16px}
footer{border-top:1px solid var(--ln);padding:28px 0 36px;color:var(--mu);font-size:14px}footer .wrap{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}footer a{font-weight:600}
@media (max-width:980px){.hg{grid-template-columns:1fr}.wall{height:340px;transform:rotate(-3deg)}.how{grid-template-columns:1fr}.faq{grid-template-columns:1fr}}
@media (max-width:760px){.chips{flex-wrap:nowrap;overflow-x:auto;scrollbar-width:none;-webkit-mask-image:linear-gradient(90deg,#000 88%,transparent);mask-image:linear-gradient(90deg,#000 88%,transparent);padding-right:30px}.srch{max-width:none}}
@media (max-width:640px){.top nav{display:none}.srch{flex:1 1 100%}.count{margin-left:0}.hero h1 .num{-webkit-text-stroke-width:2px}.wall{height:260px}.grid{grid-template-columns:1fr}}
@media (prefers-reduced-motion:reduce){*{scroll-behavior:auto!important}.col{animation:none}.card,.col a{transition:none}}
EOT;
$JS = <<<'EOT'
<script>
(()=>{const q=document.getElementById('q'),chips=document.getElementById('chips'),cnt=document.getElementById('cnt'),none=document.getElementById('none');if(!q)return;let cat='all';
const cards=[...document.querySelectorAll('.card')],secs=[...document.querySelectorAll('.sec')];
function run(){const v=q.value.trim().toLowerCase();let n=0;cards.forEach(c=>{const ok=(cat==='all'||c.dataset.cat===cat)&&(!v||c.dataset.s.includes(v));c.hidden=!ok;if(ok)n++});
secs.forEach(s=>{s.hidden=![...s.querySelectorAll('.card')].some(c=>!c.hidden)});cnt.textContent='Showing '+n+' of '+cards.length;none.hidden=n>0}
q.addEventListener('input',run);if(chips)chips.addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;chips.querySelectorAll('button').forEach(x=>x.setAttribute('aria-pressed',x===b));b.scrollIntoView({block:'nearest',inline:'center'});cat=b.dataset.c;run();document.getElementById('templates').scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'})});run();})();
</script>
EOT;
$HOW = <<<'EOT'
<section class='sec' id='how' aria-labelledby='how-t'><div class='sh'><div><h2 id='how-t'>How to use a template</h2><p>Three steps, no special software.</p></div></div><div class='how'><div><span>1</span><b>Download</b><p>Pick a template, open the live demo and click Download. You get one HTML file.</p></div><div><span>2</span><b>Edit</b><p>Open the file in any text editor and change the words, colours and photos to match your business.</p></div><div><span>3</span><b>Publish</b><p>Upload the file to GitHub Pages, Netlify or your own web host. Your site is live.</p></div></div></section>
EOT;
$NONE = <<<'EOT'
<p class='none' id='none' hidden>No templates match that search. Try a simpler word, like shop or clinic.</p>
EOT;

function faqHtml(array $FAQ): string {
    $o = "<section class='sec' aria-labelledby='faq'><div class='sh'><div><h2 id='faq'>Questions people ask</h2></div></div><div class='faq'>";
    foreach ($FAQ as $i => [$q, $a]) $o .= '<details' . ($i === 0 ? ' open' : '') . '><summary>' . e($q) . '</summary><p>' . e($a) . '</p></details>';
    return $o . '</div></section>';
}
function faqLd(array $FAQ): array {
    return ['@type' => 'FAQPage', 'mainEntity' => array_map(fn($x) => ['@type' => 'Question', 'name' => $x[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $x[1]]], $FAQ)];
}
function itemList(array $ts, string $BASE): array {
    $items = [];
    foreach ($ts as $i => $t) $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'url' => "{$BASE}{$t['cat']}/{$t['slug']}/", 'name' => "{$t['name']}: free " . low($t['type']) . ' website template'];
    return ['@type' => 'ItemList', 'numberOfItems' => count($ts), 'itemListElement' => $items];
}
function wall(array $ts, callable $prefix): string {
    $n = count($ts);
    if ($n > 10) { $step = max(1, intdiv($n, 10)); $pick = []; for ($i = 0; $i < $n; $i += $step) $pick[] = $ts[$i]; $pick = array_slice($pick, 0, 10); } else $pick = $ts;
    $half = [[], []]; foreach ($pick as $i => $t) $half[$i % 2][] = $t;
    $out = '';
    foreach ($half as $k => $h) {
        $items = '';
        foreach ($h as $t) { $p = $prefix($t); $items .= "<a href=\"{$p}{$t['slug']}/\" tabindex=\"-1\"><img src=\"{$p}{$t['slug']}/screenshot.png\" alt=\"\" width=\"1440\" height=\"900\" loading=\"" . ($k === 0 ? 'eager' : 'lazy') . '"></a>'; }
        $out .= '<div class="col' . ($k ? ' b' : '') . "\">{$items}{$items}</div>";
    }
    return "<div class=\"wall\" aria-hidden=\"true\">{$out}</div>";
}
// ---- SEO helpers: keep titles within 60 characters and descriptions within 110 to 160 ----
function mm_trim_stop(array $w): array { $stop = ['and','with','for','in','of','the','a','an','to','&','on','by','your','or']; while ($w && in_array(strtolower(end($w)), $stop, true)) array_pop($w); return $w; }
function mm_clean(string $t): string {
    $t = trim($t);
    if (substr_count($t, '(') > substr_count($t, ')')) $t = trim(substr($t, 0, strrpos($t, '(')));
    $w = mm_trim_stop(explode(' ', $t)); $t = implode(' ', $w);
    return rtrim($t, " ,;:-");
}
function mm_title(string $t): string {
    if (mb_strlen($t) <= 60) return $t;
    while (mb_strlen($t) > 60 && preg_match('/\s*\([^()]*\)/', $t)) {
        preg_match_all('/\s*\([^()]*\)/', $t, $m, PREG_OFFSET_CAPTURE); $last = end($m[0]);
        $t = trim(substr($t, 0, $last[1]) . substr($t, $last[1] + strlen($last[0])));
    }
    if (mb_strlen($t) <= 60) return mm_clean($t);
    foreach ([': ', ' | ', ' - '] as $sep) {
        $p = strpos($t, $sep);
        if ($p !== false) {
            $h = substr($t, 0, $p); $s = substr($t, $p + strlen($sep));
            if (strpos($s, ',') !== false && mb_strlen($h) <= 60) return mm_clean($h);
            $w = explode(' ', $s);
            while ($w && mb_strlen($h . $sep . implode(' ', $w)) > 60) array_pop($w);
            $r = mm_clean(implode(' ', $w));
            if ($r !== '') return $h . $sep . $r;
            $t = $h; break;
        }
    }
    if (mb_strlen($t) <= 60) return mm_clean($t);
    $w = explode(' ', $t); while ($w && mb_strlen(implode(' ', $w)) > 60) array_pop($w);
    return mm_clean(implode(' ', $w));
}
function mm_desc(string $d, string $tail): string {
    $d = trim($d);
    if (mb_strlen($d) > 160) {
        $cut = mb_substr($d, 0, 159); $p = mb_strrpos($cut, '. ');
        if ($p !== false && $p > 100) $d = mb_substr($cut, 0, $p + 1);
        else { $w = explode(' ', mb_substr($d, 0, 158)); array_pop($w); $d = rtrim(implode(' ', mm_trim_stop($w)), ',;:') . '.'; }
    }
    if (mb_strlen($d) < 110 && mb_strlen($d . ' ' . $tail) <= 160) $d .= ' ' . $tail;
    return $d;
}
function page(string $title, string $desc, string $canon, string $h1, string $intro, string $crumb, string $body, array $graph, string $wall, string $stats, string $bar): string {
    global $BASE, $REPO, $T, $CSS, $JS, $MONTH, $MMCSS;
    $img = "{$BASE}{$T[0]['cat']}/{$T[0]['slug']}/screenshot.png";
    $title = mm_title($title); $desc = mm_desc($desc, 'Free HTML templates, one file each, MIT license.');
    $src = $img; $img = rtrim($canon, '/') . '/og.jpg';
    $et = e($title); $ed = e($desc); $ld = ld(['@context' => 'https://schema.org', '@graph' => $graph]); $ei = e($intro);
    return <<<HTML
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>$et</title><meta name="description" content="$ed"><meta name="robots" content="index, follow, max-image-preview:large"><link rel="canonical" href="$canon">
<meta property="og:type" content="website"><meta property="og:site_name" content="Free HTML Templates"><meta property="og:title" content="$et"><meta property="og:description" content="$ed"><meta property="og:url" content="$canon">
<meta property="og:image" content="$img"><meta property="og:image:alt" content="$et"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><link rel="image_src" href="$src"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="$et"><meta name="twitter:description" content="$ed"><meta name="twitter:image" content="$img">
<meta name="theme-color" content="#16181F"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='14' fill='%2316181F'/><text x='32' y='42' text-anchor='middle' font-family='Arial' font-weight='700' font-size='26' fill='%23FFC53D'>100</text></svg>">
$ld
<style>$CSS$MMCSS</style></head><body>
<a class="skip" href="#templates">Skip to templates</a>
<header class="wrap top"><a class="brand" href="$BASE"><i>100</i>Free HTML Templates</a><nav aria-label="Main"><a href="{$BASE}#templates">Templates</a><a href="#how">How it works</a><a href="#faq">FAQ</a><a href="https://mmrahmanbappi.github.io/">All projects</a><a class="gh" href="$REPO">GitHub</a></nav></header>
<section class="hero" aria-labelledby="h1"><div class="wrap hg"><div>{$crumb}<span class="kick"><b>Free</b>MIT license, no sign up</span><h1 id="h1">$h1</h1><p class="lead">$ei</p><div class="btns"><a class="btn" href="#templates">Browse the templates</a><a class="btn l" href="#how">How to use them</a></div>{$stats}</div>{$wall}</div></section>
$bar
<main id="templates"><div class="wrap">$body</div></main>
<footer><div class="wrap"><span>Free, MIT-licensed HTML website templates. Photos from Unsplash. Updated $MONTH.</span><span><a href="$REPO">Source on GitHub</a></span></div></footer>
$JS
</body></html>
HTML;
}
function card(array $t, string $prefix, int $n): string {
    $c = $t['color'] ?? '#3D5AFE';
    $s = e(low($t['name'] . ' ' . $t['type'] . ' ' . $t['desc'] . ' ' . ($t['keywords'] ?? '')));
    $nn = sprintf('%03d', $n); $name = e($t['name']); $tl = e(low($t['type'])); $ty = e($t['type']); $d = e($t['desc']); $f = e($t['fonts']);
    return "<article class=\"card\" data-cat=\"{$t['cat']}\" data-s=\"$s\" style=\"--c:$c\"><div class=\"shot\"><div class=\"chrome\" aria-hidden=\"true\"><i></i><i></i><i></i></div><span class=\"idx\" aria-hidden=\"true\">#$nn</span><img src=\"{$prefix}{$t['slug']}/screenshot.png\" alt=\"$name, free $tl website template\" width=\"1440\" height=\"900\" loading=\"lazy\"></div><div class=\"body\"><span class=\"ty\"><i aria-hidden=\"true\"></i>$ty</span><h3>$name</h3><p>$d</p><div class=\"fn\">$f</div><div class=\"links\"><a href=\"{$prefix}{$t['slug']}/\">Live demo</a><a href=\"{$prefix}{$t['slug']}/index.html\" download=\"{$t['slug']}.html\">Download</a></div></div></article>";
}
function barHtml(bool $catsOn): string {
    global $C, $T, $N;
    $chips = '';
    if ($catsOn) {
        $chips = "<div class='chips' id='chips' role='group' aria-label='Filter by category'><button type='button' aria-pressed='true' data-c='all'>All<span>$N</span></button>";
        foreach ($C as $k => $v) $chips .= "<button type='button' aria-pressed='false' data-c='$k'>" . e($v['name']) . '<span>' . count(inCat($T, $k)) . '</span></button>';
        $chips .= '</div>';
    }
    return "<div class='bar'><div class='wrap'><div class='srch' role='search'><svg width='16' height='16' viewBox='0 0 24 24' aria-hidden='true'><circle cx='10' cy='10' r='7' fill='none' stroke='#5E6372' stroke-width='2.5'/><path d='M15 15l6 6' stroke='#5E6372' stroke-width='2.5'/></svg><label for='q' style='position:absolute;left:-999px'>Search templates</label><input id='q' type='search' placeholder='Search: dentist, booking, dark...'></div>{$chips}<span class='count' id='cnt' aria-live='polite'></span></div></div>";
}
function statsHtml(int $n, int $k): string { return "<ul class='stats'><li><b>$n</b>templates</li>" . ($k ? "<li><b>$k</b>categories</li>" : '') . '<li><b>1</b>HTML file each</li><li><b>MIT</b>license</li></ul>'; }

$IDX = []; foreach ($T as $i => $t) $IDX[$t['slug']] = $i + 1;

// Category pages
foreach ($C as $ck => $cv) {
    $ts = inCat($T, $ck); $canon = "{$BASE}{$ck}/";
    $body = "<section class='sec' aria-labelledby='cat-t'><div class='sh'><div><h2 id='cat-t'>" . count($ts) . ' free ' . e(low($cv['name'])) . " templates</h2><p>Click a template to see the live demo, or download it straight away.</p></div><a class='all' href='../'>All $N templates</a></div><div class='grid'>"
        . implode('', array_map(fn($t) => card($t, '', $IDX[$t['slug']]), $ts)) . '</div></section>' . $NONE . $HOW . faqHtml($FAQ);
    $graph = [
        ['@type' => 'CollectionPage', '@id' => $canon, 'url' => $canon, 'name' => $cv['title'], 'description' => $cv['desc'], 'inLanguage' => 'en', 'isPartOf' => ['@type' => 'WebSite', 'name' => 'Free HTML Templates', 'url' => $BASE], 'mainEntity' => itemList($ts, $BASE)],
        ['@type' => 'BreadcrumbList', 'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => 'Free HTML Templates', 'item' => $BASE], ['@type' => 'ListItem', 'position' => 2, 'name' => "{$cv['name']} Templates", 'item' => $canon]]],
        faqLd($FAQ)];
    $h1 = 'Free ' . e(low($cv['name'])) . " <span class='hl'>website templates</span>";
    file_put_contents("{$root}{$ck}/index.html", page($cv['title'], $cv['desc'], $canon, $h1, $cv['intro'], "<div class='crumb'><a href='../'>All templates</a> / " . e($cv['name']) . '</div>', $body, $graph, wall($ts, fn($t) => ''), statsHtml(count($ts), 0), barHtml(false)));
}

// Home page
$body = '';
foreach ($C as $ck => $cv) {
    $ts = inCat($T, $ck);
    $body .= "<section class='sec' id='$ck' aria-labelledby='h-$ck'><div class='sh'><div><h2 id='h-$ck'>" . e($cv['name']) . '</h2><p>' . e($cv['intro']) . "</p></div><a class='all' href='$ck/'>See all " . count($ts) . "</a></div><div class='grid'>"
        . implode('', array_map(fn($t) => card($t, $ck . '/', $IDX[$t['slug']]), $ts)) . '</div></section>';
}
$body .= $NONE . $HOW . faqHtml($FAQ);
$rt = "$N Free HTML Website Templates $YEAR: Download and Edit";
$pl = array_values(array_map(fn($v) => $v['short'] ?? $v['plural'] ?? $v['name'], $C));
$rd = "$N free HTML templates, one file each: " . (count($pl) > 1 ? implode(', ', array_slice($pl, 0, -1)) . ' and ' . end($pl) : $pl[0]) . '. No coding needed. Mobile friendly and SEO ready.';
if (mb_strlen($rd) > 160) $rd = str_replace(' No coding needed.', '', $rd);
if (mb_strlen($rd) > 160) $rd = "$N free HTML templates, one file each, for " . count($pl) . ' kinds of websites. No coding needed. Mobile friendly and SEO ready.';
$graph = [
    ['@type' => 'WebSite', '@id' => $BASE . '#website', 'url' => $BASE, 'name' => 'Free HTML Templates', 'description' => $rd, 'inLanguage' => 'en', 'publisher' => ['@id' => $BASE . '#author']],
    ['@type' => 'Person', '@id' => $BASE . '#author', 'name' => 'MM Rahman Bappi', 'url' => 'https://mmrahmanbappi.github.io/', 'sameAs' => ['https://github.com/mmrahmanbappi', 'https://mmseo.app/']],
    ['@type' => 'CollectionPage', '@id' => $BASE, 'url' => $BASE, 'name' => $rt, 'description' => $rd, 'isPartOf' => ['@id' => $BASE . '#website'], 'author' => ['@id' => $BASE . '#author'], 'mainEntity' => itemList($T, $BASE)],
    faqLd($FAQ)];
$h1 = "<span class='num'>$N</span>free website templates <span class='hl'>you can edit today</span>";
file_put_contents($root . 'index.html', page($rt, $rd, $BASE, $h1, 'Each template is one HTML file. Open it, change the text and photos, and upload it. No WordPress, no page builder and no monthly fees. Free for personal and business use.', '', $body, $graph, wall($T, fn($t) => $t['cat'] . '/'), statsHtml($N, count($C)), barHtml(true)));

// Root README
$rows = implode("\n", array_map(fn($t) => "| [![{$t['name']}]({$t['cat']}/{$t['slug']}/screenshot.png)]({$BASE}{$t['cat']}/{$t['slug']}/) | **[{$t['name']}]({$t['cat']}/{$t['slug']}/)**<br>{$t['type']} template<br><br>{$t['desc']}<br><br>[Live demo and download]({$BASE}{$t['cat']}/{$t['slug']}/) |", $T));
$faqmd = implode("\n\n", array_map(fn($x) => "**{$x[0]}**<br>{$x[1]}", $FAQ));
$cats = []; foreach ($C as $k => $v) $cats[] = "- **[{$v['name']}]($k/)** (" . count(inCat($T, $k)) . " templates): {$v['desc']}";
$cats = implode("\n", $cats);
$readme = <<<MD
# Free HTML Website Templates

$N free website templates, each in a single HTML file. Download one, change the text and photos, and your website is ready to upload. No WordPress, no page builder, no monthly fees.

**[See every template with a live demo and free download]($BASE)**

## Why use these templates

- **One file each.** The CSS and JavaScript are inside the HTML file, so there is nothing to install or build.
- **Works on phones.** Every template adjusts to phones, tablets and desktops.
- **SEO ready.** Page titles, meta descriptions, social sharing tags and schema markup are already set up.
- **Real example text.** Written like a real business, not lorem ipsum, so you can see how your site will read.
- **Free for business use.** MIT license. Use them for your own site or for clients.

## Templates

| Preview | Template |
|---|---|
$rows

## Categories

$cats

New templates are added every week. Coming next: more restaurant and food sites, portfolios, restaurants, clinics, schools, real estate and wedding sites.

## How to use a template

1. Go to the [template gallery]($BASE), open a live demo and click **Download this template (free)**.
2. Open the file in a text editor such as VS Code or Notepad++.
3. Change the text, colours and photos. Colours are listed at the top of the `<style>` block.
4. Upload the file to GitHub Pages, Netlify, Cloudflare Pages or your own web host.

## Questions

$faqmd

## Credits

Photos from [Unsplash](https://unsplash.com) under the Unsplash License. Fonts from [Google Fonts](https://fonts.google.com).

## License

[MIT](LICENSE). Free for personal and commercial use. If a template saved you time, please star this repository so more people can find it.

MD;
file_put_contents($root . 'README.md', $readme);

// Sitemap
$urls = array_merge([$BASE], array_map(fn($k) => "{$BASE}{$k}/", array_keys($C)), array_map(fn($t) => "{$BASE}{$t['cat']}/{$t['slug']}/", $T));
file_put_contents($root . 'sitemap.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n" . implode('', array_map(fn($u) => "  <url><loc>$u</loc><lastmod>$TODAY</lastmod></url>\n", $urls)) . "</urlset>\n");

// License
file_put_contents($root . 'LICENSE', <<<TXT
MIT License

Copyright (c) $YEAR mmrahmanbappi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

TXT);

echo "Built $N templates in " . count($C) . " categories\n";
