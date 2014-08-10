<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

class SharecheckerModelSharechecker extends JModel
{	
	function UpdateAndCheckLinks()
		{
		$updater = new SharecheckerHelper();
		$updater->SearchForLinks(MAX_SEARCH_FOR_LINKS);
		$updater->CheckLinks(MAX_CHECK_LINKS);
		return $updater;
		}	

	
}