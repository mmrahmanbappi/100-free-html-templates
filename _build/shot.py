import sys
from playwright.sync_api import sync_playwright
path=sys.argv[1]
with sync_playwright() as p:
    b=p.chromium.launch()
    pg=b.new_page(viewport={'width':1440,'height':900})
    pg.goto('file://'+path+'/index.html',wait_until='networkidle',timeout=60000)
    pg.add_style_tag(content='.reveal{opacity:1!important;transform:none!important} .track{animation:none!important} #template-bar{display:none!important} .m{opacity:1!important;transform:none!important}')
    pg.evaluate("document.querySelectorAll('img[loading=lazy]').forEach(i=>i.loading='eager')")
    pg.wait_for_timeout(2500)
    pg.screenshot(path=path+'/screenshot.png')
    pg.screenshot(path='/home/claude/full.png',full_page=True)
    m=b.new_page(viewport={'width':390,'height':844},device_scale_factor=1)
    m.goto('file://'+path+'/index.html',wait_until='networkidle',timeout=60000)
    m.add_style_tag(content='.reveal{opacity:1!important;transform:none!important}')
    m.wait_for_timeout(1500)
    m.screenshot(path='/home/claude/mobile.png',full_page=True)
    b.close()
from PIL import Image
for f,w in [('/home/claude/full.png',720),('/home/claude/mobile.png',260)]:
    im=Image.open(f); h=int(im.height*w/im.width); im=im.resize((w,h)); 
    # split tall into columns
    seg=1800; cols=[im.crop((0,y,w,min(y+seg,h))) for y in range(0,h,seg)]
    out=Image.new('RGB',(w*len(cols),min(seg,h)),'white')
    for i,c in enumerate(cols): out.paste(c,(i*w,0))
    out.save(f.replace('.png','_s.jpg'),quality=70); print(f,out.size)
