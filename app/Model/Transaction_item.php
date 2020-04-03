<?php
class Transaction_item extends AppModel{


	public $name = 'Transaction_item';
	public $primaryKey = 'id';
	public $useTable = 'transaction_items';

	public $belongsTo = array(
		'Transaction' => array(
			'className' => 'Transaction',
		)
	);

}
