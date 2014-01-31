<?php
require_once('../IbanConvertor.php');

class IbanConvertorTest extends \PHPUnit_Framework_TestCase
{
	private $convertor;
	public function setUp()
	{
		$this->convertor = new IbanConvertor;
	}

	/**
	 * @group unit
	 */
	public function testConvert()
	{
		$this->assertEquals("SK8802000000002616082058", $this->convertor->convert("0200", "2616082058"));
	}

	/**
	 * @group unit
	 */
	public function testConvertHumanReadable() 
	{
		$this->assertEquals("SK43 8360 5207 0042 0320 2233", $this->convertor->convertHumanReadable("8360", "4203202233", "520700"));
	}

	/**
	 * @group unit
	 * @expectedException IbanConvertorException 
	 */
	public function testConvertInvalidAccountInfos()
	{
		$this->convertor->convert("85", "1234567891");
	}
}