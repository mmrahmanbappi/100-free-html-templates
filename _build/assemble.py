import json, sys
BASE="https://mmrahmanbappi.github.io/free-html-templates/"; REPO="https://github.com/mmrahmanbappi/free-html-templates"
def head(cat,slug,title,desc,og,kw,color,kind,favicon,fonts,catname="Business Templates"):
    url=f"{BASE}{cat}/{slug}/"; img=url+"screenshot.png"
    g={"@context":"https://schema.org","@graph":[
     {"@type":"WebPage","@id":url+"#webpage","url":url,"name":og,"description":desc,"inLanguage":"en","primaryImageOfPage":{"@type":"ImageObject","url":img},"isPartOf":{"@type":"WebSite","name":"Free HTML Templates","url":BASE},"breadcrumb":{"@id":url+"#breadcrumb"},"mainEntity":{"@id":url+"#template"}},
     {"@type":"CreativeWork","@id":url+"#template","name":og,"description":desc,"genre":"Website template","image":img,"url":url,"license":"https://opensource.org/licenses/MIT","isAccessibleForFree":True,"encodingFormat":"text/html","inLanguage":"en","keywords":kw,"author":{"@type":"Person","name":"mmrahmanbappi","url":"https://github.com/mmrahmanbappi"},"offers":{"@type":"Offer","price":"0","priceCurrency":"USD"},"downloadUrl":f"{url}index.html"},
     {"@type":"BreadcrumbList","@id":url+"#breadcrumb","itemListElement":[{"@type":"ListItem","position":1,"name":"Free HTML Templates","item":BASE},{"@type":"ListItem","position":2,"name":catname,"item":f"{BASE}{cat}/"},{"@type":"ListItem","position":3,"name":kind+" Template","item":url}]}]}
    return f'''<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title}</title>
<meta name="description" content="{desc}">
<meta name="keywords" content="{kw}, free HTML template, one page website template">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{url}">
<meta name="theme-color" content="{color}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Free HTML Templates">
<meta property="og:title" content="{og}">
<meta property="og:description" content="{desc}">
<meta property="og:url" content="{url}">
<meta property="og:image" content="{img}">
<meta property="og:image:width" content="1440">
<meta property="og:image:height" content="900">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{og}">
<meta name="twitter:description" content="{desc}">
<meta name="twitter:image" content="{img}">
<link rel="icon" href="{favicon}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://images.unsplash.com">
<link href="{fonts}" rel="stylesheet">
<!-- Structured data for this template page. When you use the template, replace it with LocalBusiness or Organization schema for your own business (see README). -->
<script type="application/ld+json">
{json.dumps(g,indent=1)}
</script>
'''
def bar(cat,slug):
    return f'''<a class="skip" href="#main">Skip to content</a>
<!-- Template notice bar: delete this block before you publish your own site -->
<div id="template-bar" style="background:#111;color:#fff;font:500 14px/1.4 system-ui,sans-serif;text-align:center;padding:9px 16px">Free HTML template. <a href="index.html" download="{slug}.html" style="color:#FFD166;font-weight:700">Download this template (free)</a> &nbsp;|&nbsp; <a href="{BASE}" style="color:#fff">See all free templates</a></div>
'''
FOOTJS='''<script>
(()=>{const b=document.querySelector('.burger'),m=document.getElementById('menu'),y=document.getElementById('yr');
if(b&&m){b.addEventListener('click',()=>{const o=m.classList.toggle('open');b.setAttribute('aria-expanded',o)});
m.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{m.classList.remove('open');b.setAttribute('aria-expanded',false)}))}
if(y)y.textContent=new Date().getFullYear()})();
</script>
</body>
</html>
'''
if __name__=='__main__':
    meta=json.load(open(sys.argv[1])); cat,slug=meta['cat'],meta['slug']
    css=open(sys.argv[2]).read(); body=open(sys.argv[3]).read()
    import os; os.makedirs(f"/home/claude/fht/{cat}/{slug}",exist_ok=True)
    out=head(**{k:meta[k] for k in ['cat','slug','title','desc','og','kw','color','kind','favicon','fonts']},catname=meta.get('catname','Business Templates'))+"<style>\n"+css+"\n</style>\n</head>\n<body>\n"+bar(cat,slug)+body+FOOTJS
    open(f"/home/claude/fht/{cat}/{slug}/index.html",'w').write(out); print('built',slug,len(out))
