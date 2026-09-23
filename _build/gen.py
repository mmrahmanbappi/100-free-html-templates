import json, html, datetime
BASE="https://mmrahmanbappi.github.io/100-free-html-templates/"
REPO="https://github.com/mmrahmanbappi/100-free-html-templates"
D=json.load(open('themes.json')); root='/home/claude/fht/'
T=D['themes']; C=D['categories']; e=html.escape
N=len(T); YEAR=datetime.date.today().year
def fit(parts,prefix,suffix,limit=160):
    out=prefix+', '.join(parts)+suffix
    k=len(parts)
    while len(e(out))>limit and k>1:
        k-=1; out=prefix+', '.join(parts[:k])+' and more'+suffix
    return out
for ck,cv in C.items():
    ts=[t for t in T if t['cat']==ck]; n=len(ts)
    types=[' '.join(w if (w.isupper() or w=='SaaS') else w.lower() for w in t['type'].split()) for t in ts]
    cname=' '.join(w if (w.isupper() or w in ('SaaS','&')) else w.lower() for w in cv['name'].split())
    cv['title']=f"{n} Free {cv['name']} {cv.get('noun','Website Templates')} (HTML) {YEAR}"
    cv['desc']=fit(types,f"{n} free {cname} {cv.get('noun','Website Templates').lower()} in one HTML file: ",". Mobile friendly and SEO ready.")

today=datetime.date.today()
FAQ=[("Are these HTML templates really free?","Yes. Every template is free for personal and commercial use under the MIT license. You don't need to sign up, pay or add a credit link."),
("Do I need to know coding to use them?","No. Basic editing is enough. Open the index.html file in a text editor such as VS Code or Notepad, change the words, phone numbers and image links, then save the file."),
("How do I put a template online for free?","Upload the index.html file to GitHub Pages, Netlify or Cloudflare Pages. All three have free plans and take about ten minutes to set up."),
("Can I use a template for a client's website?","Yes. The MIT license lets you use, change and sell websites built with these templates, including work you do for clients."),
("Are the templates good for SEO?","Yes. Each template has a page title and description, social sharing tags, schema markup, alt text on every image and a clear heading order, so Google can read and rank the page."),
("Will the templates work on mobile phones?","Yes. Every template adjusts to phones, tablets and desktop screens, and has been checked at common screen sizes.")]

# per-theme README
for t in T:
    url=f"{BASE}{t['cat']}/{t['slug']}/"
    md=f"""# {t['name']}: Free {t['type']} Website Template (HTML)

{t['desc']}

![{t['name']}, a free {t['type'].lower()} website template](screenshot.png)

**[See the live demo and download it free]({url})** &nbsp;|&nbsp; [More free templates]({BASE})

## What you get

- One `index.html` file. The CSS and JavaScript are inside it, so there is nothing to install.
- Works on phones, tablets and desktops.
- SEO ready: page title, meta description, canonical link, social sharing tags and schema markup.
- Easy to read for everyone: clear headings, alt text on every image and keyboard-friendly menus.
- Loads fast: images load only when needed and the page uses just two Google Fonts.
- Full example text, written like a real business, so you can see how your finished site will read.

## Sections on the page

{chr(10).join('- '+s for s in t['sections'])}

## Fonts and colours

- **Fonts:** {t['fonts']}, both free from Google Fonts.
- **Colours:** {t['colors']}. You can change every colour in the `:root` section at the top of the `<style>` block.

## How to make it yours

1. Open the [live demo]({url}) and click **Download this template (free)** in the black bar at the top.
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
{{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Your Business Name",
  "url": "https://www.yourwebsite.com/",
  "telephone": "+1-000-000-0000",
  "email": "hello@yourwebsite.com",
  "address": {{
    "@type": "PostalAddress",
    "streetAddress": "123 Main Street",
    "addressLocality": "Your City",
    "postalCode": "00000",
    "addressCountry": "US"
  }},
  "openingHours": "Mo-Fr 09:00-17:00"
}}
</script>
```

## Credits

- Photos from [Unsplash](https://unsplash.com), free to use under the Unsplash License.
- Fonts from [Google Fonts](https://fonts.google.com).

## License

MIT. Use it for personal or commercial projects. A link back is nice but not required. If this template saved you time, please give the repository a star so other people can find it.

<sub>Search terms: {t['keywords']}, free HTML template, one page website template, responsive website template download</sub>
"""
    open(f"{root}{t['cat']}/{t['slug']}/README.md",'w').write(md)

