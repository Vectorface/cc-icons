<?php

use Vectorface\CCIcons\CCImageMaker;

require __DIR__ . '/../vendor/autoload.php';

$regular_images = [
    '1-icon'  => ['AMEX'],
    '2-icon'  => ['MASTERCARD', 'DINERSCLUB'],
    '3-icon'  => ['JCB', 'MASTERCARD', 'VISA'],
    '4-icon'  => ['UNIONPAY', 'DISCOVER', 'MAESTRO', 'AMEX'],
    '5-icon'  => ['VISA', 'DINERSCLUB', 'AMEX', 'DISCOVER', 'UNIONPAY'],
    '6-icon'  => ['MASTERCARD', 'JCB', 'MAESTRO', 'UNIONPAY', 'AMEX', 'DISCOVER'],
    'empty'   => [],
    'cryptocurrencies' => ['LTC', 'BCH'],
];

$special_images = [
    'padding' => [
        'types'   => ['DISCOVER', 'MASTERCARD', 'VISA'],
        'size'    => [300, 200],
        'padding' => 25,
        'layout'  => []
    ],
    'tall'    => [
        'types'   => ['DINERSCLUB', 'DISCOVER', 'JCB'],
        'size'    => [300, 400],
        'padding' => 10,
        'layout'  => []
    ],
    'wide'    => [
        'types'   => ['MAESTRO', 'UNIONPAY', 'VISA', 'MASTERCARD'],
        'size'    => [300, 100],
        'padding' => 10,
        'layout'  => []
    ],
    'layout'  => [
        'types'   => ['VISA', 'MC', 'MAESTRO'],
        'size'    => [400, 200],
        'padding' => 10,
        'layout'  => [3]
    ],
];

foreach ($regular_images as $filename => $types) {
    (new CCImageMaker)
        ->withTypes($types)
        ->saveToDisk(__DIR__ . '/images/' . $filename . '.png');
}

foreach ($special_images as $filename => $info) {
    (new CCImageMaker)
        ->withTypes($info['types'])
        ->withSize($info['size'][0], $info['size'][1])
        ->withPadding($info['padding'])
        ->withLayout($info['layout'])
        ->saveToDisk(__DIR__ . '/images/' . $filename . '.png');
}
