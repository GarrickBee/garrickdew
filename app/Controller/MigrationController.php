<?php
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
if (!class_exists('PHPExcel')) {
	throw new CakeException('Vendor class PHPExcel not found!');
}

class MigrationController extends AppController
{
	public function index()
	{
		// Assuming User might exist in the current database
		// Assuming Migrated Database contain transaction details that doesnt have
		// in current database
		// Assuming all transaction is unique
		// Date created follow the old Database
		// Date modified as date migrated
		$this->setFlash('Question: Migration of data to multiple DB table');
	}

	public function submit()
	{
		// Input Parameter
		$file = $this->data['file'];

		// Error Checking
		if (empty($file))
		{
			$this->setFlash('File upload empty, upload file require',array('class'=>'danger'));
			return $this->redirect(array('controller' => 'FileUpload','action'=>'index'));
		}
		if ($file['error'] !== 0){
			$this->setFlash('Error on file upload',array('class'=>'danger'));
			return $this->redirect(array('controller' => 'FileUpload','action'=>'index'));
		}

		//  Read Excel File
		$inputFileName = $file['tmp_name'];
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader     = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel   = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		//  Get worksheet dimensions
		$sheet          = $objPHPExcel->getSheet(0);
		$highest_row    = $sheet->getHighestRow();
		$highest_column = $sheet->getHighestColumn();

		// Loop through each row of the worksheet in turn
		// Arrange Data According to table needs
		$members_data = array();
		$temp_members = array();
		for ($row = 2; $row <= $highest_row; $row++)
		{
			$row_data    = $sheet->rangeToArray('A' . $row . ':' . $highest_column . $row,NULL,TRUE,TRUE,FALSE)[0];

			$member_array = explode(" ",$row_data[3]);
			$member_type  = $member_array[0];
			$member_no    = intval($member_array[1]);
			// Check duplicate Member ID in excel file
			// Assuming Member No is unique as Member ID
			if (!in_array($row_data[3], $temp_members))
			{
				$temp_members[] = $row_data[3];
				// Store Unique User ID For Update Later
				$members_data[] = array(
					'type'    => $member_type,
					'no'      => $member_no,
					'name'    => $row_data[2],
					'company' => $row_data[5],
					'created' => date("Y-m-d H:i:s",strtotime($row_data[0])),
				);
			}

			$transaction_data[] = array(
				'member_id'      => null,
				'type'           => $member_type,
				'no'             => $member_no,
				'member_name'    => $row_data[2],
				'member_paytype' => $row_data[4],
				'member_company' => $row_data[5],
				'date'           => date("Y-m-d ",strtotime($row_data[0])),
				'year'           => date("Y",strtotime($row_data[0])),
				'month'          => date("m",strtotime($row_data[0])),
				'ref_no'         => $row_data[1],
				'receipt_no'     => $row_data[8],
				'payment_method' => $row_data[6],
				'batch_no'       => $row_data[7],
				'cheque_no'      => $row_data[9],
				'payment_type'   => $row_data[10],
				'renewal_year'   => $row_data[11],
				'remarks'        => 'Migrated from X database',
				'subtotal'       => $row_data[12],
				'tax'            => $row_data[13],
				'total'          => $row_data[14],
				'created'        => date("Y-m-d H:i:s",strtotime($row_data[0])),
			);
			$transaction_item [] = array(
				'receipt_no'  => $row_data[8],
				'description' => "Being Payment for : {$row_data[10]} : $row_data[11]",
				'quantity'    => '1',
				'unit_price'  => $row_data[12],
				'sum'         => $row_data[12],
				'created'     => date("Y-m-d H:i:s",strtotime($row_data[0])),
			);
		}

		$current_member_data = $this->import_and_get_latest_user_id($members_data);

		// Merge into Member Transaction data
		foreach ($transaction_data as $transaction_key => $transaction_value)
		{
			foreach ($current_member_data as $member_key => $member_value)
			{
				if ($transaction_value['type'] == $member_value['type'] && $transaction_value['no'] == $member_value['no'] )
				{
					$transaction_value['member_id']                  = $member_value['id'];
					$transaction_data[$transaction_key]['member_id'] = $member_value['id'];
				}

			}
			$data[] = array(
				'Transaction'      => $transaction_value,
				'Transaction_item' => $transaction_item[$transaction_key]
			);
		}

		// $this->garrick_print($data);
		$this->loadModel('Transaction');
		$this->Transaction->saveMany($data,array('deep' => true));
		$this->setFlash("Migrated successfully.",array('class'=>'success'));
	}

	private function import_and_get_latest_user_id( $members_data='')
	{
		$this->loadModel('Member');
		// Check if there is any member has been registered in latest database
		$current_member_data = $this->Member->find('all',array(
			'fields'     => array('type','no'),
			'conditions' => array(
				'type' => array_column($members_data,'type'),
				'no'   => array_column($members_data,'no'),
			)
		));
		$current_member_data = array_column($current_member_data,'Member');

		// for insert table data only
		$insert_member_data = $members_data;
		if (!empty($current_member_data) )
		{
			// Generating member no string
			foreach ($current_member_data as $key => $value)
			{
				$temp[] = $value['type'].$value['no'];
			}
			// Compare Latest database member string with member no from excel file
			foreach (	$insert_member_data as $member_key => $member_value)
			{
				if ( in_array($member_value['type'].$member_value['no'], $temp) )
				{
					// Delete it so that wont save again into database
					unset(	$insert_member_data[$member_key]);
				}
			}
		}

		// Save unsave member
		if (!empty(	$insert_member_data))
		{
			$this->Member->saveMany(array_values(	$insert_member_data));
		}

		// Get Latest Member ID
		$current_member_data = $this->Member->find('all',array(
			'fields'     => array('id','type','no'),
			'conditions' => array(
				'type' => array_column($members_data,'type'),
				'no'   => array_column($members_data,'no'),
			)
		));

		return array_column($current_member_data,'Member');
	}

	public function q1_instruction()
	{
		$this->setFlash('Question: Migration of data to multiple DB table');
	}
}
// [0] => Date
// [1] => Ref No.
// [2] => Member Name
// [3] => Member No
// [4] => Member Pay Type
// [5] => Member Company
// [6] => Payment By
// [7] => Batch No
// [8] => Receipt No
// [9] => Cheque No
// [10] => Payment Description
// [11] => Renewal Year
// [12] => subtotal
// [13] => totaltax
// [14] => total
