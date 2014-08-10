<?php
defined('_JEXEC') or die('Restricted access');

// получаем параметры компонента
$params = JComponentHelper::getParams ( 'com_sharechecker' );

define(MAX_SEARCH_FOR_LINKS, $params->get ( 'max_search_for_links')); // Число материалов, в которых ищутся ссылки за проход
define(MAX_CHECK_LINKS, $params->get ('max_check_links')); // Число материлов с найденными ссылками, которые проверяются за проход
define(LINKS_CHECK_INTERVAL, $params->get ('links_check_interval')); // Период времени, в течение которого ссылка считается рабочей и не проверяется // - 1 HOUR
define(SECRET_CODE, $params->get ('secret_code')); // Секретный код для запуска задач по расписанию
define(SEND_EMAIL, $params->get ('send_email')); // Слать ли письма
define(USER_ID_FOR_NOTICES, $params->get ('user_id_for_notices')); // ID пользователя, которому отправляется уведомление о битых ссылках
define(MIN_BROKEN_LINKS_FOR_NOTICE, $params->get ('min_broken_links_for_notice')); // Число материалов с битыми ссылками, по достижении которого отправляется письмо

// СБРОС всех проверок: 
// UPDATE `j15_content` SET `checked_for_links_date` = NULL, `links_check_date` = NULL, `error_links` = NULL, `shared_links` = NULL
class SharecheckerHelper
	{
	private $num_updated_links = 0, $num_checked_content = 0, $num_checked = 0, $num_checked_content_for_broken_links = 0, $num_content_with_broken_links = 0;
	function __construct()
		{
		}
	function СheckСode() // проверка секретног кода для задач по расписанию
		{
		global $mainframe;
		$code = JRequest::getVar('code');
		if ($code != SECRET_CODE) $mainframe->close('Неправильный код');
		}
	function LoadStat() // загрузка статистических данных
		{
		$db = &JFactory::getDBO();
		$query = "SELECT "
		."(SELECT COUNT(`id`) FROM `#__content` WHERE `links_check_date` IS NOT NULL AND `error_links` IS NULL AND `shared_links` IS NOT NULL AND `modified`<`checked_for_links_date`) AS `TotalGoodConent`,"
		."(SELECT COUNT(`id`) FROM `#__content` WHERE `error_links` IS NOT NULL AND `modified`<`checked_for_links_date`) AS `TotalNumContentWithBrokenLinks`,"
		."(SELECT COUNT(`id`) FROM `#__content` WHERE `checked_for_links_date` IS NOT NULL AND `modified`<`checked_for_links_date`) AS `TotalNumContentChecked`";
		$db->setQuery($query);
		$result = $db->LoadObject();
		$this->TotalGoodConent = $result->TotalGoodConent;
		$this->TotalNumContentWithBrokenLinks = $result->TotalNumContentWithBrokenLinks;
		$this->TotalNumContentChecked = $result->TotalNumContentChecked;
		}
	function GetTotalGoodConent() // число материалов с проверенными и рабочими ссылками
		{
		return $this->TotalGoodConent;
		}
	function GetTotalNumContentWithBrokenLinks() // число материалов с битыми ссылками
		{
		return $this->TotalNumContentWithBrokenLinks;
		}
	function GetTotalNumContentChecked() // число проверенных материалов со ссылками
		{
		return $this->TotalNumContentChecked;
		}
	function GetNumCheckedContentForBrokenLinks() // Число проверенных статей со ссылками за проход
		{
		return $this->num_checked_content_for_broken_links;
		}
	function GetNumUpdatedLinks() // Число обновленых материалов со ссылками
		{
		return $this->num_updated_links;
		}	
	function GetNumContentWithBrokenLinks() // число материалов с битыми ссылками
		{
		return $this->num_content_with_broken_links;
		}
	function GetNumCheckedContent() // число проверенных материалов
		{
		return $this->num_checked_content;
		}


	function SearchForLinks($update_limit = 100) // Проверка материалов на ссылки и добавление их в базу
		{
		//set_time_limit(90); // нарастить время выполнения скриптов
		
		$shared_links = array(); // ссылки, обнаруженные в материалах
		$ids_array = array(); // id проверенных материалов
		$db = &JFactory::getDBO();
		$shared_sites = array('filesonic.com', '2shared.com', // файлообменники; вписать другие при необходимости; rapidshare.com выдает ошибку на JS
		'depositfiles.com', 'i-filez.com', 'depfile.com', 'letitbit.net', 'turbobit.net');

		// ПОЛУЧАЕМ МАТЕРИАЛЫ СО ССЫЛКАМИ
		$query = "SELECT `id`, CONCAT(`introtext`, `fulltext`) AS `text` "
		."FROM `#__content` "
		."WHERE `state` <>-2 AND (`checked_for_links_date` IS NULL OR `modified`>`checked_for_links_date`) AND "
		."(`introtext` LIKE '%href%' OR `fulltext` LIKE '%href%') "
		."ORDER BY `checked_for_links_date` LIMIT ".$update_limit; //
		$db->setQuery($query);
		$results = $db->loadObjectList(); // загрузили текстовую часть материалов
		// ПРОВЕРЯЕМ НАЛИЧИЕ ССЫЛОК
		if (is_array($results)) foreach ($results as $result)
			{
			preg_match_all('/<a .*?href.*?=.*?[\"|\']?(http:\/\/?.*?)?[\"|\'].*?>/si', $result->text, $matches); // вытаскиваем ссылки
			$all_links = $matches[1]; // вытащили все ссылки
			foreach ($all_links as $link)
				{
				foreach ($shared_sites as $site)
					{
					$is_shared_site = false;
					if (JString::strpos($link, $site) !== FALSE)
						{
						$is_shared_site = true;
						break;
						}
					}
				if ($is_shared_site) 
					$shared_links[$result->id][] = $link; // если обнаружена ссылка, она добавляется
				}	//die (var_dump ($shared_links)); // проверка найденных ссылок
			$ids_array[] = $result->id; // id проверенных материалов
			}

		// ЗАНОСИМ РЕЗУЛЬТАТЫ В БД
		if (!empty($shared_links)) foreach ($shared_links as $key => $value) // сохраняем найденные ссылки
			{
			$query = "UPDATE `#__content` SET `shared_links` = '".addslashes(serialize($value))
			."' WHERE `id` = ".$key;
			$db->setQuery($query);
			$db->query();
			$this->num_updated_links++; // число обновленных материлов со ссылками
			}
		if (is_array($results))
			{
			$this->num_checked_content = count($ids_array); // число проверенных материалов
			// вписываем "обновлено" и дату
			$query = "UPDATE `#__content` SET `checked_for_links_date` = NOW(), `error_links` = NULL, `links_check_date` = NULL WHERE `id` IN(".implode(',', $ids_array).")";
			$db->setQuery($query);
			$db->query();
			}
		}
	function curl_redir_exec($ch)
		{
		static $curl_loops = 0;
		static $curl_max_loops = 20;
		if ($curl_loops   >= $curl_max_loops)
		{
		$curl_loops = 0;
			return FALSE;
		}
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		list($header, $data) = explode("\n\n", $data, 2);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 301 || $http_code == 302)
		{
		$matches = array();
			preg_match('/Location:(.*?)/', $header, $matches);
		$url = @parse_url(trim(array_pop($matches)));
			if (!$url)
		{
			//couldn't process the url to redirect to
			$curl_loops = 0;
			return $data;
			}
		$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
		if (!$url['scheme'])
			$url['scheme'] = $last_url['scheme'];
		if (!$url['host'])
			$url['host'] = $last_url['host'];
		if (!$url['path'])
			$url['path'] = $last_url['path'];
		$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
		curl_setopt($ch, CURLOPT_URL, $new_url);
		//debug('Redirecting to', $new_url);
		$curl_loops++;
		return curl_redir_exec($ch);
		} 	else {
			$curl_loops=0;
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			$data = curl_exec($ch);
			return $data;
			}
		}
	function CheckLinks($update_limit = 5) // проверка ссылок
		{
		$db = &JFactory::getDBO();
		// сиггатуры, по которым скрипт узнает об отсутствии файла, дописать варианты при изменении на ФО
		$error_responses = array('This file was deleted', 'File not found',
		'Такого файла не существует или он был удален из-за нарушения авторских прав',
		'Такого файла не существует, доступ к нему ограничен или он был удален из-за нарушения авторских прав', // проверено для depositfiles
		'Файл не найден в базе i-FileZ.com Возможно Вы неправильно указали ссылку',
		'Запрашиваемый файл не найден', // проверено для letitibit
		'Файл не найден. Возможно он был удален',
		'Запрашиваемая вами страница не существует',
		'The file link that you requested is not valid',
		'Файл удален владельцем');
		
		$query = "SELECT `id`, `shared_links` "
		."FROM `#__content` "
		."WHERE `state` <>-2 AND `shared_links` IS NOT NULL " //  AND `id` = 9   // 27 проверяем!
		."AND (`links_check_date` IS NULL OR `links_check_date` < ADDDATE(NOW(), INTERVAL ".LINKS_CHECK_INTERVAL."))" // 10 MINUTE
		."ORDER BY `links_check_date` LIMIT ".$update_limit; // выбираем не удаленные, обновленные со ссылками
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$this->num_checked_content_for_broken_links = count($results);
		if (is_array($results))foreach ($results as $result)
			{
			$result->shared_links = unserialize($result->shared_links); // получаем все ссылки материала
			$broken_links = array();
			foreach ($result->shared_links as $link) // проверяем все предоставленные ссылки
				{
				if($curl = curl_init()) // если установлен CURL
					{
					curl_setopt_array($curl, array(
						CURLOPT_URL				=> $link,
						CURLOPT_RETURNTRANSFER	=> true,
						CURLOPT_FOLLOWLOCATION  =>false,
						CURLOPT_HTTPGET         =>true
						));
					$server_response = $this->curl_redir_exec($curl);
					$server_response_utf = iconv('windows-1251', 'utf-8', $server_response);
					$link_is_broken = false;
					foreach ($error_responses as $error_response)
						{
						if (JString::strpos($server_response, $error_response) !== FALSE OR 
							JString::strpos($server_response_utf, $error_response) !== FALSE)
							{
							$link_is_broken = true;
							break;
							}
						}
					if ($link_is_broken) $broken_links[] = $link;
					} else die('Не установлен CURL');
				}
			if (!empty($broken_links)) $this->num_content_with_broken_links++;
			$query = "UPDATE `#__content` SET `error_links` = "
			.(empty($broken_links) ? 'NULL': "'".addslashes(serialize($broken_links))."'")
			.", `links_check_date` = NOW() WHERE `id` = ".$result->id;
			//die($query);
			$db->setQuery($query);
			$db->query();

			}
		}
	}
class ShareCheckerUrl
	{
	static function cron_search_for_links()
		{
		return 'http://'.$_SERVER['HTTP_HOST'].'/index2.php?option=com_sharechecker&task=search_for_url&code='.SECRET_CODE;
		}	
	static function cron_check_links()
		{
		return 'http://'.$_SERVER['HTTP_HOST'].'/index2.php?option=com_sharechecker&task=check_links&code='.SECRET_CODE;
		}
	static function cron_send_mail()
		{
		return 'http://'.$_SERVER['HTTP_HOST'].'/index2.php?option=com_sharechecker&task=send_mail&code='.SECRET_CODE;
		}
	static function edit_article($id)
		{
		return '/administrator/index.php?option=com_sharechecker&task=editarticle&id='.$id;
		}	
	static function show_broken_articles()
		{
		return '/administrator/index.php?option=com_sharechecker&view=results';
		}	
	static function show_good_articles()
		{
		return '/administrator/index.php?option=com_sharechecker&view=results&type=good';
		}
	}
?>