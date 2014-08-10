<?php
//запрет на прямой вызов
defined( '_JEXEC' ) or die( 'Restricted access' ); 
 
// подключаем контролер и хелпер
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helper.php' );
$controller = new SharecheckerController();

$controller->registerTask( 'editarticle', 'editarticle' );

$controller->execute( JRequest::getCmd( 'task' ) );
$controller->redirect(); 
?>