CSS="""*{box-sizing:border-box}body{margin:0;font-family:"Hanken Grotesk",system-ui,-apple-system,"Segoe UI",sans-serif;color:#141A26;background:#F4F5F7;line-height:1.6;font-size:17px}
a{color:inherit}.wrap{max-width:1200px;margin:0 auto;padding:0 24px}
header{background:#141A26;color:#fff;padding:64px 0 60px}header h1{font-size:clamp(32px,5vw,56px);line-height:1.1;margin:0 0 16px;letter-spacing:-.02em;max-width:22ch}
header p{font-size:19px;color:#C4CAD6;max-width:62ch;margin:0}.crumb{font-size:15px;color:#8E97A8;margin-bottom:18px}.crumb a{color:#fff}
.meta{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}.meta a{background:#fff;color:#141A26;text-decoration:none;font-weight:700;padding:12px 20px;border-radius:10px}.meta a.alt{background:transparent;color:#fff;border:1px solid #3A4458}
.perks{display:flex;gap:28px;flex-wrap:wrap;margin-top:30px;color:#C4CAD6;font-size:15px;padding:0;list-style:none}.perks li::before{content:"✓ ";color:#7FB2FF;font-weight:700}
main{padding:56px 0 40px}h2{font-size:28px;margin:0 0 6px}.sub{color:#5B6475;margin:0 0 26px}
.cats{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:48px}.cats a{background:#fff;border:1px solid #DDE1E8;border-radius:999px;padding:8px 16px;text-decoration:none;font-weight:600}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:26px;margin-bottom:56px}
.card{background:#fff;border:1px solid #DDE1E8;border-radius:16px;overflow:hidden;display:flex;flex-direction:column}
.card img{width:100%;height:auto;aspect-ratio:16/10;object-fit:cover;object-position:top;border-bottom:1px solid #DDE1E8}
.card .body{padding:20px 22px 22px;display:flex;flex-direction:column;flex:1}.card h3{margin:0 0 4px;font-size:20px}.card .type{color:#2F6BFF;font-weight:700;font-size:14px}
.card p{color:#5B6475;font-size:15px;margin:10px 0 18px}.links{margin-top:auto;display:flex;gap:10px}.links a{flex:1;text-align:center;text-decoration:none;font-weight:700;padding:10px;border-radius:9px;border:1px solid #DDE1E8;font-size:15px}.links a:first-child{background:#2F6BFF;color:#fff;border-color:#2F6BFF}
.faq{max-width:820px;margin:20px 0 40px}.faq details{background:#fff;border:1px solid #DDE1E8;border-radius:12px;padding:18px 22px;margin-bottom:10px}.faq summary{font-weight:700;cursor:pointer}.faq p{margin:10px 0 0;color:#3E4757}
.how{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin:10px 0 56px}.how div{background:#fff;border:1px solid #DDE1E8;border-radius:14px;padding:22px}.how b{display:block;font-size:18px;margin-bottom:6px}.how p{margin:0;color:#5B6475;font-size:15px}
footer{border-top:1px solid #DDE1E8;padding:28px 0;color:#5B6475;font-size:14px}
@media (max-width:760px){.how{grid-template-columns:1fr}}@media (max-width:520px){.grid{grid-template-columns:1fr}}"""

