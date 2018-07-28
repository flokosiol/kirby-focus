# Kirby Focus

![Version](https://img.shields.io/badge/Version-0.1.0-orange.svg) ![Kirby](https://img.shields.io/badge/Kirby-3.x-red.svg)

This is the **k-next** experimental branch for Kirby Focus. You need to have access to the beta version of Kirby 3.

[More information about Kirby Next](https://getkirby.com/next).



## Requirements

+ Kirby CMS, Version **3.x Beta**

## Work in progress!

So far, the panel implementation already works and you can place the known methods like `<?= $image->focusCrop(200) ?>` in your plugins. **BUT** the cropping does not work. You will get the default thumb! Stay tuned for updates.


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
