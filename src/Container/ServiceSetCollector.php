<?php


namespace Two\Container;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Two\Service;

/**
 * Collects argument data from \Two\Service::set and \Two\Service::alias calls so that a .phpstorm.meta.php
 * file can be generated for IDE autocomplete assistance.
 *
 * Expects PhpParser\NodeVisitor\NameResolver to be in the node traverser before this.
 *
 * @package Two\Container
 */
class ServiceSetCollector extends NodeVisitorAbstract
{
    private $className;
    public $argumentSet = [];
    public $overrides = [];

    /**
     * ServiceSetCollector constructor.
     * @param $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    public function leaveNode(Node $node)
    {
        if ( $node instanceof Node\Expr\StaticCall ) {
            $p = $node->class->parts;
            // check for \Two\Service::set() and \Two\Service::alias()
            if ( count($p) === 2 && $p[0] === 'Two' && $p[1] === $this->className ) {
                $func = $node->name->name;
                if ( $func === 'set' ) {
                    $className = $this->resolveName($node->args[0]);
                    $this->argumentSet[$className] = true;

                    // handle automatic aliases.
                    if ( $node->args[1] instanceof Node\Scalar\String_ ) {
                        $name = $this->resolveName($node->args[1]);
                    } else {
                        $name = '';
                    }
                    $class = str_replace('::class', '', $className);
                    $this->overrides[Service::makeAlias($class, $name)] = $className;
                } else if ( $func === 'alias' ) {
                    $this->overrides[$this->resolveName($node->args[0])] = $this->resolveName($node->args[1]);
                }
            }
        }
    }

    private function resolveName(Node\Arg $arg): string
    {
        if ( $arg->value instanceof Node\Scalar\String_ ) {
            return $arg->value->value;
        } else if ( $arg->value instanceof Node\Expr\ClassConstFetch ) {
            $fqn = '\\' . implode('\\', $arg->value->class->parts);
            return "$fqn::class";
        }

        throw new InvalidArgumentException('Unable to handle arg types other than strings and ClassName::class');
    }
}