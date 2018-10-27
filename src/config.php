<?php
return [
    'app_id' => env('WFC_APP_ID', ''),
    'secret' => env('WFC_SECRET', ''),

    // 缓存前缀（前缀+openid）
    'cache_prefix' => env('WFC_CACHE_PREFIX', 'formid_'),

    // formId的过期时长
    'expire_second' => env('WFC_EXPIRE_SECOND', 7 * 24 * 3600),

    // 缓存驱动
    'cache_driver' => env('WFC_CACHE_DRIVER', 'file'),
];