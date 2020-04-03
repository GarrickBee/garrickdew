<?php
echo $this->Form->create(false, array(
	'url' => array('controller' => 'Migration', 'action' => 'submit'),
	'enctype' => 'multipart/form-data',
));
echo $this->Form->input('file', array('label' => 'File Upload', 'type' => 'file'));
echo $this->Form->submit('Upload', array('class' => 'btn btn-primary'));
echo $this->Form->end();
?>
