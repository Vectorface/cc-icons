# CC Icons

This is a PHP class that generates an image based on a
list of supported credit card types. It makes it easy to
get an image showing all supported payment types for a
payment processor.

## Installation

Run the following [composer](https://getcomposer.org/) command:

```shell script
$ composer require vectorface/cc-icons
```

## Using CC Icons

To create an image, create a new CCImageMaker object and
specify which icons to include.

```php
use Vectorface\CCIcons\CCImageMaker;

(new CCImageMaker)
    ->withTypes(["Visa", "Mastercard"])
    ->getDataUri();
```

This will return the data URI (RFC 2397) string corresponding
to the image that was created. By default, each image is
300x200, transparent, and includes 10px padding between icons.

## Methods

- `withTypes(array $processors)` - Specify which icons to include in the image
- `withPadding(int $new_padding)` - Specify how much padding to include between icons
- `withSize(int $width, int $height)` - Specify the size of the output image
- `withLayout(array $layout)` - Specify how many icons to play on each row. This method
does not check whether the number of icons in the layout matches the number
of icons desired.
- `saveToDisk(string $path)` - Generate and save the image to the given location on disk
- `getDataUri()` - Generate and get the RFC 2397 string corresponding to the image

## List of Supported Icons

Icons should be specified as a single word, case-insensitive
- AMEX
- Dankort
- DinersClub
- Discover
- JCB
- Maestro -- Aliases: `Switch`, `Solo`
- Mastercard -- Alias: `MC`
- PostePay
- UnionPay
- Visa -- Aliases: `Delta`, `UKE`
