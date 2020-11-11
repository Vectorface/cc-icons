<?php

use Vectorface\CCIcons\CCImageMaker;

require 'vendor/autoload.php';

$regular_images = [
    '1-icon'  => ['AMEX'],
    '2-icon'  => ['MASTERCARD', 'DINERSCLUB'],
    '3-icon'  => ['JCB', 'MASTERCARD', 'VISA'],
    '4-icon'  => ['UNIONPAY', 'DISCOVER', 'MAESTRO', 'AMEX'],
    '5-icon'  => ['VISA', 'DINERSCLUB', 'AMEX', 'DISCOVER', 'UNIONPAY'],
    '6-icon'  => ['MASTERCARD', 'JCB', 'MAESTRO', 'UNIONPAY', 'AMEX', 'DISCOVER'],
    'empty'   => []
];

$special_images = [
    'padding' => [
        'types'   => ['DISCOVER', 'MASTERCARD', 'VISA'],
        'size'    => [350, 225],
        'padding' => 25
    ],
    'tall'    => [
        'types'   => ['DINERSCLUB', 'DISCOVER', 'JCB'],
        'size'    => [300, 300],
        'padding' => 10
    ],
    'wide'    => [
        'types'   => ['MAESTRO', 'UNIONPAY', 'VISA', 'MASTERCARD'],
        'size'    => [300, 100],
        'padding' => 10
    ]
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
        ->saveToDisk(__DIR__ . '/images/' . $filename . '.png');
}
