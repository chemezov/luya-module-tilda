LUYA Tilda module
=================
LUYA module with Tilda block

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist chemezov/luya-module-tilda "*"
```

or add

```
"chemezov/luya-module-tilda": "*"
```

to the require section of your `composer.json` file.


Usage
-----

In order to add the modules to your project go into the modules section of your config:

```php
return [
    'modules' => [
        // ...
        'tilda' => [
            'class' => 'chemezov\luya\tilda\frontend\Module',
            'useAppViewPath' => true, // When enabled the views will be looked up in the @app/views folder, otherwise the views shipped with the module will be used.
            
            // Required fields
            'cache' => 'tildaCache',
            'publicKey' => 'YOUR_PUBLIC_KEY',
            'secretKey' => 'YOUR_SECRET_KEY',
            
            // Optional fields
            // You can leave this field empty. In this case, all projects will be shown.
            'projectIds' => [
                123, // Project ID
                456, // Project ID
            ],
            // Block wrapper css class
            'defaultBlockCssClasses' => 'b-tilda-content-outer',
            'blockGroup' => \app\blockgroups\ProjectGroup::class,
            
            // The directory for downloading resources (css, js, img). Make sure it has write permissions.
            'assetsPath' => '@webroot/uploads/tilda',
            'assetsUrl' => '/uploads/tilda',
        ],
        // ...
    ],
    'components' => [
        // Do not specify an empty cache, otherwise go beyond the API request limit
        'tildaCache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
```

Make sure you have correct write permissions to `assetsPath` and `assetsUrl` can be reached through http(s) request.

Also make sure that the `queue` is started via cron.

```shell
* * * * * cd /path/to/www && ./luya queue/run -v
```

This is necessary when reloading resources via the webhook.
