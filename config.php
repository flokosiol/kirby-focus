<?php

Kirby::plugin('flokosiol/focus', [
    // 'components' => [
    //     'thumb' => function (App $kirby, string $src, string $dst, array $options) {
    //         $thumbRoot = Flokosiol\focus::thumb($src, $dst, $options);
    //         return $thumbRoot;
    //     }
    // ],
    'fields' => [
        'focus' => [
            'props' => [
                'value' => function ($value = '{"x":0.5,"y":0.5}') {
                    return $value;
                },
                'image' => function() {
                    if ($this->model()->type() == "image") {
                        return $this->model()->url();
                    }
                    else {
                        return false;
                    }
                }
            ],
            'computed' => [

            ]
        ]
    ],
    'fileMethods' => [
        'focusX' => function () {
            return Flokosiol\focus::coordinates($this, 'x');
        },
        'focusY' => function () {
            return Flokosiol\focus::coordinates($this, 'y');
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
            // set random crop value to force cropping in Darkroom::preprocess
            $options['crop'] = 'focus';

            // width and height - if no height is given use width to crop a square
            $options['width'] = $width;
            $options['height'] = ($height) ? $height : $width;

            // determine aspect ratios
            $ratioSource = Flokosiol\focus::ratio($this->width(), $this->height());
            $ratioThumb  = Flokosiol\focus::ratio($options['width'], $options['height']);

            // no cropping necessary
            if ($ratioSource == $ratioThumb) {
                return $this->thumb($options);
            }

            $options['focus'] = true;
            $options['ratio'] = Flokosiol\focus::numberFormat($ratioThumb);
            $options['fit']   = ($ratioThumb < $ratioSource) ? 'height' : 'width';

            // if forced coordinate is set, use it - otherwise look at file field values or use center as default
            $options['focusX'] = (!empty($options['focusX'])) ? Flokosiol\focus::numberFormat($options['focusX']) : Flokosiol\focus::coordinates($this, 'x');
            $options['focusY'] = (!empty($options['focusY'])) ? Flokosiol\focus::numberFormat($options['focusY']) : Flokosiol\focus::coordinates($this, 'y');

            // convert localized floats
            $options['focusX'] = Flokosiol\focus::numberFormat($options['focusX']);
            $options['focusY'] = Flokosiol\focus::numberFormat($options['focusY']);

            // create base filename
            $options['filename'] = '{safeName}-' . $options['width'] . 'x' . $options['height'] . '-' . $options['focusX'] * 100 . '-' . $options['focusY'] * 100;

            // quality
            if (isset($options['quality']) && is_numeric($options['quality'])) {
                $options['filename'] .= '-q' . $options['quality'];
            }

            // blur
            if (isset($options['blur']) && $options['blur'] === true) {
                $options['filename'] .= '-blur';
            }

            // grayscale
            if (isset($options['grayscale']) && $options['grayscale'] === true) {
                $options['filename'] .= '-bw';
            }

            // add extension to filename
            $options['filename'] .= '.{extension}';

            // filename with hash
            $hash = option('flokosiol.focus.filename.hash', false);
            if ($hash) {
                $options['filename'] = '{safeName}-' . md5(serialize($options)) . '.{extension}';
            }

            // calculate crop coordinates
            $focusCropValues = Flokosiol\focus::cropValues($this, $options);

            #FIXME: Create image based on GD or IM
            // https://github.com/flokosiol/kirby-focus/tree/master/drivers

            #NOTESTOMYSELF
            // Kirby thumbs component (s.o. + focus::thumb)
            // https://www.notion.so/Core-Components-f7c14536f99e40acbef65fb32616e29b


            // Cms/App.php => thumb()
            // $darkroom->preprocess notwendig?
            // In Image/Darkroom/GDLib.php wird in resize() die SimpleImage thumbnail() Methode genutzt,
            // die wiederrum einen Anker (top left, center, …) benötigt
            // Stattdessen müsste direkt die SimpleImage crop() Methode auf das Originalbild angewendet werden
            // Anschließend das gecroppte Originalbild auf die Thumbnailgröße resizen
            // Alter Code:
            // $img->crop($focusCropValues['x1'], $focusCropValues['y1'], $focusCropValues['x2'], $focusCropValues['y2'])->thumbnail($thumb->options['width'], $thumb->options['height']);

            return $this->thumb($options);
        }
    ]
]);