<?php

namespace Test\Util;

class Output {
    public static function capture(callable $code): string {
        ob_start();
        $code();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
