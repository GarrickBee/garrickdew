<?php
class Transaction extends AppModel{
	public $name = 'Transaction';
	public $primaryKey = 'id';
	public $useTable = 'transactions';

	public $hasOne = array(
		'Transaction_item' => array(
			'className' => 'Transaction_item',
			'foreignKey' => 'transaction_id',
		)
	);


}
