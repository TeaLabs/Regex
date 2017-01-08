# Regex

## Available Components

#### The `RegularExpression` object.

Represents a regex pattern and provides the various regex functions. Ie: `match()`, `replace()`, `split()` e.t.c.

#### The `Builder`.

Provides a human-readable API for building regular expressions. Integrates regular expressions into the programming language, thereby making them easy to read and maintain. Regular Expressions are created by using chained methods.

#### The `Regex` static facade.



## Quick Start

#### The RegularExpression Instance.


Creating an instance.

	use Tea\Regex\RegularExpression;
	$regex = new RegularExpression('^([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)$');

	// Using the Regex facade class (more about this later).
	use Tea\Regex\Regex;
	$regex = Regex::create('^([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)$');

	// Using the re() function.
	use function Tea\Regex\re;
	$regex = re('^([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)$');
