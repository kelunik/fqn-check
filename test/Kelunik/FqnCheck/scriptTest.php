<?php

namespace Test\Kelunik\FqnCheck;

use org\bovigo\vfs\VfsStream;
use org\bovigo\vfs\VfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class scriptTest extends TestCase {

    /** @var VfsStreamDirectory */
    private $directory;

    public function setUp() {
        $this->directory = vfsStream::setup('directory');
    }

    /**
     * @test
     */
    public function test(): void {
        // given
        $argc = 2;
        $argv = ['fqn-check', $this->directory->url()];

        // when
        require("../../../bin/fqn-check"); // uses exit() :/ Impossible to test

        // then
        $this->fail("Script uses exit(), making it impossible to test");
    }
}
