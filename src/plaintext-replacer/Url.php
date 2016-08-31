<?php
/**
 * This file is part of Plaintext Replacer library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\PlaintextReplacer;

class Url extends AbstractReplacer
{
  private static function _makeUrlClickable($matches)
  {
    $string = '';
    $url = $matches[2];

    if (empty($url))
      return $matches[0];
    // removed trailing [.,;:] from URL
    if (in_array(substr($url, -1), ['.', ',', ';', ':']) === true) {
      $string = substr($url, -1);
      $url = substr($url, 0, strlen($url) - 1);
    }

    return $matches[1] . '<a href="' . $url . '" rel="nofollow" target="_blank">' . $url . '</a>' . $string;
  }

  private static function _makeFtpClickable($matches)
  {
    $string = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;

    if (empty($dest))
      return $matches[0];
    // removed trailing [,;:] from URL
    if (in_array(substr($dest, -1), ['.', ',', ';', ':']) === true) {
      $string = substr($dest, -1);
      $dest = substr($dest, 0, strlen($dest) - 1);
    }

    return $matches[1] . '<a href="' . $dest . '" rel="nofollow" target="_blank">' . $dest . '</a>' . $string;
  }

  private static function _makeEmailClickable($matches)
  {
    $email = $matches[2] . '@' . $matches[3];

    return $matches[1] . '<a href="mailto:' . $email . '">' . $email . '</a>';
  }

  public static function convert($string)
  {
    $string = ' ' . $string;
    // in testing, using arrays here was found to be faster
    $string = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'self::_makeUrlClickable', $string);
    $string = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'self::_makeFtpClickable', $string);
    $string = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'self::_makeEmailClickable', $string);

    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $string = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $string);
    $string = trim($string);

    return $string;
  }

  private function __construct()
  {

  }

  private function __clone()
  {

  }

  private function __wakeup()
  {

  }
}