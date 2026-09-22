import glob,asyncio
from playwright.async_api import async_playwright
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch();pg=await b.new_page(viewport={'width':1440,'height':900})
        for f in sorted(glob.glob('/home/claude/fht/*/*/index.html')):
            await pg.goto('file://'+f);await pg.wait_for_timeout(800)
            bad=await pg.evaluate('''()=>[...document.images].filter(i=>i.clientWidth>40).filter(i=>{const a=getComputedStyle(i).aspectRatio;if(!a||a==='auto'||a.startsWith('auto'))return false;const [x,y]=a.split('/').map(Number);const want=y?x/y:x;return Math.abs(i.clientWidth/i.clientHeight/want-1)>.06}).map(i=>i.alt.slice(0,30)+' '+i.clientWidth+'x'+i.clientHeight)''')
            if bad: print(f.split('fht/')[1],bad[:3])
        await b.close()
asyncio.run(main())
