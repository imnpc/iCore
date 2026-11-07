<?php

declare(strict_types=1);
use InteractionDesignFoundation\GeoIP\Services\IP2Location;
use InteractionDesignFoundation\GeoIP\Services\IPApi;
use InteractionDesignFoundation\GeoIP\Services\IPData;
use InteractionDesignFoundation\GeoIP\Services\IPFinder;
use InteractionDesignFoundation\GeoIP\Services\IPGeoLocation;
use InteractionDesignFoundation\GeoIP\Services\MaxMindDatabase;
use InteractionDesignFoundation\GeoIP\Services\MaxMindWebService;

return [

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for when a location is not found
    | for the IP provided.
    |
    | Requires Monolog to be installed: `composer install monolog/monolog`
    |
    */

    'log_failures' => true,

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeouts
    |--------------------------------------------------------------------------
    |
    | Seconds (floats allowed) that HTTP-based services wait for a connection
    | and for the whole response. Keep them short: a lookup runs inside the
    | web request. Any service below may override either key.
    |
    */

    'connect_timeout' => 2,

    'timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | Include Currency in Results
    |--------------------------------------------------------------------------
    |
    | When enabled the system will do it's best in deciding the user's currency
    | by matching their ISO code to a preset list of currencies.
    |
    */

    'include_currency' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Service
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default storage driver that should be used
    | by the framework using the services listed below.
    |
    | Supported: "maxmind_database", "maxmind_api", "ipapi", "ipgeolocation", "ipdata", "ipfinder", "ip2location"
    |
    */

    'service' => env('GEOIP_SERVICE', 'maxmind_database'),

    /*
    |--------------------------------------------------------------------------
    | Storage Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many storage drivers as you wish.
    |
    */

    'services' => [

        'maxmind_database' => [
            'class' => MaxMindDatabase::class,
            'database_path' => storage_path('app/geoip.mmdb'),
            // 凭据通过 HTTP Basic auth 发送到 MaxMind 永久链接端点（不再出现在 URL 中）。
            // 未配置 account_id 时将回退到 license_key 在查询字符串中的旧端点。
            'account_id' => env('MAXMIND_ACCOUNT_ID'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
            'edition_id' => 'GeoLite2-City',
            'locales' => ['en'],
        ],

        'maxmind_api' => [
            'class' => MaxMindWebService::class,
            'user_id' => env('MAXMIND_USER_ID'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
            'locales' => ['en'],
        ],

        'ipapi' => [
            'class' => IPApi::class,
            'secure' => true,
            'key' => env('IPAPI_KEY'),
            'continent_path' => storage_path('app/continents.json'),
            'lang' => 'en',
        ],

        'ipgeolocation' => [
            'class' => IPGeoLocation::class,
            'secure' => true,
            'key' => env('IPGEOLOCATION_KEY'),
            'continent_path' => storage_path('app/continents.json'),
            'lang' => 'en',
        ],

        'ipdata' => [
            'class' => IPData::class,
            'key' => env('IPDATA_API_KEY'),
            'secure' => true,
        ],

        'ipfinder' => [
            'class' => IPFinder::class,
            'key' => env('IPFINDER_API_KEY'),
            'secure' => true,
            'locales' => ['en'],
        ],

        'ip2location' => [
            'class' => IP2Location::class,
            'key' => env('IP2LOCATION_API_KEY'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Cache Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify the type of caching that should be used
    | by the package.
    |
    | Options:
    |
    |  all  - All location are cached
    |  some - Cache only the requesting user
    |  none - Disable cached
    |
    */

    'cache' => 'all',

    /*
    |--------------------------------------------------------------------------
    | Cache Tags
    |--------------------------------------------------------------------------
    |
    | Cache tags are not supported when using the file or database cache
    | drivers in Laravel. This is done so that only locations can be cleared.
    |
    */

    'cache_tags' => ['laravel-geoip-location'],

    /*
    |--------------------------------------------------------------------------
    | Cache Expiration
    |--------------------------------------------------------------------------
    |
    | Cache's time to live in seconds.
    |
    */

    'cache_expires' => 30,

    /*
    |--------------------------------------------------------------------------
    | Not-Found Cache Expiration
    |--------------------------------------------------------------------------
    |
    | Cache's time to live in seconds for addresses the service reports as
    | not found (the default location is cached for them). Transient errors
    | are never cached. Null uses `cache_expires`.
    |
    */

    'cache_not_found_expires' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix used for cache keys (in addition to globally configured prefix).
    |
    */

    'cache_prefix' => 'geoip:',

    /*
    |--------------------------------------------------------------------------
    | Default Location
    |--------------------------------------------------------------------------
    |
    | Return when a location is not found.
    |
    */

    'default_location' => [
        'ip' => '127.0.0.0',
        'iso_code' => 'US',
        'country' => 'United States',
        'city' => 'New Haven',
        'state' => 'CT',
        'state_name' => 'Connecticut',
        'postal_code' => '06510',
        'lat' => 41.31,
        'lon' => -72.92,
        'timezone' => 'America/New_York',
        'continent' => 'NA',
        'default' => true,
        'currency' => 'USD',
    ],

];
