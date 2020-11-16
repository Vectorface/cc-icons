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
            [
                'empty.png',
                ['interac'] // Invalid CC id
            ],
            [
                '1-icon.png',
                [CCImageMaker::AMEX]
            ],
            [
                '2-icon.png',
                [CCImageMaker::MASTERCARD, CCImageMaker::DINERSCLUB]
            ],
            [
                '3-icon.png',
                [CCImageMaker::JCB, CCImageMaker::MASTERCARD, CCImageMaker::VISA]
            ],
            [
                '4-icon.png',
                [CCImageMaker::UNIONPAY, CCImageMaker::DISCOVER, CCImageMaker::MAESTRO, CCImageMaker::AMEX]
            ],
            [
                '5-icon.png',
                [CCImageMaker::VISA, CCImageMaker::DINERSCLUB, CCImageMaker::AMEX, CCImageMaker::DISCOVER, CCImageMaker::UNIONPAY]
            ],
            [
                '6-icon.png',
                [CCImageMaker::MASTERCARD, CCImageMaker::JCB, CCImageMaker::MAESTRO, CCImageMaker::UNIONPAY, CCImageMaker::AMEX, CCImageMaker::DISCOVER]
            ],
            // Use strings instead of constants
            [
                '3-icon.png',
                ['JCB', 'Mc', 'vISa']
            ],
            [
                'padding.png',
                [CCImageMaker::DISCOVER, CCImageMaker::MASTERCARD, CCImageMaker::VISA],
                [300, 200],
                25
            ],
            [
                'tall.png',
                [CCImageMaker::DINERSCLUB, CCImageMaker::DISCOVER, CCImageMaker::JCB],
                [300, 400]
            ],
            [
                'wide.png',
                [CCImageMaker::MAESTRO, CCImageMaker::UNIONPAY, CCImageMaker::VISA, CCImageMaker::MASTERCARD],
                [300, 100]
            ],
            [
                'layout.png',
                [CCImageMaker::VISA, CCImageMaker::MASTERCARD, CCImageMaker::MAESTRO],
                [400, 200],
                10,
                [3 => [3]]
            ]
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
