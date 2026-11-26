<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MenuBuilderController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index()
    {
        $cafe = $this->cafe();
        $categories = $cafe->categories()->with('items')->get();

        return view('dashboard.menu.index', compact('cafe', 'categories'));
    }

    // ---------- Categories ----------
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required_without:name_am', 'nullable', 'string', 'max:60'],
            'name_am' => ['nullable', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'in:'.implode(',', array_keys(\App\Support\Icons::CATEGORY))],
        ]);
        $cafe = $this->cafe();
        $cafe->categories()->create([
            'name' => $this->pair($data, 'name'),
            'icon' => $data['icon'] ?? null,
            'sort_order' => (int) $cafe->categories()->max('sort_order') + 1,
        ]);

        return back()->with('ok', 'Category added.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $this->own($category);
        $data = $request->validate([
            'name_en' => ['required_without:name_am', 'nullable', 'string', 'max:60'],
            'name_am' => ['nullable', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'in:'.implode(',', array_keys(\App\Support\Icons::CATEGORY))],
            'visible' => ['nullable', 'boolean'],
        ]);
        $category->update([
            'name' => $this->pair($data, 'name'),
            'icon' => $data['icon'] ?? null,
            'visible' => $request->boolean('visible'),
        ]);

        return back()->with('ok', 'Category saved.');
    }

    public function destroyCategory(Category $category)
    {
        $this->own($category);
        foreach ($category->items as $item) {
            $this->images->delete($item->image);
        }
        $category->delete();

        return back()->with('ok', 'Category deleted.');
    }

    public function moveCategory(Request $request, Category $category)
    {
        $this->own($category);
        $this->move($this->cafe()->categories()->get(), $category, $request->input('dir'));

        return back();
    }

    // ---------- Items ----------
    public function createItem(Request $request)
    {
        $cafe = $this->cafe();
        $categories = $cafe->categories()->get();
        if ($categories->isEmpty()) {
            return redirect()->route('dashboard.menu')->with('error', 'Add a category first, like “Coffee” or “Breakfast”.');
        }
        $item = new Item(['category_id' => (int) $request->query('category', $categories->first()->id), 'visible' => true]);

        return view('dashboard.menu.item', compact('cafe', 'categories', 'item'));
    }

    public function editItem(Item $item)
    {
        $this->own($item);
        $cafe = $this->cafe();
        $categories = $cafe->categories()->get();

        return view('dashboard.menu.item', compact('cafe', 'categories', 'item'));
    }

    public function storeItem(Request $request)
    {
        $item = new Item;
        $item->cafe_id = $this->cafe()->id;

        return $this->saveItem($request, $item);
    }

    public function updateItem(Request $request, Item $item)
    {
        $this->own($item);

        return $this->saveItem($request, $item);
    }

    private function saveItem(Request $request, Item $item)
    {
        $cafe = $this->cafe();
        $data = $request->validate([
            'category_id' => ['required', 'integer'],
            'name_en' => ['required_without:name_am', 'nullable', 'string', 'max:100'],
            'name_am' => ['nullable', 'string', 'max:100'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_am' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'in:'.implode(',', array_keys(config('xmenu.tags')))],
            'variants' => ['nullable', 'array', 'max:12'],
            'addons' => ['nullable', 'array', 'max:20'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ], [
            'name_en.required_without' => 'Give the item a name in at least one language.',
            'photo.max' => 'Photos can be up to 5 MB.',
        ]);

        abort_unless($cafe->categories()->whereKey($data['category_id'])->exists(), 422);

        if ($request->hasFile('photo')) {
            try {
                $new = $this->images->store($request->file('photo'), "cafes/{$cafe->id}/items", ImageService::ITEM_WIDTHS, 4 / 3);
            } catch (RuntimeException $e) {
                return back()->withInput()->withErrors(['photo' => $e->getMessage()]);
            }
            $this->images->delete($item->image);
            $item->image = $new;
        } elseif ($request->boolean('remove_photo')) {
            $this->images->delete($item->image);
            $item->image = null;
        }

        $isNew = ! $item->exists;
        $item->fill([
            'category_id' => $data['category_id'],
            'name' => $this->pair($data, 'name'),
            'description' => $this->pair($data, 'description'),
            'price' => $data['price'],
            'tags' => array_values($data['tags'] ?? []),
            'variants' => $this->options($data['variants'] ?? []),
            'addons' => $this->options($data['addons'] ?? []),
            'visible' => $request->boolean('visible'),
            'sold_out' => $request->boolean('sold_out'),
        ]);
        if ($isNew) {
            $item->sort_order = (int) Item::where('category_id', $data['category_id'])->max('sort_order') + 1;
        }
        $item->save();

        if ($request->input('after') === 'another') {
            return redirect()->route('dashboard.items.create', ['category' => $item->category_id])->with('ok', 'Saved “'.tr($item->name).'”. Add the next one.');
        }

        return redirect()->route('dashboard.menu')->with('ok', 'Saved “'.tr($item->name).'”.')->withFragment('cat-'.$item->category_id);
    }

    /** Clean the repeater rows for sizes and add-ons. */
    private function options(array $rows): array
    {
        return collect($rows)
            ->map(fn ($r) => [
                'name' => $this->pair($r, 'name'),
                'price' => is_numeric($r['price'] ?? null) ? (float) $r['price'] : 0,
            ])
            ->filter(fn ($r) => $r['name']['en'] || $r['name']['am'])
            ->values()->all();
    }

    public function quickUpdate(Request $request, Item $item)
    {
        $this->own($item);
        $data = $request->validate([
            'price' => ['sometimes', 'numeric', 'min:0', 'max:9999999'],
            'sold_out' => ['sometimes', 'boolean'],
            'visible' => ['sometimes', 'boolean'],
        ]);
        $item->update($data);

        return response()->json(['ok' => true, 'price' => money($item->price)]);
    }

    public function duplicateItem(Item $item)
    {
        $this->own($item);
        $copy = $item->replicate();
        $name = $item->name;
        foreach ($name as $k => $v) {
            $name[$k] = $v ? $v.' (copy)' : $v;
        }
        $copy->name = $name;
        $copy->image = null; // Photos aren't shared; the owner can add one.
        $copy->sort_order = $item->sort_order + 1;
        $copy->save();

        return redirect()->route('dashboard.items.edit', $copy)->with('ok', 'Copied. Change what’s different and save.');
    }

    public function destroyItem(Item $item)
    {
        $this->own($item);
        $this->images->delete($item->image);
        $item->delete();

        return back()->with('ok', 'Item deleted.');
    }

    public function moveItem(Request $request, Item $item)
    {
        $this->own($item);
        $this->move(Item::where('category_id', $item->category_id)->orderBy('sort_order')->orderBy('id')->get(), $item, $request->input('dir'));

        return back()->withFragment('cat-'.$item->category_id);
    }

    private function move($list, $model, ?string $dir): void
    {
        $list = $list->values();
        $i = $list->search(fn ($m) => $m->id === $model->id);
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if ($i === false || $j < 0 || $j >= $list->count()) {
            return;
        }
        $ordered = $list->all();
        [$ordered[$i], $ordered[$j]] = [$ordered[$j], $ordered[$i]];
        DB::transaction(function () use ($ordered) {
            foreach ($ordered as $pos => $m) {
                if ($m->sort_order !== $pos) {
                    $m->sort_order = $pos;
                    $m->save();
                }
            }
        });
    }

    // ---------- CSV import ----------
    public function importForm()
    {
        return view('dashboard.menu.import', ['cafe' => $this->cafe()]);
    }

    public function importTemplate()
    {
        $rows = [
            ['category', 'name_en', 'name_am', 'description_en', 'price', 'tags', 'sizes'],
            ['Coffee', 'Macchiato', 'ማኪያቶ', 'Espresso with a dash of milk foam', '45', 'popular', ''],
            ['Coffee', 'Latte', 'ላቴ', '', '60', '', 'Small:60|Large:80'],
            ['Breakfast', 'Firfir', 'ፍርፍር', 'Injera with berbere sauce', '120', 'fasting|spicy', ''],
            ['Juice', 'Avocado juice', 'የአቮካዶ ጭማቂ', 'Fresh, layered', '90', 'fasting|vegetarian', ''],
        ];
        $csv = "\xEF\xBB\xBF".collect($rows)->map(fn ($r) => collect($r)->map(fn ($c) => '"'.str_replace('"', '""', $c).'"')->implode(','))->implode("\r\n");

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="xmenu-template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $cafe = $this->cafe();

        $fh = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($fh);
        if (! $header) {
            return back()->withErrors(['file' => 'The file is empty.']);
        }
        $header = array_map(fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h))), $header);
        foreach (['category', 'price'] as $required) {
            if (! in_array($required, $header, true)) {
                return back()->withErrors(['file' => "The file needs a “{$required}” column. Download the template to see the format."]);
            }
        }

        $validTags = array_keys(config('xmenu.tags'));
        $cats = $cafe->categories()->get()->keyBy(fn ($c) => mb_strtolower(tr($c->name, 'en') ?: tr($c->name)));
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($fh, $header, $cafe, &$cats, &$created, &$skipped, $validTags) {
            while (($row = fgetcsv($fh)) !== false) {
                if (count(array_filter($row, fn ($c) => trim((string) $c) !== '')) === 0) {
                    continue;
                }
                $r = array_combine($header, array_pad(array_slice($row, 0, count($header)), count($header), ''));
                $catName = trim($r['category'] ?? '');
                $nameEn = trim($r['name_en'] ?? $r['name'] ?? '');
                $nameAm = trim($r['name_am'] ?? '');
                if ($catName === '' || ($nameEn === '' && $nameAm === '')) {
                    $skipped++;

                    continue;
                }
                $key = mb_strtolower($catName);
                if (! isset($cats[$key])) {
                    $cats[$key] = $cafe->categories()->create([
                        'name' => ['en' => $catName, 'am' => null],
                        'icon' => \App\Support\Icons::guess($catName),
                        'sort_order' => (int) $cafe->categories()->max('sort_order') + 1,
                    ]);
                }
                $cat = $cats[$key];

                $variants = collect(explode('|', $r['sizes'] ?? ''))
                    ->map(fn ($v) => array_map('trim', explode(':', $v)))
                    ->filter(fn ($v) => count($v) === 2 && $v[0] !== '' && is_numeric($v[1]))
                    ->map(fn ($v) => ['name' => ['en' => $v[0], 'am' => null], 'price' => (float) $v[1]])
                    ->values()->all();

                $item = new Item([
                    'category_id' => $cat->id,
                    'name' => ['en' => $nameEn ?: null, 'am' => $nameAm ?: null],
                    'description' => ['en' => trim($r['description_en'] ?? $r['description'] ?? '') ?: null, 'am' => trim($r['description_am'] ?? '') ?: null],
                    'price' => is_numeric(trim($r['price'] ?? '')) ? (float) trim($r['price']) : 0,
                    'tags' => collect(explode('|', strtolower($r['tags'] ?? '')))->map('trim')->intersect($validTags)->values()->all(),
                    'variants' => $variants,
                    'addons' => [],
                    'visible' => true,
                    'sold_out' => false,
                    'sort_order' => (int) Item::where('category_id', $cat->id)->max('sort_order') + 1,
                ]);
                $item->cafe_id = $cafe->id;
                $item->save();
                $created++;
            }
        });
        fclose($fh);

        return redirect()->route('dashboard.menu')->with('ok', "Imported {$created} items".($skipped ? " ({$skipped} rows skipped: missing category or name)" : '').'.');
    }
}
