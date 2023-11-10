<?php

namespace Vectorface\Tests\CCIcons;

use Imagick;
use PHPUnit\Framework\TestCase;
use Lupka\PHPUnitCompareImages\CompareImagesTrait;
use Vectorface\CCIcons\CCImageMaker;

class CCImageMakerTest extends TestCase
{
    use CompareImagesTrait;

    /**
     * Data provider for makeImage tests.
     * Arguments are $types, $size, $padding, $compareImg
     */
    public function makeImageProvider()
    {
        return [
            'invalid CC type returns empty' => [
                'empty.png',
                ['interac'] // Invalid CC id
            ],
            'single AMEX icon' => [
                '1-icon.png',
                [CCImageMaker::AMEX]
            ],
            'Mastercard and Diners Club icons' => [
                '2-icon.png',
                [CCImageMaker::MASTERCARD, CCImageMaker::DINERSCLUB]
            ],
            'JCB, Mastercard, and Visa icons' => [
                '3-icon.png',
                [CCImageMaker::JCB, CCImageMaker::MASTERCARD, CCImageMaker::VISA]
            ],
            'UnionPay, Discover, Maestro, and AMEX icons' => [
                '4-icon.png',
                [CCImageMaker::UNIONPAY, CCImageMaker::DISCOVER, CCImageMaker::MAESTRO, CCImageMaker::AMEX]
            ],
            'Visa, Diners Club, AMEX, Discover, and UnionPay icons' => [
                '5-icon.png',
                [CCImageMaker::VISA, CCImageMaker::DINERSCLUB, CCImageMaker::AMEX, CCImageMaker::DISCOVER, CCImageMaker::UNIONPAY]
            ],
            'Mastercard, JCB, Maestro, UnionPay, AMEX, and Discover icons' => [
                '6-icon.png',
                [CCImageMaker::MASTERCARD, CCImageMaker::JCB, CCImageMaker::MAESTRO, CCImageMaker::UNIONPAY, CCImageMaker::AMEX, CCImageMaker::DISCOVER]
            ],
            'JCB, Mastercard, and Visa icons using case-insensitive strings' => [
                '3-icon.png',
                ['JCB', 'Mc', 'vISa']
            ],
            'Discover, Mastercard, and Visa using extra padding between icons' => [
                'padding.png',
                [CCImageMaker::DISCOVER, CCImageMaker::MASTERCARD, CCImageMaker::VISA],
                [300, 200],
                25
            ],
            'Diners Club, Discover, and JCB creating a tall image' => [
                'tall.png',
                [CCImageMaker::DINERSCLUB, CCImageMaker::DISCOVER, CCImageMaker::JCB],
                [300, 400]
            ],
            'Maestro, UnionPay, Visa, and Mastercard creating a wide image' => [
                'wide.png',
                [CCImageMaker::MAESTRO, CCImageMaker::UNIONPAY, CCImageMaker::VISA, CCImageMaker::MASTERCARD],
                [300, 100]
            ],
            'Visa, Mastercard, and Maestro with a custom layout using 3 icons in a row' => [
                'layout.png',
                [CCImageMaker::VISA, CCImageMaker::MASTERCARD, CCImageMaker::MAESTRO],
                [400, 200],
                10,
                [3 => [3]]
            ],
            'Cryptocurrencies LTC and BCH' => [
                'cryptocurrencies.png',
                [CCImageMaker::LTC, CCImageMaker::BCH],
            ],
        ];
    }

    /**
     * @dataProvider makeImageProvider
     */
    public function testMakeImage(string $compareImg, array $types, array $size = [300, 200], int $padding = 10, array $layout = [])
    {
        $generated = (new CCImageMaker)
            ->withTypes($types)
            ->withSize($size[0], $size[1])
            ->withPadding($padding)
            ->withLayout($layout)
            ->getDataUri();
        $imagick = new Imagick();
        $imagick->readImageBlob(file_get_contents($generated));
        $this->assertImageSimilarity(
            $imagick,
            __DIR__ . '/images/' . $compareImg,
            0.00001
        );
    }

    public function testSaveToDisk()
    {
        $save_dir = __DIR__ . '/tmp/test.png';
        (new CCImageMaker(true))
            ->saveToDisk($save_dir);
        $this->assertFileExists($save_dir);
        unlink($save_dir);
    }
}
