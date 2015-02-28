<?php

class SharecheckerFinder {

  private
    $db,
    $sharedLinks = array(),
    $idsArray = array(),
    $results = array();

  public
    $numChecked = 0, // Число проверенных материалов
    $numFinded = 0; // Число материалов с найденными ссылками

  public function __construct() {
    $this->db = JFactory::getDBO();
  }


  // Проверка материалов на ссылки и добавление их в базу
  public function searchForLinks($limit = 100) {
    if(!$this->getContent($limit)) return;
    $this->extractUris();
    $this->setUris();
    $this->markChecked();
    $this->numChecked = count($this->results);
    $this->numFinded = count($this->sharedLinks);
  }

  private function extractUris() {
    foreach ($this->results as $result) {
      $this->idsArray[] = $result->id;
      $allLinks = $this->getAllLinks($result->text);
      foreach ($allLinks as $link) {
        if (SharecheckerSharesite::isShared($link)) {
          $this->sharedLinks[$result->id][] = $link;
        }
      }
    }
  }

  private function setUris() {
    if(count($this->sharedLinks) == 0) return;
    foreach ($this->sharedLinks as $id => $uris) {
      $query = "UPDATE `#__content` SET `uri` = '".addslashes(serialize($uris))."' WHERE `id` = ".$id;
      $this->db->setQuery($query);
      $this->db->query();
    }
  }

  // вписываем "обновлено" и дату
  private function markChecked() {
    $query = "UPDATE `#__content` SET `uri_date` = NOW(), `uri_check_date` = '0000-00-00 00:00:00', `error_uri` = '', `undefined_uri` = '' WHERE `id` IN(".implode(',', $this->idsArray).")";
    $this->db->setQuery($query);
    $this->db->query();
  }

  private function getContent($limit) {
    $query = "
        SELECT `id`, CONCAT_WS(' ', `introtext`, `fulltext`) AS `text`
        FROM `#__content`
        WHERE `state` <>-2 AND (`uri_date` = '0000-00-00 00:00:00' OR `modified` > `uri_date`) AND
        (`introtext` LIKE '%href%' OR `fulltext` LIKE '%href%')
        ORDER BY `uri_date` LIMIT ".$limit;
    $this->db->setQuery($query);
    $this->results = $this->db->loadObjectList();
    return is_array($this->results) && count($this->results) != 0;
  }

  // все ссылки в тексте
  private function getAllLinks($text) {
    preg_match_all('~<a .*?href.*?=.*?[\"|\']?(https?:\/\/?.*?)?[\"|\'].*?>~si', $text, $matches);
    return $matches[1];
  }





}