<?php

namespace Vectorface\CCIcons;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class CCImageMaker
{
    /** @var ImageManager */
    private $manager;
    /** @var array */
    private $processors;
    /** @var int */
    private $padding;
    /** @var int */
    private $width;
    /** @var int */
    private $height;
    /** @var Image */
    private $img;

    /* Give every CC icon a constant to reference */
    const AMEX = 1;
    const DANKORT = 2;
    const DINERSCLUB = 3;
    const DISCOVER = 4;
    const JCB = 5;
    const MAESTRO = 6;
    const MASTERCARD = 7;
    const POSTEPAY = 8;
    const UNIONPAY = 9;
    const VISA = 10;

    /** Link supported CC string representations to constants */
    protected static $cc_strings = [
        "VISA"       => self::VISA,
        "MASTERCARD" => self::MASTERCARD,
        "MC"         => self::MASTERCARD,
        "DISCOVER"   => self::DISCOVER,
        "UKE"        => self::VISA,         // Short for UK Electron / Visa Electron
        "SWITCH"     => self::MAESTRO,      // Rebranded as Maestro in 2002
        "SOLO"       => self::MAESTRO,      // Discontinued in 2011, used the Maestro processing system
        "DINERSCLUB" => self::DINERSCLUB,
        "DANKORT"    => self::DANKORT,      // National debit card of Denmark
        "DELTA"      => self::VISA,         // Rebranded as Visa Debit
        "AMEX"       => self::AMEX,
        "JCB"        => self::JCB,
        "UNIONPAY"   => self::UNIONPAY,
        "POSTEPAY"   => self::POSTEPAY      // Italian Post Office
    ];

    /** Link credit cards to their file name in src/icons/ */
    protected static $supported_cc_types = [
        self::AMEX       => "amex",
        self::DANKORT    => "dankort",
        self::DINERSCLUB => "dinersclub",
        self::DISCOVER   => "discover",
        self::JCB        => "jcb",
        self::MAESTRO    => "maestro",
        self::MASTERCARD => "mastercard",
        self::POSTEPAY   => "postepay",
        self::UNIONPAY   => "unionpay",
        self::VISA       => "visa"
    ];

    /** Width/height of the icon files in src/icons, must be divisible by 4 */
    const ICON_SIZE = 200;

    /**
     * CCImageMaker constructor.
     * @param bool $useGd Set to true to use GD instead of Imagick
     */
    public function __construct(bool $useGd = false)
    {
        if (extension_loaded('imagick') && class_exists(\Imagick::class) && !$useGd) {
            // Use Imagick by default if available
            $this->manager = new ImageManager(['driver' => 'imagick']);
        } else {
            // Fall back to GD if Imagick not present or otherwise specified
            $this->manager = new ImageManager();
        }
        $this->processors = [];
        $this->padding = 10;
    }

    /**
     * Specify the list of processors to be included in the image, can use either the processor's constant or a
     * string representation.
     * @param array $processors The new processors to be included
     * @return $this
     */
    public function withTypes(array $processors)
    {
        // Remove unsupported entries (not referencing a valid constant or string)
        $filtered_processors = array_filter($processors, function ($processor) {
            return isset(self::$supported_cc_types[$processor])
                || (is_string($processor) && isset(self::$cc_strings[strtoupper($processor)]));
        });

        foreach ($filtered_processors as $i => $processor) {
            if (is_string($processor)) {
                $filtered_processors[$i] = self::$cc_strings[strtoupper($processor)];
            }
        }

        // Discard processors if more than 6
        $this->processors = array_slice($filtered_processors, 0, 6);
        return $this;
    }

    /**
     * Set the amount of padding to place between icons - default is 10
     * @param int $new_padding The padding to place between icons
     * @return $this
     */
    public function withPadding(int $new_padding)
    {
        $this->padding = $new_padding;
        return $this;
    }

