<?php
namespace App\TreeWalker\WalkingStrategy;

class BreadthFirst implements WalkingStrategyInterface
{
    public function append(&$nodesToWalk, &$newNodes)
    {
        foreach ($newNodes as &$node) {
            $nodesToWalk[] = &$node;
        }
        unset($node);
    }
}