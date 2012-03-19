<?php

/**
 * Copyright (c) 2012 Marcos García de La Fuente
 * 
 * Contact: marcosgdf@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Annuity
{
	/**
	 * Amount of money to pay in that period
	 * @var float
	 */
	public $amount;
	/**
	 * Interest paid
	 * @var float
	 */
	public $interest;
	/**
	 * Amortized money of the loan
	 * @var float
	 */
	public $amortization;
	/**
	 * Amount of money amortized since the life of the loan
	 * @var float
	 */
	public $amortized;
	/**
	 * Remaining money of the loan
	 * @var float
	 */
	public $remaining;
}

/**
 * Loan Schedule Calculator Class.
 * 
 * @link http://github.com/marcosgdf/LoanSchedule
 * @version 0.1
 */
class LoanSchedule
{
	public $capital, $interest, $periods = null;
	public $precision = 2;
	public $useBCMath = false;

	/**
	 * Sets the capital of the loan
	 * @author Marcos García   <marcosgdf@gmail.com>
	 * @param  float $capital
	 */
	public function setCapital($capital)
	{
		$this->capital = (float)$capital;
	}

	/**
	 * Sets the period interest of the loan
	 * @author Marcos García    <marcosgdf@gmail.com>
	 * @param  float $interest
	 */
	public function setInterest($interest)
	{
		$this->interest = (float)$interest;
	}

	/**
	 * Sets the number of decimals you want to return
	 * @author Marcos García     <marcosgdf@gmail.com>
	 * @param  integer $precision
	 */
	public function setPrecision($precision)
	{
		$this->precision = (int)$precision;
	}

	/**
	 * You can choose if you want to use BCMath functions or the native ones
	 * @author Marcos García   <marcosgdf@gmail.com>
	 * @param  boolean $boolean
	 */
	public function useBCMath($boolean)
	{
		$this->useBCMath = (boolean)$boolean;
	}

	/**
	 * The number of periods of the loan. (i.e 1 year = 12 periods/payments)
	 * @author Marcos García  <marcosgdf@gmail.com>
	 * @param  integer $period
	 */
	public function setPeriods($period)
	{
		$this->periods = (int)$period;
	}

	/**
	 * Checks if all necessary data was entered
	 * @author Marcos García <marcosgdf@gmail.com>
	 */
	private function check()
	{
		if ($this->capital && $this->interest && $this->periods)
		{
			if ($this->periods == 0)
			{
				throw new Exception('Periods can\'t be 0.');
			}
		}
		else
		{
			throw new Exception('Missing parameters.');
		}
	}

	/**
	 * Generates the payment table with equal payments every period
	 * @author Marcos García <marcosgdf@gmail.com>
	 * @return array
	 */
	public function generateEqualPayment()
	{
		$this->check();

		if ($this->useBCMath)
		{
			$installment = bcdiv(bcmul($this->capital, $this->interest), (bcsub(1, bcpow(bcadd(1, $this->interest), bcmul($this->periods, -1)))));
		}
		else
		{
			$installment = ($this->capital*$this->interest)/(1-pow(1+$this->interest, ($this->periods*-1)));
		}
		
		$tmp_remaining = $this->capital;
		$tmp_amortized = 0;

		$to_return = array();

		for ($i = 0; $i < $this->periods; $i++)
		{
			if ($this->useBCMath)
			{
				$interest = bcmul($tmp_remaining, $this->interest);
				$amortization = bcsub($installment, $interest);

				$tmp_remaining = bcsub($tmp_remaining, $amortization);
				$tmp_amortized = bcadd($tmp_amortized, $amortization);
			}
			else
			{
				$interest = $tmp_remaining * $this->interest;
				$amortization = $installment - $interest;

				$tmp_remaining = $tmp_remaining - $amortization;
				$tmp_amortized = $tmp_amortized + $amortization;
			}
			

			$annuity = new Annuity();
			$annuity->amount = round($installment, $this->precision);
			$annuity->interest = round($interest, $this->precision);
			$annuity->amortized = round($tmp_amortized, $this->precision);
			$annuity->remaining = round($tmp_remaining, $this->precision);
			$annuity->amortization = round($amortization, $this->precision);

			$to_return[] = $annuity;
			
		}

		return $to_return;
	}

