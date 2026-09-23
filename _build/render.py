import glob,asyncio
from playwright.async_api import async_playwright
BASE='https://mmrahmanbappi.github.io/100-free-html-templates/'
ALL=[f.replace('/home/claude/fht/','').replace('index.html','') for f in sorted(glob.glob('/home/claude/fht/**/index.html',recursive=True))]
import sys
a,z=int(sys.argv[1]),int(sys.argv[2]);pages=ALL[a:z]
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch();res=[]
        for vw,name in [(375,'phone'),(1440,'desktop')]:
            ctx=await b.new_context(viewport={'width':vw,'height':800});pg=await ctx.new_page();errs=[]
            pg.on('pageerror',lambda e:errs.append(str(e)[:80]))
            for r in pages:
                errs.clear()
                await pg.goto(BASE+r,wait_until='networkidle',timeout=60000)
                ov=await pg.evaluate('document.documentElement.scrollWidth-window.innerWidth')
                broken=await pg.evaluate('[...document.images].filter(i=>i.complete&&i.naturalWidth===0&&!i.loading.includes("lazy")).length')
                if ov>2 or errs or broken: res.append((name,r or '/',f'overflow {ov}px' if ov>2 else '',errs[:2],f'{broken} broken img' if broken else ''))
            await ctx.close()
        await b.close()
        print('checked',len(pages),'pages on phone and desktop');[print('ISSUE',x) for x in res] or print('no overflow, no JS errors, no broken images')
asyncio.run(main())
