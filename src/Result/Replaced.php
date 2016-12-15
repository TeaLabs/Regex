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

	public static function for($pattern, $replacement, $subject, $limit)
	{
		try {
			list($result, $count) = is_callable($replacement) ?
				static::doReplacementWithCallable($pattern, $replacement, $subject, $limit) :
				static::doReplacement($pattern, $replacement, $subject, $limit);
		} catch (Exception $exception) {
			throw RegexFailed::replace($pattern, $subject, $exception->getMessage());
		}

		if ($result === null) {
			throw RegexFailed::replace($pattern, $subject, static::lastPregError());
		}

		return new static($pattern, $replacement, $subject, $result, $count);
	}

	protected static function doReplacement($pattern, $replacement, $subject, $limit)
	{
		$count = 0;

		$result = preg_replace($pattern, $replacement, $subject, $limit, $count);

		return [$result, $count];
	}

	protected static function doReplacementWithCallable($pattern, callable $replacement, $subject, $limit)
	{
		$replacement = function (array $matches) use ($pattern, $subject, $replacement) {
			return $replacement(new MatchResult($pattern, $subject, true, $matches));
		};

		$count = 0;

		$result = preg_replace_callback($pattern, $replacement, $subject, $limit, $count);

		return [$result, $count];
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


}
