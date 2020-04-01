<?php
class FormatController extends AppController
{
	public function q1()
	{
		$this->setFlash('Question: Please change Pop Up to mouse over (soft click)');
	}

	public function result()
	{
		$result = $this->data['Type']['type'];
		$this->setFlash("Your submited answer is {$result}",array('class'=>'success'));
	}

	public function q1_detail()
	{
		$this->setFlash('Question: Please change Pop Up to mouse over (soft click)');
	}

}
