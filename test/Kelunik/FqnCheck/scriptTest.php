<?php

namespace Test\Kelunik\FqnCheck;

use org\bovigo\vfs\VfsStream;
use org\bovigo\vfs\VfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Test\Util\Output;

class scriptTest extends TestCase {

    /** @var VfsStreamDirectory */
    private $directory;

    public function setUp() {
        $this->directory = vfsStream::setup('directory');
    }

    /**
     * @test
     */
    public function testNoFqn(): void {
        // given
        vfsStream::copyFromFileSystem(__DIR__ . '/../../resources', $this->directory);

        // when
        $output = Output::capture(function () {
            // when
            $errorCode = $this->runScript(['fqn-check', $this->directory->url()]);

            // then
            $this->assertErrorCode(1, $errorCode);
        });

        // then
        $this->assertEquals("#!/usr/bin/env php\n" .
            'Checking 2 files…' . PHP_EOL . PHP_EOL .
            'Found not fully qualified function calls and/or constants in the following files:' . PHP_EOL .
            ' - TRegx\Match\Predicate.php on line 16 (call_user_func)' . PHP_EOL .
            ' - TRegx\Replace\Limit.php on line 19 (call_user_func)' . PHP_EOL .
            ' - TRegx\Replace\Limit.php on line 24 (call_user_func)' . PHP_EOL .
            ' - TRegx\Replace\Limit.php on line 32 (call_user_func)' . PHP_EOL . PHP_EOL .
            'Found 4 not fully qualified function calls and/or constants.',
            $output);
    }

    /**
     * @test
     */
    public function testCorrect(): void {
        // given
        vfsStream::newFile('index.php')->at($this->directory)->setContent('<?php \mb_strlen("");');
        vfsStream::newFile('file.php')->at($this->directory)->setContent('<?php');

        // when
        $output = Output::capture(function () {
            // when
            $errorCode = $this->runScript(['fqn-check', $this->directory->url()]);

            // then
            $this->assertErrorCode(0, $errorCode);
        });

        // then
        $this->assertEquals("#!/usr/bin/env php\n" .
            "Checking 2 files…" . PHP_EOL . PHP_EOL .
            "Everything is fine." . PHP_EOL, $output);
    }

    /**
     * @test
     */
    public function testParseError(): void {
        // given
        vfsStream::newFile('index.php')->at($this->directory)->setContent('<?php echo echo');

        // when
        $output = Output::capture(function () {
            // when
            $errorCode = $this->runScript(['fqn-check', $this->directory->url()]);

            // then
            $this->assertErrorCode(0, $errorCode); // not sure about that?
        });

        // then
        $this->assertEquals("#!/usr/bin/env php\n" .
            "Checking 1 file…" . PHP_EOL . PHP_EOL .
            "Syntax error, unexpected T_ECHO on line 1 (errored in file: index.php)" . PHP_EOL .
            "Everything is fine." . PHP_EOL, $output);
    }

    /**
     * @test
     */
    public function testNoFiles(): void {
        // when
        $output = Output::capture(function () {
            // when
            $errorCode = $this->runScript(['fqn-check', $this->directory->url()]);

            // then
            $this->assertErrorCode(0, $errorCode);
        });

        // then
        $this->assertEquals("#!/usr/bin/env php\n" .
            "Checking 0 files…" . PHP_EOL . PHP_EOL .
            "Everything is fine." . PHP_EOL, $output);
    }

    /**
     * @test
     */
    public function shouldFailForNoArguments(): void {
        // when
        $errorCode = $this->runScript([]);

        // then
        $this->assertErrorCode(0, $errorCode);
    }

    public function runScript(array $arguments): int {
        $argc = count($arguments); // set PHP variables for test
        $argv = $arguments;
        return require(__DIR__ . "/../../../bin/fqn-check.php");
    }

    public function assertErrorCode(int $expected, int $actual): void {
        $this->assertEquals($expected, $actual, "Failed to assert that while checking two non-FQN files, error code is $expected");
    }
}
