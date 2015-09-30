yii2-sms-ru
===========

Yii2 wrapper for [sms.ru](https://sms.ru/) API

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require alexeevdv/yii2-sms-ru "dev-master"
```

or add

```
"alexeevdv/yii2-sms-ru": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Configuration

### Through application component
```php
"components" => [
    //...
    "sms" => [
        "class" => "alexeevdv\sms\ru\Client",
        "api_id" => "YOUR_API_ID",
    ],
    //...
],
```

## Usage

```php
use alexeevdv\sms\ru\Sms;
$response = \Yii::$app->sms->send(new Sms([
    "to" => "+9530000000",
    "text" => "Hello my friend!",
]));

echo $response->code;
```
