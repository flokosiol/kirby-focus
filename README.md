# Kirby Focus

![Version](https://img.shields.io/badge/Version-3.0.1.alpha1-green.svg) ![Kirby](https://img.shields.io/badge/Kirby-3.x-red.svg)

This is the **k-next** experimental branch for Kirby Focus. You need to have access to the alpha version of Kirby 3.

[More information about Kirby Next](https://getkirby.com/next).



## Requirements

+ Kirby CMS, Version **3.x Alpha 2**

## Proof of concept

So far, it is **just a first idea of the Panel implementation**. This means you will only get a preview of the backend part of the focus field. You can't use it in templates and you can't make use of the know methods like `<?= $image->focusCrop(200) ?>`


## Installation

+ copy the content of `panel.js` to `/assets/js/panel.js`
+ copy the content of `panel.css` to `/assets/css/panel.css`


Add the focus field to the **file fields** of your blueprint to set name and type to `focus` like this:

```
fields:
  -
    label: My Focus Field
    name: focus
    type: focus
```
