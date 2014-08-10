<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

class SharecheckerModelResults extends JModel
{	
	function LoadResults()
		{
		$this->type = JRequest::getVar('type');
		
		$db = &JFactory::getDBO();
		switch ($this->type)
			{
			case 'good':
				$query = "SELECT `id`, `title`, `shared_links`, `hits`, DATEDIFF(NOW(), `modified`) AS `days_modified`, DATEDIFF(NOW(), `created`) AS `days_passed`, "
				."(SELECT `hits`/`days_passed`) AS `rating` FROM `#__content` WHERE "
				."`links_check_date` IS NOT NULL AND `error_links` IS NULL AND `shared_links` IS NOT NULL "
				."AND `modified`<`checked_for_links_date` ORDER BY `rating` DESC LIMIT 300";
				break;
			default:
				$query = "SELECT `id`, `title`, `shared_links`, `hits`, DATEDIFF(NOW(), `modified`) AS `days_modified`, DATEDIFF(NOW(), `created`) AS `days_passed`, "
				."(SELECT `hits`/`days_passed`) AS `rating` FROM `#__content` WHERE "
				."`error_links` IS NOT NULL AND `modified` < `checked_for_links_date` ORDER BY `rating` DESC LIMIT 300";
			}
		$db->setQuery($query);
		$this->results = $db->LoadObjectList();
		if (is_array($this->results)) foreach ($this->results as &$result)
			{
			$result->shared_links = unserialize($result->shared_links);
			$result->num_shared_links = count($result->shared_links);
			foreach($result->shared_links as &$link)
				$link = '<a href="'.$link.'" target="_blank">'.$link.'</a>';
			}
		return null;
		}	

	
}