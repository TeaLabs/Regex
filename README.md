# Regex

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
