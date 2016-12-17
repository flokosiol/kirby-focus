<?php

/**
 * Overriding default ImageMagick Driver
 */
thumb::$drivers['im'] = function($thumb) {
  $command = array();

  $command[] = isset($thumb->options['bin']) ? $thumb->options['bin'] : 'convert';
  $command[] = '"' . $thumb->source->root() . '"';
  $command[] = '-strip';

  if ($thumb->options['interlace']) {
    $command[] = '-interlace line';
  }

  if ($thumb->source->extension() === 'gif') {
    $command[] = '-coalesce';
  }

  if ($thumb->options['grayscale']) {
    $command[] = '-colorspace gray';
  }

  if (isset($thumb->options['focus']) && isset($thumb->options['fit']) && isset($thumb->options['ratio']) && isset($thumb->options['focusX']) && isset($thumb->options['focusY'])) {

    // calculate crop coordinates and width/height for the original image
    $focusCropValues = focus::cropValues($thumb);

    // crop original image with thumb ratio and resize it to thumb dimensions
    $command[] = '-crop ' . $focusCropValues['width'] . 'x' . $focusCropValues['height'] . '+' . $focusCropValues['x1'] . '+' . $focusCropValues['y1'];
    $command[] = '-resize "' . $thumb->options['width'] . 'x' . $thumb->options['height'] . '^"';
  }
  else if ($thumb->options['crop']) {
    if (empty($thumb->options['height'])) {
      $thumb->options['height'] = $thumb->options['width'];
    }
    $command[] = '-resize';
    $command[] = '"' . $thumb->options['width'] . 'x' . $thumb->options['height'] . '^"';
    $command[] = '-gravity Center -crop ' . $thumb->options['width'] . 'x' . $thumb->options['height'] . '+0+0';
  } 
  else {
    $dimensions = clone $thumb->source->dimensions();
    $dimensions->fitWidthAndHeight($thumb->options['width'], $thumb->options['height'], $thumb->options['upscale']);
    $command[] = '-resize';
    $command[] = $dimensions->width() . 'x' . $dimensions->height() . '!';
  }

  $command[] = '-quality ' . $thumb->options['quality'];

  if ($thumb->options['blur']) {
    $command[] = '-blur 0x' . $thumb->options['blurpx'];
  }

  // https://forum.getkirby.com/t/auto-orient-ftp-imagemagick/2367/13?u=flokosiol
  if ($thumb->options['autoOrient']) {
    $command[] = '-auto-orient';
  }

  $command[] = '-limit thread 1';
  $command[] = '"' . $thumb->destination->root . '"';

  exec(implode(' ', $command));
  
};
