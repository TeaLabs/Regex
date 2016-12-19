<?php
namespace Tea\Regex\Contracts;

interface Pattern
{
	/**
	 * Get the regex pattern body.
	 *
	 * @return string
	 */
	public function getBody();

	/**
	 * Get the regex pattern modifiers.
	 *
	 * @return string
	 */
	public function getModifiers();

	/**
	 * Get the regex pattern delimiter.
	 *
	 * @return string
	 */
	public function getDelimiter();

	/**
	 * Cast the regex pattern object to string.
	 *
	 * @return string
	 */
	public function __toString();
}

