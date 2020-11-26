<?php

namespace TRegx\Replace;

use InvalidArgumentException;

class Limit
{
    /** @var callable */
    private $patternFactory;

    public function __construct(callable $patternFactory)
    {
        $this->patternFactory = $patternFactory;
    }

    public function all(): Limit
    {
        return call_user_func($this->patternFactory, -1);
    }

    public function first(): Limit
    {
        return call_user_func($this->patternFactory, 1);
    }

    public function only(int $limit): Limit
    {
        if ($limit < 0) {
            throw new InvalidArgumentException("Negative limit: $limit");
        }
        return call_user_func($this->patternFactory, $limit);
    }
}
