<?php
namespace App\TreeWalker\WalkingStrategy;

interface WalkingStrategyInterface
{
    public function append(&$nodesToWalk, &$newNodes);
}
