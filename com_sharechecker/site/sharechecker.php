<?php
// проверяем доступ
defined( '_JEXEC' ) or die( 'Restricted access' );
// подключаем базовый контроллер
require_once( JPATH_COMPONENT.DS.'controller.php' );
// подключаем хелпер
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php' ); 
// Создаем контроллер
$classname    = 'SharecheckerController'.$controller;
$controller   = new $classname();

// Register Extra tasks
$controller->registerTask( 'search_for_url', 'search_for_url' );
$controller->registerTask( 'check_links', 'check_links' );
$controller->registerTask( 'send_mail', 'send_mail' );

// Выполняем задачу
$controller->execute( JRequest::getVar('task'));
 
// Пересылаем на контроллер
$controller->redirect();
?>