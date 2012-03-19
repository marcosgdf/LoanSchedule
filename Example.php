<?php

require 'LoanSchedule.php';

$calc = new LoanSchedule();

//The amount of debt we want to 'repay'
$calc->setCapital(1000);
//The precission of the round() function. Not used if useBCMath is set to true
$calc->setPrecision(2);
//If we want to use BCMath instead of php mathematic functions, set it to true
$calc->useBCMath(false);
//If you want to use BCMath, you should set its precission with the bcscale function
//bcscale(2);

//The interest of each period.
//i.e If we have an anual interest of 3% and we are going to pay monthly, this
//    amount will be 0.03/12
$calc->setInterest(0.03);
//The number of payments.
//i.e If we are going to pay monthly and during 4 years, this number will be 4*12
$calc->setPeriods(4);

/**
 * Now it's time to generate the loan repayment table
 */

try {

	echo '<table>
		<tr>
			<td>Annuity</td>
			<td>Interest</td>
			<td>Amortization</td>
			<td>Amortized</td>
			<td>Pending cap.</td>
		</tr>';
	foreach ($calc->generateSingleRepayment() as $period)
	{
		echo '<tr>
				<td>'.$period->amount.'</td>
				<td>'.$period->interest.'</td>
				<td>'.$period->amortization.'</td>
				<td>'.$period->amortized.'</td>
				<td>'.$period->remaining.'</td>
			</tr>';
	}

	echo '</table>';

} catch (Exception $e) {

	die($e->getMessage());

}