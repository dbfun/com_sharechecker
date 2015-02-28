<?php

class SharecheckerChecker {

  const
    CHECK_OK = 1,
    CHECK_NO = 2,
    CHECK_UNDEFINED = 3;

  private
    $db,
    $sharedLinks = array(),
    $params;

  public
    $numChecked = 0, // Число проверенных материалов
    $numBrokenLinks = 0, // число битых ссылок
    $numUndefinedLinks = 0, // число ссылок с неудачной проверкой
    $numGoodLinks = 0; // число рабочих ссылок

  public function __construct() {
    $this->db = JFactory::getDBO();
  }

  public function checkLinks($limit = 5) { // проверка ссылок
    $this->params = JComponentHelper::getParams('com_sharechecker');
    if(!$this->getContent($limit)) return;
    $this->checkUris();
    $this->setUris();
    $this->numChecked = count($this->results);
  }

  private function checkUris() {
    foreach ($this->results as $result) {
      $sharedLinks = unserialize($result->uri);

      foreach ($sharedLinks as $link) {
        $share = SharecheckerSharesite::getShare($link);
        if($share !== null && method_exists($this, $share['method'])) {
          $shareCkeckStatus = $this->{$share['method']}($link);
          switch ($shareCkeckStatus) {
            case self::CHECK_NO:
              $this->numBrokenLinks++;
              $this->sharedLinks[$result->id]['broken'][] = $link;
              break;
            case self::CHECK_UNDEFINED:
              $this->numUndefinedLinks++;
              $this->sharedLinks[$result->id]['undefined'][] = $link;
              break;
            case self::CHECK_OK:
              $this->numGoodLinks++;
              $this->sharedLinks[$result->id]['good'][] = $link;
              break;
          }
        }
      }
    }
  }

  private function setUris() {
    foreach ($this->sharedLinks as $id => $uriSet) {
      $_countBL = count($uriSet['broken']) == 0 ? "''": "'".addslashes(serialize($uriSet['broken']))."'";
      $_countUL = count($uriSet['undefined']) == 0 ? "''": "'".addslashes(serialize($uriSet['undefined']))."'";
      $query = "UPDATE `#__content`
                SET `uri_check_date` = NOW(),
                `error_uri` = {$_countBL},
                `undefined_uri` = {$_countUL}
                WHERE `id` = {$id}";
      $this->db->setQuery($query);
      $this->db->query();
    }


  }

  // выбираем не удаленные, обновленные со ссылками
  private function getContent($limit) {
    $interval = $this->params->get('links_check_interval');
    $db = JFactory::getDBO();
    $query = "
      SELECT `id`, `uri`
      FROM `#__content`
      WHERE `state` <>-2 AND `uri` <> ''
      AND (`uri_check_date` = '0000-00-00 00:00:00' OR `uri_check_date` < ADDDATE(NOW(), INTERVAL ".$interval.") OR `undefined_uri` <> '')
      ORDER BY `uri_check_date` LIMIT ".$limit;
    $db->setQuery($query);
    $this->results = $db->loadObjectList();
    return is_array($this->results) && count($this->results) != 0;
  }

  private $letitbitApiKeyOk = true;
  private function letitbitCheck($uri) {
    // return self::CHECK_NO;
    if(!$this->letitbitApiKeyOk) return self::CHECK_UNDEFINED;

    $data = 'r=["'.$this->params->get('letitbit_api_key').'",["download/check_link", {"link": "' . $uri . '"}]]';

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL             => 'https://api.letitbit.net/',
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_FOLLOWLOCATION  => false,
      CURLOPT_HTTPGET         => true,
      CURLOPT_POST            => true,
      CURLOPT_POSTFIELDS      => $data,
      CURLOPT_SSL_VERIFYHOST  => false,
      CURLOPT_SSL_VERIFYPEER  => false,
      CURLOPT_CONNECTTIMEOUT  => 60,
      CURLOPT_TIMEOUT         => 60
    ));
    $response = curl_exec($curl);

    if(empty($response)) return self::CHECK_UNDEFINED;

    $response = json_decode($response);
    if($response->status == 'FAIL' && $response->data == 'key exhausted') {
      $this->letitbitApiKeyOk = false;
      global $mainframe;
      $mainframe->enqueueMessage('Letitbit API: key exhausted');
      return self::CHECK_UNDEFINED;
    }
    if($response->status != 'OK') return self::CHECK_UNDEFINED;
    if(!isset($response->data[0])) throw new Exception("Wrang response");
    return $response->data[0] > 0 ? self::CHECK_OK : self::CHECK_NO;
  }

}