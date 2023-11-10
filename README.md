# CC Icons

This is a PHP class that generates an image based on a
list of supported credit card or cryptocurrency types.
It makes it easy to get an image showing all supported
payment types for a payment processor.

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
- `withLayout(array $override)` - Override one or more default layouts
- `saveToDisk(string $path)` - Generate and save the image to the given location on disk
- `getDataUri()` - Generate and get the RFC 2397 string corresponding to the image

## List of Supported Icons

Icons should be specified as a single word, case-insensitive
- Credit Cards:
    - `AMEX`
    - `Dankort`
    - `DinersClub`
    - `Discover`
    - `JCB`
    - `Maestro` -- Aliases: `Switch`, `Solo`
    - `Mastercard` -- Alias: `MC`
    - `PostePay`
    - `UnionPay`
    - `Visa` -- Aliases: `Delta`, `UKE`
- Cryptocurrencies:
    - `BTC` (Bitcoin)
    - `LTC` (Litecoin)
    - `BCH` (Bitcoin Cash)
    - `BNB` (Binance Coin)
    - `ETH` (Ethereum)
    - `USDT` (Tether)
    - `USDC` (USD Coin)


## Default Layouts

Layouts specify number of icons per row.

```php
[
    1 => [1],
    2 => [2],
    3 => [2, 1],
    4 => [2, 2],
    5 => [3, 2],
    6 => [3, 3],
]
```
