<?php

namespace Database\Seeders;

use App\Http\Controllers\AuthController;
use App\Models\Cafe;
use App\Models\Event;
use App\Models\Game;
use App\Models\Item;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $prod = app()->isProduction();

        // ---------- Super admin (local only; on a live server use: php artisan xmenu:admin you@example.com) ----------
        if (! $prod) {
            $admin = User::firstOrCreate(['email' => 'admin@xmenu.test'], ['name' => 'Xmenu Admin', 'password' => 'password']);
            $admin->is_admin = true;
            $admin->save();
        }

        // ---------- Plans (prices are placeholders: edit them in Admin → Plans) ----------
        $plans = [
            ['trial', 'Trial', 'Everything, free for 30 days', 0, null, ['All features unlocked']],
            ['basic', 'Basic', 'For small cafés', 500, 10, ['Menu with photos', 'Fasting & dietary filters', 'Info & payment tab', 'Games for customers', 'Up to 10 table QR codes', 'Basic stats']],
            ['pro', 'Pro', 'For busy cafés and restaurants', 1200, null, ['Everything in Basic', 'Unlimited table QR codes', 'All QR designs & sizes', 'Full stats', 'Priority support']],
            ['chain', 'Chain', 'For 2+ branches, per branch', 1000, null, ['Everything in Pro', 'Discount per branch', 'Help moving your menus']],
        ];
        foreach ($plans as $i => [$key, $name, $tagline, $price, $tables, $features]) {
            Plan::updateOrCreate(['key' => $key], compact('name', 'tagline', 'price', 'features') + ['max_tables' => $tables, 'currency' => 'ETB', 'sort_order' => $i, 'active' => true]);
        }

        Setting::put('trial_days', 30);
        if (! Setting::get('billing_accounts')) {
            Setting::put('billing_accounts', [['label' => 'Commercial Bank of Ethiopia', 'name' => 'Xmenu', 'number' => '1000000000000'], ['label' => 'Telebirr', 'name' => 'Xmenu', 'number' => '0900000000']]);
        }

        // ---------- Games (self-hosted, MIT, written for Xmenu) ----------
        $games = [
            ['tictactoe', 'Tic-Tac-Toe', 'ኤክስ ኦ', 'Three in a row wins. Best-of series, or take on an unbeatable computer.', 'ሶስት በመስመር ያሸንፋል። ከጓደኛ ወይም ከኮምፒውተር ጋር።', 2, 'pass_and_play', true, 'tictactoe', '#2350B8'],
            ['connect4', 'Connect Four', 'አራት አገናኝ', 'Drop discs and connect four before your opponent. Plays a smart computer too.', 'ዲስኮችን ጥለው አራት ያገናኙ።', 2, 'pass_and_play', true, 'connect4', '#1F56D6'],
            ['memory', 'Memory Match', 'የማስታወስ ጨዋታ', 'Find every pair. Play solo against the clock, with a friend or the computer.', 'ጥንዶቹን ሁሉ ያግኙ።', 2, 'pass_and_play', true, 'memory', '#0F6B4F'],
            ['tapduel', 'Reaction Duel', 'የፍጥነት ውድድር', 'Split-screen reflex battle. Wait for green, then tap faster than your friend.', 'አረንጓዴ ሲሆን ከጓደኛዎ በፊት ይንኩ።', 2, 'split_screen', false, 'reaction', '#E2621B'],
        ];
        foreach ($games as $i => [$slug, $en, $am, $dEn, $dAm, $players, $mode, $ai, $art, $color]) {
            Game::updateOrCreate(['slug' => $slug], [
                'name' => ['en' => $en, 'am' => $am], 'description' => ['en' => $dEn, 'am' => $dAm],
                'source_url' => "games/$slug/index.html", 'hosted' => true, 'license' => 'MIT', 'players' => $players,
                'mode' => $mode, 'has_ai' => $ai, 'size_kb' => 12, 'art' => $art, 'color' => $color, 'sort_order' => $i, 'active' => true,
            ]);
        }

        // ---------- Demo café ----------
        // Rebuilt whenever it is missing or still lacks photos (older seeds had none).
        $existing = Cafe::where('slug', 'demo-cafe')->first();
        if ($existing && $existing->cover && $existing->items()->whereNotNull('image')->exists()) {
            return;
        }
        if ($existing) {
            $images = app(ImageService::class);
            foreach ($existing->items as $old) {
                $images->delete($old->image);
            }
            $images->delete($existing->cover);
            $images->delete($existing->logo);
            $existing->delete();
        }
        // On a live server nobody can sign in as the demo owner: its password is random and never shown.
        $owner = User::firstOrCreate(['email' => 'owner@xmenu.test'], ['name' => 'Dawit Bekele', 'phone' => '+251911000000', 'password' => $prod ? IlluminateSupportStr::password(40) : 'password']);
        $cafe = AuthController::createCafe($owner, 'Buna House');
        $cafe->slug = 'demo-cafe';
        $cafe->fill([
            'tagline' => ['en' => 'Fresh Ethiopian coffee & breakfast since 2015', 'am' => 'ከ2007 ጀምሮ ትኩስ የኢትዮጵያ ቡና እና ቁርስ'],
            'phone' => '+251 911 000 000',
            'address' => 'Bole Road, near Edna Mall, Addis Ababa',
            'wifi_name' => 'BunaHouse_Guest',
            'wifi_password' => 'coffee2026',
            'socials' => ['telegram' => 'https://t.me/bunahouse', 'instagram' => 'https://instagram.com/bunahouse', 'tiktok' => 'https://www.tiktok.com/@bunahouse'],
            'service_charge_pct' => 5,
            'theme' => ['color' => '#C2410C', 'layout' => 'grid', 'header' => 'cover', 'font' => 'modern', 'radius' => 'round', 'mode' => 'light', 'surface' => 'warm', '_saved' => true],
        ]);
        $cafe->cover = $this->demoPhoto('cover', "cafes/{$cafe->id}/brand", ImageService::COVER_WIDTHS, 16 / 9);
        $cafe->save();
        // Late-night café: 06:30 until 02:00 the next morning.
        $cafe->openingHours()->update(['opens_at' => '06:30', 'closes_at' => '02:00', 'closed' => false]);

        // [photo in seeders/demo, name en, name am, description, price, tags, variants]
        $menu = [
            ['coffee', 'Coffee & Tea', 'ቡና እና ሻይ', [
                ['macchiato', 'Macchiato', 'ማኪያቶ', 'Espresso with a cloud of milk foam', 45, ['popular'], []],
                ['buna', 'Traditional Buna', 'ባህላዊ ቡና', 'Jebena coffee with popcorn and incense', 60, ['popular', 'fasting'], []],
                ['latte', 'Latte', 'ላቴ', 'Smooth espresso and steamed milk', 70, [], [['Small', 'ትንሽ', 60], ['Large', 'ትልቅ', 85]]],
                ['spris', 'Spris', 'ስፕሪስ', 'Half tea, half coffee. An Addis classic.', 40, ['fasting'], []],
                ['tea', 'Shai (Tea)', 'ሻይ', 'Black tea with cinnamon and cloves', 25, ['fasting'], []],
            ]],
            ['breakfast', 'Breakfast', 'ቁርስ', [
                ['ful', 'Ful', 'ፉል', 'Mashed fava beans with onion, tomato, chili and bread', 120, ['fasting', 'vegetarian', 'popular'], []],
                ['eggs', 'Egg Firfir', 'እንቁላል ፍርፍር', 'Scrambled eggs with tomato, onion and green pepper', 110, ['vegetarian'], []],
                ['genfo', 'Genfo', 'ገንፎ', 'Warm barley porridge with spiced butter and berbere', 140, ['chef'], []],
            ]],
            ['utensils', 'Lunch', 'ምሳ', [
                ['beyaynetu', 'Beyaynetu', 'በያይነቱ', 'A colourful fasting platter of lentils, greens and vegetables on injera', 180, ['fasting', 'vegetarian', 'popular'], []],
                ['tibs', 'Special Tibs', 'ልዩ ጥብስ', 'Sautéed beef with onion, rosemary and green pepper, served on injera', 320, ['spicy', 'chef'], []],
            ]],
            ['juice', 'Fresh Juice', 'ጭማቂ', [
                ['avocado', 'Avocado Juice', 'የአቮካዶ ጭማቂ', 'Thick, fresh, with a lime squeeze', 90, ['fasting', 'vegetarian', 'popular'], []],
                ['mango', 'Mango Juice', 'የማንጎ ጭማቂ', 'Ripe mango, blended to order', 85, ['fasting', 'vegetarian'], []],
                ['orange', 'Orange Juice', 'የብርቱካን ጭማቂ', 'Freshly squeezed', 80, ['fasting', 'vegetarian', 'new'], []],
            ]],
            ['burger', 'Burgers', 'በርገር', [
                ['burger', 'Classic Burger', 'ክላሲክ በርገር', 'Beef patty, cheese, lettuce, tomato, fries', 280, [], [['Single', 'ነጠላ', 280], ['Double', 'ድርብ', 380]]],
                ['veggie', 'Veggie Burger', 'የአትክልት በርገር', 'Lentil patty, fresh vegetables, fries', 240, ['vegetarian', 'fasting', 'new'], []],
            ]],
            ['cake', 'Cakes', 'ኬኮች', [
                ['chocolate', 'Chocolate Cake', 'የቸኮሌት ኬክ', 'Baked fresh every morning', 120, [], []],
                ['tiramisu', 'Tiramisu', 'ቲራሚሱ', 'Coffee-soaked layers with mascarpone and cocoa', 150, ['new'], []],
                ['forest', 'Black Forest', 'ብላክ ፎረስት', 'Chocolate sponge, cherries and cream', 130, [], []],
            ]],
        ];
        foreach ($menu as $ci => [$icon, $en, $am, $items]) {
            $cat = $cafe->categories()->create(['name' => ['en' => $en, 'am' => $am], 'icon' => $icon, 'sort_order' => $ci]);
            foreach ($items as $ii => [$photo, $nEn, $nAm, $desc, $price, $tags, $variants]) {
                $item = new Item([
                    'category_id' => $cat->id, 'name' => ['en' => $nEn, 'am' => $nAm], 'description' => ['en' => $desc ?: null, 'am' => null],
                    'price' => $price, 'tags' => $tags,
                    'variants' => array_map(fn ($v) => ['name' => ['en' => $v[0], 'am' => $v[1]], 'price' => $v[2]], $variants),
                    'addons' => $nEn === 'Classic Burger' ? [['name' => ['en' => 'Extra cheese', 'am' => 'ተጨማሪ አይብ'], 'price' => 30], ['name' => ['en' => 'Fried egg', 'am' => 'የተጠበሰ እንቁላል'], 'price' => 25]] : [],
                    'sort_order' => $ii, 'visible' => true, 'sold_out' => $nEn === 'Black Forest',
                ]);
                $item->cafe_id = $cafe->id;
                $item->image = $this->demoPhoto($photo, "cafes/{$cafe->id}/items", ImageService::ITEM_WIDTHS, 4 / 3);
                $item->save();
            }
        }

        foreach ([['bank', 'Commercial Bank of Ethiopia', '1000123456789'], ['bank', 'Awash Bank', '0132045678900'], ['telebirr', null, '0911000000'], ['cbe_birr', null, '0911000000']] as $i => [$type, $provider, $number]) {
            $cafe->paymentAccounts()->create(['type' => $type, 'provider' => $provider, 'account_name' => 'Buna House PLC', 'account_number' => $number, 'sort_order' => $i, 'visible' => true]);
        }

        // A few weeks of sample analytics so dashboards aren't empty.
        $itemIds = $cafe->items()->pluck('id')->all();
        $gameIds = Game::pluck('id')->all();
        $rows = [];
        for ($d = 20; $d >= 0; $d--) {
            $n = random_int(15, 60);
            for ($k = 0; $k < $n; $k++) {
                $at = now()->subDays($d)->setTime(random_int(7, 21), random_int(0, 59));
                if ($at->isFuture()) {
                    $at = now()->subMinutes(random_int(5, 600)); // today: only times that already happened
                }
                $t = (string) random_int(1, 12);
                $rows[] = ['cafe_id' => $cafe->id, 'table_no' => $t, 'type' => 'menu_view', 'item_id' => null, 'game_id' => null, 'value' => null, 'created_at' => $at];
                if (random_int(0, 2)) {
                    $rows[] = ['cafe_id' => $cafe->id, 'table_no' => $t, 'type' => 'item_open', 'item_id' => $itemIds[array_rand($itemIds)], 'game_id' => null, 'value' => null, 'created_at' => $at];
                }
                if (random_int(0, 4) === 0) {
                    $rows[] = ['cafe_id' => $cafe->id, 'table_no' => $t, 'type' => 'game_open', 'item_id' => null, 'game_id' => $gameIds[array_rand($gameIds)], 'value' => null, 'created_at' => $at];
                }
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            Event::insert($chunk);
        }
    }

    /** Store a bundled demo photo (database/seeders/demo, credits in CREDITS.md) the same way an owner upload is stored. */
    private function demoPhoto(string $name, string $dir, array $widths, float $ratio): ?string
    {
        $path = database_path("seeders/demo/$name.jpg");
        if (! is_file($path)) {
            return null;
        }

        return app(ImageService::class)->store(new UploadedFile($path, "$name.jpg", 'image/jpeg', null, true), $dir, $widths, $ratio);
    }
}
