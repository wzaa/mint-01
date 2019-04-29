<?php
namespace App\TreeWalker\WalkingStrategy;

class DepthFirst implements WalkingStrategyInterface
{
    public function append(&$nodesToWalk, &$newNodes)
    {
        $newNodesByReferences = [];

        foreach ($newNodes as &$child) {
            $newNodesByReferences[] = &$child;
        }
        unset($child);

        $nodesToWalk = array_merge($newNodesByReferences, $nodesToWalk);
    }
}
