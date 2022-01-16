<?php

Namespace Flokosiol;

use Kirby\Image;
// use Kirby\Image\Darkroom;

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


    /**
     * Returns the focus-cropped image
     */
    public static function focusCrop($file, $width, $height, $options) {
        // Darkroom::$types['gd'] = 'Flokosiol\Focus\GdLib';
        // Darkroom::$types['im'] = 'Flokosiol\Focus\ImageMagick';

        // width and height - if no height is given use width to crop a square
        $options['width'] = $width;
        $options['height'] = ($height) ? $height : $width;

        // determine aspect ratios
        $ratioSource = Focus::ratio($file->width(), $file->height());
        $ratioThumb  = Focus::ratio($options['width'], $options['height']);

        // no cropping necessary
        if ($ratioSource == $ratioThumb) {
            return $file->thumb($options);
        }

        $options['focus'] = true;
        $options['ratio'] = Focus::numberFormat($ratioThumb);
        $options['fit']   = ($ratioThumb < $ratioSource) ? 'height' : 'width';

        // if forced coordinate is set, use it - otherwise look at file field values or use center as default
        $options['focusX'] = (!empty($options['focusX'])) ? Focus::numberFormat($options['focusX']) : Focus::coordinates($file, 'x');
        $options['focusY'] = (!empty($options['focusY'])) ? Focus::numberFormat($options['focusY']) : Focus::coordinates($file, 'y');

        // convert localized floats
        $options['focusX'] = Focus::numberFormat($options['focusX']);
        $options['focusY'] = Focus::numberFormat($options['focusY']);

        // set crop value to force cropping in Darkroom::preprocess
        $options['crop'] = $options['focusX'] * 100 . '-' . $options['focusY'] * 100;

        // filename with hash
        // $hash = option('flokosiol.focus.filename.hash', false);
        // if ($hash) {
        //     $options['crop'] = md5(serialize($options));
        // }

        return $file->thumb($options);
    }


    /**
     * @see kirby/src/Cms/FileModifications.php
     */
    public static function focusSrcset($file, $sizes = null): ?string
    {
        if (is_string($sizes) === true) {
            $preset = kirby()->option('flokosiol.focus.srcsets.' . $sizes);
            return Focus::focusSrcset($file, $preset);
        }

        // old srcset syntax or no settings => go for default srcset()
        if (empty($sizes) === true || is_array($sizes) === false ) {
            return $file->srcset($sizes);
        }

        $set = [];

        $focusOptions = [
            'focus'  => true,
            'crop'   => $file->focusPercentageX() . '-' . $file->focusPercentageY(),
            'focusX' => $file->focusX(),
            'focusY' => $file->focusY(),
        ];

        foreach ($sizes as $key => $value) {
            if (is_array($value)) {
                $options = $value;
                $condition = $key;

                // add focus options
                $ratioSource = Focus::ratio($file->width(), $file->height());
                $ratioThumb  = Focus::ratio($options['width'], $options['height']);

                $options = $options + $focusOptions;
                $options['ratio'] = Focus::numberFormat($ratioThumb);
                $options['fit']   = ($ratioThumb < $ratioSource) ? 'height' : 'width';
                $options['crop']  = $options['focusX'] * 100 . '-' . $options['focusY'] * 100;

            } elseif (is_string($value) === true) {
                $options = [
                    'width' => $key
                ];
                $condition = $value;
            } else {
                $options = [
                    'width' => $value
                ];
                $condition = $value . 'w';
            }
            // var_dump($options);
            $set[] = $file->thumb($options)->url() . ' ' . $condition;
        }

        return implode(', ', $set);
    }

}
