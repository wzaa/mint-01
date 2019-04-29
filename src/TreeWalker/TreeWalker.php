<?php
namespace App\TreeWalker;

use App\TreeWalker\WalkingStrategy\WalkingStrategyInterface;
use function count;
use Generator;

class TreeWalker
{
    /**
     * @var WalkingStrategyInterface
     */
    private $walkingStrategy;

    /**
     * @var string
     */
    private $childrenKey;

    public function __construct(
        WalkingStrategyInterface $walkingStrategy,
        string $childrenKey
    )
    {
        $this->walkingStrategy = $walkingStrategy;
        $this->childrenKey = $childrenKey;
    }

    public function &walk(array &$tree): Generator
    {
        $nodesToWalk = [];

        foreach ($tree as &$subtree) {
            $nodesToWalk[] = &$subtree;
        }
        unset($subtree);

        while (count($nodesToWalk) > 0) {
            $currentNode = &$nodesToWalk[0];
            array_shift($nodesToWalk);

            yield $currentNode;

            if (isset($currentNode[$this->childrenKey])) {
                $this->walkingStrategy->append($nodesToWalk, $currentNode[$this->childrenKey]);
            }
        }
    }
}
