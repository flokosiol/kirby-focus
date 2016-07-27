<?php

/**
 * Focus field
 *
 * @package   Kirby CMS
 * @author    Flo Kosiol <git@flokosiol.de>
 * @link      http://flokosiol.de
 * @version   1.0
 */

class FocusField extends InputField {

  public $type = 'focus';
  public $readonly = TRUE;
  public $decoded = FALSE;
  
  // assets
  static public $assets = array(
    'js' => array(
      'focus.js'
    ),
    'css' => array(
      'focus.css'
    ),
  );

  // load current file
  public function file() {
    if (!empty(panel()->route->arguments[1])) {
      $fileName = urldecode(panel()->route->arguments[1]);
      return $this->page()->file($fileName);
    }
    return NULL;
  }

  // decode stored value
  public function decoded() {
    if (!empty($this->value())) {
      return json_decode($this->value());
    }
    return FALSE;
  }

  // x coordinate in percentage
  public function x() {
    $decoded = $this->decoded();
    if (isset($decoded->x)) {
      return $decoded->x * 100;
    }
    return 50;
  }

  // y coordinate in percentage
  public function y() {
    $decoded = $this->decoded();
    if (isset($decoded->y)) {
      return $decoded->y * 100;
    }
    return 50;
  }

  // preview image
  public function preview() {
    $preview = new Brick('div');
    $preview->addClass('focus-preview');

    $data = array(
      'field' => $this,
      'file'  => $this->file(),
      'x'     => $this->x(),
      'y'     => $this->y(),
    );

    $preview->html(tpl::load(__DIR__ . DS . 'template.php', $data));
    
    return $preview;
  }

  // custom content brick to add unique id for js targeting
  public function content() {
    $content = new Brick('div');
    $content->addClass('field-content');
    $content->attr('id','js-field-focus');
    $content->append($this->input());
    return $content;
  }

  // Template
  public function template() {

    // field is used for pages
    if (!$this->file()) {
      return FALSE;
    }

    // file can't have thumb
    if ($this->file() && !$this->file()->canHaveThumb()) {
      return FALSE;
    }

    return $this->element()
      ->append($this->label())
      ->append($this->preview())
      ->append($this->content())
      ->append($this->help());
  }

}
