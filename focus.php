<?php

/**
 * Focus plugin
 *
 * @package   Kirby CMS
 * @author    Flo Kosiol <git@flokosiol.de>
 * @link      http://flokosiol.de
 * @version   1.0.9
 */

include __DIR__ . DS . 'core' . DS . 'focus.php';
include __DIR__ . DS . 'methods' . DS . 'file.php';
include __DIR__ . DS . 'drivers' . DS . 'gd.php';
include __DIR__ . DS . 'drivers' . DS . 'im.php';

$kirby->set('field', 'focus', __DIR__ . DS . 'fields' . DS . 'focus');
