<?php  
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'sharechecker.css','administrator/components/com_sharechecker/assets/');
$UpdateAndCheckLinks = &$this->UpdateAndCheckLinks;
$UpdateAndCheckLinks->LoadStat();

?>
<h2>В данный момент проверка запущена вручную</h2>
<p>Выполнен 1 проход. Для дополнительного прохода обновите страницу.</p>
<h3>1. Сканирование материалов на наличие ссылок на ФО</h3>
<p>За данный проход проверено материалов: <strong><?php echo $UpdateAndCheckLinks->GetNumCheckedContent(); ?></strong>
<?php if($UpdateAndCheckLinks->GetNumCheckedContent()==0) echo '. <span class="sh_alert">Все ссылки уже извлечены. В дальнейшем они будут извлекаться для вновь созданных и измененных материалов.</span>'; ?>
</p>
<p>из них содержат ссылки на ФО: <strong><?php echo $UpdateAndCheckLinks->GetNumUpdatedLinks(); ?></strong></p>
<h4>Общая статистика:</h4>
<p>Всего уже проверенных материалов со ссылками (в т.ч. не на ФО): <strong><?php echo $UpdateAndCheckLinks->GetTotalNumContentChecked(); ?></strong></p>
<h3>2. Сканирование найденных ссылок на доступность</h3>
<p>Проверено материалов с извлеченными ссылками за данный проход: <strong><?php echo $UpdateAndCheckLinks->GetNumCheckedContentForBrokenLinks();?></strong>
<?php if($UpdateAndCheckLinks->GetNumCheckedContentForBrokenLinks()==0) echo '. <span class="sh_alert">Все ссылки проверены. В дальнейшем они будут проверяться по мере устаревания даты последней проверки и появления новых.</span>'; ?>
</p>
<p>из них с битыми ссылками: <strong><?php echo $UpdateAndCheckLinks->GetNumContentWithBrokenLinks();?></strong></p>
<h4>Общая статистика:</h4>
<p>Найдено всего материалов с битыми ссылками: <a href="<?php echo ShareCheckerUrl::show_broken_articles(); ?>"><strong><?php echo $UpdateAndCheckLinks->GetTotalNumContentWithBrokenLinks();?></strong></a></p>
<p>Число материалов со ссылками на файлообменники и рабочими ссылками: <a href="<?php echo ShareCheckerUrl::show_good_articles(); ?>"><strong><?php echo $UpdateAndCheckLinks->GetTotalGoodConent();?></strong></a></p>
<h2>Внимание!</h2>
<p>С целью снижения нагрузки на сервер, удобства, а также в силу ограничений на память и время выполнения скрипта, не рекомендуется:</p>
<ol>
<li>Значительно увеличивать число операций за проход</li>
</ol>
<p>Рекомендуется:</p>
<ol>
<li>Настроить e-mail для уведомлений</li>
<li>Выполнять проверку по расписанию, для чего настроить cron</li>
</ol>
<h2>Для автоматизации процесса проверки ссылок и разгрузки сервера следует повесить на cron 3 задачи</h2>
<ol>
<li>Сканирование материалов на наличие ссылок на ФО (еженедельно...ежедневно): <em><?php echo '/usr/bin/wget -O /dev/null -q "'.ShareCheckerUrl::cron_search_for_links().'" >/dev/null 2>&1';?></em></li>
<li>Сканирование найденных ссылок на доступность (каждые 3 часа...каждые 15 минут): <em><?php echo '/usr/bin/wget -O /dev/null -q "'.ShareCheckerUrl::cron_check_links().'" >/dev/null 2>&1';?></em></li>
<li>Отправку писем с уведомлением (ежедневно): <em><?php echo '/usr/bin/wget -O /dev/null -q "'.ShareCheckerUrl::cron_send_mail().'" >/dev/null 2>&1';?></em></li>
</ol>
<h2>Настройки прохода</h2>
<ul>
<li>Число материалов, в которых ищутся ссылки за проход: <strong><?php echo MAX_SEARCH_FOR_LINKS; ?></strong></li>
<li>Число материлов с найденными ссылками, которые проверяются за проход: <strong><?php echo MAX_CHECK_LINKS; ?></strong></li>
<li>Период времени, в течение которого ссылка считается рабочей и не проверяется: <strong><?php echo LINKS_CHECK_INTERVAL; ?></strong></li>
</ul>
<p><a href="http://mycomponent.ru" target="_blank"><img src="//mycomponent.ru/logo/mycomponent.jpg" alt="Mycomponent.ru"/></a></p>