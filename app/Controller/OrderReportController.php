<?php
class OrderReportController extends AppController{

	public function index(){

		$this->setFlash('Multidimensional array.');

		$this->loadModel('Order');
		$orders = $this->Order->find('all',array('conditions'=>array('Order.valid'=>1),'recursive'=>2));
		// debug($orders);exit;

		$this->loadModel('Portion');
		$portions = $this->Portion->find('all',array('conditions'=>array('Portion.valid'=>1),'recursive'=>2));
		// debug($portions);exit;


		// To Do - write your own array in this format
		$order_reports = array('Order 1' => array(
			'Ingredient A' => 1,
			'Ingredient B' => 12,
			'Ingredient C' => 3,
			'Ingredient G' => 5,
			'Ingredient H' => 24,
			'Ingredient J' => 22,
			'Ingredient F' => 9,
		),
		'Order 2' => array(
			'Ingredient A' => 13,
			'Ingredient B' => 2,
			'Ingredient G' => 14,
			'Ingredient I' => 2,
			'Ingredient D' => 6,
		),
	);

	$this->set('order_reports',$order_reports);

	$this->set('title',__('Orders Report'));
}

public function Question(){

	$this->setFlash('Multidimensional array.');

	$this->loadModel('Order');
	$orders = $this->Order->find('all',array('conditions'=>array('Order.valid'=>1),'recursive'=>2));

	// debug($orders);exit;
	$this->set('orders',$orders);

	$this->loadModel('Portion');
	$portions = $this->Portion->find('all',array('conditions'=>array('Portion.valid'=>1),'recursive'=>2));


	$order_details['item']     = array_column($orders[0]['OrderDetail'],'Item'); // Quantity Neededs


	// $this->garrick_print($portions);
	echo "<pre>";
	foreach ($order_details['item'] as $item_key => $item_value)
	{
		$item[$item_key]['name'] = $item_value['name'];
		foreach ($portions as $key => $portion)
		{
			if ($portion['Item']['id'] == $item_value['id'] )
			{
				$item[$item_key]['portion_detail'] = $portion['PortionDetail'];
			}
		}
	}
	echo "</pre>";

	// $item['portion_detail'] = array_map(array($this,'calculate_ingredient'), $item);
	$this->garrick_print($item);


	$this->set('portions',$portions);

	$this->set('title',__('Question - Orders Report'));
}


private function calculate_ingredient($portion='')
{
	$return = array(
		'value' => $portion['portion_detail']['value'],
		'part'=> $portion['portion_detail']['Part']['name'],
	);
	return $return;
}


}
