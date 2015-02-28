<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

class SharecheckerModelResults extends JModel {
  public $_total, $_pagination, $limitstart, $limit;

  public function __construct() {
    parent::__construct();
    global $mainframe;
    $this->limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    $this->limitstart = JRequest::getVar('limitstart', 0, '', 'int');
    $this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
  }

  public function getPagination() {
  // Load the content if it doesn't already exist
    if (empty($this->_pagination)) {
      jimport('joomla.html.pagination');
      $this->_pagination = new JPagination($this->getTotal(), $this->limitstart, $this->limit );
    }
    return $this->_pagination;
  }

  public function getTotal() {
    return $this->_total;
  }

  public function LoadResults() {
    $this->type = JRequest::getVar('type');

    $db = JFactory::getDBO();
    $query = "SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `uri`, `hits`, DATEDIFF(NOW(), `modified`) AS `days_modified`, DATEDIFF(NOW(), `created`) AS `days_passed`,
        (SELECT `hits` / `days_passed`) AS `rating` FROM `#__content` WHERE ";

    switch ($this->type) {
      case 'good':
        $query .= "`uri_check_date` <> '0000-00-00 00:00:00' AND `error_uri` = '' AND `uri` <> ''
                    AND `modified` < `uri_date` ORDER BY `rating` DESC";
        break;
      default:
        $query .= "`error_uri` <> '' AND `modified` < `uri_date` ORDER BY `rating` DESC";
    }

    if($this->limit != 0) $query .= " LIMIT ".$this->limitstart.", ".$this->limit;

    $db->setQuery($query);
    $this->results = $db->LoadObjectList();

    $db->setQuery('SELECT FOUND_ROWS();');
    $this->_total = $db->loadResult();

    if (is_array($this->results)) foreach ($this->results as &$result) {
      $result->uri = unserialize($result->uri);
      $result->num_shared_links = count($result->uri);
      foreach($result->uri as &$link) {
        $link = '<a href="'.$link.'" target="_blank">'.$link.'</a>';
      }
    }
    return null;
  }


}