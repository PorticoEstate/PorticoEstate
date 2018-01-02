<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function get_external_generator()
	{
		$numberGenerator = CreateObject('booking.sobilling_sequential_number_generator');
		assert('$numberGenerator instanceof booking_sobilling_sequential_number_generator')
			AND pass_test('$numberGenerator instanceof booking_sobilling_sequential_number_generator');
		$numberGeneratorInstance = $numberGenerator->get_generator_instance('external');
		assert('$numberGeneratorInstance instanceof booking_sobilling_sequential_number_generator_instance')
			AND pass_test('$numberGeneratorInstance instanceof booking_sobilling_sequential_number_generator_instance');
		return $numberGeneratorInstance;
	}

	function testNumberGenerator( PhpgwContext $c )
	{
		$numberGeneratorInstance = get_external_generator();

		###############################################################
		$logic_exception_if_no_active_transaction = false;
		try
		{
			$numberGeneratorInstance->increment();
		}
		catch (LogicException $e)
		{
			$logic_exception_if_no_active_transaction = true;
		}
		assert('$logic_exception_if_no_active_transaction') and pass_test('logic_exception_if_no_active_transaction');

		#############################################################
		print_info("Pre Transaction");
		$c->getDb()->transaction_begin(); //Begin transaction
		$no_logic_exception_if_active_transaction = false;

		try
		{
			$numberGeneratorInstance->increment();
			$currentValue = $numberGeneratorInstance->get_current();
			$numberGeneratorInstance->increment();
			$numberGeneratorInstance->increment();
			$no_logic_exception_if_active_transaction = true;
		}
		catch (LogicException $e)
		{

		}
		$c->getDb()->transaction_abort();

		assert($no_logic_exception_if_active_transaction) AND pass_test("no_logic_exception_if_active_transaction");

		print_info("Post Transaction");
		#############################################################

		$previousNumberGeneratorInstance = $numberGeneratorInstance;
		$numberGeneratorInstance = get_external_generator();

		$new_generator_is_created_post_transaction = $previousNumberGeneratorInstance != $numberGeneratorInstance;

		assert('$new_generator_is_created_post_transaction') AND pass_test("new_generator_is_created_post_transaction");

		#############################################################
		print_info("Start Transaction");
		$c->getDb()->transaction_begin(); //Begin transaction
		#############################################################

		$cannotGetCurrentWithoutLock = false;

		try
		{
			$numberGeneratorInstance->get_current();
		}
		catch (Exception $e)
		{
			print_info($e->getMessage());
			$cannotGetCurrentWithoutLock = true;
		}

		assert('$cannotGetCurrentWithoutLock') AND pass_test("Cannot get current without lock");

		##############################################################

		$canGetCurrentWithLock = false;
		$numberGeneratorInstance->increment(); //Locked
		print_info("Locked (->increment)");
		$previousValue = $numberGeneratorInstance->get_current();
		$numberGeneratorInstance->increment();
		$canGetCurrentWithLock = true;
		assert($canGetCurrentWithLock) AND pass_test("Able to get_current with lock");

		############################################################
		assert('$numberGeneratorInstance->get_current() === $previousValue+1') AND pass_test("Incremented value by one");
		############################################################

		$c->getDb()->transaction_abort();
		print_info("Abort transaction");

		############################################################
		$unableToUseGeneratorPostTransaction = false;

		try
		{
			$numberGeneratorInstance->get_current();
		}
		catch (LogicException $e)
		{
			$unableToUseGeneratorPostTransaction = true;
		}

		assert($unableToUseGeneratorPostTransaction) AND pass_test("Unable to use generator post transaction");

		print_info("Sleep test");
		$c->getDb()->transaction_begin(); //Begin transaction
		$numberGeneratorInstance = get_external_generator();
		sleep(4);
		for ($i = 0; $i < 10; $i++)
		{
			print_info($numberGeneratorInstance->increment()->get_current());
		}
		$c->getDb()->transaction_abort();
		sleep(4);

		print_info("Sleep test 2");
		$c->getDb()->transaction_begin(); //Begin transaction
		$numberGeneratorInstance = get_external_generator();
		sleep(4);
		for ($i = 0; $i < 10; $i++)
		{
			print_info($numberGeneratorInstance->increment()->get_current());
		}
		$c->getDb()->transaction_commit();
		sleep(4);
	}

	function pass_test( $message )
	{
		echo "PASSED: " . $message . "\n";
	}

	function print_info( $message )
	{
		echo "INFO: " . $message . "\n";
	}

	// Create a handler function
	function my_assert_handler( $file, $line, $code )
	{
		echo "Assertion Failed:\n" .
		"File '$file'\n" .
		"Line '$line'\n" .
		"Code '$code'\n";
	}
	// Set up the callback
	assert_options(ASSERT_CALLBACK, 'my_assert_handler');

	PhpgwEntry::phpgw_call('testNumberGenerator');
