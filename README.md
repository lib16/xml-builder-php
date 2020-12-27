# XML Builder for PHP 7
A library for creating XML documents written in PHP 7.

[![Build Status](https://travis-ci.com/lib16/xml-builder-php.svg?branch=master)](https://travis-ci.com/lib16/xml-builder-php)
[![Coverage](https://codecov.io/gh/lib16/xml-builder-php/branch/master/graph/badge.svg)](https://codecov.io/gh/lib16/xml-builder-php)

## Installation with Composer
This package is available on [packagist](https://packagist.org/packages/lib16/xml),
so you can use [Composer](https://getcomposer.org) to install it:
```composer require lib16/xml```

## Basic Usage
```php
<?php
require_once 'vendor/autoload.php';

use Lib16\XML\Xml;

class Kml extends Xml
{
    const MIME_TYPE = 'application/vnd.google-earth.kml+xml';
    const FILENAME_EXTENSION = 'kml';
    const XML_NAMESPACE = 'http://www.opengis.net/kml/2.2';

    public static function createKml(): self
    {
        return static::createRoot('kml');
    }

    public function placemark(
        string $name,
        string $description,
        float $longitude,
        float $latitude,
        float $altitude = null
    ): self {
        $pm = $this->append('Placemark');
        $pm->append('name', $name);
        $pm->append('description', $description);
        $pm->append('Point')->append(
            'coordinates',
            implode(',', array_filter([
                $longitude,
                $latitude,
                $altitude
            ]))
        );
        return $pm;
    }
}

$myKml = Kml::createKml();
$myKml->placemark(
    'Cologne Cathedral',
    'Cologne Cathedral is a Roman Catholic cathedral in Cologne, Germany.',
    50.9413,
    6.958
);
//$myKml->headerfields('cologne-cathedral');
print $myKml;
```

The generated markup:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<kml xmlns="http://www.opengis.net/kml/2.2">
    <Placemark>
        <name>Cologne Cathedral</name>
        <description>Cologne Cathedral is a Roman Catholic cathedral in Cologne, Germany.</description>
        <Point>
            <coordinates>50.9413,6.958</coordinates>
        </Point>
    </Placemark>
</kml>
```


### The `Adhoc` trait

`Adhoc` allows you to use any method name not previously defined to add XML elements or attributes.

```php
<?php
require_once 'vendor/autoload.php';

use Lib16\XML\{Xml, Adhoc};

class Html extends Xml
{
    use Adhoc;

    const XML_DECLARATION = false;
    const DOCTYPE = '<!DOCTYPE html>';
    const HTML_MODE_ENABLED = true;

    public static function createHtml(
        string $lang = null,
        string $manifest = null
    ): self {
        return static::createRoot('html')
            ->attrib('lang', $lang)
            ->setManifest($manifest);
    }
}

$html = Html::createHtml('en');
$body = $html->body();
$article = $body->article();
$article->h1('Scripting languages');
$article->p(
    Html::abbr('PHP')->setTitle('PHP: Hypertext Preprocessor')
    . ' is a server-side scripting language designed for web development but also used as a general-purpose programming language.'
);

print $html;
```

The generated markup:

```html
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html>
<html lang="en">
    <body>
        <article>
            <h1>Scripting languages</h1>
            <p><abbr title="PHP: Hypertext Preprocessor">PHP</abbr> is a
                server-side scripting language designed for web development
                but also used as a general-purpose programming language.</p>
        </article>
    </body>
</html>
```

