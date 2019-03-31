<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
//		$employeeTable='employee';
		//$departmentTable='department';

		$employeeTableIsExists = Schema::hasTable('employee');
		if ($employeeTableIsExists) {
			Schema::drop('employee');
		}

		$departmentTableIsExists = Schema::hasTable('department');
		if ($departmentTableIsExists) {
			Schema::drop('department');
		}

		Schema::create('department', function ($table) {
			$table->integer('id');
			$table->string('department');
			$table->primary('id');
		});
		$departmentData = $this->csvFileToArray('data/department_table.csv');
		DB::table('department')->insert($departmentData);

		Schema::create('employee', function ($table) {
			$table->integer('id');
			$table->integer('dep_id');
			$table->string('full_name');
			$table->integer('salary');
			$table->primary('id');
			$table->foreign('dep_id')->references('id')->on('department');
		});
		$employeeData = $this->csvFileToArray('data/employee_table.csv');
		DB::table('employee')->insert($employeeData);
	}

	private function csvFileToArray(string $filePatch) : array
	{
		$rows = array_map(function ($item) {
			$clearItem = preg_replace ('/[^a-z0-9;_ ]/i',"",$item);
			return str_getcsv($clearItem, ';');
		}, file($filePatch));

		$header = array_shift($rows);

		$csv = array();
		foreach ($rows as $row)
			$csv[] = array_combine($header, $row);

		return $csv;
	}
}
