<?php

class SharecheckerUri {

  static function cronCheck() {
    $params = JComponentHelper::getParams('com_sharechecker');
    return 'http://'.$_SERVER['HTTP_HOST'].'/index2.php?option=com_sharechecker&task=check&code='.$params->get('secret_code');
  }

  static function editArticle($id) {
    return '/administrator/index.php?option=com_sharechecker&task=editarticle&id='.$id;
  }

  static function showBrokenArticles() {
    return '/administrator/index.php?option=com_sharechecker&view=results';
  }

  static function showGoodArticles() {
    return '/administrator/index.php?option=com_sharechecker&view=results&type=good';
  }
}