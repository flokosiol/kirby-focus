<?php

Namespace Flokosiol;

use Kirby\Image;

class Focus {

    /**
     * Calculates the image ratio by dividing width / height
     */
    public static function ratio($width, $height)
    {
        if ($height != 0) {
            return $width / $height;
        }
        return 0;
    }

    /**
    * Correct format, even for localized floats
    */
    public static function numberFormat($number)
    {
        return number_format($number,2,'.','');
    }


    /**
    * Calculates crop coordinates and width/height to crop and resize the original image
    */
    public static function cropValues($options)
    {

        // calculate new height for original image based crop ratio
        if ($options['fit'] == 'width') {
            $width  = $options['originalWidth'];
            $height = floor($options['originalWidth'] / $options['ratio']);

            $heightHalf = floor($height / 2);

            // calculate focus for original image
            $focusX = floor($width * 0.5);
            $focusY = floor($options['originalHeight'] * $options['focusY']);

            $x1 = 0;
            $y1 = $focusY - $heightHalf;

            // $y1 off canvas?
            $y1 = ($y1 < 0) ? 0 : $y1;
            $y1 = ($y1 + $height > $options['originalHeight']) ? $options['originalHeight'] - $height : $y1;

        }

        // calculate new width for original image based crop ratio
        if ($options['fit'] == 'height') {
            $width  = $options['originalHeight'] * $options['ratio'];
            $height = $options['originalHeight'];

            $widthHalf = floor($width / 2);

            // calculate focus for original image
            $focusX = floor($options['originalWidth'] * $options['focusX']);
            $focusY = $height * 0.5;

            $x1 = $focusX - $widthHalf;
            $y1 = 0;

            // $x1 off canvas?
            $x1 = ($x1 < 0) ? 0 : $x1;
            $x1 = ($x1 + $width > $options['originalWidth']) ? $options['originalWidth'] - $width : $x1;
        }

        $x2 = floor($x1 + $width);
        $y2 = floor($y1 + $height);

        return [
            'x1' => $x1,
            'x2' => $x2,
            'y1' => $y1,
            'y2' => $y2,
            'width' => floor($width),
            'height' => floor($height),
        ];
    }


    /**
    * Get the stored coordinates
    */
    public static function coordinates($file, $axis = null)
    {
        $focusCoordinates = [
            'x' => focus::numberFormat(0.5),
            'y' => focus::numberFormat(0.5),
        ];

        $focusFieldKey = option('flokosiol.focus.field.key', 'focus');

        if ($file->$focusFieldKey()->isNotEmpty()) {
            $focus = json_decode($file->$focusFieldKey()->value());
            $focusCoordinates = [
                'x' => focus::numberFormat($focus->x),
                'y' => focus::numberFormat($focus->y),
            ];
        }

        if (isset($axis) && isset($focusCoordinates[$axis])) {
            return $focusCoordinates[$axis];
        }

        return $focusCoordinates;
    }

}
