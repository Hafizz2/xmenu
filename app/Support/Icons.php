<?php

namespace App\Support;

/**
 * One stroke-icon library (24×24, stroke = currentColor) used everywhere: panel, admin,
 * landing page and the customer menu. No emoji anywhere in the product.
 */
class Icons
{
    /** Food & drink icons owners can pick for a category. key => label */
    public const CATEGORY = [
        'coffee' => 'Coffee', 'tea' => 'Tea', 'juice' => 'Juice', 'cocktail' => 'Cocktails', 'wine' => 'Wine',
        'beer' => 'Beer', 'bottle' => 'Soft drinks', 'breakfast' => 'Breakfast', 'bread' => 'Bakery', 'cake' => 'Cakes',
        'icecream' => 'Ice cream', 'burger' => 'Burgers', 'pizza' => 'Pizza', 'sandwich' => 'Sandwiches', 'fries' => 'Snacks',
        'salad' => 'Salads', 'soup' => 'Soups', 'pasta' => 'Pasta', 'chicken' => 'Chicken', 'meat' => 'Meat', 'fish' => 'Fish',
        'traditional' => 'Traditional', 'utensils' => 'Mains', 'star' => 'Specials',
    ];

    public const PATHS = [
        // ---------- UI ----------
        'home' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h10"/>',
        'book' => '<path d="M4 4.5A1.5 1.5 0 0 1 5.5 3H20v16H5.5A1.5 1.5 0 0 0 4 20.5z"/><path d="M4 20.5A1.5 1.5 0 0 0 5.5 22H20"/><path d="M9 8h7M9 12h5"/>',
        'palette' => '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.8-.8 1.8-1.8 0-.5-.2-.9-.5-1.2-.3-.3-.5-.7-.5-1.2 0-1 .8-1.8 1.8-1.8H17a4 4 0 0 0 4-4C21 6.6 17 3 12 3z"/><circle cx="7.5" cy="11" r="1.2"/><circle cx="10" cy="7" r="1.2"/><circle cx="15" cy="7" r="1.2"/>',
        'qr' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><path d="M14 14h3v3h-3zM20 14v.01M14 20h.01M17 17h3v3h-3"/>',
        'store' => '<path d="M4 9.5 5.5 4h13L20 9.5"/><path d="M4 9.5a2.7 2.7 0 0 0 5.3 0 2.7 2.7 0 0 0 5.4 0 2.7 2.7 0 0 0 5.3 0"/><path d="M5 11.5V20h14v-8.5"/><path d="M10 20v-5h4v5"/>',
        'wallet' => '<path d="M19 7V5a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-4a2 2 0 0 0 0 4h4v2a1 1 0 0 1-1 1H5a2 2 0 0 1-2-2V6"/>',
        'card' => '<rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20M6 15h4"/>',
        'game' => '<rect x="2" y="7" width="20" height="11" rx="5"/><path d="M7 11v3M5.5 12.5h3M15 12h.01M18 13.5h.01"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
        'more' => '<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>',
        'dots' => '<circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'minus' => '<path d="M5 12h14"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash' => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>',
        'copy' => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/>',
        'up' => '<path d="m18 15-6-6-6 6"/>',
        'down' => '<path d="m6 9 6 6 6-6"/>',
        'right' => '<path d="m9 18 6-6-6-6"/>',
        'left' => '<path d="m15 18-6-6 6-6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 5-5.5"/>',
        'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off' => '<path d="M17.9 17.9A10 10 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 4.1-5M9.9 5.2A9 9 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M3 3l18 18M14.1 14.1a3 3 0 0 1-4.2-4.2"/>',
        'external' => '<path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        'download' => '<path d="M12 3v12M7 10l5 5 5-5M5 21h14"/>',
        'upload' => '<path d="M12 21V9M7 14l5-5 5 5M5 3h14"/>',
        'image' => '<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="M8 17v-5M13 17V8M18 17v-9"/>',
        'trend' => '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'pin' => '<path d="M12 22s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
        'wifi' => '<path d="M5 12.5a10 10 0 0 1 14 0M8.5 16a5 5 0 0 1 7 0M2 9a15 15 0 0 1 20 0M12 19.5h.01"/>',
        'lock' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'sparkle' => '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path d="M19 17l.8 2.2L22 20l-2.2.8L19 23l-.8-2.2L16 20l2.2-.8z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'file' => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M9 14h6M9 18h4"/>',
        'receipt' => '<path d="M5 2v20l2.5-1.5L10 22l2-1.5 2 1.5 2.5-1.5L19 22V2l-2.5 1.5L14 2l-2 1.5L10 2 7.5 3.5z"/><path d="M8.5 8h7M8.5 12h7M8.5 16h4"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        'bolt' => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
        'printer' => '<path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="7" rx="1"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'filter' => '<path d="M3 5h18l-7 8v6l-4 2v-8z"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/>',
        'help' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14M12 17.5h.01"/>',
        'refresh' => '<path d="M21 12a9 9 0 1 1-2.6-6.4L21 8"/><path d="M21 3v5h-5"/>',
        'undo' => '<path d="M9 14 4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-3"/>',
        'pause' => '<rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/>',
        'play' => '<path d="M7 4.5v15a1 1 0 0 0 1.5.9l12-7.5a1 1 0 0 0 0-1.8l-12-7.5A1 1 0 0 0 7 4.5z"/>',
        'volume' => '<path d="M11 5 6 9H2v6h4l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/>',
        'mute' => '<path d="M11 5 6 9H2v6h4l5 4z"/><path d="m22 9-6 6M16 9l6 6"/>',
        'maximize' => '<path d="M8 3H5a2 2 0 0 0-2 2v3M21 8V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3M16 21h3a2 2 0 0 0 2-2v-3"/>',
        'trophy' => '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M17 5h3v2a4 4 0 0 1-4 4M7 5H4v2a4 4 0 0 0 4 4"/>',
        'cpu' => '<rect x="5" y="5" width="14" height="14" rx="2"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        'layers' => '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
        'moon' => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'type' => '<path d="M4 7V5h16v2M9 19h6M12 5v14"/>',
        'scissors' => '<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.1 15.9M14.5 14.5 20 20M8.1 8.1 12 12"/>',
        'send' => '<path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'link' => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
        'table' => '<path d="M3 9h18M5 9v11M19 9v11M8 9V5h8v4"/>',
        'zap' => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
        'smartphone' => '<rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/>',
        'monitor' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'social' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
        'inbox' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.8 4H7.2a2 2 0 0 0-1.7 1.1z"/>',

        // ---------- Dietary tags ----------
        'leaf' => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.4 5.2-6.1C9.5 14.4 12 13 13 12"/>',
        'sprout' => '<path d="M7 20h10M12 20v-8"/><path d="M12 12C12 8 9 6 5 6c0 4 3 6 7 6z"/><path d="M12 10c0-3 2.5-5 6.5-5 0 3.5-2.5 5-6.5 5z"/>',
        'pepper' => '<path d="M13 6c4 0 6 3 6 6 0 5-6 9-12 9 0 0 0-1 1-2 3-3 1-6 2-10 .6-2 1.5-3 3-3z"/><path d="M13 6c0-2 1-3 3-4"/>',
        'flame' => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.4-.5-2-1-3-1.1-2.1-.2-4 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.2.4-2.3 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>',
        'chef' => '<path d="M6 13.9A4 4 0 0 1 7 6a5 5 0 0 1 10 0 4 4 0 0 1 1 7.9V20H6z"/><path d="M6 17h12"/>',
        'heart' => '<path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7z"/>',

        // ---------- Food & drink ----------
        'coffee' => '<path d="M4 8h12v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M16 9h1.5a2.5 2.5 0 0 1 0 5H16"/><path d="M8 2.5c-.6.8-.6 1.7 0 2.5M12 2.5c-.6.8-.6 1.7 0 2.5"/><path d="M3 21h15"/>',
        'tea' => '<path d="M4 10h13v3a6 6 0 0 1-6 6h-1a6 6 0 0 1-6-6z"/><path d="M17 11h1.5a2.5 2.5 0 0 1 0 5H16"/><path d="M10.5 10V4.5"/><rect x="8.5" y="2" width="4" height="3.5" rx=".8"/><path d="M3 22h15"/>',
        'juice' => '<path d="M6 8h12l-1.4 12.2a1.5 1.5 0 0 1-1.5 1.3H8.9a1.5 1.5 0 0 1-1.5-1.3z"/><path d="M13 8l2-5.5h3"/><path d="M6.6 13h10.8"/>',
        'cocktail' => '<path d="M4 4h16l-8 9z"/><path d="M12 13v8M8 21h8M7 7.5h10"/>',
        'wine' => '<path d="M8 2h8l-.5 6.5a3.5 3.5 0 0 1-7 0z"/><path d="M12 12v9M8 21h8M8.3 6h7.4"/>',
        'beer' => '<path d="M6 8h10v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2z"/><path d="M16 10.5h2a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2"/><path d="M6 8a2.5 2.5 0 0 1 2.5-3.5 3 3 0 0 1 5.5 0A2.5 2.5 0 0 1 16 8"/><path d="M9.5 12v5M12.5 12v5"/>',
        'bottle' => '<path d="M10 2h4v3l1.5 2.5V20a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2V7.5L10 5z"/><path d="M8.5 12h7M8.5 16h7"/>',
        'breakfast' => '<path d="M17.5 7.5C19.5 9 21 11 20 14s-3.5 3.5-5 5-4 2.5-7 1.5S3 17 3.5 13.5 5 9 6 7s3-4 6-4 3.5 3 5.5 4.5z"/><circle cx="11.5" cy="12" r="3"/>',
        'bread' => '<path d="M5 10.5A3.8 3.8 0 0 1 6.5 3h11A3.8 3.8 0 0 1 19 10.5V19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/><path d="M9.5 8.5l1.5 1.5M13 8.5l1.5 1.5"/>',
        'cake' => '<path d="M4 21v-8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8"/><path d="M4 15.5c1.5 1 3 1 4 0s3-1 4 0 3 1 4 0 3-1 4 0"/><path d="M2 21h20M12 11V7.5"/><path d="M12 7c-1-1-1-2.6 0-4.5 1 1.9 1 3.5 0 4.5z"/>',
        'icecream' => '<path d="M7.5 11h9L12 22z"/><path d="M7 11a5 5 0 0 1 10 0"/><path d="M9.3 15h5.4"/>',
        'burger' => '<path d="M4 11a8 6 0 0 1 16 0z"/><path d="M3 14.5h18"/><path d="M4 17.5h16v.5a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18z"/><path d="M9 7.5h.01M12 6.5h.01M15 7.5h.01"/>',
        'pizza' => '<path d="M12 21.5 3.5 6.5a14.5 14.5 0 0 1 17 0z"/><path d="M5.3 9.6a11.5 11.5 0 0 1 13.4 0"/><circle cx="12" cy="12" r="1.2"/><circle cx="10" cy="15.6" r="1"/><circle cx="14.7" cy="10" r="1"/>',
        'sandwich' => '<path d="M3 11 12 5l9 6z"/><path d="M3 11h18v2.5H3z"/><path d="M4 16.5h16V18a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18z"/><path d="M3 13.5 5 16.5M21 13.5l-2 3"/>',
        'fries' => '<path d="M5.5 10h13l-1.6 11h-9.8z"/><path d="M8 10 7 3M11 10V2M14 10l.5-7M16.5 10 18 4"/>',
        'salad' => '<path d="M3 12h18a9 9 0 0 1-18 0z"/><path d="M8 12c0-3 1.5-5 4-6M12.5 12c0-2.5 1.5-4.5 4.5-5.5M7 9.5C5.5 9 4.5 8 4 6.5"/>',
        'soup' => '<path d="M3 11h18a9 7.5 0 0 1-18 0z"/><path d="M8 3c-.7 1-.7 2 0 3M12 3c-.7 1-.7 2 0 3M16 3c-.7 1-.7 2 0 3"/><path d="M7 21.5h10"/>',
        'pasta' => '<path d="M3 13h18a9 7.5 0 0 1-18 0z"/><path d="M8 13c0-3 2-6 2-9.5M12 13c0-3 1-6 3-9.5M16 13c0-2-1-4 0-6"/>',
        'chicken' => '<path d="M15.4 3.6a5 5 0 0 1 2.2 8.3c-1.7 1.7-4.2 2-6.1 1l-2.7 2.7"/><path d="M15.4 3.6a5 5 0 0 0-5.3 1.2c-1.7 1.7-2 4.2-1 6.1"/><path d="m8.8 15.6-2.3 2.3a1.6 1.6 0 1 1-2.2 2.2 1.6 1.6 0 1 1-2.2-2.2 1.6 1.6 0 0 1 2.2 0l2.3-2.3"/>',
        'meat' => '<path d="M15 3.5c3 1 5.5 4 5.5 7.5 0 5-4 9.5-9 9.5-4 0-7.5-2.5-7.5-6 0-2.5 2-3.5 3.5-5.5S9 4 11 3.5s2.5-.5 4 0z"/><circle cx="14.5" cy="10" r="2.2"/>',
        'fish' => '<path d="M5 12c2.5-3.5 6-5.5 9.5-5.5S20 9 22 12c-2 3-4.5 5.5-7.5 5.5S7.5 15.5 5 12z"/><path d="M5 12 2 8.5v7z"/><path d="M17 11h.01"/>',
        'traditional' => '<circle cx="12" cy="12" r="9.5"/><circle cx="12" cy="12" r="5.5"/><path d="M12 6.5v.01M15.5 8.5h.01M17 12h.01M8.5 8.5h.01M7 12h.01M10 15.5h.01M14 15.5h.01"/>',
        'utensils' => '<path d="M3 2v7a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V2M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6a2 2 0 0 0 2 2h3zm0 0v7"/>',
        'star' => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3l-5.5 2.9 1-6.2L3 9.6l6.2-.9z"/>',
    ];