	/**
	 * Generates the payment table with a constant amortization method
	 * @author Marcos García <marcosgdf@gmail.com>
	 * @return array 
	 */
	public function generateConstantAmortization()
	{
		$this->check();

		if ($this->useBCMath)
		{
			$amortization = bcdiv($this->capital, $this->periods);
		}
		else
		{
			$amortization = $this->capital/$this->periods;
		}
		
		$tmp_remaining = $this->capital;
		$tmp_amortized = 0;

		$to_return = array();

		for ($i = 0; $i < $this->periods; $i++)
		{
			if ($this->useBCMath)
			{
				$interest = bcmul($tmp_remaining, $this->interest);
				$installment = bcadd($interest, $amortization);

				$tmp_remaining = bcsub($tmp_remaining, $amortization);
				$tmp_amortized = bcadd($tmp_amortized, $amortization);
			}
			else
			{
				$interest = $tmp_remaining * $this->interest;
				$installment = $interest + $amortization;

				$tmp_remaining = $tmp_remaining - $amortization;
				$tmp_amortized = $tmp_amortized + $amortization;
			}
			

			$annuity = new Annuity();
			$annuity->amount = round($installment, $this->precision);
			$annuity->interest = round($interest, $this->precision);
			$annuity->amortized = round($tmp_amortized, $this->precision);
			$annuity->remaining = round($tmp_remaining, $this->precision);
			$annuity->amortization = round($amortization, $this->precision);

			$to_return[] = $annuity;
			
		}

		return $to_return;
	}

	/**
	 * Generates the payment table with an Interest Only Repayment method
	 * @author Marcos García <marcosgdf@gmail.com>
	 * @return array
	 */
	public function generateInterestOnly()
	{
		$this->check();
		
		$tmp_remaining = $this->capital;
		$tmp_amortized = 0;

		$to_return = array();

		for ($i = 0; $i < $this->periods; $i++)
		{
			if ($i == ($this->periods - 1 ))
			{
				$amortization = $this->capital;
			}
			else
			{
				$amortization = 0;
			}

			if ($this->useBCMath)
			{
				$interest = bcmul($tmp_remaining, $this->interest);
				$installment = bcadd($interest, $amortization);

				$tmp_remaining = bcsub($tmp_remaining, $amortization);
				$tmp_amortized = bcadd($tmp_amortized, $amortization);
			}
			else
			{
				$interest = $tmp_remaining * $this->interest;
				$installment = $interest + $amortization;

				$tmp_remaining = $tmp_remaining - $amortization;
				$tmp_amortized = $tmp_amortized + $amortization;
			}
			

			$annuity = new Annuity();
			$annuity->amount = round($installment, $this->precision);
			$annuity->interest = round($interest, $this->precision);
			$annuity->amortized = round($tmp_amortized, $this->precision);
			$annuity->remaining = round($tmp_remaining, $this->precision);
			$annuity->amortization = round($amortization, $this->precision);

			$to_return[] = $annuity;
			
		}

		return $to_return;
	}

	/**
	 * Generates the payment table with a Single Repayment method
	 * @author Marcos García <marcosgdf@gmail.com>
	 * @return array
	 */
	public function generateSingleRepayment()
	{
		$this->check();
		
		$tmp_remaining = $this->capital;
		$tmp_amortized = 0;

		$to_return = array();

		for ($i = 0; $i < $this->periods; $i++)
		{
			$amortization = 0;
			$installment = 0;

			if ($this->useBCMath)
			{
				$interest = bcmul($tmp_remaining, $this->interest);

				if ($i == ($this->periods - 1))
				{
					$amortization = bcadd($tmp_remaining, $interest);
					$installment = bcadd($tmp_remaining, $interest);
				}

				$tmp_remaining = bcadd($tmp_remaining, $interest);
				$tmp_amortized = bcadd($tmp_amortized, $amortization);
			}
			else
			{
				$interest = $tmp_remaining * $this->interest;

				if ($i == ($this->periods - 1))
				{
					$amortization = $tmp_remaining + $interest;
					$installment = $tmp_remaining + $interest;
				}

				$tmp_remaining = $tmp_remaining + $interest;
				$tmp_amortized = $tmp_amortized + $amortization;
			}			

			$annuity = new Annuity();
			$annuity->amount = round($installment, $this->precision);
			$annuity->interest = round($interest, $this->precision);
			$annuity->amortized = round($tmp_amortized, $this->precision);
			$annuity->remaining = round($tmp_remaining, $this->precision);
			$annuity->amortization = round($amortization, $this->precision);

			$to_return[] = $annuity;
			
		}

		return $to_return;
	}
}