    /**
     * Set the size of the image that will be output
     * @param int $width The width of the image
     * @param int $height The height of the image
     * @return $this
     */
    public function withSize(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Save the image to a specified location on disk
     * @param string $path The location to save the image
     * @return $this
     */
    public function saveToDisk(string $path)
    {
        $this->makeImage();
        $this->img->save($path);
        return $this;
    }

    /**
     * Get the image as a RFC 2397 encoded string
     * @return string
     */
    public function getDataUri()
    {
        $this->makeImage();
        return (string)$this->img->encode('data-url');
    }


    /**
     * Make an image object containing specified credit card processors
     */
    private function makeImage()
    {
        if (empty($this->processors)) {
            $this->processors = [self::MASTERCARD, self::VISA];
        }

        // Create a 3:2 canvas proportional to icon size, including padding
        $this->img = $this->manager->canvas(
            3 / 2 * self::ICON_SIZE + 2 * $this->padding,
            self::ICON_SIZE + $this->padding
        );

        $processor_count = count($this->processors);
        switch ($processor_count) {
            case 1:
                // Insert image in middle of canvas
                $this->img->insert(self::getIconPath($this->processors[0]), 'center');
                break;
            case 2:
                // Scale icons to 0.75x and insert them on edges of canvas
                foreach ($this->processors as $i => $processor) {
                    $processor_icon = $this->manager->make(self::getIconPath($processor));
                    $processor_icon->widen(3 / 4 * self::ICON_SIZE);
                    // Offset second icon to right side of image
                    $this->img->insert($processor_icon, 'left', $i * ($this->img->getWidth() / 2 + $this->padding));
                }
                break;
            case 3:
            case 4:
            case 5:
            case 6:
                // Make top and bottom rows separately, then merge them together
                $top_icon_count = (int)round($processor_count / 2);

                $top_row = $this->makeRow(array_slice($this->processors, 0, $top_icon_count));
                $bottom_row = $this->makeRow(array_slice($this->processors, $top_icon_count, $processor_count - $top_icon_count));

                $this->img->insert($top_row, 'top');
                $this->img->insert($bottom_row, 'bottom');
                break;
        }

        // Resize output if both width and height parameters are present
        if (isset($this->width) && isset($this->height)) {
            $this->resizeImage();
        }
    }

    /**
     * Creates an image containing one row of processor icons
     * @param array $processors List of processors to be included, max of 3
     * @return Image
     */
    private function makeRow(array $processors)
    {
        // Scale padding to account for resize at end
        $adjusted_padding = 2 * $this->padding;
        $row = $this->manager->canvas(3 * self::ICON_SIZE + 2 * $adjusted_padding, self::ICON_SIZE);

        switch (count($processors)) {
            case 1:
                // Insert image in middle of row
                $row->insert(
                    $this->getIconPath($processors[0]),
                    'top-left',
                    $row->getWidth() / 2 - self::ICON_SIZE / 2
                );
                break;
            case 2:
                // Centre images with 2 * $adjusted_padding between them
                $row->insert(
                    $this->getIconPath($processors[0]),
                    'top-left',
                    $row->getWidth() / 2 - self::ICON_SIZE - $adjusted_padding
                );
                $row->insert(
                    $this->getIconPath($processors[1]),
                    'top-left',
                    $row->getWidth() / 2 + $adjusted_padding
                );
                break;
            case 3:
                // Insert all 3 images equally spaced
                foreach ($processors as $i => $processor) {
                    // Offset determined by index in array
                    $row->insert(
                        $this->getIconPath($processor),
                        'top-left',
                        $i * (self::ICON_SIZE + $adjusted_padding)
                    );
                }
                break;
        }

        // Resize row to proper size
        $row->widen($row->getWidth() / 2);
        return $row;
    }

    /**
     * Resizes the image to the proper size while constraining aspect ratio
     */
    private function resizeImage()
    {
        $width_ratio = $this->width / $this->img->getWidth();
        $height_ratio = $this->height / $this->img->getHeight();
        
        if ($width_ratio > $height_ratio) {
            $this->img->heighten($this->height);
        } else {
            $this->img->widen($this->width);
        }
        $this->img->resizeCanvas($this->width, $this->height);
    }

    /**
     * Gets the pathname for a processor icon file
     * @param int $processor The processor id associated with the desired icon
     * @return string
     */
    private function getIconPath(int $processor)
    {
        return __DIR__ . '/icons/' . self::$supported_cc_types[$processor] . '.png';
    }
}
