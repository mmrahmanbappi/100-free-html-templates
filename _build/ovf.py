import asyncio,sys
from playwright.async_api import async_playwright
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch();pg=await b.new_page(viewport={'width':375,'height':800})
        for r in sys.argv[1:]:
            await pg.goto('file:///home/claude/fht/'+r+'/index.html');await pg.wait_for_timeout(800)
            o=await pg.evaluate('''()=>{const W=innerWidth;return [...document.querySelectorAll('body *')].filter(e=>{const r=e.getBoundingClientRect();return r.right>W+2&&r.width>0}).filter(e=>{let p=e.parentElement;while(p){const s=getComputedStyle(p);if(s.overflowX!=='visible'||s.position==='fixed')return false;p=p.parentElement}return getComputedStyle(e).position!=='fixed'}).slice(0,4).map(e=>e.tagName+'.'+e.className+' '+Math.round(e.getBoundingClientRect().right))}''')
            print(r,o)
        await b.close()
asyncio.run(main())
