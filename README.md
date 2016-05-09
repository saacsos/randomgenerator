# saacsos/randomgenerator
Laravel 5 Custom String Random Generator

## Installation
```
$ composer require saacsos/randomgenerator
```

## Basic Usage
```php
<?php
use Saacsos\Randomgenerator\Util\RandomGenerator;

// Create an object
$randomGenerator = new RandomGenerator();

// Get random password
$randomGenerator->password();
$password = $randomGenerator->get();
// or
$password = $randomGenerator->password()->get();

// Get random access token (48 chars)
$accessToken = $randomGenerator->accessToken()->get();

```

## Level & Length
Level of strength
* 1 = number ([0-9])
* 2 = hexadecimal ([0-9a-f])
* 4 = lowercase (a-z except i,l,o)
* 8 = uppercase (A-Z except I,L,O)
* 16 = special character !@#$%^&*()_=[]{}?,
Combine level for complex password
* 5 = 1 + 4 = number + lowercase
* 13 = 1 + 4 + 8 = number + lowercase + uppercase
* 29 = 1 + 4 + 8 + 16 = number + lowercase + uppercase + special character
```php
<?php

// get password 8 characters in level 5 
$password = $randomGenerator->level(5)->length(8)->password()->get();
```
Default level = 13, Default length = 8

## isMatch($string, $strict=false)
Validate if $string match the level or not
Strict mode will check $string must has at least 1 character in its level
```php
<?php

$match = $randomGenerator->min(8)->max(8)->level(13)->isMatch('password'); // true
$match = $randomGenerator->min(8)->max(8)->level(13)->isMatch('password', true); // false because no uppercase
```

## Laravel 5 Service Provider and Facades
Add your new provider to the `providers` array of `config/app.php`:
```php
    'providers' => [
        // ...
        Saacsos\Randomgenerator\ServiceProvider\RandomGeneratorServiceProvider::class,
        // ...
    ],
```

Add class alias to the `aliases` array of `config/app.php`:
```php
    'aliases' => [
        // ...
        'RandomGenerator' => Saacsos\Randomgenerator\Facades\RandomGenerator::class,
        // ...
    ],
```
And you can use 
```php
    $password = \RandomGenerator::password()->get();
```