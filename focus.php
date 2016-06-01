<?php

/**
 * Focus plugin
 *
 * @package   Kirby CMS
 * @author    Flo Kosiol <git@flokosiol.de>
 * @link      http://flokosiol.de
 * @version   0.2
 */

$kirby->set('field', 'focus', __DIR__ . DS . 'fields' . DS . 'focus');


/**
 * custom field method 'focus' (resize and crop with special focus)
 */
file::$methods['focusCrop'] = function($file, $width, $height = null, $quality = null) {

  // don't scale thumbs further down
  if ($file->original()) {
    throw new Exception('Thumbnails cannot be modified further');
  }

  $params = array();
  $params['width'] = $width;

  // if no height is given use width to crop a square
  $params['height'] = ($height) ? $height : $width;

  // determine aspect ratios
  $ratioSource = $file->height() / $file->width();
  $ratioThumb = $params['height'] / $params['width'];

  if ($ratioSource == $ratioThumb) {
    // no cropping, just resize
    return $file->thumb($params);
  }

  if ($ratioThumb < $ratioSource) {
    // full width, cropped height
    $params['fit'] = 'width';
  } else {
    // full height, cropped width
    $params['fit'] = 'height';
  }

  $params['focus'] = TRUE;
  $params['ratio'] = $ratioThumb;

  // center as default focus
  $params['focusX'] = 0.5;
  $params['focusY'] = 0.5;

  // get name of the focus field
  $focusFieldKey = c::get('focus.field.key', 'focus');

  // get focus from image field
  if ($file->$focusFieldKey()->isNotEmpty()) {
    $focus = json_decode($file->$focusFieldKey()->value());
    $params['focusX'] = $focus->x;
    $params['focusY'] = $focus->y;
  }

  $params['filename'] = '{safeName}-' . $params['width'] . 'x' . $params['height'] . '-' . $params['focusX']*100 . '-' . $params['focusY']*100 . '.{extension}';

  // quality set?
  if ($quality) $params['quality'] = $quality;

  return $file->thumb($params);
};


/**
 * Custom thumb driver based on GDLib Driver
 */
thumb::$drivers['focus'] = function($thumb) {

  try {
    $img = new abeautifulsite\SimpleImage($thumb->root());
    $img->quality = $thumb->options['quality'];

    if (isset($thumb->options['focus']) && isset($thumb->options['fit']) && isset($thumb->options['ratio']) && isset($thumb->options['focusX']) && isset($thumb->options['focusY'])) {

      // get original image dimensions
      $dimensions = clone $thumb->source->dimensions();

      // calculate new height for original image based crop ratio
      if ($thumb->options['fit'] == 'width') {
        $width  = $dimensions->width();
        $height = floor($dimensions->width() * $thumb->options['ratio']);

        $heightHalf = floor($height / 2);

        // calculate focus for original image
        $focusX = floor($width * 0.5);
        $focusY = floor($dimensions->height() * $thumb->options['focusY']);

        $x1 = 0;
        $y1 = $focusY - $heightHalf;

        // $x1 off canvas?
        $y1 = ($y1 < 0) ? 0 : $y1;
        $y1 = ($y1 + $height > $dimensions->height()) ? $dimensions->height() - $height : $y1;

      }

      // calculate new width for original image based crop ratio
      if ($thumb->options['fit'] == 'height') {
        $width  = $dimensions->height() / $thumb->options['ratio'];
        $height = $dimensions->height();

        $widthHalf = floor($width / 2);

        // calculate focus for original image
        $focusX = floor($dimensions->width() * $thumb->options['focusX']);
        $focusY = $height * 0.5;

        $x1 = $focusX - $widthHalf;
        $y1 = 0;

        // $x1 off canvas?
        $x1 = ($x1 < 0) ? 0 : $x1;
        $x1 = ($x1 + $width > $dimensions->width()) ? $dimensions->width() - $width : $x1;

      }

      $x2 = $x1 + $width;
      $y2 = $y1 + $height;

      // crop original image with thumb ratio and resize it to thumb dimensions
      $img->crop($x1, $y1, $x2, $y2)->thumbnail($thumb->options['width'], $thumb->options['height']);
    }
    else if ($thumb->options['crop']) {
      @$img->thumbnail($thumb->options['width'], $thumb->options['height']);
    }
    else {
      $dimensions = clone $thumb->source->dimensions();
      $dimensions->fitWidthAndHeight($thumb->options['width'], $thumb->options['height'], $thumb->options['upscale']);
      @$img->resize($dimensions->width(), $dimensions->height());
    }

    if ($thumb->options['grayscale']) {
      $img->desaturate();
    }

    if ($thumb->options['blur']) {
      $img->blur('gaussian', $thumb->options['blurpx']);
    }

    if ($thumb->options['autoOrient']) {
      $img->auto_orient();
    }

    @$img->save($thumb->destination->root);
  } catch(Exception $e) {
    $thumb->error = $e;
  }
};
