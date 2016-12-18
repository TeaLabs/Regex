<?php
namespace Tea\Regex\Tests\Mocks;

/**
*
*/
class StringObject
{
	public $value;

	public function __construct($value = '')
	{
		$this->value = (string) $value;
	}

	public static function create($value='')
	{
		return new static($value);
	}

	public function __toString()
	{
		return $this->value;
	}
}