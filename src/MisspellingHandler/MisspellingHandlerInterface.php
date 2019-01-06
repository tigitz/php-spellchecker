<?php

declare(strict_types=1);

namespace PhpSpellcheck\MisspellingHandler;

use PhpSpellcheck\MisspellingInterface;

interface MisspellingHandlerInterface
{
    /**
     * @param MisspellingInterface[] $misspellings
     *
     * @return mixed
     */
    public function handle(iterable $misspellings);
}
