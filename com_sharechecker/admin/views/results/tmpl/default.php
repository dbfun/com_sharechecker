<?php  
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'sharechecker.css','administrator/components/com_sharechecker/assets/');
$results = &$this->results;
$type = &$this->type;
$even_row_class = ($type == 'good' ? 'sh_even_work' : 'sh_even_broken'); // выбираем цветовую гамму - для битых красную, для хороших - зеленую
$uneven_row_class = ($type == 'good' ? 'sh_uneven_work' : 'sh_uneven_broken');
?>
<h2>Результаты проверки <?php echo ($type == 'good' ? 'рабочих' : 'битых')?> ссылок</h2>
<?php if (!empty($results)): ?>
<table class="sh_table" cellspacing="0">
<tr class="sh_header">
<td><?php echo JText::_('id'); ?></td>
<td><?php echo JText::_('title'); ?></td>
<td><?php echo JText::_('links'); ?></td>
<td><?php echo JText::_('hits'); ?></td>
<td><?php echo JText::_('rating'); ?></td>
<td><?php echo JText::_('days_modified'); ?></td>
</tr>
<?php foreach($results as $result): ?>
<tr class="<?php echo (($even_row = $even_row ^ 1) == 1 ? $uneven_row_class : $even_row_class); ?>">
<td><?php echo $result->id; ?></td>
<td><?php echo '<a href="'.(ShareCheckerUrl::edit_article($result->id)).'" target="_blank">'.$result->title.'</a>'; ?></td>
<td><?php echo ($result->num_shared_links > 1 ? $result->num_shared_links.' ссылок(ки)<br/>' : null).implode(',<br />', $result->shared_links); ?></td>
<td><?php echo $result->hits; ?></td>
<td><?php echo $result->rating; ?></td>
<td><?php echo ($result->days_modified >0 ? $result->days_modified : null); ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php 
else:
?>
<p>Ссылки еще не найдены. Запустите их поиск вручную или дождитесь проверки по расписанию.</p>
<?php
endif;
?>
<p><a href="http://mycomponent.ru" target="_blank"><img src="//mycomponent.ru/logo/mycomponent.jpg" alt="Mycomponent.ru"/></a></p>