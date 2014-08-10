<?php
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
 
class SharecheckerController extends JController
{
	function search_for_url()
		{
		SharecheckerHelper::СheckСode();
		global $mainframe;
		$updater = new SharecheckerHelper();
		$updater->SearchForLinks(MAX_SEARCH_FOR_LINKS);
		$mainframe->close('Процесс поиска ссылок окончен');
		}
	function check_links()
		{
		SharecheckerHelper::СheckСode();
		global $mainframe;
		$updater = new SharecheckerHelper();
		$updater->CheckLinks(MAX_CHECK_LINKS);
		$mainframe->close('Процесс проверки ссылок окончен');
		}
	function send_mail()
		{
		SharecheckerHelper::СheckСode();
		global $mainframe;
		if (SEND_EMAIL == 0) $mainframe->close('Письма не отсылаем');	
		$db = &JFactory::getDBO();
		
		// проверяем, есть ли битые ссылки выше лимита, и как давно заходил пользователь
		$updater = new SharecheckerHelper;
		$updater->LoadStat(); // загружаем статистику
		if ($updater->GetTotalNumContentWithBrokenLinks() >= MIN_BROKEN_LINKS_FOR_NOTICE) // если превышен лимит, начинаем действовать
			{
			// получаем информацию из профиля
			$user =& JFactory::getUser(USER_ID_FOR_NOTICES);
			// получим часовой пояс по умолчанию из реестра пространства имен Config
			$registry =& JFactory::getConfig();
			$tzdefault = $registry->getValue('config.offset');
			$user_tz =& $user->getParam('timezone', $tzdefault);
			
			// получаем информацию компонента
			$params = JComponentHelper::getParams ( 'com_sharechecker' );
			$last_notice_date = $params->get ('last_notice_date'); // Время, когда было отправлено последнее письмо
			if ($last_notice_date >= $user->lastvisitDate) $mainframe->close('Письмо уже отправлено, последний заход пользователя '.$user->lastvisitDate); // письмо уже было отправлено, пользователь не явился

			
			jimport('joomla.utilities.mail');
			$mailer =& JFactory::getMailer();
			$mailer->setSender('info@'.$_SERVER['HTTP_HOST'], 'Системный робот');
			$mailer->addRecipient($user->email);
			$mailer->setSubject('На сайте '.$_SERVER['HTTP_HOST'].' имеются битые ссылки');
			$msg = 'Добрый день. Вы получили это письмо потому что подписаны на уведомления о наличии битых ссылок на сайте '.$_SERVER['HTTP_HOST'].".\r\n";
			$msg .= 'В данный момент на сайте обнаружено '.$updater->GetTotalNumContentWithBrokenLinks().' материала(ов) с битыми ссылками.'."\r\n";
		
			$msg .= '---'."\r\n";
			$msg .= 'Это письмо было отослано автоматически. Не отвечайте на него. Больше подобное письмо отсылаться не будет до тех пор, пока вы не зайдете на сайт.';
			$mailer->setBody($msg);
			if ($mailer->Send()) 
				{
				$params->set('last_notice_date', $user->lastvisitDate) ;// если письмо отправлено, записываем его числом-последним заходом пользователя, иначе будут проблемы с часовыми поясами
				$table =& JTable::getInstance('component');
				$table->loadByOption( 'com_sharechecker' ); 
				
				$table->params = $params->toString(); 
				if (!$table->check()) $mainframe->close('Error 1');
				if (!$table->store()) $mainframe->close('Error 2');
				$mainframe->close('Письмо успешно отправлено, последний заход пользователя '.$user->lastvisitDate);
				}
			}
		$mainframe->close();
		}
}
?>