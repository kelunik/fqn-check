<?php

namespace Test\Kelunik\FqnCheck;

use Kelunik\FqnCheck\FqnChecker;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class FqnCheckerTest extends TestCase {
    /**
     * @test
     */
    public function test() {
        // given
        $code = <<<'Code'
<?php
namespace TRegx\CleanRegex\Internal\Match;

class Predicate
{
    public function test(Match $match): bool
    {
        $result = call_user_func($this->predicate, $match);
        if (is_bool($result)) {
            return $result;
        }
        throw InvalidReturnValueException::forFilter($result);
    }
}
Code;

        // when
        $findings = $this->parse($code);

        // then
        $this->assertEquals([
            ['line' => 8, 'function' => 'call_user_func',],
            ['line' => 9, 'function' => 'is_bool'],
        ], $findings);
    }

    /**
     * @test
     */
    public function shouldIgnoreTrue() {
        // when
        $findings = $this->parse("<?php true;");

        // then
        $this->assertEmpty($findings);
    }

    /**
     * @test
     */
    public function shouldIgnoreFalse() {
        // when
        $findings = $this->parse("<?php false;");

        // then
        $this->assertEmpty($findings);
    }

    /**
     * @test
     */
    public function shouldIgnoreNull() {
        // when
        $findings = $this->parse("<?php null;");

        // then
        $this->assertEmpty($findings);
    }

    /**
     * @test
     */
    public function shouldIgnoreKeywordsCaseInsensitive() {
        // when
        $findings = $this->parse("<?php tRuE + fAlSe + NuLL;");

        // then
        $this->assertEmpty($findings);
    }

    public function parse(string $code): array {
        $fqnChecker = new FqnChecker();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($fqnChecker);
        $nodeTraverser->traverse((new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer())->parse($code));
        return $fqnChecker->getFindings();
    }
}
