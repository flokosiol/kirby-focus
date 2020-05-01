<?php

load([
    'flokosiol\\focus' => 'src/Focus.php',
    'flokosiol\\focus\\gdlib' => 'src/Focus/GdLib.php',
    'flokosiol\\focus\\imagemagick' => 'src/Focus/ImageMagick.php'
], __DIR__);

// use Kirby\Cms\App;
use Kirby\Image\Darkroom;

Kirby\Image\Darkroom::$types['gd'] = 'Flokosiol\Focus\GdLib';
Kirby\Image\Darkroom::$types['im'] = 'Flokosiol\Focus\ImageMagick';

Kirby::plugin('flokosiol/focus', [
    // 'components' => [
    //     'thumb' => function (App $kirby, string $src, string $dst, array $options): string {
    //         if (isset($options['focus'])) {
    //             Kirby\Image\Darkroom::$types['gd'] = 'Flokosiol\Focus\GdLib';
    //             Kirby\Image\Darkroom::$types['im'] = 'Flokosiol\Focus\ImageMagick';
    //         }

    //         // @see kirby/config/components.php
    //         $core = require $kirby->root('kirby') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'components.php';
    //         return $core['thumb']($kirby, $src, $dst, $options);
    //     },
    // ],
    'fields' => [
        'focus' => [
            'props' => [
                'value' => function ($value = '{"x":0.5,"y":0.5}') {
                    return $value;
                }
            ],
            'computed' => [
                'isFileBlueprint' => function() {
                    $fileTypes = ['image','document','archive','code','video','audio'];
                    return in_array($this->model()->type(), $fileTypes);
                },
                'image' => function() {
                    if ($this->model()->type() == "image") {
                        return $this->model()->url();
                    }
                    else {
                        return false;
                    }
                },
                'video' => function() {
                    if ($this->model()->type() == "video") {
                        return [
                            'url'  => $this->model()->url(),
                            'mime' => $this->model()->mime()
                        ];
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
            return Flokosiol\Focus::focusCrop($this, $width, $height, $options);
        },
        'focusSrcset' => function ($sizes = null) {
            return Flokosiol\Focus::focusSrcset($this, $sizes);
        },
        'focusPreset' => function (string $preset) {
            $config = $this->kirby()->option('flokosiol.focus.presets.' . $preset);

            if (isset($config['width'])) {
                $width   = $config['width'];
                $height  = $config['height'] ?? null;
                $options = $config['options'] ?? null;

                return $this->focusCrop($width, $height, $options);
            }
            return '';
        }
    ]
]);