def ld(obj): return '<script type="application/ld+json">\n'+json.dumps(obj,indent=1,ensure_ascii=False)+'\n</script>'
def faq_html(): return "<h2 id='faq'>Questions people ask</h2><div class='faq'>"+''.join(f"<details{' open' if i==0 else ''}><summary>{e(q)}</summary><p>{e(a)}</p></details>" for i,(q,a) in enumerate(FAQ))+"</div>"
def faq_ld(): return {"@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in FAQ]}
def itemlist(ts): return {"@type":"ItemList","numberOfItems":len(ts),"itemListElement":[{"@type":"ListItem","position":i+1,"url":f"{BASE}{t['cat']}/{t['slug']}/","name":f"{t['name']}: free {t['type'].lower()} website template"} for i,t in enumerate(ts)]}
def page(title,desc,canon,h1,intro,crumb,body,graph):
    img=f"{BASE}{T[0]['cat']}/{T[0]['slug']}/screenshot.png"
    return f"""<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{e(title)}</title><meta name="description" content="{e(desc)}"><meta name="robots" content="index, follow, max-image-preview:large"><link rel="canonical" href="{canon}">
<meta property="og:type" content="website"><meta property="og:site_name" content="Free HTML Templates"><meta property="og:title" content="{e(title)}"><meta property="og:description" content="{e(desc)}"><meta property="og:url" content="{canon}">
<meta property="og:image" content="{img}"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="{e(title)}"><meta name="twitter:description" content="{e(desc)}"><meta name="twitter:image" content="{img}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='14' fill='%23141A26'/><path d='M22 22l-8 10 8 10M42 22l8 10-8 10' stroke='%232F6BFF' stroke-width='6' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>">
{ld({"@context":"https://schema.org","@graph":graph})}
<style>{CSS}</style></head><body>
<header><div class="wrap">{crumb}<h1>{e(h1)}</h1><p>{e(intro)}</p><ul class="perks"><li>100% free, MIT license</li><li>One HTML file each</li><li>Works on phones</li><li>SEO ready</li></ul><div class="meta"><a href="#templates">Browse the templates</a><a class="alt" href="#faq">How to use them</a></div></div></header>
<main id="templates"><div class="wrap">{body}</div></main>
<footer><div class="wrap">Free, MIT-licensed HTML website templates. Photos from Unsplash. Updated {today:%B %Y}.</div></footer>
</body></html>"""
def card(t,prefix):
    return f"""<article class="card"><img src="{prefix}{t['slug']}/screenshot.png" alt="{e(t['name'])}, free {e(t['type'].lower())} website template" width="1440" height="900" loading="lazy"><div class="body"><span class="type">{e(t['type'])}</span><h3>{e(t['name'])}</h3><p>{e(t['desc'])}</p><div class="links"><a href="{prefix}{t['slug']}/">Live demo</a><a href="{prefix}{t['slug']}/index.html" download="{t['slug']}.html">Download</a></div></div></article>"""
HOW="<h2>How to use a template</h2><p class='sub'>Three steps, no special software.</p><div class='how'><div><b>1. Download</b><p>Pick a template, open the live demo and click Download. You get one HTML file.</p></div><div><b>2. Edit</b><p>Open the file in any text editor and change the words, colours and photos to match your business.</p></div><div><b>3. Publish</b><p>Upload the file to GitHub Pages, Netlify or your own web host. Your site is live.</p></div></div>"
# category pages
for ck,cv in C.items():
    ts=[t for t in T if t['cat']==ck]; canon=f"{BASE}{ck}/"
    body=f"<h2>{len(ts)} free {e(cv['name'].lower())} templates</h2><p class='sub'>Click a template to see the live demo, or download it straight away.</p><div class='grid'>"+''.join(card(t,'') for t in ts)+"</div>"+HOW+faq_html()
    graph=[{"@type":"CollectionPage","@id":canon,"url":canon,"name":cv['title'],"description":cv['desc'],"inLanguage":"en","isPartOf":{"@type":"WebSite","name":"Free HTML Templates","url":BASE},"mainEntity":itemlist(ts)},
           {"@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Free HTML Templates","item":BASE},{"@type":"ListItem","position":2,"name":f"{cv['name']} Templates","item":canon}]},faq_ld()]
    open(f"{root}{ck}/index.html",'w').write(page(cv['title'],cv['desc'],canon,f"Free {cv['name'].lower()} website templates",cv['intro'],f"<div class='crumb'><a href='../'>All templates</a> / {e(cv['name'])}</div>",body,graph))
# root
cats="<div class='cats'>"+''.join(f"<a href='{k}/'>{e(v['name'])} ({sum(t['cat']==k for t in T)})</a>" for k,v in C.items())+"</div>"
body=cats
for ck,cv in C.items():
    ts=[t for t in T if t['cat']==ck]
    body+=f"<h2 id='{ck}'>{e(cv['name'])} templates</h2><p class='sub'>{e(cv['intro'])} <a href='{ck}/'>See all {e(cv['name'].lower())} templates</a></p><div class='grid'>"+''.join(card(t,ck+'/') for t in ts)+"</div>"
