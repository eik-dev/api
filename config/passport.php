<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Passport Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Passport will use when
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your auth configuration file.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys to generate secure access tokens. These
    | keys are created using the "passport:keys" Artisan command and can
    | be retrieved from the storage/passport directory.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Client UUIDs
    |--------------------------------------------------------------------------
    |
    | By default, Passport uses auto-incrementing primary keys when assigning
    | IDs to clients. However, if Passport is installed using the provided
    | --uuids switch, this will be set to "true" and UUIDs will be used.
    |
    */

    'client_uuids' => false,

    /*
    |--------------------------------------------------------------------------
    | Personal Access Client
    |--------------------------------------------------------------------------
    |
    | If you enable client hashing, you should set the personal access client
    | ID and unhashed secret within your environment file. The values will
    | get used when issuing personal access tokens to your application.
    |
    */

    'personal_access_client' => [
        'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
        'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Passport Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration value allows you to customize the storage options
    | for Passport, such as the database connection that should be used
    | by Passport's internal database queries.
    |
    */

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Passport Token Expiration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the lifetime of access tokens and refresh tokens.
    | These values are used when issuing tokens to users and third-party
    | applications that connect to your API via Passport.
    |
    */

    'token_expiration' => [
        'access_token' => env('PASSPORT_ACCESS_TOKEN_EXPIRE', 60), // minutes
        'refresh_token' => env('PASSPORT_REFRESH_TOKEN_EXPIRE', 20160), // minutes (14 days)
        'personal_access_token' => env('PASSPORT_PERSONAL_ACCESS_TOKEN_EXPIRE', 60), // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Passport Scopes
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the scopes that will be available within
    | your application. These scopes are used to grant fine-grained access
    | to your application's API endpoints and resources.
    |
    */

    'scopes' => [
        'read-user' => 'Read user information',
        'read-profile' => 'Read user profile data',
        'write-profile' => 'Update user profile data',
        'read-files' => 'Read user files',
        'write-files' => 'Upload and manage user files',
        'read-certificates' => 'Read user certificates',
        'write-certificates' => 'Request and manage certificates',
        'read-training' => 'Read training information',
        'write-training' => 'Register for training',
        'read-payments' => 'Read payment information',
        'admin' => 'Full administrative access',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Scope
    |--------------------------------------------------------------------------
    |
    | Here you may define the default scope that will be attached to all
    | access tokens issued by your application. This scope may be used
    | to provide basic access to your application's resources.
    |
    */

    'default_scope' => 'read-user',

    /*
    |--------------------------------------------------------------------------
    | Passport Route Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the routing options for Passport, including
    | the prefix and middleware that will be applied to all Passport routes.
    |
    */

    'routes' => [
        'prefix' => 'oauth',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Hashing
    |--------------------------------------------------------------------------
    |
    | This option allows you to enable client hashing, which will hash the
    | client secrets before storing them in the database. This provides
    | additional security for your OAuth client credentials.
    |
    */

    'hash_client_secrets' => env('PASSPORT_HASH_CLIENT_SECRETS', false),

]; 