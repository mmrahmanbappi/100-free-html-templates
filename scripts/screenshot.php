<?php
// Takes the 1440 x 900 screenshot.png for templates.
// php scripts/screenshot.php                 -> only templates that have no screenshot yet
// php scripts/screenshot.php a/b/index.html  -> these templates (plus any missing ones)
// php scripts/screenshot.php --all           -> every template
declare(strict_types=1);
$root = dirname(__DIR__);
$T = json_decode(file_get_contents(__DIR__ . '/themes.json'), true)['themes'];
$chrome = getenv('CHROME') ?: trim((string)shell_exec('command -v google-chrome || command -v chromium || command -v chromium-browser'));
if ($chrome === '') { fwrite(STDERR, "Chrome not found\n"); exit(1); }

$args = array_slice($argv, 1); $all = in_array('--all', $args, true);
$want = [];
foreach ($T as $t) {
    $dir = "{$t['cat']}/{$t['slug']}";
    $changed = false;
    foreach ($args as $a) if (str_starts_with(ltrim($a, './'), $dir . '/')) $changed = true;
    if ($all || $changed || !is_file("$root/$dir/screenshot.png")) $want[] = $dir;
}
if (!$want) { echo "No screenshots needed\n"; exit(0); }
if (!function_exists('imagecreatefrompng')) { fwrite(STDERR, "The PHP GD extension is needed\n"); exit(1); }

// Headless Chrome keeps some window height for its hidden toolbar, so the page gets less than
// the window size. Measure that once, then open a taller window and crop back to 1440 x 900.
$probe = sys_get_temp_dir() . '/vh-probe.html';
file_put_contents($probe, '<html><body><script>document.title="VH"+innerHeight</script></body></html>');
$dom = (string)shell_exec(sprintf('timeout 30 %s --headless=new --no-sandbox --disable-gpu --window-size=1440,900 --dump-dom %s 2>/dev/null', escapeshellarg($chrome), escapeshellarg('file://' . $probe)));
$extra = preg_match('~VH(\d+)~', $dom, $m) ? max(0, 900 - (int)$m[1]) : 0;
echo "Chrome toolbar space: {$extra}px\n";

// Same view as the gallery previews: animations finished, notice bar hidden, all photos loaded.
$fix = '<style>.reveal,.m{opacity:1!important;transform:none!important}.track{animation:none!important}#template-bar{display:none!important}</style>';
$fails = 0;
foreach ($want as $dir) {
    $src = "$root/$dir/index.html";
    if (!is_file($src)) { echo "SKIP $dir (no index.html)\n"; continue; }
    $html = file_get_contents($src);
    $html = str_replace('loading="lazy"', 'loading="eager"', $html);
    $html = preg_replace('~</head>~i', $fix . '</head>', $html, 1);
    $tmp = "$root/$dir/.shot.html";
    file_put_contents($tmp, $html);
    $png = "$root/$dir/screenshot.png"; $t0 = microtime(true);
    $cmd = sprintf('timeout 60 %s --headless=new --no-sandbox --disable-gpu --hide-scrollbars --window-size=1440,%d --run-all-compositor-stages-before-draw --virtual-time-budget=20000 --screenshot=%s %s 2>/dev/null',
        escapeshellarg($chrome), 900 + $extra, escapeshellarg($png), escapeshellarg('file://' . $tmp));
    exec($cmd, $o, $rc);
    unlink($tmp);
    if ($rc === 0 && is_file($png)) {
        $im = imagecreatefrompng($png);
        if ($im && (imagesx($im) !== 1440 || imagesy($im) !== 900)) {
            $out = imagecreatetruecolor(1440, 900);
            imagecopy($out, $im, 0, 0, 0, 0, 1440, 900);
            imagepng($out, $png, 6);
        }
    }
    echo ($rc === 0 ? 'saved ' : "FAILED ($rc) ") . "$dir in " . round(microtime(true) - $t0, 1) . "s\n";
    if ($rc !== 0) $fails++;
}
exit($fails ? 1 : 0);
