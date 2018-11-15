# Kirby Focus

![Version](https://img.shields.io/badge/Version-0.2.0-orange.svg) ![Kirby](https://img.shields.io/badge/Kirby-3.x-red.svg)

This is the **k-next** experimental branch for Kirby Focus. You need to have access to the beta version of Kirby 3.

[More information about Kirby Next](https://getkirby.com/next).



## Requirements

+ Kirby CMS, Version **3.x Beta 5+** (or the latests dev branch)

## Work in progress!

The plugin seem to work fine, but I still need to do some more testing. If you find any errors, please feel free to create an issue here on Github. Thanks!


## Installation

+ download and extract the files from this branch
+ rename the folder to `focus` and drop it into the `site/plugins/` folder of your Kirby 3 installation

Add the focus field to the **file fields** of your blueprint and set type to `focus` like this:

```
fields:
  focus:
    label: My Focus Field
    type: focus
```

Use the the `focusCrop()` method in your templates like this:

```
<?= $image->focusCrop(200) ?>
<?= $image->focusCrop(200, 300) ?>
<?= $image->focusCrop(150, 150, ['grayscale' => true]) ?>
```
