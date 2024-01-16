<?php
/**
 * Zend Framework.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */

/** @see Zend_Barcode_Renderer_RendererAbstract*/
// require_once 'Zend/Barcode/Renderer/RendererAbstract.php';

/**
 * Class for rendering the barcode as image.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Renderer_Image extends Zend_Barcode_Renderer_RendererAbstract
{
    /**
     * List of authorized output format.
     *
     * @var array
     */
    protected $_allowedImageType = [
        'png',
        'jpeg',
        'gif',
    ];

    /**
     * Image format.
     *
     * @var string
     */
    protected $_imageType = 'png';

    /**
     * Resource for the image.
     *
     * @var resource
     */
    protected $_resource;

    /**
     * Resource for the font and bars color of the image.
     *
     * @var int
     */
    protected $_imageForeColor;

    /**
     * Resource for the background color of the image.
     *
     * @var int
     */
    protected $_imageBackgroundColor;

    /**
     * Height of the rendered image wanted by user.
     *
     * @var int
     */
    protected $_userHeight = 0;

    /**
     * Width of the rendered image wanted by user.
     *
     * @var int
     */
    protected $_userWidth = 0;

    public function __construct($options = null)
    {
        if (!function_exists('gd_info')) {
            // require_once 'Zend/Barcode/Renderer/Exception.php';
            throw new Zend_Barcode_Renderer_Exception('Zend_Barcode_Renderer_Image requires the GD extension');
        }

        parent::__construct($options);
    }

    /**
     * Set height of the result image.
     *
     * @param int|null $value
     *
     * @return self
     *
     * @throws Zend_Barcode_Renderer_Exception
     */
    public function setHeight($value)
    {
        if (!is_numeric($value) || intval($value) < 0) {
            // require_once 'Zend/Barcode/Renderer/Exception.php';
            throw new Zend_Barcode_Renderer_Exception('Image height must be greater than or equals 0');
        }
        $this->_userHeight = intval($value);

        return $this;
    }

    /**
     * Get barcode height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->_userHeight;
    }

    /**
     * Set barcode width.
     *
     * @return self
     *
     * @throws Zend_Barcode_Renderer_Exception
     */
    public function setWidth($value)
    {
        if (!is_numeric($value) || intval($value) < 0) {
            // require_once 'Zend/Barcode/Renderer/Exception.php';
            throw new Zend_Barcode_Renderer_Exception('Image width must be greater than or equals 0');
        }
        $this->_userWidth = intval($value);

        return $this;
    }

    /**
     * Get barcode width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->_userWidth;
    }

    /**
     * Set an image resource to draw the barcode inside.
     *
     * @return self
     *
     * @throws Zend_Barcode_Renderer_Exception
     */
    public function setResource($image)
    {
        if (('resource' === gettype($image) && 'gd' === get_resource_type($image)) || 'GdImage' === get_class($image)) {
            $this->_resource = $image;

            return $this;
        }
        throw new Zend_Barcode_Renderer_Exception('Invalid image resource provided to setResource()');
    }

    /**
     * Set the image type to produce (png, jpeg, gif).
     *
     * @param string $value
     *
     * @return self
     *
     * @throws Zend_Barcode_Renderer_Exception
     */
    public function setImageType($value)
    {
        if ('jpg' == $value) {
            $value = 'jpeg';
        }

        if (!in_array($value, $this->_allowedImageType)) {
            // require_once 'Zend/Barcode/Renderer/Exception.php';
            throw new Zend_Barcode_Renderer_Exception(sprintf('Invalid type "%s" provided to setImageType()', $value));
        }

        $this->_imageType = $value;

        return $this;
    }

    /**
     * Retrieve the image type to produce.
     *
     * @return string
     */
    public function getImageType()
    {
        return $this->_imageType;
    }

    /**
     * Initialize the image resource.
     *
     * @return void
     *
     * @throws Zend_Barcode_Exception
     */
    protected function _initRenderer()
    {
        if (!extension_loaded('gd')) {
            // require_once 'Zend/Barcode/Exception.php';
            $e = new Zend_Barcode_Exception(
                'Gd extension must be loaded to render barcode as image'
            );
            $e->setIsRenderable(false);
            throw $e;
        }

        $barcodeWidth = $this->_barcode->getWidth(true);
        $barcodeHeight = $this->_barcode->getHeight(true);

        if (null !== $this->_resource) {
            $foreColor = $this->_barcode->getForeColor();
            $backgroundColor = $this->_barcode->getBackgroundColor();
            $this->_imageBackgroundColor = imagecolorallocate(
                $this->_resource,
                ($backgroundColor & 0xFF0000) >> 16,
                ($backgroundColor & 0x00FF00) >> 8,
                $backgroundColor & 0x0000FF
            );
            $this->_imageForeColor = imagecolorallocate(
                $this->_resource,
                ($foreColor & 0xFF0000) >> 16,
                ($foreColor & 0x00FF00) >> 8,
                $foreColor & 0x0000FF
            );
        } else {
            $width = (int) $barcodeWidth;
            $height = (int) $barcodeHeight;
            if ($this->_userWidth && 'error' != $this->_barcode->getType()) {
                $width = (int) $this->_userWidth;
            }
            if ($this->_userHeight && 'error' != $this->_barcode->getType()) {
                $height = (int) $this->_userHeight;
            }

            $foreColor = $this->_barcode->getForeColor();
            $backgroundColor = $this->_barcode->getBackgroundColor();
            $this->_resource = imagecreatetruecolor($width, $height);

            $this->_imageBackgroundColor = imagecolorallocate(
                $this->_resource,
                ($backgroundColor & 0xFF0000) >> 16,
                ($backgroundColor & 0x00FF00) >> 8,
                $backgroundColor & 0x0000FF
            );
            $this->_imageForeColor = imagecolorallocate(
                $this->_resource,
                ($foreColor & 0xFF0000) >> 16,
                ($foreColor & 0x00FF00) >> 8,
                $foreColor & 0x0000FF
            );
            $white = imagecolorallocate($this->_resource, 255, 255, 255);
            imagefilledrectangle($this->_resource, 0, 0, $width - 1, $height - 1, $white);
        }
        $this->_adjustPosition(imagesy($this->_resource), imagesx($this->_resource));
        imagefilledrectangle(
            $this->_resource,
            (int) $this->_leftOffset,
            (int) $this->_topOffset,
            (int) ($this->_leftOffset + $barcodeWidth - 1),
            (int) ($this->_topOffset + $barcodeHeight - 1),
            (int) $this->_imageBackgroundColor
        );
    }

    /**
     * Check barcode parameters.
     *
     * @return void
     */
    protected function _checkParams()
    {
        $this->_checkDimensions();
    }

    /**
     * Check barcode dimensions.
     *
     * @return void
     *
     * @throws Zend_Barcode_Renderer_Exception
     */
    protected function _checkDimensions()
    {
        if (null !== $this->_resource) {
            if (imagesy($this->_resource) < $this->_barcode->getHeight(true)) {
                // require_once 'Zend/Barcode/Renderer/Exception.php';
                throw new Zend_Barcode_Renderer_Exception('Barcode is define outside the image (height)');
            }
        } else {
            if ($this->_userHeight) {
                $height = $this->_barcode->getHeight(true);
                if ($this->_userHeight < $height) {
                    // require_once 'Zend/Barcode/Renderer/Exception.php';
                    throw new Zend_Barcode_Renderer_Exception(sprintf("Barcode is define outside the image (calculated: '%d', provided: '%d')", $height, $this->_userHeight));
                }
            }
        }
        if (null !== $this->_resource) {
            if (imagesx($this->_resource) < $this->_barcode->getWidth(true)) {
                // require_once 'Zend/Barcode/Renderer/Exception.php';
                throw new Zend_Barcode_Renderer_Exception('Barcode is define outside the image (width)');
            }
        } else {
            if ($this->_userWidth) {
                $width = $this->_barcode->getWidth(true);
                if ($this->_userWidth < $width) {
                    // require_once 'Zend/Barcode/Renderer/Exception.php';
                    throw new Zend_Barcode_Renderer_Exception(sprintf("Barcode is define outside the image (calculated: '%d', provided: '%d')", $width, $this->_userWidth));
                }
            }
        }
    }

    /**
     * Draw and render the barcode with correct headers.
     *
     * @return void
     */
    public function render()
    {
        $this->draw();
        header('Content-Type: image/'.$this->_imageType);
        $functionName = 'image'.$this->_imageType;
        call_user_func($functionName, $this->_resource);
        @imagedestroy($this->_resource);
    }

    /**
     * Draw a polygon in the image resource.
     *
     * @param array $points
     * @param int   $color
     * @param bool  $filled
     */
    protected function _drawPolygon($points, $color, $filled = true)
    {
        $newPoints = [
            $points[0][0] + $this->_leftOffset,
            $points[0][1] + $this->_topOffset,
            $points[1][0] + $this->_leftOffset,
            $points[1][1] + $this->_topOffset,
            $points[2][0] + $this->_leftOffset,
            $points[2][1] + $this->_topOffset,
            $points[3][0] + $this->_leftOffset,
            $points[3][1] + $this->_topOffset,
        ];

        $allocatedColor = imagecolorallocate(
            $this->_resource,
            ($color & 0xFF0000) >> 16,
            ($color & 0x00FF00) >> 8,
            $color & 0x0000FF
        );

        if ($filled) {
            if (\PHP_VERSION_ID >= 80100) {
                imagefilledpolygon($this->_resource, $newPoints, $allocatedColor);
            } else {
                imagefilledpolygon($this->_resource, $newPoints, 4, $allocatedColor);
            }
        } else {
            imagepolygon($this->_resource, $newPoints, 4, $allocatedColor);
        }
    }

    /**
     * Draw a polygon in the image resource.
     *
     * @param string    $text
     * @param float     $size
     * @param array     $position
     * @param string    $font
     * @param int       $color
     * @param string    $alignment
     * @param float|int $orientation
     *
     * @throws Zend_Barcode_Renderer_Exception
     */
    protected function _drawText($text, $size, $position, $font, $color, $alignment = 'center', $orientation = 0)
    {
        $allocatedColor = imagecolorallocate(
            $this->_resource,
            ($color & 0xFF0000) >> 16,
            ($color & 0x00FF00) >> 8,
            $color & 0x0000FF
        );

        if (null == $font) {
            $font = 3;
        }
        $position[0] += $this->_leftOffset;
        $position[1] += $this->_topOffset;

        if (is_numeric($font)) {
            if ($orientation) {
                /*
                 * imagestring() doesn't allow orientation, if orientation
                 * needed: a TTF font is required.
                 * Throwing an exception here, allow to use automaticRenderError
                 * to informe user of the problem instead of simply not drawing
                 * the text
                 */
                // require_once 'Zend/Barcode/Renderer/Exception.php';
                throw new Zend_Barcode_Renderer_Exception('No orientation possible with GD internal font');
            }
            $fontWidth = imagefontwidth($font);
            $positionY = $position[1] - imagefontheight($font) + 1;
            switch ($alignment) {
                case 'left':
                    $positionX = $position[0];
                    break;
                case 'center':
                    $positionX = $position[0] - ceil(($fontWidth * strlen((string) $text)) / 2);
                    break;
                case 'right':
                    $positionX = $position[0] - ($fontWidth * strlen((string) $text));
                    break;
            }
            imagestring($this->_resource, $font, $positionX, $positionY, $text, $color);
        } else {
            if (!function_exists('imagettfbbox')) {
                // require_once 'Zend/Barcode/Renderer/Exception.php';
                throw new Zend_Barcode_Renderer_Exception('A font was provided, but this instance of PHP does not have TTF (FreeType) support');
            }

            $box = imagettfbbox($size, 0, $font, $text);
            switch ($alignment) {
                case 'left':
                    $width = 0;
                    break;
                case 'center':
                    $width = ($box[2] - $box[0]) / 2;
                    break;
                case 'right':
                    $width = ($box[2] - $box[0]);
                    break;
            }
            imagettftext(
                $this->_resource,
                $size,
                $orientation,
                (int) ($position[0] - ($width * cos(pi() * $orientation / 180))),
                (int) ($position[1] + ($width * sin(pi() * $orientation / 180))),
                $allocatedColor,
                $font,
                $text
            );
        }
    }
}
