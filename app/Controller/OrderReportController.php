<?php
class OrderReportController extends AppController{

	public function index()
	{

		$this->setFlash('Multidimensional array.');

		// Base On question and sample answer give , Order Name are Unique as Order ID
		// AS Order ID is used as the key of each order reports

		// Generate Order
		$this->loadModel('Order');
		$order_details = $this->Order->query("SELECT orders.`id`,orders.`name`,order_details.`item_id`,order_details.`quantity`,`items`.`name`
			FROM `orders`
			LEFT JOIN `order_details`
			ON `order_details`.`order_id` = `orders`.`id` AND `order_details`.`valid` ='1'
			LEFT JOIN `items`
			ON `items`.`id` = `order_details`.`item_id`
			WHERE `orders`.`valid` = '1'
			ORDER BY `orders`.`id` ASC"
		);
		foreach ($order_details as $key => $order_detail)
		{
			$orders[$order_detail['orders']['name']][] = array(
				'items_id'=> $order_detail['order_details']['item_id'],
				'quantity'=> $order_detail['order_details']['quantity'],
			);
		}

		// Generate Portions
		$this->loadModel('Portion');
		$portion_details = $this->Portion->query("SELECT `portions`.`item_id`,`portion_details`.`value`,`parts`.`name`
			FROM `portions`
			LEFT JOIN `portion_details`
			ON `portion_details`.`portion_id` = `portions`.`id` AND `portion_details`.`valid`='1'
			LEFT JOIN `parts`
			ON `parts`.`id` = `portion_details`.`part_id` AND `parts`.`valid` = '1'
			WHERE `portions`.`valid` = '1'"
		);
		foreach ($portion_details as $key => $portion_detail)
		{
			$items[$portion_detail['portions']['item_id']][] = array(
				$portion_detail['parts']['name'] => $portion_detail['portion_details']['value'],
			);
		}

		// First Merge Order Details and Items
		foreach ($orders as $key => $order)
		{
			$orders[$key] = array_map(function ($order_value) use ($items)
			{
				return array(
					'item'     => $items[$order_value['items_id']],
					'quantity' => $order_value['quantity']
				);
			},$order);

		}
		// Calculate Ingredients Total
		// Generate Final Order Reports
		$order_reports = array();
		foreach ($orders as $orders_key => $order)
		{
			foreach ($order as $order_key => $order_value)
			{

				foreach ($order_value['item'] as $ingrediant_key => $ingrediant_value)
				{
					$order_reports[$orders_key][key($ingrediant_value)] = array_values($ingrediant_value)[0]*$order_value['quantity'];
				}

			}
		}
	//
	// 	$this->garrick_print($orders);
	// 	$order_reports[$key][] = array_map( function($order_key,$order_value) use ($order)
	// 	{
	// 		return array(
	// 			$order_key =>($order_value['value'] * $order['quantity'])
	// 		);
	// 	},$order);
	//
	// 	$this->garrick_print($orders);
	// 	$order_reports = array('Order 1' => array(
	// 		'Ingredient A' => 1,
	// 		'Ingredient B' => 12,
	// 		'Ingredient C' => 3,
	// 		'Ingredient G' => 5,
	// 		'Ingredient H' => 24,
	// 		'Ingredient J' => 22,
	// 		'Ingredient F' => 9,
	// 	),
	// 	'Order 2' => array(
	// 		'Ingredient A' => 13,
	// 		'Ingredient B' => 2,
	// 		'Ingredient G' => 14,
	// 		'Ingredient I' => 2,
	// 		'Ingredient D' => 6,
	// 	),
	// );


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


	$this->set('portions',$portions);

	$this->set('title',__('Question - Orders Report'));
}


}
