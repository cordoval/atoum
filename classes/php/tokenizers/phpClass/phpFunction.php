<?php

namespace mageekguy\atoum\php\tokenizers\phpClass;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass\phpFunction
;

class phpFunction extends php\tokenizers\phpFunction
{
	public function getIteratorInstance()
	{
		return new phpFunction\iterator();
	}

	public function canTokenize(tokenizer\tokens $tokens)
	{
		$canTokenize = parent::canTokenize($tokens);

		if (
				$canTokenize === false
				&&
				(
					$tokens->currentTokenHasName(T_FINAL)
					||
					$tokens->currentTokenHasName(T_ABSTRACT)
					||
					$tokens->currentTokenHasName(T_STATIC)
					||
					$tokens->currentTokenHasName(T_PUBLIC)
					||
					$tokens->currentTokenHasName(T_PROTECTED)
					||
					$tokens->currentTokenHasName(T_PRIVATE)
				)
				&&
				$tokens->valid() === true
			)
		{
			$key = $tokens->key();

			$goToNextToken = true;

			while ($tokens->valid() === true && $goToNextToken === true)
			{
				$tokens->next();

				switch (true)
				{
					case $tokens->currentTokenHasName(T_WHITESPACE):
					case $tokens->currentTokenHasName(T_FINAL):
					case $tokens->currentTokenHasName(T_ABSTRACT):
					case $tokens->currentTokenHasName(T_STATIC):
					case $tokens->currentTokenHasName(T_PUBLIC):
					case $tokens->currentTokenHasName(T_PROTECTED):
					case $tokens->currentTokenHasName(T_PRIVATE):
						break;

					case  $tokens->currentTokenHasName(T_FUNCTION):
						$canTokenize = parent::canTokenize($tokens);
						$goToNextToken = false;
						break;

					default:
						$goToNextToken = false;
				}
			}

			$tokens->seek($key);
		}

		return $canTokenize;
	}
}

?>