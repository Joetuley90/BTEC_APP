<?php
/*
 * Crafticulum: an invoice as a PDF file, made on the server with nothing to install.
 * It uses the two fonts every PDF reader has built in (Helvetica and Helvetica Bold), so the file is small and opens anywhere.
 * The same invoice is the page at its private address; this is the copy that goes with the email.
 */
declare(strict_types=1);

function ccInvTotals(array $inv): array { $net = 0; foreach ((array) ($inv['lines'] ?? []) as $l) $net += (int) round(((float) ($l['q'] ?? 1)) * (int) ($l['u'] ?? 0)); $vat = (int) round($net * ((float) ($inv['vatRate'] ?? 0)) / 100); return ['net' => $net, 'vat' => $vat, 'total' => $net + $vat]; }

/* how wide each character is in the two fonts, in thousandths of the font size (characters 32 to 126; anything else counts as 556) */
const CC_PDF_W = [
    'R' => [278,278,355,556,556,889,667,191,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,278,278,584,584,584,556,1015,667,667,722,722,667,611,778,722,278,500,667,556,833,722,778,667,778,722,667,611,722,667,944,667,667,611,278,278,278,469,556,333,556,556,500,556,556,278,556,556,222,222,500,222,833,556,556,556,556,333,500,278,556,500,722,500,500,500,334,260,334,584],
    'B' => [278,333,474,556,556,889,722,238,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,333,333,584,584,584,611,975,722,722,722,722,667,611,778,722,278,556,722,611,833,722,778,667,778,722,667,611,722,667,944,667,667,611,333,278,333,584,556,333,556,611,556,611,556,333,611,611,278,278,556,278,889,611,611,611,611,389,556,333,611,556,778,556,556,500,389,280,389,584],
];
/* text in the PDF's own character set (Windows-1252), so £ and curly quotes come out right */
function ccPdfEnc(string $s): string { $s = str_replace(["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{2013}", "\u{2014}", "\u{2026}"], ["'", "'", '"', '"', '-', '-', '...'], $s); $o = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s); return $o === false ? (string) preg_replace('/[^\x20-\x7E]/', '?', $s) : $o; }
function ccPdfWidth(string $enc, float $size, bool $bold = false): float { $w = 0; $t = CC_PDF_W[$bold ? 'B' : 'R']; for ($i = 0, $n = strlen($enc); $i < $n; $i++) { $c = ord($enc[$i]); $w += ($c >= 32 && $c <= 126) ? $t[$c - 32] : 556; } return $w * $size / 1000; }
function ccPdfWrap(string $text, float $size, float $max, bool $bold = false): array { $out = [];
    foreach (preg_split('/\r?\n/', $text) as $para) { $line = ''; foreach (preg_split('/\s+/', trim($para)) as $word) { if ($word === '') continue; $try = $line === '' ? $word : "$line $word";
            if ($line !== '' && ccPdfWidth(ccPdfEnc($try), $size, $bold) > $max) { $out[] = $line; $line = $word; } else $line = $try; }
        $out[] = $line; }
    return $out; }

