<?php

/**
 * Custom file methods to get the X and Y coordinate
 */
file::$methods['focusX'] = function($file) {
  return focus::coordinates($file, 'x');
};

file::$methods['focusY'] = function($file) {
  return focus::coordinates($file, 'y');
};

file::$methods['focusPercentageX'] = function($file, $roundTo = 1) {
  $focusX = focus::coordinates($file, 'x');
  return ($roundTo * ceil($focusX*100 / $roundTo));
};

file::$methods['focusPercentageY'] = function($file, $roundTo = 1) {
  $focusY = focus::coordinates($file, 'y');
  return ($roundTo * ceil($focusY*100 / $roundTo));
};

/**
 * Custom file method 'focusCrop'
 */
file::$methods['focusCrop'] = function($file, $width, $height = null, $params = array()) {

  // don't scale thumbs further down
  if ($file->original()) {
    throw new Exception('Thumbnails cannot be modified further');
  }

  // keep backwards compatibility with quality set as third argument
  if (!is_array($params)) {
    if (is_numeric($params)) {
      $params = array('quality' => $params);
    }
    else {
      $params = array();
    }
  }

  $params['width'] = $width;

  // if no height is given use width to crop a square
  $params['height'] = ($height) ? $height : $width;

  // determine aspect ratios
  $ratioSource = focus::ratio($file->width(), $file->height());
  $ratioThumb  = focus::ratio($params['width'], $params['height']);

  if ($ratioSource == $ratioThumb) {
    // no cropping, just resize
    return $file->thumb($params);
  }

  if ($ratioThumb < $ratioSource) {
    $params['fit'] = 'height';
  } else {
    $params['fit'] = 'width';
  }

  $params['focus'] = TRUE;
  $params['ratio'] = $ratioThumb;

  // if forced coordinate is set, use it - otherwise look at file field values or use center as default
  $params['focusX'] = (!empty($params['focusX'])) ? focus::numberFormat($params['focusX']) : focus::coordinates($file, 'x');
  $params['focusY'] = (!empty($params['focusY'])) ? focus::numberFormat($params['focusY']) : focus::coordinates($file, 'y');

  // create base filename
  $params['filename'] = '{safeName}-' . $params['width'] . 'x' . $params['height'] . '-' . $params['focusX']*100 . '-' . $params['focusY']*100;

  // quality
  if (isset($params['quality']) && is_numeric($params['quality'])) {
    $params['filename'] .= '-q' . $params['quality'];
  }

  // blur
  if (isset($params['blur']) && $params['blur'] === true) {
    $params['filename'] .= '-blur';
  }

  // grayscale
  if (isset($params['grayscale']) && $params['grayscale'] === true) {
    $params['filename'] .= '-bw';
  }

  // add extension to filename
  $params['filename'] .= '.{extension}';

  // filename with hash
  $hash = c::get('focus.filename.hash', false);
  if ($hash) {
    $params['filename'] = '{safeName}-' . md5(serialize($params)) . '.{extension}';
  }

  // convert localized floats
  $params['ratio'] = focus::numberFormat($params['ratio']);
  $params['focusX'] = focus::numberFormat($params['focusX']);
  $params['focusY'] = focus::numberFormat($params['focusY']);

  return $file->thumb($params);
};
