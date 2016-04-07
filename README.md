# DaftAPI

Package to use [Daft.ie](http://daft.ie) API easily from any PHP project.

## Install

Via Composer

``` bash
$ composer require vgomes/daftapi
```

## Usage

In order to use this, you'll need a Daft api key. Get in touch with them in order to get one.

``` php
$api = new DaftAPI('your_api_key_here');
$propsForSale = $api->sale();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email victor.j.gomes@gmail.com instead of using the issue tracker.

## Credits

- [Victor Gomes][https://github.com/vgomes]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
