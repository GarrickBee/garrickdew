<?php

class FileUploadController extends AppController {

	public function index()
	{
		// Receive File
		$this->set('title', __('File Upload Answer'));
		$file_uploads = $this->FileUpload->find('all');
		$this->set(compact('file_uploads'));
	}

	public function add()
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
		// CSV file type
		$file_type_required = array(
			'text/csv',
			'text/plain',
			'application/csv',
			'text/comma-separated-values',
			'application/excel',
			'application/vnd.ms-excel',
			'application/vnd.msexcel',
			'text/anytext',
			'application/octet-stream',
			'application/txt',
		);
		if (!in_array($file['type'],$file_type_required)){
			return $this->setFlash('Error on upload. Only CSV file type upload are allowed.');
		}
		
		// Import File
		$data_imported = $this->parse_csv( file_get_contents($file['tmp_name']) );
		$data_field    = array_map('strtolower',$data_imported[0]);
		unset($data_imported[0]);
		$field_allow   = array('name','email');		// Table field allowed

		// Check table field exist
		foreach ($data_field as $field_key => $field_value)
		{
			if ( !in_array(strtolower($field_value),$field_allow))
			{
				return $this->setFlash("Error on uploading. Field Value : {$field_value} does not exist in database.");
			}
		}
		// Genrate Cake PHP data format
		foreach ($data_imported as $data_key => $data_value)
		{
			$insert_data[$data_key]['name']  = empty($data_value[array_search('name', $data_field)])?'':$data_value[array_search('name', $data_field)];
			$insert_data[$data_key]['email'] = empty($data_value[array_search('email', $data_field)])?'':$data_value[array_search('email', $data_field)];
		}

		// Insert Data
		$this->FileUpload->savemany($insert_data);
		$this->setFlash("File imported successfully.",array('class'=>'success'));
		if ( !$this->FileUpload->save() )
		{
			$this->setFlash($this->FileUpload->error(),array('class'=>'danger'));
		}
		return $this->redirect(array('controller' => 'FileUpload','action'=>'index'));
	}

	private function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
	{
		return array_map(
			function ($line) use ($delimiter, $trim_fields) {
				return array_map(
					function ($field) {
						return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
					},
					$trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line)
				);
			},
			preg_split(
				$skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s',
				preg_replace_callback(
					'/"(.*?)"/s',
					function ($field) {
						return urlencode(utf8_encode($field[1]));
					},
					$enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string)
					)
					) );
				}

			}
