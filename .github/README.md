# Kirby Focus

![Version](https://img.shields.io/badge/Version-3.1.0-blue.svg) ![License](https://img.shields.io/badge/License-MIT-green.svg) ![Kirby](https://img.shields.io/badge/Kirby-3.6.x-f0c674.svg)

With this plugin for [Kirby CMS](http://getkirby.com) you can prevent the most important part of an image from being cropped when creating automated thumbs.

## Kirby 4

> [!NOTE]
> ✨ **A core feature of Kirby 4** ✨
>
> As of Kirby 4 this plugin has moved into core! Please find more infos on the [official Kirby feature page](https://getkirby.com/docs/guide/files#setting-a-focus-point).
> 
> There is a documented way [how to migrate](https://github.com/flokosiol/kirby-focus/issues/75) the existing data of your project using regex search and replace. Thanks [@FynnZW](https://github.com/FynnZW)!  
> And [@bnomei](https://github.com/bnomei) created a [Kirby CLI command](https://github.com/flokosiol/kirby-focus/issues/75#issuecomment-1834419319) to automate this process even further.
>
> **Thanks for your amazing feedback in the last years!**



## Kirby 3

**The plugin does two things:**

1. It provides a **custom field** that allows you to set a focus point. The focus point is saved to the meta data file.
2. It provides a **new method** `focusCrop()`, which uses the focus point saved in the meta data file to crop an image in such a way that the focus point is in the center of the cropped image – or (if that's not possible) at least isn't cropped.

## ⚙️ Requirements

+ Kirby CMS, Version **3.6**
+ GD Library or ImageMagick

Please check out the `kirby-3.5` branch, if you are using [Kirby 3.5 or a version even older](https://github.com/flokosiol/kirby-focus/tree/kirby-3.5) than that.

## 💰 Commercial Usage

This plugin is free but if you use it in a commercial project please consider to [make a donation](https://www.paypal.me/flokosiol/10).

## 🛠️ Installation

### Download

Download and extract this repository, rename the folder to `focus` and drop it into the plugins folder of your Kirby 3 installation. You should end up with a folder structure like this:

```
site/plugins/focus/
```

### Composer

If you are using Composer, you can install the plugin with

```
composer require flokosiol/focus
```

### Git submodule

```
git submodule add https://github.com/flokosiol/kirby-focus.git site/plugins/focus
```

## 🖼️ Usage

### 1. Blueprint

Add the focus field to the **fields of your [file blueprint](https://getkirby.com/docs/reference/panel/blueprints/file) (!)** and set type to `focus` like this:

```
fields:
  focus:
    label: My Focus Field
    type: focus
    width: 1/2
```

### 2. Template

Use the `focusCrop()` method in your template to get a complete `<img>` tag:

```
<?php
  // you need a Kirby image object like this
  $image = $page->images()->first();

  // crop a square of 200px x 200px
  echo $image->focusCrop(200);

  // crop a rectangle of 300px x 200px
  echo $image->focusCrop(300, 200);

  // crop a rectangle of 200px x 400px with a quality of 80%
  echo $image->focusCrop(200, 400, ['quality' => 80]);

  // crop a grayscale square of 300px x 300px
  echo $image->focusCrop(300, 300, ['grayscale' => true]);

  // crop a rectangle of 200px x 300px and force coordinates (overrides user input)
  echo $image->focusCrop(200, 300, ['focusX' => 0.3, 'focusY' => 0.6]);
?>
```

As with every Kirby **image object** you can use all the known [methods](https://getkirby.com/docs/cheatsheet/file) like this:

```
<?php
  $url = $image->focusCrop(200, 300)->url();
  $filename = $image->focusCrop(150)->filename();
?>
```

### Some more stuff …

The plugin comes with some helper methods to get the x and y coordinates as floats or percentage values.

```
<?php
  $x = $image->focusX();
  $y = $image->focusY();

  $x = $image->focusPercentageX();
  $y = $image->focusPercentageY();
?>
```

### Focus (without cropping)

As mentioned by several people ([Matthias](https://forum.getkirby.com/t/focus-define-an-image-focus-point/4249/11?u=flokosiol), [Guillaume](https://forum.getkirby.com/t/focus-define-an-image-focus-point/4249/53?u=flokosiol) and [Ola](https://forum.getkirby.com/t/focus-define-an-image-focus-point/4249/71?u=flokosiol)) the plugin can also be used to set a custom background position without cropping the image.

```
<div style="background-image: url(<?php echo $image->url() ?>); background-size: cover; background-position: <?php echo $image->focusPercentageX() ?>% <?php echo $image->focusPercentageY() ?>%;"></div>

<img src="<?php echo $image->url() ?>" style="object-fit: cover; object-position: <?php echo $image->focusPercentageX() ?>% <?php echo $image->focusPercentageY() ?>%;" />

```

### Presets

If you are using the same config for focus-cropped images over and over again in your project, as of version 3.0.3 you can define them as presets in your `config.php` like this:

```
return [
  'flokosiol' => [
    'focus' => [
      'presets' => [
        'square' => [
          'width'=> 500
        ],
        'rectangle' => [
          'width'=> 500,
          'height'=> 300,
          'options' => [
            'grayscale' => true
          ]
        ]
      ]
    ]
  ]
];
```

Afterwards, you can use the presets in your templates (assuming `$image` is a Kirby image object).

```
<?= $image->focusPreset('square') ?>
<?= $image->focusPreset('rectangle') ?>
```

### Focus and `srcset` becomes `focusSrcset()`

As of Kirby 3.2 a [new `srcset` method was introduced](https://getkirby.com/docs/reference/objects/file/srcset#example__more-complex-sizes-options). Since version 3.0.3 of the Focus plugin, you can use the following syntax in your templates to respect the focus point in your srcset options:

```
<img 
  src="<?= $image->focusCrop(1000, 1000)->url() ?>"
  srcset="<?=
    $image->focusSrcset([
      '800w' => [
        'width' => 800,
        'height' => 800,
      ],
      '1400w' => [
        'width' => 1400,
        'height' => 1400,
      ]
    ]);
  ?>"
>
```

As of version 3.0.7 you can furthermore define your srcsets in your `config.php`.

```
return [
  'flokosiol' => [
    'focus' => [
      'srcsets' => [
        'homer' => [
          '800w' => [
            'width' => 800,
            'height' => 800,
          ],
          '1400w' => [
            'width' => 1400,
            'height' => 1400,
          ]
        ],
        'bart' => [
          '100w' => [
            'width' => 100,
            'height' => 100,
          ],
          '900w' => [
            'width' => 900,
            'height' => 900,
          ]
        ]
      ]
    ]
  ]
];
```

In your template file you can use the defined srcset like this:

```
<?= $image->focusSrcset('bart') ?>
``` 


## ➕ Extensions and implementations

### Autofocus

[Sylvain](https://github.com/sylvainjule) created the first Focus extension. Make sure to check it out!

> This plugin acts as a JS image.upload / image.replace hook, processing the / each image with the focus component, determining its appropriate focus point and saving it to the meta data file.

[https://github.com/sylvainjule/kirby-autofocus](https://github.com/sylvainjule/kirby-autofocus)

### Colorist

[Kirby Colorist](https://github.com/fundevogel/kirby3-colorist) by @S1SYPHOS is fully compatible with Kirby Focus. This plugin is capable of image conversion to `AVIF`, `WebP` and some other formats, as well as resizing them.

## 📋 License

[MIT](https://github.com/flokosiol/kirby-focus/blob/master/LICENSE)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

## 🙌 Credits

Special thanks to all [contributors](https://github.com/flokosiol/kirby-focus/graphs/contributors)!
	
