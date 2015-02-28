<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'sharechecker.css','administrator/components/com_sharechecker/assets/');
JHTML::script('admin.js', 'administrator/components/com_sharechecker/assets/');
$finder = $this->finder;
$checker = $this->checker;
$statistics = $this->statistics;
$params = $this->params;
JHTML::_('behavior.tooltip');
?>
<div class="sharechecker">

<h2>Статистика</h2>

<table class="statistics">
  <tr class="caption">
    <th>&nbsp;</th>
    <th>За проход</th>
    <th>Общее</th>
  </tr>
  <tr>
    <th colspan="3">Поиск ссылок</th>
  </tr>
  <tr>
    <th>число проверенных материалов</th>
    <td>
      <?=$finder->numChecked; ?>
      <?php if($finder->numChecked == 0) echo JHTML::_('tooltip', 'В дальнейшем ссылки будут извлекаться для новых и измененных материалов',
        'Все ссылки уже извлечены', 'checkin.png', '', ''); ?>
    </td>
    <td><?php echo $statistics->numMarked; ?></td>
  </tr>
  <tr>
    <th>число материалов со ссылками</th>
    <td><?=$finder->numFinded; ?></td>
    <td><?=$statistics->numFinded; ?></td>
  </tr>
  <tr>
    <th colspan="3">Проверка ссылок
      <?php echo JHTML::_('tooltip', 'Проверяются ссылки по мере устаревания даты последней проверки и появления новых.',
        'О проверке', 'checkin.png', '', ''); ?>
    </th>
  </tr>
  <tr>
    <th>Битые ссылки</th>
    <td><?=$checker->numBrokenLinks; ?></td>
    <td><?=$statistics->numBrokenLinks; ?></td>
  </tr>
  <tr>
    <th>Непроверенные ссылки</th>
    <td><?=$checker->numUndefinedLinks; ?></td>
    <td><?=$statistics->numUndefinedLinks; ?></td>
  </tr>
  <tr>
    <th>Рабочие ссылки</th>
    <td><?=$checker->numGoodLinks; ?></td>
    <td><?=$statistics->numGoodLinks; ?></td>
  </tr>
</table>

<h2>Автоматизация процесса проверки ссылок</h2>
Запуск задачи по расписанию (каждые 3 часа...каждые 15 минут): <br /><em><?php echo '/usr/bin/wget -O /dev/null -q "'.ShareCheckerUri::cronCheck().'" >/dev/null 2>&1';?></em>

<h2>Текущие настройки прохода</h2>
<ul>
  <li>Число материалов, в которых ищутся ссылки за проход: <strong><?php echo $params->get('max_search_for_links'); ?></strong></li>
  <li>Число материлов с найденными ссылками, которые проверяются за проход: <strong><?php echo $params->get('max_check_links'); ?></strong></li>
  <li>Период времени, в течение которого ссылка считается рабочей и не проверяется: <strong><?php echo $params->get('links_check_interval'); ?></strong></li>
</ul>

</div>