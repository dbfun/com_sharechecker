<?php

class SharecheckerSharesite {

  private static $sharedSites = array(
    'depositfiles.com' => array('uri' => 'depositfiles.com', 'method' => ''), // TODO
    'letitbit.net' => array('uri' => 'letitbit.net', 'method' => 'letitbitCheck')
  );

  public static function isShared($uri) {
    return preg_match('~'.self::getSharedSitesRegex().'~i', $uri);
  }

  public static function getShare($uri) {
    foreach(self::$sharedSites as $site) {
      if(preg_match('~'.preg_quote($site['uri']).'~i', $uri)) {
        return $site;
      }
    }
    return null;
  }

  private static $sharedSitesRegex;
  private static function getSharedSitesRegex() {
    if(isset(self::$sharedSitesRegex)) return self::$sharedSitesRegex;
    $data = array();
    foreach(self::$sharedSites as $site) {
      $data[] = preg_quote($site['uri']);
    }
    self::$sharedSitesRegex = implode('|', $data);
    return self::$sharedSitesRegex;
  }

}