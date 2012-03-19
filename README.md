## Loan Schedule Calculator Class ##

PHP5 required. Calcs can be used with BCMath. 

It is used to generate repayment loan tables of 4 types of loan:
1. Equal payment (French method)
2. Constant amortization (Equal principal payment method)
3. Interest only (American method)
4. Single repayment

### How to use it ###
Check Example.php to see an example of usage

IMPORTANT: Errors are handled with exceptions. They can be either one of the following ones:
1. Periods can't be 0.
2. Missing parameters.

### Methods available ###
1. generateSingleRepayment()
2. generateEqualPayment()
3. generateConstantAmortization()
4. generateInterestOnly()

All of these functions will return an array. Each element will contain an Annuity object, that has the following properties:

* amount (float - amount of the payment)
* interest (float - interest paid in the period)
* amortization (float - amount of money amortized in the period)
* amortized (float - amount of money amortized since the life of the loan)
* remaining (float - pending money of the loan)

### Questions? Bugs? ###
Check <http://github.com/marcosgdf/LoanSchedule> or contact me at <marcosgdf@gmail.com>