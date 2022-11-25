<?php

namespace App;

use Exception;
use Imagine\Exception\RuntimeException;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Throwable;

/**
 * 
 * @package App
 */
class ImageOptimizer
{
    private const MAX_WIDTH = 200;
    private const MAX_HEIGHT = 150;

    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * 
     * @param string $filename 
     * @return void 
     * @throws Exception 
     * @throws Throwable 
     * @throws RuntimeException 
     * @throws InvalidArgumentException 
     */
    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);
        $ratio = $iwidth / $iheight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);
    }
}
