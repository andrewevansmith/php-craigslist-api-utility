## Simple Craigslist API utility
Provides convenient way of getting listing data by various Craigslist filters.

###IMPORTANT NOTE - For educational purposes only.  This software was developed as an experiment to demonstrate web scraping basics.  Craigslist may prohibit use of automated gathering tools.  Use at your own discretion.

Features
------------
* Get listings by city and category
* Get listings by custom URLs
* Merge sets of listings from multiple requests


Dependency
------------
* [PHP 5.4+] (http://php.net/)
* [PHP Simple HTML Dom Parser] (https://github.com/sunra/php-simple-html-dom-parser)


Installation
------------

Issue following command in console:

```php
composer require andrewevansmith/php-craigslist-api-utility:dev-master
```

Alternatively  edit composer.json by adding following line and run **`composer update`**
```php
"require": { 
    ....,
    "andrewevansmith/php-craigslist-api-utility": "dev-master",
	
},
```

Usage
------------

### Example: making a simple Craigslist request:
```php

    use Craigslist\CraigslistRequest;
    use Craigslist\CraigslistApi;

    $request = new CraigslistRequest([
        'city' => 'louisville',
        'category' => 'pet',
        'query' => 'pup'
    ]);
    $api = new CraigslistApi();
    $result = $api->get($request);
    ....
```

### Example: making a detailed Craigslist request, getting custom fields (photo urls):
```php

    use Craigslist\CraigslistRequest;
    use Craigslist\CraigslistApi;

    $request = new CraigslistRequest(array(
        'city' => 'louisville',
        'category' => 'pet',
        'query' => 'pup',
        'follow_links' => true,
        'selectors' => array(
            array('label' => 'photos', 'element' => 'img', 'limit' => 10, 'target' => 'src'),
        ),
    ));
    $api = new CraigslistApi();
    $result = $api->get($request);
    ....
```

### Example: making multiple Craigslist requests:
```php

    use Craigslist\CraigslistRequest;
    use Craigslist\CraigslistApi;

    $requests = array( 
        new CraigslistRequest(array(
            'city' => 'louisville',
            'category' => 'pet',
            'query' => 'pup',
            'follow_links' => true,
            'selectors' => array( 
                array('label' => 'photos', 'element' => 'img', 'limit' => 3, 'target' => 'src'),
            )
        )),
        new CraigslistRequest(array(
            'city' => 'lexington',
            'category' => 'pet',
            'query' => 'cat',
            'follow_links' => true,
            'selectors' => array(
                array('label' => 'photos', 'element' => 'img', 'limit' => 3, 'target' => 'src'),
            )
        )),
    );
    $api = new CraigslistApi();
    $result = $api->get($requests);
    ....
```

Support
-------

[Please open an issue on GitHub](https://github.com/andrewevansmith/php-craigslist-api-utility/issues)


License
-------

This software is released under the MIT License. See the bundled
[LICENSE](https://github.com/andrewevansmith/php-craigslist-api-utility/blob/master/LICENSE)
file for details.
