# Regex

PHP comes with a built-in regular expression library *(`PCRE`)*, which provides the `preg_*` functions. Most of the time, these functions require some odd patterns like passing variables by reference and treating `false` or `null` values as errors. To make working with regex a bit more developer-friendly, `tea/regex` provides a clean interface for the `preg_*` functions as well as a human-readable API for building regular expressions.

## Basic Usage.

To build and work with regular expressions, `tea\regex` provides a couple of flexible components.

 - The `RegularExpression` object which represents a regex pattern and provides various regex methods *(`match()`, `replace()`, `split()`)*.
 - The `Builder` which provides a human-readable API for building regular expressions.
 - The `Regex` static facade which provides a static interface for the `RegularExpression` object.

#### Getting Started.

To perform regex functions such as `match`, `replace`, `split` etc, we need to create a `RegularExpression` instance. There are various ways to do this.

	use Tea\Regex\Regex;
	use function Tea\Regex\re;
	use Tea\Regex\RegularExpression;

	// 1. Creating directly.
	$regex = new RegularExpression('^\d+-([A-Za-z]+)-([A-Za-z]+)');
	$matches = $regex->match('254-foo-bar-baz'); // 'Tea\Regex\Result\Matches' object
	$matches->any(); // true
	$matches->all(); // ['254-foo-bar', 'foo', 'bar']
	$matches->group(1); // 'foo'
	$matches->groups(); // ['foo', 'bar']

	// 2. Using the Regex static facade.
	$regex = Regex::create('^[A-Za-z]+');
	$replaced = $regex->replace('STARTING_ALPHABETS', 'foo-bar');

	// 3. Using the re() function.
	$regex = re('^[A-Za-z]+');
	$exploded = $regex->split();