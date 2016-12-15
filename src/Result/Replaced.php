<?php
namespace Tea\Regex\Result;

use Exception;

class Replaced extends Result
{
	/**
	 * @var string|iterable
	 */
	protected $replacement;

	/**
	 * @var string|iterable
	 */
	protected $result;

	/**
	 * @var int
	 */
	protected $count;

	public function __construct($pattern, $subject, $replacement, $result, $count)
	{
		parent::__construct($pattern, $subject);

		$this->replacement = $replacement;
		$this->result = $result;
		$this->count = $count;
	}


	/**
	 * Get the raw result.
	 *
	 * @return string|array
	 */
	public function result()
	{
		return $this->result;
	}

	/**
	 * Count the number of found matches.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->count;
	}

	/**
	 * Determine whether the replaced result is an array.
	 *
	 * @return bool
	 */
	public function isArray()
	{
		return is_array($this->result);
	}


}
