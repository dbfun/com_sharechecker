<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

class SharecheckerModelSharechecker extends JModel {

  private $params;

  public function getParams() {
    if(isset($this->params)) return $this->params;
    $this->params = JComponentHelper::getParams('com_sharechecker');
    return $this->params;
  }

  public function runFinder() {
    $params = $this->getParams();
    $finder = new SharecheckerFinder();
    $finder->searchForLinks($params->get('max_search_for_links'));
    return $finder;
  }

  public function runChecker() {
    $params = $this->getParams();
    $checker = new SharecheckerChecker();
    $checker->checkLinks($params->get('max_check_links'));
    return $checker;
  }

  public function getStatistics() {
    return new SharecheckerStatistics();
  }





}