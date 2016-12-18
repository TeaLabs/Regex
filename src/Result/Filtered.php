<?php
namespace Tea\Regex\Result;

use Exception;
use ArrayIterator;
use JsonSerializable;
use Tea\Regex\Helpers;
use Tea\Regex\Exception\InvalidReplacementKey;
use Tea\Regex\Exception\UnknownReplacementKey;
use Tea\Regex\Exception\IllegalReplacementTypeAccess;

class Filtered extends Replacement
{
	/**
	 * Get the replaced string. If the subject was an array, the first element
	 * will be returned.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->isString() ? $this->replaced : Helpers::iterFirst($this->replaced, null, '');
	}
}
