<?php
defined('_JEXEC') or die ('Доступ запрешён');
jimport('joomla.application.component.controller');
class SharecheckerController extends JController {

  function editarticle() {
    global $mainframe;
    $db = JFactory::getDBO();
    $id = JRequest::getInt('id');
    $query = "UPDATE `#__content` SET `uri_date` = NULL, `uri_check_date` = NULL, `error_uri` = NULL, `uri` = NULL
                WHERE `id` = ".$id;
    $db->setQuery($query);
    $db->query();
    $mainframe->redirect('/administrator/index.php?option=com_content&sectionid=-1&task=edit&cid[]='.$id, 'Статья поставлена в очередь на проверку');
  }

  function resetAll() {
    global $mainframe;
    $db = JFactory::getDBO();
    $query = "UPDATE `ok_content` SET `uri_date` = '0000-00-00 00:00:00', `uri_check_date` = '0000-00-00 00:00:00', `error_uri` = '', `undefined_uri` = '', `uri` = '';";
    $db->setQuery($query);
    $db->query();
    $mainframe->redirect('/administrator/index.php?option=com_sharechecker', 'Все ссылки сброшены');
  }

  function resetBroken() {
    global $mainframe;
    $db = JFactory::getDBO();
    $query = "UPDATE `ok_content` SET `uri_date` = '0000-00-00 00:00:00', `uri_check_date` = '0000-00-00 00:00:00', `error_uri` = '', `undefined_uri` = '', `uri` = ''
      WHERE `error_uri` <> '';";
    $db->setQuery($query);
    $db->query();
    $mainframe->redirect('/administrator/index.php?option=com_sharechecker', 'Битые ссылки сброшены');
  }

  function display() {
    parent::display();
  }
}