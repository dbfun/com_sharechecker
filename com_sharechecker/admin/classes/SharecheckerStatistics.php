<?php

class SharecheckerStatistics {
  private $data;
  public function __construct() { // загрузка статистических данных
    $db = JFactory::getDBO();
    $query = "
      SELECT
      (SELECT COUNT(*) FROM `#__content` WHERE `uri_date` <> '0000-00-00 00:00:00') AS `numMarked`,
      (SELECT COUNT(*) FROM `#__content` WHERE `uri` <> '') AS `numFinded`,
      (SELECT COUNT(*) FROM `#__content` WHERE `error_uri` <> '') AS `numBrokenLinks`,
      (SELECT COUNT(*) FROM `#__content` WHERE `undefined_uri` <> '') AS `numUndefinedLinks`,
      (SELECT COUNT(*) FROM `#__content` WHERE
        `uri` <> '' AND `error_uri` = '' AND `undefined_uri` = '' AND `modified` < `uri_date` AND `uri_check_date` <> '0000-00-00 00:00:00') AS `numGoodLinks`;
      ";
    $db->setQuery($query);
    $this->data = $db->LoadObject();
  }

  public function __get($name) {
    return isset($this->data->{$name}) ? $this->data->{$name} : null;
  }
}