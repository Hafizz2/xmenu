<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Services\QrService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QrController extends Controller
{
    public function __construct(private QrService $qr) {}

    public function index()
    {
        $cafe = $this->cafe();
        $settings = $this->settings($cafe, []);

        return view('dashboard.qr', compact('cafe', 'settings'));
    }

    /** Live preview of one card, rendered by the same template as the PDF. */
    public function preview(Request $request)
    {
        $cafe = $this->cafe();
        $s = $this->settings($cafe, $this->validated($request));
        $tables = $this->tableList($s);
        $card = $this->card($cafe, $s, $tables[0] ?? null);
        $paper = config("xmenu.paper.{$s['size']}");

        return view('qr.preview', ['card' => $card, 's' => $s, 'paper' => $paper, 'count' => count($tables)]);
    }

    public function pdf(Request $request)
    {
        $cafe = $this->cafe();
        $s = $this->settings($cafe, $this->validated($request));
        $tables = $this->tableList($s);
        $cards = array_map(fn ($t) => $this->card($cafe, $s, $t), $tables);

        $paper = config("xmenu.paper.{$s['size']}");
        $tile = $s['sheet'] === 'tile' && $s['size'] !== 'A4';
        if ($tile && $s['size'] === 'A5') {
            $page = ['w' => 297, 'h' => 210, 'cols' => 2, 'rows' => 1];
        } elseif ($tile) {
            $page = ['w' => 210, 'h' => 297, 'cols' => 2, 'rows' => 2];
        } else {
            $page = ['w' => $paper['w'], 'h' => $paper['h'], 'cols' => 1, 'rows' => 1];
        }
        $perPage = $page['cols'] * $page['rows'];
        if ($s['mode'] === 'cafe' && $perPage > 1) {
            $cards = array_fill(0, $perPage, $cards[0]); // one café QR: fill the sheet with copies
        }
        $pages = array_chunk($cards, $perPage);

        $html = view('qr.sheet', compact('pages', 'page', 'paper', 's', 'tile'))->render();

        $mm = 72 / 25.4;
        if (! is_dir(storage_path('fonts'))) {
            mkdir(storage_path('fonts'), 0755, true);
        }
        $pdf = Pdf::setOption(['dpi' => 150, 'isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans', 'isFontSubsettingEnabled' => true])
            ->setPaper([0, 0, $page['w'] * $mm, $page['h'] * $mm]);
        // Amharic needs an Ethiopic font; DejaVu has no Ethiopic glyphs.
        $metrics = $pdf->getDomPDF()->getFontMetrics();
        foreach (['normal' => 'Regular', 'bold' => 'Bold'] as $weight => $file) {
            $path = resource_path("fonts/NotoSansEthiopic-{$file}.ttf");
            if (is_file($path)) {
                $metrics->registerFont(['family' => 'Ethiopic', 'style' => 'normal', 'weight' => $weight], $path);
            }
        }
        $pdf->loadHTML($html);

        $s['downloaded_at'] = now()->toIso8601String();
        $cafe->qr_settings = $s;
        $cafe->save();

        $name = Str::slug($cafe->name).'-qr-'.strtolower($s['size']).'-'.$s['design'].'.pdf';

        return $pdf->download($name);
    }

    public function png(Request $request)
    {
        $cafe = $this->cafe();
        $s = $this->settings($cafe, $this->validated($request));
        $table = $request->query('table');
        $png = $this->qr->png($cafe->menuUrl($table ?: null), $this->qrDark($s['color']), '#FFFFFF', $s['qr_style'], 32, 3);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.Str::slug($cafe->name).'-qr'.($table ? '-table-'.Str::slug($table) : '').'.png"',
        ]);
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'design' => ['nullable', Rule::in(array_keys(config('xmenu.qr_designs')))],
            'size' => ['nullable', Rule::in(array_keys(config('xmenu.paper')))],
            'sheet' => ['nullable', Rule::in(['single', 'tile'])],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'headline' => ['nullable', 'string', 'max:40'],
            'subline' => ['nullable', 'string', 'max:70'],
            'qr_style' => ['nullable', Rule::in(['square', 'dots'])],
            'mode' => ['nullable', Rule::in(['cafe', 'tables'])],
            'tables' => ['nullable', 'string', 'max:300'],
            'amharic' => ['nullable', 'boolean'],
        ]);
    }

    private function settings(Cafe $cafe, array $input): array
    {
        $defaults = [
            'design' => 'classic',
            'size' => 'A6',
            'sheet' => 'single',
            'color' => $cafe->themeValue('color'),
            'headline' => 'Scan for our menu',
            'subline' => 'Menu · Games · Wi-Fi · Pay',
            'qr_style' => 'dots',
            'mode' => 'cafe',
            'tables' => '1-10',
            'amharic' => multilang(),
        ];
        $saved = array_intersect_key($cafe->qr_settings ?? [], $defaults);
        $s = array_merge($defaults, $saved, array_filter($input, fn ($v) => $v !== null && $v !== ''));
        if (array_key_exists('amharic', $input)) {
            $s['amharic'] = (bool) $input['amharic'];
        }
        $s['color'] = strtoupper($s['color']);

        return $s;
    }

    /** "1-10, VIP, Terrace" → ['1', ..., '10', 'VIP', 'Terrace'] (max 200). */
    private function tableList(array $s): array
    {
        if ($s['mode'] !== 'tables') {
            return [null];
        }
        $out = [];
        foreach (preg_split('/[,;\n]+/', (string) $s['tables']) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $part, $m)) {
                [$a, $b] = [(int) $m[1], (int) $m[2]];
                if ($a > $b) {
                    [$a, $b] = [$b, $a];
                }
                for ($i = $a; $i <= $b && count($out) < 200; $i++) {
                    $out[] = (string) $i;
                }
            } else {
                $out[] = Str::limit($part, 20, '');
            }
            if (count($out) >= 200) {
                break;
            }
        }

        return $out ? array_values(array_unique($out)) : [null];
    }

    private function qrDark(string $color): string
    {
        // Keep strong contrast for scanning: deepen the brand colour, or use near-black for light colours.
        return ink_on($color) === '#FFFFFF' ? mix_hex($color, '#000000', 0.35) : '#151515';
    }

    private function card(Cafe $cafe, array $s, ?string $table): array
    {
        $url = $cafe->menuUrl($table);
        static $logoCache = [];
        if (! array_key_exists($cafe->id, $logoCache)) {
            $logo = null;
            $ratio = 1;
            if ($cafe->logo) {
                $disk = Storage::disk('public');
                foreach (["{$cafe->logo}-512.png", "{$cafe->logo}-256.webp"] as $p) {
                    if ($disk->exists($p)) {
                        $bytes = $disk->get($p);
                        $size = @getimagesizefromstring($bytes);
                        $ratio = $size && $size[1] ? $size[0] / $size[1] : 1;
                        $mime = str_ends_with($p, '.png') ? 'image/png' : 'image/webp';
                        $logo = "data:$mime;base64,".base64_encode($bytes);
                        break;
                    }
                }
            }
            $logoCache[$cafe->id] = [$logo, $ratio];
        }
        [$logo, $ratio] = $logoCache[$cafe->id];

        return [
            'name' => $cafe->name,
            'tagline' => tr($cafe->tagline, 'en'),
            'logo' => $logo,
            'logo_ratio' => $ratio,
            'initial' => mb_strtoupper(mb_substr($cafe->name, 0, 1)),
            'qr' => $this->qr->dataUri($url, $this->qrDark($s['color']), $s['qr_style'], 14),
            'url' => preg_replace('#^https?://#', '', $url),
            'table' => $table,
            'wifi_name' => $cafe->wifi_name,
            'wifi_password' => $cafe->wifi_password,
        ];
    }
}
