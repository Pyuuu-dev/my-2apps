<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stock Alert Thresholds
    |--------------------------------------------------------------------------
    | Item dengan stok di bawah threshold akan ditandai "perlu restock".
    | Threshold per kategori (fruit) dipisah berdasarkan rarity karena
    | item rare/mythical secara natural punya stok lebih sedikit.
    */
    'thresholds' => [
        'fruit' => [
            'Common' => 5,
            'Uncommon' => 3,
            'Rare' => 2,
            'Legendary' => 1,
            'Mythical' => 1,
        ],
        'skin' => 2,
        'gamepass' => 3,
        'permanent' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (seconds)
    |--------------------------------------------------------------------------
    | Stock alert di-cache untuk hindari query setiap render layout.
    */
    'cache_ttl' => 300, // 5 menit
];
