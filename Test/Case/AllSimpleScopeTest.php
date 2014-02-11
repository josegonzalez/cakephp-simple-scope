<?php
/**
 * All SimpleScope plugin tests
 */
class AllSimpleScopeTest extends CakeTestCase {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All SimpleScope test');

		$path = CakePlugin::path('SimpleScope') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
