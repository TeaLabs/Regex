<?php
namespace Tea\Regex\Tests;

use Tea\Regex\Regex;
use Tea\Regex\RegexFailed;

class RegexTest extends TestCase
{

/***Match***/

	/** @test */
	public function it_can_determine_if_a_match_was_made()
	{
		$this->assertTrue(Regex::match('abc', 'abc')->hasMatch());
		$this->assertFalse(Regex::match('abc', 'def')->hasMatch());
	}

	/**
	 * @expectedException \Tea\Regex\RegexFailed
	 */
	// public function _test_it_throws_an_exception_if_a_match_throws_an_error()
	// {
	// 	echo "\n****\n".Regex::compile('/abc/qw');
	// 	return;
	// 	$this->expectException(RegexFailed::class);
	// 	$this->expectExceptionMessage(
	// 		RegexFailed::match('/abc/qw', 'abc', 'preg_match(): No ending delimiter \'/\' found')->getMessage()
	// 	);

	// 	Regex::match('/abc/qw', 'abc');
	// }

	/**
	 * @expectedException \Tea\Regex\RegexFailed
	 */
	// public function _test_it_throws_an_exception_if_a_match_throws_a_preg_error()
	// {
	// 	$this->expectException(RegexFailed::class);
	// 	$this->expectExceptionMessage(
	// 		RegexFailed::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', 'PREG_BACKTRACK_LIMIT_ERROR')->getMessage()
	// 	);

	// 	Regex::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar');
	// }

	/** @test */
	public function it_can_retrieve_the_matched_result()
	{
		$this->assertEquals('abc', Regex::match('/abc/', 'abcdef')->result());
	}

	/** @test */
	public function it_returns_null_if_a_result_is_queried_for_a_subject_that_didnt_match_a_pattern()
	{
		$this->assertNull(Regex::match('/abc/', 'def')->result());
	}

	/** @test */
	public function it_can_retrieve_a_matched_group()
	{
		$this->assertEquals('a', Regex::match('/(a)bc/', 'abcdef')->group(1));
	}

	/**
	 * @expectedException \Tea\Regex\RegexFailed
	 */
	// public function _test_it_throws_an_exception_if_a_non_existing_group_is_queried()
	// {
	// 	$this->expectException(RegexFailed::class);
	// 	$this->expectExceptionMessage(RegexFailed::indexedGroupDoesntExist('/(a)bc/', 'abcdef', 2)->getMessage());

	// 	Regex::match('/(a)bc/', 'abcdef')->group(2);
	// }

	/** @test */
	public function it_can_retrieve_a_matched_named_group()
	{
		$this->assertSame('a', Regex::match('/(?<samename>a)bc/', 'abcdef')->namedGroup('samename'));
	}

	/**
	 * @expectedException \Tea\Regex\RegexFailed
	 */
	// public function _test_it_throws_an_exception_if_a_non_existing_named_group_is_queued()
	// {
	// 	$this->expectException(RegexFailed::class);
	// 	$this->expectExceptionMessage(
	// 		RegexFailed::namedGroupDoesntExist('/(?<samename>a)bc/', 'abcdef', 'invalidname')->getMessage()
	// 	);

	// 	Regex::match('/(?<samename>a)bc/', 'abcdef')->namedGroup('invalidname');
	// }

/***End Match***/

/***Match All***/

	 /** @test */
	public function it_can_determine_if_a_match_all_was_made()
	{
		$this->assertTrue(Regex::matchAll('/a/', 'aaa')->hasMatch());
		$this->assertFalse(Regex::matchAll('/b/', 'aaa')->hasMatch());
	}

	/** @test */
	public function it_can_retrieve_the_matched_results()
	{
		$results = Regex::matchAll('/a/', 'aaa')->results();

		$this->assertCount(3, $results);
		$this->assertEquals('a', $results[0]->result());
		$this->assertEquals('a', $results[1]->result());
		$this->assertEquals('a', $results[2]->result());
	}

	/** @test */
	public function it_returns_an_empty_array_if_a_result_is_queried_for_a_subject_that_didnt_match_a_pattern()
	{
		$this->assertEmpty(Regex::matchAll('/abc/', 'def')->results());
	}

	// public function _test_it_throws_an_exception_if_a_match_throws_an_error()
	// {
	// 	$this->expectException(RegexFailed::class);
	// 	$this->expectExceptionMessage(
	// 		RegexFailed::match('/abc', 'abc', 'preg_match_all(): No ending delimiter \'/\' found')->getMessage()
	// 	);

	// 	Regex::matchAll('/abc', 'abc');
	// }


	// public function _test_it_throws_an_exception_if_a_match_throws_a_preg_error()
	// {
	// 	$this->expectException(RegexFailed::class);
	// 	$this->expectExceptionMessage(
	// 		RegexFailed::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', 'PREG_BACKTRACK_LIMIT_ERROR')->getMessage()
	// 	);

	// 	Regex::matchAll('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar');
	// }

	/** @test */
	public function it_can_retrieve_groups_from_the_matched_results()
	{
		$results = Regex::matchAll('/a(b)/', 'abab')->results();

		$this->assertCount(2, $results);
		$this->assertEquals('ab', $results[0]->result());
		$this->assertEquals('b', $results[0]->group(1));
		$this->assertEquals('ab', $results[1]->result());
		$this->assertEquals('b', $results[1]->group(1));
	}

/***End Match All***/


/***Replace***/

 	/** @test */
	public function it_can_replace_a_pattern_with_a_string()
	{
		$this->assertEquals('bbbb', Regex::replace('/a/', 'b', 'aabb')->result());
	}

	/** @test */
	public function it_can_replace_a_patterns_with_a_callback()
	{
		$this->assertEquals('ababc', Regex::replace('/a(b)/', function ($match) {
			return $match->result().$match->result();
		}, 'abc')->result());
	}

	/** @test */
	public function it_can_replace_an_array_of_patterns_with_a_replacement()
	{
		$this->assertEquals('cccc', Regex::replace(['/a/', '/b/'], 'c', 'aabb')->result());
	}

	/** @test */
	public function it_can_replace_an_array_of_patterns_with_an_array()
	{
		$this->assertEquals('ccdd', Regex::replace(['/a/', '/b/'], ['c', 'd'], 'aabb')->result());
	}

	/** @test */
	public function it_can_limit_the_amount_of_replacements()
	{
		$this->assertEquals('babb', Regex::replace('/a/', 'b', 'aabb', 1)->result());
	}

	/** @test */
	public function it_counts_the_amount_of_replacements()
	{
		$this->assertEquals(2, Regex::replace('/a/', 'b', 'aabb')->count());
	}

/***End Replace***/

}