/* a very small page builder: text, lines and boxes, top-down in points (A4 is 595 x 842) */
final class CcPdfPage {
    public string $ops = '';
    private float $H = 842;
    private static function col(string $hex): string { $hex = ltrim($hex, '#'); return sprintf('%.3f %.3f %.3f', hexdec(substr($hex, 0, 2)) / 255, hexdec(substr($hex, 2, 2)) / 255, hexdec(substr($hex, 4, 2)) / 255); }
    public function text(float $x, float $y, string $s, float $size = 10, bool $bold = false, string $color = '1e1b4b', string $align = 'left'): void {
        $e = ccPdfEnc($s); $w = ccPdfWidth($e, $size, $bold); if ($align === 'right') $x -= $w; elseif ($align === 'center') $x -= $w / 2;
        $this->ops .= sprintf("BT %s rg /%s %.2f Tf %.2f %.2f Td (%s) Tj ET\n", self::col($color), $bold ? 'F2' : 'F1', $size, $x, $this->H - $y, strtr($e, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)'])); }
    public function rect(float $x, float $y, float $w, float $h, string $fill): void { $this->ops .= sprintf("%s rg %.2f %.2f %.2f %.2f re f\n", self::col($fill), $x, $this->H - $y - $h, $w, $h); }
    public function line(float $x1, float $y1, float $x2, float $y2, string $color = 'e0e7ff', float $w = 0.8): void { $this->ops .= sprintf("%s RG %.2f w %.2f %.2f m %.2f %.2f l S\n", self::col($color), $w, $x1, $this->H - $y1, $x2, $this->H - $y2); }
    /* the Crafticulum mark: the same shape as the logo, drawn at (x, y) and s points wide */
    public function mark(float $x, float $y, float $s): void { $k = $s / 100; $P = fn($a, $b) => sprintf('%.2f %.2f', $x + $a * $k, $this->H - ($y + $b * $k)); $q = 0.5523;
        $this->ops .= self::col('4f46e5') . " rg\n" . $P(22, 8) . " m " . $P(64, 8) . " l " . $P(64, 36) . " l " . $P(36, 36) . " l " . $P(36, 64) . " l " . $P(92, 64) . " l " . $P(92, 78) . " l "
            . $P(92, 78 + 14 * $q) . ' ' . $P(78 + 14 * $q, 92) . ' ' . $P(78, 92) . " c " . $P(22, 92) . " l " . $P(22 - 14 * $q, 92) . ' ' . $P(8, 78 + 14 * $q) . ' ' . $P(8, 78) . " c " . $P(8, 22) . " l "
            . $P(8, 22 - 14 * $q) . ' ' . $P(22 - 14 * $q, 8) . ' ' . $P(22, 8) . " c h f\n" . self::col('a5b4fc') . " rg " . $P(64, 8) . " m " . $P(92, 36) . " l " . $P(64, 36) . " l h f\n"; }
}
function ccPdfFile(array $pages, string $title): string {
    $objs = []; $add = function (string $s) use (&$objs): int { $objs[] = $s; return count($objs); };
    $f1 = $add('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>'); $f2 = $add('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>');
    $pagesId = count($objs) + 1; $add(''); $kids = [];
    foreach ($pages as $p) { $c = $add("<< /Length " . strlen($p->ops) . " >>\nstream\n" . $p->ops . "endstream"); $kids[] = $add("<< /Type /Page /Parent $pagesId 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 $f1 0 R /F2 $f2 0 R >> >> /Contents $c 0 R >>"); }
    $objs[$pagesId - 1] = '<< /Type /Pages /Kids [' . implode(' ', array_map(fn($k) => "$k 0 R", $kids)) . '] /Count ' . count($kids) . ' >>';
    $cat = $add("<< /Type /Catalog /Pages $pagesId 0 R >>"); $info = $add('<< /Title (' . strtr(ccPdfEnc($title), ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']) . ') /Producer (Crafticulum) /CreationDate (D:' . date('YmdHis') . ') >>');
    $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n"; $at = [];
    foreach ($objs as $i => $o) { $at[] = strlen($out); $out .= ($i + 1) . " 0 obj\n$o\nendobj\n"; }
    $x = strlen($out); $out .= "xref\n0 " . (count($objs) + 1) . "\n0000000000 65535 f \n"; foreach ($at as $o) $out .= sprintf("%010d 00000 n \n", $o);
    return $out . "trailer\n<< /Size " . (count($objs) + 1) . " /Root $cat 0 R /Info $info 0 R >>\nstartxref\n$x\n%%EOF\n";
}

/* the invoice itself, laid out like the page at its private address */
function ccInvPdf(array $inv, array $set, string $contact = ''): string {
    $t = ccInvTotals($inv); $st = (string) ($inv['status'] ?? ''); $due = (int) ($inv['due'] ?? 0); $over = $st === 'sent' && $due && $due < time();
    $label = $st === 'paid' ? 'Paid ' . date('j M Y', (int) ($inv['paidAt'] ?? time())) : ($st === 'void' ? 'Cancelled' : ($st === 'draft' ? 'Draft' : ($over ? 'Overdue' : 'Awaiting payment')));
    $d = fn($ts) => $ts ? date('j F Y', (int) $ts) : ''; $money = fn(int $p) => '£' . number_format($p / 100, 2);
    $pages = []; $p = new CcPdfPage(); $pages[] = $p; $L = 50; $R = 545; $y = 0;
    $p->rect(0, 0, 595, 6, '4f46e5'); $p->rect(297, 0, 298, 6, '7c3aed');
    $p->mark($L, 44, 30); $p->text($L + 38, 66, 'craft', 22, true, '1e1b4b'); $p->text($L + 38 + ccPdfWidth('craft', 22, true), 66, 'iculum', 22, true, '4f46e5');
    $y = 96; foreach (array_merge(preg_split('/\r?\n/', trim((string) ($set['from'] ?? ''))), ($set['vatNo'] ?? '') !== '' ? ['VAT number ' . $set['vatNo']] : []) as $l) { if (trim($l) === '') continue; $p->text($L, $y, $l, 9, false, '64748b'); $y += 12; }
    $p->text($R, 64, 'Invoice', 26, true, '1e1b4b', 'right'); $p->text($R, 84, (string) (($inv['number'] ?? '') ?: 'Draft'), 12, true, '1e1b4b', 'right');
    $bw = ccPdfWidth(ccPdfEnc($label), 9, true) + 16; $bc = $st === 'paid' ? ['dcfce7', '166534'] : ($st === 'void' ? ['f1f5f9', '475569'] : ($over ? ['fee2e2', '991b1b'] : ['eef2ff', '4338ca']));
    $p->rect($R - $bw, 92, $bw, 16, $bc[0]); $p->text($R - $bw / 2, 103.5, $label, 9, true, $bc[1], 'center');
    $y = max($y, 120) + 18; $top = $y;
    $p->text($L, $y, 'INVOICE TO', 8, true, '64748b'); $y += 15; $p->text($L, $y, (string) ($inv['to']['name'] ?? ''), 11, true); $y += 14;
    foreach (preg_split('/\r?\n/', trim((string) ($inv['to']['address'] ?? ''))) as $l) { if (trim($l) === '') continue; $p->text($L, $y, $l, 9.5); $y += 12.5; }
    if (($inv['to']['email'] ?? '') !== '') { $p->text($L, $y, (string) $inv['to']['email'], 9.5, false, '64748b'); $y += 12.5; }
    $y2 = $top; $X = 330; $p->text($X, $y2, 'DETAILS', 8, true, '64748b'); $y2 += 15;
    $kv = function (string $k, string $v) use ($p, $X, &$y2) { $p->text($X, $y2, $k, 9.5, false, '475569'); $p->text($X + 92, $y2, $v, 9.5, true); $y2 += 13.5; };
    $kv('Date', $d($inv['issued'] ?? 0) ?: 'Not issued yet'); $kv('Pay by', $d($due)); if (($inv['po'] ?? '') !== '') $kv('Purchase order', (string) $inv['po']); if (!empty($inv['from'])) $kv('Covers', date('j M Y', (int) $inv['from']) . ' to ' . date('j M Y', (int) ($inv['until'] ?? 0)));
    $y = max($y, $y2) + 18;
    /* the lines */
    $cD = $L + 8; $cQ = 370; $cU = 455; $cA = $R - 8;
    $head = function () use ($p, $L, $R, $cD, $cQ, $cU, $cA, &$y) { $p->rect($L, $y, $R - $L, 22, 'eef2ff'); $p->text($cD, $y + 14.5, 'Description', 9, true, '3730a3'); $p->text($cQ, $y + 14.5, 'Quantity', 9, true, '3730a3', 'right'); $p->text($cU, $y + 14.5, 'Price', 9, true, '3730a3', 'right'); $p->text($cA, $y + 14.5, 'Amount', 9, true, '3730a3', 'right'); $y += 30; };
    $head();
    foreach ((array) ($inv['lines'] ?? []) as $l) { $q = (float) ($l['q'] ?? 1); $u = (int) ($l['u'] ?? 0); $rows = ccPdfWrap((string) ($l['d'] ?? ''), 9.5, $cQ - 60 - $cD);
        if ($y + 13 * count($rows) > 700) { $p = new CcPdfPage(); $pages[] = $p; $y = 50; $head(); }
        $p->text($cQ, $y, rtrim(rtrim(number_format($q, 2), '0'), '.'), 9.5, false, '1e1b4b', 'right'); $p->text($cU, $y, $money($u), 9.5, false, '1e1b4b', 'right'); $p->text($cA, $y, $money((int) round($q * $u)), 9.5, false, '1e1b4b', 'right');
        foreach ($rows as $r) { $p->text($cD, $y, $r, 9.5); $y += 13; } $y += 4; $p->line($L, $y - 8, $R, $y - 8); }
    /* the totals */
    if ($y > 640) { $p = new CcPdfPage(); $pages[] = $p; $y = 60; }
    $y += 6; $tx = 340; $sum = function (string $k, int $v, bool $big = false) use ($p, $tx, $cA, &$y) { $p->text($tx, $y, $k, $big ? 11 : 9.5, $big, $big ? '1e1b4b' : '475569'); $p->text($cA, $y, '£' . number_format($v / 100, 2), $big ? 13 : 9.5, true, '1e1b4b', 'right'); $y += $big ? 20 : 15; };
    $sum('Subtotal', $t['net']); if ((float) ($inv['vatRate'] ?? 0) > 0) $sum('VAT at ' . rtrim(rtrim(number_format((float) $inv['vatRate'], 1), '0'), '.') . '%', $t['vat']);
    $p->line($tx, $y - 9, $R, $y - 9, 'c7d2fe', 1); $y += 4; $sum($st === 'paid' ? 'Total paid' : 'Total to pay', $t['total'], true);
    /* how to pay */
    if ($st !== 'paid' && $st !== 'void') { $bank = trim((string) ($set['bank'] ?? '')); $lines = $bank !== '' ? preg_split('/\r?\n/', $bank) : ['By bank transfer. Bank details will be sent with this invoice.']; $wrapped = [];
        foreach ($lines as $l) foreach (ccPdfWrap($l, 9.5, $R - $L - 28) as $w) $wrapped[] = $w; $q = 'Please quote ' . (($inv['number'] ?? '') ?: 'the invoice number') . ' with your payment.';
        $h = 36 + 13 * count($wrapped) + 16; if ($y + $h > 780) { $p = new CcPdfPage(); $pages[] = $p; $y = 60; }
        $y += 10; $p->rect($L, $y, $R - $L, $h, 'f5f3ff'); $p->rect($L, $y, 3, $h, '7c3aed'); $yy = $y + 18; $p->text($L + 14, $yy, 'HOW TO PAY', 8, true, '6d28d9'); $yy += 15;
        foreach ($wrapped as $w) { $p->text($L + 14, $yy, $w, 9.5); $yy += 13; } $p->text($L + 14, $yy + 3, $q, 9.5, true); $y += $h + 6; }
    if (trim((string) ($inv['note'] ?? '')) !== '') { $y += 12; foreach (ccPdfWrap((string) $inv['note'], 9.5, $R - $L) as $w) { if ($y > 790) { $p = new CcPdfPage(); $pages[] = $p; $y = 60; } $p->text($L, $y, $w, 9.5, false, '334155'); $y += 13; } }
    $foot = ((float) ($inv['vatRate'] ?? 0) > 0 ? '' : 'No VAT has been charged on this invoice. ') . 'Questions about this invoice: ' . (($set['email'] ?? '') !== '' ? $set['email'] : $contact);
    foreach ($pages as $i => $pg) { $pg->line($L, 806, $R, 806, 'e2e8f0'); $pg->text($L, 820, $foot, 8, false, '64748b'); if (count($pages) > 1) $pg->text($R, 820, 'Page ' . ($i + 1) . ' of ' . count($pages), 8, false, '64748b', 'right'); }
    return ccPdfFile($pages, 'Invoice ' . (($inv['number'] ?? '') ?: 'draft'));
}
function ccInvPdfName(array $inv): string { return 'Crafticulum invoice ' . preg_replace('/[^A-Za-z0-9-]/', '', (string) (($inv['number'] ?? '') ?: 'draft')) . '.pdf'; }