body+=HOW+faq_html()
rt=f"{N} Free HTML Website Templates {YEAR}: Download and Edit"
pl=[v.get("short",v.get("plural",v["name"])) for v in C.values()]
rd=f"{N} free HTML templates, one file each: "+(", ".join(pl[:-1])+" and "+pl[-1] if len(pl)>1 else pl[0])+". No coding needed. Mobile friendly and SEO ready."
if len(rd)>160: rd=rd.replace(" No coding needed.","")
if len(rd)>160: rd=f"{N} free HTML templates, one file each, for {len(pl)} kinds of websites. No coding needed. Mobile friendly and SEO ready."
graph=[{"@type":"WebSite","@id":BASE+"#website","url":BASE,"name":"Free HTML Templates","description":rd,"inLanguage":"en"},
       {"@type":"CollectionPage","@id":BASE,"url":BASE,"name":rt,"description":rd,"isPartOf":{"@id":BASE+"#website"},"mainEntity":itemlist(T)},faq_ld()]
open(root+'index.html','w').write(page(rt,rd,BASE,"Free HTML website templates you can edit in minutes","Each template is one HTML file. Open it, change the text and photos, and upload it. No WordPress, no page builder and no monthly fees. Free for personal and business use.","",body,graph))
# root README
rows="\n".join(f"| [![{t['name']}]({t['cat']}/{t['slug']}/screenshot.png)]({BASE}{t['cat']}/{t['slug']}/) | **[{t['name']}]({t['cat']}/{t['slug']}/)**<br>{t['type']} template<br><br>{t['desc']}<br><br>[Live demo and download]({BASE}{t['cat']}/{t['slug']}/) |" for t in T)
faqmd="\n\n".join(f"**{q}**<br>{a}" for q,a in FAQ)
readme=f"""# Free HTML Website Templates

{N} free website templates, each in a single HTML file. Download one, change the text and photos, and your website is ready to upload. No WordPress, no page builder, no monthly fees.

**[See every template with a live demo and free download]({BASE})**

## Why use these templates

- **One file each.** The CSS and JavaScript are inside the HTML file, so there is nothing to install or build.
- **Works on phones.** Every template adjusts to phones, tablets and desktops.
- **SEO ready.** Page titles, meta descriptions, social sharing tags and schema markup are already set up.
- **Real example text.** Written like a real business, not lorem ipsum, so you can see how your site will read.
- **Free for business use.** MIT license. Use them for your own site or for clients.

## Templates

| Preview | Template |
|---|---|
{rows}

## Categories

{chr(10).join(f"- **[{v['name']}]({k}/)** ({sum(t['cat']==k for t in T)} templates): {v['desc']}" for k,v in C.items())}

New templates are added every week. Coming next: more restaurant and food sites, portfolios, restaurants, clinics, schools, real estate and wedding sites.

## How to use a template

1. Go to the [template gallery]({BASE}), open a live demo and click **Download this template (free)**.
2. Open the file in a text editor such as VS Code or Notepad++.
3. Change the text, colours and photos. Colours are listed at the top of the `<style>` block.
4. Upload the file to GitHub Pages, Netlify, Cloudflare Pages or your own web host.

## Questions

{faqmd}

## Credits

Photos from [Unsplash](https://unsplash.com) under the Unsplash License. Fonts from [Google Fonts](https://fonts.google.com).

## License

[MIT](LICENSE). Free for personal and commercial use. If a template saved you time, please star this repository so more people can find it.
"""
open(root+'README.md','w').write(readme)
urls=[BASE]+[f"{BASE}{k}/" for k in C]+[f"{BASE}{t['cat']}/{t['slug']}/" for t in T]
open(root+'sitemap.xml','w').write('<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'+''.join(f"  <url><loc>{u}</loc><lastmod>{today.isoformat()}</lastmod></url>\n" for u in urls)+'</urlset>\n')
open(root+'LICENSE','w').write(f"""MIT License

Copyright (c) {today.year} mmrahmanbappi

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
""")
print('ok')

print("ROOT:",len(rt),rt,"|",len(rd),rd)
for v in C.values(): print("CAT:",len(v['title']),v['title'],"|",len(v['desc']),v['desc'])
open('/home/claude/repo_desc.txt','w').write(f"{N} free HTML templates, one file each: "+", ".join(pl[:-1])+" and "+pl[-1]+". Mobile friendly, SEO ready, new templates every week.")
