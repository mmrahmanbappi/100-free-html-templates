import re,json,hashlib,urllib.request,glob,xml.etree.ElementTree as ET
BASE='https://mmrahmanbappi.github.io/free-html-templates/'
def get(u):
    try:
        r=urllib.request.urlopen(urllib.request.Request(u,headers={'User-Agent':'Mozilla/5.0 (compatible; Googlebot/2.1)'}),timeout=30);return r.status,r.read()
    except urllib.error.HTTPError as e: return e.code,b''
    except Exception as e: return 0,str(e).encode()
# sitemap
st,sm=get(BASE+'sitemap.xml');print('sitemap.xml',st)
urls=[e.text for e in ET.fromstring(sm).iter('{http://www.sitemaps.org/schemas/sitemap/0.9}loc')]
print('URLs in sitemap:',len(urls))
local=sorted(glob.glob('/home/claude/fht/**/index.html',recursive=True))
print('local pages:',len(local))
exp={BASE+f.replace('/home/claude/fht/','').replace('index.html','') for f in local}
print('missing from sitemap:',sorted(exp-set(urls)) or 'none');print('extra in sitemap:',sorted(set(urls)-exp) or 'none')
st,rb=get(BASE+'robots.txt');print('robots.txt',st)
issues=[];ok=0
for f in local:
    rel=f.replace('/home/claude/fht/','');u=BASE+rel.replace('index.html','')
    st,body=get(u)
    if st!=200: issues.append((rel,'HTTP',st));continue
    if hashlib.md5(body).hexdigest()!=hashlib.md5(open(f,'rb').read()).hexdigest(): issues.append((rel,'live differs from local'))
    s=body.decode()
    t=re.search(r'<title>(.*?)</title>',s,re.S);d=re.search(r'<meta name="description" content="([^"]*)"',s)
    can=re.search(r'<link rel="canonical" href="([^"]*)"',s)
    chk={'lang':'<html lang="en"' in s,'viewport':'name="viewport"' in s,'title':bool(t) and len(t.group(1).replace('&amp;','&'))<=62,
      'desc':bool(d) and 50<len(d.group(1))<=160,'canonical=url':bool(can) and can.group(1)==u,'og:title':'property="og:title"' in s,
      'og:image':'property="og:image"' in s,'twitter':'name="twitter:card"' in s,'no noindex':'noindex' not in s.lower(),
      'one h1':len(re.findall(r'<h1[\s>]',s))==1}
    for b in re.findall(r'<script type="application/ld\+json">(.*?)</script>',s,re.S):
        try: json.loads(b)
        except Exception: chk['jsonld']=False
    imgs=re.findall(r'<img\b[^>]*>',s);chk['img alt']=all(' alt=' in i for i in imgs)
    ids=set(re.findall(r'\bid="([^"]+)"',s));bad=[h for h in re.findall(r'href="#([^"]+)"',s) if h not in ids]
    if bad: chk['anchors ok']=False
    if rel!='index.html' and rel.count('/')==2:
        st2,_=get(u+'index.html');chk['download file']=st2==200
        st3,_=get(u+'screenshot.png');chk['screenshot']=st3==200
        og=re.search(r'property="og:image" content="([^"]*)"',s)
        if og: st4,_=get(og.group(1));chk['og image loads']=st4==200
    fails=[k for k,v in chk.items() if not v]
    if fails: issues.append((rel,fails,bad[:3] if bad else ''))
    else: ok+=1
print('pages fully passing:',ok,'of',len(local))
for i in issues: print('ISSUE',i)
