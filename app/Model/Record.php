<?php
class Record extends AppModel{
	public $hasMany = array('RecordItem');

	public function datatable_get_record($param='')
	{
		// Default table field
		$table_field = array('id','name');
		$limit       = empty($param['iDisplayLength'])?'':$param['iDisplayLength'];
		$order       = empty($param['iSortCol_0'])?"":$table_field[$param['iSortCol_0']]." ".$param['sSortDir_0'];
		$offset      = empty($param['iDisplayStart'])?'':$param['iDisplayStart'];
		$conditions  = array();
		if (!empty($param['sSearch']))
		{
			foreach ($table_field as $key => $value)
			{
				$conditions['OR'][$value." LIKE"] = "%".$param['sSearch']."%";
			}
		}
		// Total Row
		$total = $this->find('count');
		// Query Row
		$records = $this->find('all',array(
			'fields'     => array('id', 'name'),
			'conditions' => $conditions,
			'limit'      => $limit,
			'order'      => $order,
			'offset'     => $offset,
		));
		// Query Total Row
		$total_query = sizeof($records);

		$records = array_column($records,'Record');
		foreach ($records as $key => $record) {
			$data_return[] = array_values($record);
		}

		$output = array(
			"sEcho"                => intval($_GET['sEcho']),
			"iTotalRecords"        => $total_query,
			"iTotalDisplayRecords" => $total,
			"aaData"               => empty($data_return)?'':$data_return,
		);
		return $output;
	}
}
