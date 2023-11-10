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
    /** @var array */
    private $layout;

    /* Give every CC icon an ID to reference */
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

    /* Give every cryptocurrency icon an ID to reference */
    const BTC = 1001;
    const LTC = 1002;
    const BCH = 1003;
    const BNB = 1004;
    const ETH = 1005;
    const USDT = 1006;
    const USDC = 1007;

    /** Link supported CC string representations to constants */
    protected static $cc_strings = [
        // Credit Cards
        "VISA"       => self::VISA,
        "MASTERCARD" => self::MASTERCARD,
        "MC"         => self::MASTERCARD,
        "MAESTRO"    => self::MAESTRO,
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
        "POSTEPAY"   => self::POSTEPAY,      // Italian Post Office

        // Cryptocurrencies
        "BTC"        => self::BTC,
        "LTC"        => self::LTC,
        "BCH"        => self::BCH,
        "BNB"        => self::BNB,
        "ETH"        => self::ETH,
        "USDT"       => self::USDT,
        "USDC"       => self::USDC,
    ];

    /** Link IDs to their file name in src/icons/ */
    protected static $supported_cc_types = [
        // Credit Cards
        self::AMEX       => "amex",
        self::DANKORT    => "dankort",
        self::DINERSCLUB => "dinersclub",
        self::DISCOVER   => "discover",
        self::JCB        => "jcb",
        self::MAESTRO    => "maestro",
        self::MASTERCARD => "mastercard",
        self::POSTEPAY   => "postepay",
        self::UNIONPAY   => "unionpay",
        self::VISA       => "visa",

        // Cryptocurrencies
        self::BTC        => "btc",
        self::LTC        => "ltc",
        self::BCH        => "bch",
        self::BNB        => "bnb",
        self::ETH        => "eth",
        self::USDT       => "usdt",
        self::USDC       => "usdc",
    ];

    /** Width/height of the icon files in src/icons, must be divisible by 4 */
    const ICON_SIZE = 200;

    /** The default amount of padding used with the default layout */
    const DEFAULT_PADDING = 10;

    /** The default layout to be used, indexed by number of icons */
    const DEFAULT_LAYOUT = [
        1 => [1],
        2 => [2],
        3 => [2, 1],
        4 => [2, 2],
        5 => [3, 2],
        6 => [3, 3],
    ];

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

        $this->width = 3 / 2 * self::ICON_SIZE;
        $this->height = self::ICON_SIZE;
        $this->processors = [];
        $this->padding = self::DEFAULT_PADDING;
        $this->layout = self::DEFAULT_LAYOUT;
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
     * Override one or more default layouts. Expects an array with keys corresponding to the layout to override.
     *
     * Example:
     *
     * [
     *      4 => [1, 2, 1]
     *      5 => [2, 3]
     * ]
     * @param array $override The icon layouts to use
     * @return $this
     */
    public function withLayout(array $override)
    {
        $this->layout = array_replace($this->layout, $override);
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

        $this->img = $this->manager->canvas($this->width, $this->height);
        // Use default layout if none specified
        $layout = $this->layout[count($this->processors)];

        // Height is (total height - total padding) / number of rows
        $row_height = ($this->height - (count($layout) - 1) * $this->padding) / count($layout);
        // Padding is (total height - total padding - total row height) / 2
        $top_padding = ($this->height - (count($layout) * ($row_height + $this->padding) - $this->padding)) / 2;

        // Get scaled icon size to fit all icons in a row size $this->width x $row_height
        $max_icons = max($layout);
        $icon_size = min(
            ($this->width - ($max_icons - 1) * $this->padding) / $max_icons,
            $row_height
        );

        $processors_added = 0;
        foreach ($layout as $i => $count) {
            $row = $this->makeRow(
                array_slice($this->processors, $processors_added, $count),
                $row_height,
                round($icon_size)
            );
            // Offset from top by padding amount + $i * (row height + padding)
            $this->img->insert($row, 'top-center', 0, round($top_padding + $i * ($this->padding + $row_height)));
            $processors_added += $count;
        }
    }

    /**
     * Creates an image containing one row of processor icons
     * @param array $processors List of processors to be included
     * @param int $row_height The height of the row
     * @param int $icon_size The size of icons to insert
     * @return Image
     */
    private function makeRow(array $processors, int $row_height, int $icon_size)
    {
        $row = $this->manager->canvas($this->width, $row_height);

        // Divide available width evenly among icons as long as they are shorter than the row height
        $processor_count = count($processors);

        // Padding on either side of the row of icons
        $left_padding = ($this->width - ($processor_count * ($icon_size + $this->padding) - $this->padding)) / 2;

        foreach ($processors as $i => $processor) {
            $processor_icon = $this->manager->make($this->getIconPath($processor));
            $processor_icon->widen($icon_size);
            // Offset from left by padding amount + $i * (size of icon + padding)
            $row->insert(
                $processor_icon,
                'left',
                round($left_padding + $i * ($icon_size + $this->padding))
            );
        }

        return $row;
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