    /** Best-guess category icon from a name like "Hot drinks" or "ቡና". */
    public static function guess(string $name): string
    {
        $n = mb_strtolower($name);
        $map = [
            'coffee' => ['coffee', 'espresso', 'buna', 'ቡና', 'macchiato'], 'tea' => ['tea', 'ሻይ', 'hot drink'],
            'juice' => ['juice', 'ጭማቂ', 'smoothie', 'shake'], 'cocktail' => ['cocktail', 'mocktail'], 'wine' => ['wine', 'ወይን'],
            'beer' => ['beer', 'ቢራ'], 'bottle' => ['soft', 'soda', 'water', 'drink', 'beverage'],
            'breakfast' => ['breakfast', 'ቁርስ', 'egg', 'brunch'], 'bread' => ['bakery', 'bread', 'pastr', 'ዳቦ'],
            'cake' => ['cake', 'ኬክ', 'dessert', 'sweet'], 'icecream' => ['ice cream', 'gelato'], 'burger' => ['burger', 'በርገር'],
            'pizza' => ['pizza', 'ፒዛ'], 'sandwich' => ['sandwich', 'ሳንድዊች', 'wrap', 'club'], 'fries' => ['snack', 'fries', 'side'],
            'salad' => ['salad', 'ሰላጣ', 'vegan', 'vegetable'], 'soup' => ['soup', 'ሾርባ'], 'pasta' => ['pasta', 'ፓስታ', 'spaghetti', 'noodle'],
            'chicken' => ['chicken', 'ዶሮ'], 'meat' => ['meat', 'ስጋ', 'tibs', 'ጥብስ', 'steak', 'grill'], 'fish' => ['fish', 'ዓሳ', 'sea'],
            'traditional' => ['traditional', 'ባህላዊ', 'injera', 'እንጀራ', 'habesha', 'fasting', 'ጾም'],
        ];
        foreach ($map as $icon => $words) {
            foreach ($words as $w) {
                if (str_contains($n, $w)) {
                    return $icon;
                }
            }
        }

        return 'utensils';
    }

    public static function path(?string $name): string
    {
        return self::PATHS[$name] ?? self::PATHS['utensils'];
    }

    public static function svg(?string $name, string $class = 'i', array $attrs = []): string
    {
        $extra = '';
        foreach ($attrs as $k => $v) {
            $extra .= ' '.$k.'="'.e($v).'"';
        }

        return '<svg class="'.e($class).'" viewBox="0 0 24 24" aria-hidden="true"'.$extra.'>'.self::path($name).'</svg>';
    }

    /** <symbol> sprite for the given names — the customer menu inlines it once and uses <use>. */
    public static function sprite(array $names): string
    {
        $out = '<svg width="0" height="0" style="position:absolute" aria-hidden="true">';
        foreach (array_unique($names) as $n) {
            $out .= '<symbol id="i-'.e($n).'" viewBox="0 0 24 24">'.self::path($n).'</symbol>';
        }

        return $out.'</svg>';
    }
}
