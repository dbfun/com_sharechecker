<?php
defined('_JEXEC') or die ('Доступ запрешён');
jimport('joomla.application.component.controller');
class SharecheckerController extends JController
{	
	function editarticle()
		{
		global $mainframe;
		$db = &JFactory::getDBO();
		$id = JRequest::getInt('id');
		$query = "UPDATE `#__content` SET `checked_for_links_date` = NULL, `links_check_date` = NULL, `error_links` = NULL, `shared_links` = NULL "
		."WHERE `id` = ".$id;
		$db->setQuery($query);
		$db->query();	
		$mainframe->redirect('/administrator/index.php?option=com_content&sectionid=-1&task=edit&cid[]='.$id, 'Статья поставлена в очередь на проверку');
		}
	function display()
		{
		parent::display();
		}
}
?>