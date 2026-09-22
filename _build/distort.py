import glob,asyncio
from playwright.async_api import async_playwright
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch();pg=await b.new_page(viewport={'width':1440,'height':900})
        for f in sorted(glob.glob('/home/claude/fht/*/*/index.html')):
            await pg.goto('file://'+f);await pg.wait_for_timeout(1500)
            bad=await pg.evaluate('''()=>[...document.images].filter(i=>i.naturalWidth&&i.clientWidth>40).filter(i=>{const cs=getComputedStyle(i);if(cs.objectFit!=='fill')return false;const r=i.clientWidth/i.clientHeight,n=i.naturalWidth/i.naturalHeight;return Math.abs(r/n-1)>.06}).map(i=>i.className+' '+i.src.slice(40,70)+' '+i.clientWidth+'x'+i.clientHeight)''')
            if bad: print(f.split('fht/')[1],bad[:3])
        await b.close()
asyncio.run(main())
