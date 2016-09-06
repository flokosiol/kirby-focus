<?php

/**
 * Overriding default GDLib Driver
 */
thumb::$drivers['gd'] = function($thumb) {
  try {
    $img = new abeautifulsite\SimpleImage($thumb->root());
    $img->quality = $thumb->options['quality'];

    if (isset($thumb->options['focus']) && isset($thumb->options['fit']) && isset($thumb->options['ratio']) && isset($thumb->options['focusX']) && isset($thumb->options['focusY'])) {
      
      // calculate crop coordinates and width/height for the original image
      $focusCropValues = focus::cropValues($thumb);

      // crop original image with thumb ratio and resize it to thumb dimensions
      $img->crop($focusCropValues['x1'], $focusCropValues['y1'], $focusCropValues['x2'], $focusCropValues['y2'])->thumbnail($thumb->options['width'], $thumb->options['height']);
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

// keep backwards compatibility
thumb::$drivers['focus'] = thumb::$drivers['gd'];
