<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Employee;

Route::get('/', function () {
	$reusult_1 = DB::select(/** @lang MySQL */
		'
SELECT dt.id,
       dt.department,
       MAX(et.salary) max_salary,
       SUM(et.salary) sum_salary,
       MIN(et.salary) min_salary
FROM departments dt
JOIN employees et ON dt.id = et.dep_id
GROUP BY dt.id,
         dt.department
HAVING MIN(et.salary) > 1000
');

	$reusult_2 = DB::select(/** @lang MySQL */
		"
SELECT dt.id,
       dt.department,
       MAX(et.salary) max_salary,
       SUM(et.salary) sum_salary,
       MIN(et.salary) min_salary,
       SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT et.full_name
                                    ORDER BY et.salary DESC SEPARATOR ', '), ', ', 1) full_name
FROM employees et
JOIN departments dt ON et.dep_id = dt.id
GROUP BY dt.id,
         dt.department
HAVING MIN(et.salary) > 1000
	"
);

	$reusult_3 = DB::select(/** @lang MySQL */
		"
SELECT
((SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(et.salary 
ORDER BY
   et.salary), ',', FLOOR(1 + ((COUNT(et.salary) - 1) / 2))), ',', - 1)) + (SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(et.salary 
ORDER BY
   et.salary), ',', CEILING(1 + ((COUNT(et.salary) - 1) / 2))), ',', - 1))) / 2 AS median 
FROM
   employees et;
		");

	$reusult_4 = DB::select(/** @lang MySQL */
		"
SELECT
   dt.id,
   dt.department,
   AVG(et2.salary) avg_salary,
   SUM(et2.salary) sum_salary,
   (
      SELECT
((SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(et.salary 
      ORDER BY
         et.salary), ',', FLOOR(1 + ((COUNT(et.salary) - 1) / 2))), ',', - 1)) + (SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(et.salary 
      ORDER BY
         et.salary), ',', CEILING(1 + ((COUNT(et.salary) - 1) / 2))), ',', - 1))) / 2 AS median 
      FROM
         employees et 
      WHERE
         et.dep_id = dt.id
   )
   median_salary 
FROM
   departments dt 
   JOIN
      employees et2 
      ON dt.id = et2.dep_id 
GROUP BY
   dt.id,
    dt.department
		");

	return view('welcome',[$reusult_1,$reusult_2,$reusult_3,$reusult_4]);
});
