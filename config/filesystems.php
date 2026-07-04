<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disco de sistema de archivos predeterminado
    |--------------------------------------------------------------------------
    |
    | Aquí puede especificar el disco del sistema de archivos predeterminado
    | que utilizará el framework. Su aplicación tiene a su disposición tanto
    | el disco "local" como diversos discos basados ​​en la nube para el
    | almacenamiento de archivos.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |
--------------------------------------------------------------------------
    | Discos del sistema de archivos
    |--------------------------------------------------------------------------
    |
    | A continuación, puedes configurar tantos discos del sistema de archivos
    | como sea necesario, e incluso configurar varios discos para el mismo
    | controlador. Aquí se incluyen, a modo de referencia, ejemplos para la
    | mayoría de los controladores de almacenamiento compatibles.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | 
Enlaces simbólicos
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar los enlaces simbólicos que se crearán al ejecutar
    | el comando Artisan `storage:link`. Las claves del array deben corresponder
    | a las ubicaciones de los enlaces y los valores a sus destinos.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
