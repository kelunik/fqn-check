<?php

namespace TRegx\Match;

use function is_bool;

class Predicate {
    /** @var callable */
    private $predicate;

    public function __construct(callable $predicate) {
        $this->predicate = $predicate;
    }

    public function test($match): bool {
        $result = call_user_func($this->predicate, $match);
        if (is_bool($result)) {
            return $result;
        }
        throw new \Exception($result);
    }
}
