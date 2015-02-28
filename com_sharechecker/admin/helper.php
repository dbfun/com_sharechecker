<?php
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SharecheckerFinder.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SharecheckerChecker.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SharecheckerSharesite.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SharecheckerStatistics.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SharecheckerUri.php');

/*

SELECT `uri`, `uri_date`, `uri_check_date`, `error_uri`, `undefined_uri` FROM `ok_content`;


 // Период времени, в течение которого ссылка считается рабочей и не проверяется // - 1 HOUR
 // Секретный код для запуска задач по расписанию



class SharecheckerHelper {

  private static $errorResponses = array('This file was deleted', 'File not found',
      'Такого файла не существует или он был удален из-за нарушения авторских прав',
      'Такого файла не существует, доступ к нему ограничен или он был удален из-за нарушения авторских прав', // проверено для depositfiles
      'Файл не найден в базе i-FileZ.com Возможно Вы неправильно указали ссылку',
      'Запрашиваемый файл не найден', // проверено для letitibit
      'Файл не найден. Возможно он был удален',
      'Запрашиваемая вами страница не существует',
      'The file link that you requested is not valid',
      'Файл удален владельцем');

  private function oldCheck($uri) {
    if($curl != curl_init()) die('Не установлен CURL');
    $isBroken = false;

    curl_setopt_array($curl, array(
      CURLOPT_URL             => $uri,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_FOLLOWLOCATION  => false,
      CURLOPT_HTTPGET         => true
      ));
    $server_response = $this->curl_redir_exec($curl);
    $server_response_utf = iconv('windows-1251', 'utf-8', $server_response);

    foreach (self::$errorResponses as $error_response) {
      if (JString::strpos($server_response, $error_response) !== FALSE || JString::strpos($server_response_utf, $error_response) !== FALSE) {
        $isBroken = true;
        break;
      }
    }
    return $isBroken ? self::CHECK_NO : self::CHECK_OK;
  }

  function curl_redir_exec($ch) {
    static $curl_loops = 0;
    static $curl_max_loops = 20;
    if ($curl_loops   >= $curl_max_loops) {
    $curl_loops = 0;
      return FALSE;
    }
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    list($header, $data) = explode("\n\n", $data, 2);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 301 || $http_code == 302)
    {
    $matches = array();
      preg_match('/Location:(.*?)/', $header, $matches);
    $url = @parse_url(trim(array_pop($matches)));
    if (!$url) {
      //couldn't process the url to redirect to
      $curl_loops = 0;
      return $data;
    }
    $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
    if (!$url['scheme'])
      $url['scheme'] = $last_url['scheme'];
    if (!$url['host'])
      $url['host'] = $last_url['host'];
    if (!$url['path'])
      $url['path'] = $last_url['path'];
    $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
    curl_setopt($ch, CURLOPT_URL, $new_url);
    //debug('Redirecting to', $new_url);
    $curl_loops++;
    return SharecheckerHelper::curl_redir_exec($ch);
    } else {
      $curl_loops=0;
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 2);
      $data = curl_exec($ch);
      return $data;
    }
  }


}


*/