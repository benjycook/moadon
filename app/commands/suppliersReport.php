<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class suppliersReport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'suppliersReport';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'suppliersReport';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		
	$fp = fopen('suppliers.csv', 'w');
	$headers = ['ע.מ/ ח.פ','שם פרטי','שם משפחה','נייד','דוא"ל','שם ספק','יישוב','כתובת','טלפון ראשי','קטגוריה'];
	fputcsv($fp,$headers);
	$suppliers = Supplier::with('sitedetails')->get();
	$cities    = City::lists('name','id');
	$list = [];
	foreach ($suppliers as $supplier) {
		$contact = $supplier->contacts()->first();
		if(!$contact)
			$contact = ['firstName'=>'','lastName'=>'','mobile'=>'','email'=>''];
		$city = '';
		if($supplier['sitedetails']['cities_id'])
			$city = $cities[$supplier['sitedetails']['cities_id']];
		$cat = '';
		$category = $supplier->categories()->first();
		if($category)
			$cat = $category->name;
		$list[] = [
					$supplier->idNumber,$contact['firstName'],$contact['lastName'],$contact['mobile'],
					$contact['email'],$supplier['sitedetails']['supplierName'],$city,$supplier['sitedetails']['address'],
					$supplier['sitedetails']['phone2'],$cat
				];
	}
	foreach ($list as $fields) {
	    fputcsv($fp, $fields);
	}

	fclose($fp);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
