<?php
// ��������� ������
defined( '_JEXEC' ) or die( 'Restricted access' );
// ���������� ������� ����������
require_once( JPATH_COMPONENT.DS.'controller.php' );
// ���������� ������
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php' ); 
// ������� ����������
$classname    = 'SharecheckerController'.$controller;
$controller   = new $classname();

// Register Extra tasks
$controller->registerTask( 'search_for_url', 'search_for_url' );
$controller->registerTask( 'check_links', 'check_links' );
$controller->registerTask( 'send_mail', 'send_mail' );

// ��������� ������
$controller->execute( JRequest::getVar('task'));
 
// ���������� �� ����������
$controller->redirect();
?>