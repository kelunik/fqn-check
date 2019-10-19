<?php

namespace Kelunik\FqnCheck;

use PhpParser\Node;
use PhpParser\NodeVisitor;

class FqnChecker implements NodeVisitor {
    private $findings;

    public function __construct() {
        $this->reset();
    }

    private function reset() {
        $this->findings = [];
    }

    public function beforeTraverse(array $nodes) {
        $this->reset();
    }

    public function enterNode(Node $node) {
        if (!($node instanceof Node\Expr\FuncCall || $node instanceof Node\Expr\ConstFetch)) {
            return;
        }

        if (!$node->name instanceof Node\Name) {
            return;
        }

        if ($node->name->isFullyQualified()) {
            return;
        }

        if (in_array(\strtolower($node->name->toString()), ['true', 'false', 'null'], true)) {
            return;
        }

        $this->findings[] = [
            "line" => $node->getLine(),
            "function" => (string) $node->name,
        ];
    }

    public function leaveNode(Node $node) {
        return $node;
    }

    public function afterTraverse(array $nodes) {
        return $nodes;
    }

    public function getFindings() {
        return $this->findings;
    }
}