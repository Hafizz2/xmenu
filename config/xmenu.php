<?php

return [

    'name' => 'Xmenu',

    'trial_days' => 30,

    // Customer menus are English only for now. Add 'am' => 'አማርኛ' back to bring Amharic
    // (language switch, Amharic fields in the owner forms) back everywhere.
    'languages' => [
        'en' => 'English',
    ],

    // Dietary / highlight tags an owner can attach to items.
    'tags' => [
        'fasting'    => ['en' => 'Fasting',      'am' => 'የጾም',        'icon' => 'leaf'],
        'vegetarian' => ['en' => 'Vegetarian',   'am' => 'ቬጀቴሪያን',     'icon' => 'sprout'],
        'spicy'      => ['en' => 'Spicy',        'am' => 'ቅመም ያለው',    'icon' => 'pepper'],
        'new'        => ['en' => 'New',          'am' => 'አዲስ',        'icon' => 'sparkle'],
        'popular'    => ['en' => 'Popular',      'am' => 'ተወዳጅ',       'icon' => 'flame'],
        'chef'       => ['en' => "Chef's pick",  'am' => 'የሼፍ ምርጫ',    'icon' => 'chef'],
    ],

    'payment_types' => [
        'bank'     => 'Bank account',
        'telebirr' => 'Telebirr',
        'cbe_birr' => 'CBE Birr',
        'other'    => 'Other wallet',
    ],

    'banks' => [
        'Commercial Bank of Ethiopia', 'Awash Bank', 'Bank of Abyssinia', 'Dashen Bank',
        'Hibret Bank', 'Wegagen Bank', 'Nib International Bank', 'Cooperative Bank of Oromia',
        'Abay Bank', 'Bunna Bank', 'Berhan Bank', 'Zemen Bank', 'Oromia Bank', 'Enat Bank',
        'Lion International Bank', 'Global Bank Ethiopia', 'Amhara Bank', 'Siinqee Bank',
        'Ahadu Bank', 'Tsehay Bank', 'Gadaa Bank', 'Hijra Bank', 'ZamZam Bank',
    ],

    'socials' => [
        'telegram'  => 'Telegram',
        'instagram' => 'Instagram',
        'tiktok'    => 'TikTok',
        'facebook'  => 'Facebook',
        'whatsapp'  => 'WhatsApp',
        'website'   => 'Website',
    ],

    // ---------- Menu design system ----------
    'theme_defaults' => [
        'color'   => '#C2410C',
        'layout'  => 'list',      // list | grid | cards
        'header'  => 'cover',     // cover | solid | minimal
        'font'    => 'modern',    // modern | elegant | rounded | classic
        'radius'  => 'soft',      // sharp | soft | round
        'mode'    => 'light',     // light | auto | dark (light reads best in bright cafés)
        'surface' => 'warm',      // white | warm | cool
        // What customers see. Owners switch these on the Design page.
        'show_wifi'    => '1',
        'show_search'  => '1',
        'show_filters' => '1',
        'show_popular' => '1',
    ],

    'theme_switches' => [
        'show_wifi'    => ['Wi-Fi password', 'Shows your Wi-Fi name and password with a Copy button.'],
        'show_search'  => ['Search bar', 'Lets customers type to find a dish.'],
        'show_filters' => ['Label filters', 'Buttons like Fasting and Vegetarian that filter the menu.'],
        'show_popular' => ['Most loved row', 'Big photos of dishes you labelled Popular, at the top.'],
    ],

    'palette' => [
        '#C2410C' => 'Terracotta', '#B45309' => 'Coffee', '#15803D' => 'Garden',
        '#0F766E' => 'Teal', '#1D4ED8' => 'Royal', '#6D28D9' => 'Plum',
        '#BE123C' => 'Berry', '#111827' => 'Charcoal', '#854D0E' => 'Espresso',
        '#CA8A04' => 'Honey',
    ],

    'layouts' => [
        'list'  => ['label' => 'List',  'hint' => 'One dish per row with a small photo. Best for long menus.'],
        'grid'  => ['label' => 'Grid',  'hint' => 'Two big photos per row. Best when most dishes have photos.'],
        'cards' => ['label' => 'Cards', 'hint' => 'Big photo cards. Perfect for short, visual menus.'],
    ],

    'headers' => [
        'cover'   => ['label' => 'Cover photo', 'hint' => 'Your cover image behind the logo.'],
        'solid'   => ['label' => 'Brand colour', 'hint' => 'A clean band in your colour.'],
        'minimal' => ['label' => 'Minimal',     'hint' => 'Logo and name on a plain background.'],
    ],

    'fonts' => [
        'modern'  => ['label' => 'Modern',  'stack' => 'system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans Ethiopic","Helvetica Neue",Arial,sans-serif'],
        'elegant' => ['label' => 'Elegant', 'stack' => 'Georgia,"Times New Roman","Noto Serif Ethiopic","Noto Sans Ethiopic",serif'],
        'rounded' => ['label' => 'Rounded', 'stack' => 'ui-rounded,"SF Pro Rounded","Nunito","Varela Round",system-ui,"Noto Sans Ethiopic",sans-serif'],
        'classic' => ['label' => 'Classic', 'stack' => '"Palatino Linotype",Palatino,"Book Antiqua","Noto Serif Ethiopic",serif'],
    ],

    'radii' => [
        'sharp' => ['label' => 'Sharp', 'px' => 4],
        'soft'  => ['label' => 'Soft',  'px' => 12],
        'round' => ['label' => 'Round', 'px' => 20],
    ],

    'surfaces' => [
        'white' => ['label' => 'Clean white', 'bg' => '#FFFFFF', 'card' => '#FFFFFF', 'line' => '#ECECEC'],
        'warm'  => ['label' => 'Warm paper',  'bg' => '#FBF8F4', 'card' => '#FFFFFF', 'line' => '#EEE7DE'],
        'cool'  => ['label' => 'Cool grey',   'bg' => '#F5F7FA', 'card' => '#FFFFFF', 'line' => '#E5E9F0'],
    ],

    // ---------- QR print designs ----------
    'qr_designs' => [
        'classic' => ['label' => 'Classic', 'hint' => 'White card with a brand-colour frame.'],
        'bold'    => ['label' => 'Bold',    'hint' => 'Full brand-colour background.'],
        'split'   => ['label' => 'Split',   'hint' => 'Colour top, clean bottom.'],
        'minimal' => ['label' => 'Minimal', 'hint' => 'Lots of white space, thin lines.'],
        'wifi'    => ['label' => 'Menu + Wi-Fi', 'hint' => 'Adds your Wi-Fi name and password.'],
    ],

    // Paper sizes in millimetres (portrait).
    'paper' => [
        'A4' => ['w' => 210, 'h' => 297],
        'A5' => ['w' => 148, 'h' => 210],
        'A6' => ['w' => 105, 'h' => 148],
    ],

    // ---------- Customer UI strings ----------
    'ui' => [
        'en' => [
            'menu' => 'Menu', 'games' => 'Games', 'info' => 'Info', 'pay' => 'Pay',
            'search' => 'Search the menu', 'all' => 'All', 'sold_out' => 'Sold out',
            'open_now' => 'Open now', 'closed_now' => 'Closed now', 'no_results' => 'Nothing matches that.',
            'clear' => 'Clear', 'popular' => 'Most loved', 'free_wifi' => 'Free Wi-Fi', 'back' => 'Back to menu', 'tap_hint' => 'Tap any item to see more', 'copy' => 'Copy', 'copied' => 'Copied!', 'call' => 'Call',
            'directions' => 'Directions', 'wifi' => 'Wi-Fi', 'network' => 'Network', 'password' => 'Password',
            'hours' => 'Opening hours', 'contact' => 'Contact', 'follow' => 'Follow us', 'closed' => 'Closed',
            'pay_title' => 'How to pay', 'pay_note' => 'After paying, show your receipt to the cashier.',
            'account_name' => 'Account name', 'order_note' => 'Order with your waiter.',
            'vat_incl' => 'Prices include VAT.', 'vat_excl' => 'Prices exclude VAT.',
            'service' => ':pct% service charge applies.', 'sizes' => 'Sizes', 'addons' => 'Add-ons',
            'games_title' => 'Play while you wait', 'games_sub' => 'Grab a friend — most games are for two players on one phone.',
            'players_1' => '1 player', 'players_2' => '2 players', 'play' => 'Play', 'close' => 'Close',
            'table' => 'Table', 'loading' => 'Loading…', 'no_pay' => 'Ask your waiter how to pay.',
            'empty_menu' => 'The menu is being prepared. Check back soon!',
            'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        ],
        'am' => [
            'menu' => 'ምናሌ', 'games' => 'ጨዋታዎች', 'info' => 'መረጃ', 'pay' => 'ክፍያ',
            'search' => 'ምናሌውን ይፈልጉ', 'all' => 'ሁሉም', 'sold_out' => 'አልቋል',
            'open_now' => 'አሁን ክፍት ነው', 'closed_now' => 'አሁን ዝግ ነው', 'no_results' => 'ምንም አልተገኘም።',
            'clear' => 'አጽዳ', 'popular' => 'ተወዳጆች', 'free_wifi' => 'ነጻ ዋይፋይ', 'back' => 'ወደ ምናሌው ተመለስ', 'tap_hint' => 'ለዝርዝር ማንኛውንም ይንኩ', 'copy' => 'ቅዳ', 'copied' => 'ተቀድቷል!', 'call' => 'ደውል',
            'directions' => 'አቅጣጫ', 'wifi' => 'ዋይፋይ', 'network' => 'ኔትወርክ', 'password' => 'የይለፍ ቃል',
            'hours' => 'የስራ ሰዓት', 'contact' => 'አድራሻ', 'follow' => 'ይከተሉን', 'closed' => 'ዝግ',
            'pay_title' => 'እንዴት እንደሚከፍሉ', 'pay_note' => 'ከከፈሉ በኋላ ደረሰኙን ለገንዘብ ተቀባዩ ያሳዩ።',
            'account_name' => 'የሂሳብ ስም', 'order_note' => 'ትዕዛዝዎን ለአስተናጋጁ ይንገሩ።',
            'vat_incl' => 'ዋጋዎቹ ተ.እ.ታን ያካትታሉ።', 'vat_excl' => 'ዋጋዎቹ ተ.እ.ታን አያካትቱም።',
            'service' => ':pct% የአገልግሎት ክፍያ ይታከላል።', 'sizes' => 'መጠኖች', 'addons' => 'ተጨማሪዎች',
            'games_title' => 'እየጠበቁ ይጫወቱ', 'games_sub' => 'ጓደኛዎን ይጋብዙ — አብዛኞቹ ጨዋታዎች በአንድ ስልክ ለሁለት ሰው ናቸው።',
            'players_1' => '1 ተጫዋች', 'players_2' => '2 ተጫዋቾች', 'play' => 'ተጫወት', 'close' => 'ዝጋ',
            'table' => 'ጠረጴዛ', 'loading' => 'በመጫን ላይ…', 'no_pay' => 'እንዴት እንደሚከፍሉ አስተናጋጁን ይጠይቁ።',
            'empty_menu' => 'ምናሌው በመዘጋጀት ላይ ነው። በቅርቡ ይመለሱ!',
            'days' => ['እሁድ', 'ሰኞ', 'ማክሰኞ', 'ረቡዕ', 'ሐሙስ', 'ዓርብ', 'ቅዳሜ'],
        ],
    ],
];
