# DaftAPI

Package to use [Daft.ie](http://daft.ie) API easily from any PHP project.

## Install

Via Composer

``` bash
$ composer require vgomes/daftapi
```

You'll need also PHP [SOAP extension](http://php.net/manual/en/book.soap.php) installed and configured in your server.

## Usage

In order to use this, you'll need a Daft api key. Get in touch with them in order to get one.

``` php
$api = new DaftAPI('your_api_key_here');
$propsForSale = $api->sale();
```

By default, Daft API doesn't cover overseas properties, so that functionality is achieved through some HTML parsing. Support is added to use in a similar way to the API.

``` php
$overseas = new DaftOverseas('_your_overseas_key_');
$properties = $overseas->->properties([
	'country' 	=> DaftOverseas::SPAIN,
	'max_price'	=> 500000, 
	'limit' 	=> 5
])); // 5 properties on Spain with price under 500k

$property = $overseas->property(_your_property_id_);
```

Provided methods to get a list of properties, information about a particular property and get the pictures for a particular ad. I couldn't fnid information on more specific params to refine searches appart of the included ones. So feel free to contact me to add support for more if you have information about them.


## Documentation

Check oficial [Daft.ie API docs](http://api.daft.ie/doc/v3) to get information on what parameters you can use on requests and the type of results you can get.
You can also check [some code examples](http://api.daft.ie/examples/php5/).

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email victor.j.gomes@gmail.com instead of using the issue tracker.

## Credits

- [Victor Gomes](https://github.com/vgomes)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
