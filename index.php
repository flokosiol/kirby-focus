<?php

load([
    'flokosiol\\focus' => 'src/Focus.php',
    'flokosiol\\focus\\gdlib' => 'src/Focus/GdLib.php',
    'flokosiol\\focus\\focus' => 'src/Focus/ImageMagick.php'
], __DIR__);

// use Exeption;
use Kirby\Toolkit\F;
use Kirby\Cms\Filename;
use Kirby\Image\Darkroom;

Kirby\Image\Darkroom::$types['gd'] = 'Flokosiol\Focus\GdLib';
Kirby\Image\Darkroom::$types['im'] = 'Flokosiol\Focus\ImageMagick';

Kirby::plugin('flokosiol/focus', [
    'fields' => [
        'focus' => [
            'props' => [
                'value' => function ($value = '{"x":0.5,"y":0.5}') {
                    return $value;
                }
            ],
            'computed' => [
                'image' => function() {
                    if ($this->model()->type() == "image") {
                        return $this->model()->url();
                    }
                    else {
                        return false;
                    }
                }
            ]
        ]
    ],
    'fileMethods' => [
        'focusX' => function () {
            return Flokosiol\Focus::coordinates($this, 'x');
        },
        'focusY' => function () {
            return Flokosiol\Focus::coordinates($this, 'y');
        },
        'focusPercentageX' => function (int $roundTo = 1) {
            $focusX = $this->focusX();
            return ($roundTo * ceil($focusX * 100 / $roundTo));
        },
        'focusPercentageY' => function (int $roundTo = 1) {
            $focusY = $this->focusY();
            return ($roundTo * ceil($focusY * 100 / $roundTo));
        },
        'focusCrop' => function (int $width, int $height = null, $options = []) {

            // width and height - if no height is given use width to crop a square
            $options['width'] = $width;
            $options['height'] = ($height) ? $height : $width;

            // determine aspect ratios
            $ratioSource = Flokosiol\Focus::ratio($this->width(), $this->height());
            $ratioThumb  = Flokosiol\Focus::ratio($options['width'], $options['height']);

            // no cropping necessary
            if ($ratioSource == $ratioThumb) {
                return $this->thumb($options);
            }

            $options['focus'] = true;
            $options['ratio'] = Flokosiol\Focus::numberFormat($ratioThumb);
            $options['fit']   = ($ratioThumb < $ratioSource) ? 'height' : 'width';

            // if forced coordinate is set, use it - otherwise look at file field values or use center as default
            $options['focusX'] = (!empty($options['focusX'])) ? Flokosiol\Focus::numberFormat($options['focusX']) : Flokosiol\Focus::coordinates($this, 'x');
            $options['focusY'] = (!empty($options['focusY'])) ? Flokosiol\Focus::numberFormat($options['focusY']) : Flokosiol\Focus::coordinates($this, 'y');

            // convert localized floats
            $options['focusX'] = Flokosiol\Focus::numberFormat($options['focusX']);
            $options['focusY'] = Flokosiol\Focus::numberFormat($options['focusY']);

            // set crop value to force cropping in Darkroom::preprocess
            $options['crop'] = $options['focusX'] * 100 . '-' . $options['focusY'] * 100;

            // filename with hash
            // $hash = option('flokosiol.focus.filename.hash', false);
            // if ($hash) {
            //     $options['crop'] = md5(serialize($options));
            // }

            return $this->thumb($options);
        }
    ]
]);
