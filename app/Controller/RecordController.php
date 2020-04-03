<?php
class RecordController extends AppController
{
	public function index()
	{
		ini_set('memory_limit','256M');
		set_time_limit(0);

		$this->setFlash('Listing Record page too slow, try to optimize it.');
		$this->set('title',__('List Record'));
	}

	public function get_record()
	{
		// Param
		$param        = $this->request->query;

		// Datatable Model
		$output = $this->Record->datatable_get_record($param);

		// Success Return
		$this->set('json',$output);
		$this->layout = 'json';
	}

	public function get_record_backup()
	{
		// Param
		$param        = $this->request->query;
		// Default table field
		$table_field     = array('id','name');
		$limit  = empty($param['iDisplayLength'])?'':$param['iDisplayLength'];
		$order  = empty($param['iSortCol_0'])?"":$table_field[$param['iSortCol_0']]." ".$param['sSortDir_0'];
		$offset = empty($param['iDisplayStart'])?'':$param['iDisplayStart'];
		$conditions = array();
		
		if (!empty($param['sSearch']))
		{
			foreach ($table_field as $key => $value)
			{
				$conditions['OR'][$value." LIKE"] = "%".$param['sSearch']."%";
			}
		}
		// Total Row
		$total = $this->Record->find('count');
		// Query Row
		$records = $this->Record->find('all',array(
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
			'param' => $param,
		);
		$this->set('json',$output);
		// Success Return
		$this->layout = 'json';
	}
}
