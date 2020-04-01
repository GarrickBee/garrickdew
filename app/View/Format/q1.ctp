<div id="message1">
	<?php echo $this->Form->create(false,array( 'url' => array('controller' => 'format', 'action' => 'result'),'id'=>'form_type','method'=>'POST'))?>
	<?php echo __("Hi, please choose a type below:")?>
	<?php
	// Data from database
	$options = array(
		array(
			'value' => 'Type 1',
			'title' => 'Type 1',
			'content' => '<ul><li>Description .......</li><li>Description 2</li></ul>'
		),
		array(
			'value' => 'Type 2',
			'title' => 'Type 2',
			'content' => '<ul><li>Description .......</li><li>Description 2</li></ul>'
		)
	);
	foreach ($options as $option_key => $option)
	{
		$content = str_replace('"', "'", $option['content']);
		echo "<label for'type-{$option_key}' class='radio'>";
		echo "<input type='radio' id='type-{$option_key}' name='data[Type][type]' value='{$option['value']}' class='custom-popover' required>";
		echo "<span	class='custom-popover text-info' data-content='{$content}'
		data-title='{$option['title']}'
		data-html=true
		data-trigger='hover'
		data-placement='right'> {$option['title']}</span>";
		?>
	</label>
	<div class="custom-popover-container-<?php echo $option_key ?>" ></div>
<?php } ?>
<div class="btn-group">
	<button type="submit" name="submit" class="btn btn-primary">Submit</button>
</div>
<?php echo $this->Form->end();?>
</div>
<?php $this->start('script_own')?>
<script>
$(document).ready(function()
{
	$(".custom-popover").popover();
});
</script>
<?php $this->end()?>
