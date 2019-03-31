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
		$employeeTable = Schema::hasTable('employee_table');
		if ($employeeTable) {
			Schema::drop('employee_table');
		}

		$departmentTableIsExists = Schema::hasTable('department_table');
		if ($departmentTableIsExists) {
			Schema::drop('department_table');
		}

		Schema::create('department_table', function ($table) {
			$table->integer('id');
			$table->string('department');
			$table->primary('id');
		});
		$departmentTable = $this->csvFileToArray('data/department_table.csv');
		DB::table('department_table')->insert($departmentTable);

		Schema::create('employee_table', function ($table) {
			$table->integer('id');
			$table->integer('dep_id');
			$table->string('full_name');
			$table->integer('salary');
			$table->primary('id');
			$table->foreign('dep_id')->references('id')->on('department_table');
		});
		$employeeTable = $this->csvFileToArray('data/employee_table.csv');
		DB::table('employee_table')->insert($employeeTable);
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
