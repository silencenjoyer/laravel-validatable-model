# Short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/silencenjoyer/laravel-validatable-model.svg?style=flat-square)](https://packagist.org/packages/silencenjoyer/laravel-validatable-model)
[![Total Downloads](https://img.shields.io/packagist/dt/silencenjoyer/laravel-validatable-model.svg?style=flat-square)](https://packagist.org/packages/silencenjoyer/laravel-validatable-model)
![GitHub Actions](https://github.com/silencenjoyer/laravel-validatable-model/actions/workflows/main.yml/badge.svg)

This package provides possibility to validate a model using its own validation rules. This can be particularly useful if you need to reuse validation code or if you want to make your controller code more concise.  
This can be done either in the controller or during the model lifecycle.

## Installation

You can install the package via composer:

```bash
composer require silencenjoyer/laravel-validatable-model
```

## Usage

```php
class MyModel extends \Illuminate\Database\Eloquent\Model
{
    use \Silencenjoyer\LaravelValidatableModel\ValidationTrait;

    /**
     * {@inheritDoc} 
     * @return array
     */
    public function fields() : array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc} 
     * @return array
     */
    public function rules() : array{
        return [
            'name' => [
                'required',
                'max:75',
                'min:2',
                'regex:/^([\p{L}\'`]*[\s\-]?){1,3}$/u',
                'not_regex:/[-`\']{2,}/'
            ],
            'email' => 'email',
        ];
    }
}
```
```php
class SomeController extends Illuminate\Http\Request\Controller
{
    public function someMethod(Request $request)
    {
        $model = (new MyModel())->fill($request->all());
        $model->validate();
        ...
    }
}
```

### Testing

```bash
composer test  
composer test-coverage  
cd tests/docker && docker-compose -f docker-compose.test.yml up
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email an_gebrich@outlook.com instead of using the issue tracker.

## Credits

-   [Andrew Gebrich](https://github.com/silencenjoyer)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
