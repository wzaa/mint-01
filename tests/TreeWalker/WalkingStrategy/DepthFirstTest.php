<?php
namespace App\Tests\TreeWalker\WalkingStrategy;

use App\TreeWalker\WalkingStrategy\DepthFirst;
use App\TreeWalker\WalkingStrategy\WalkingStrategyInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class DepthFirstTest extends TestCase
{
    public function testObjectCanBeCreated(): void
    {
        $strategy = new DepthFirst();

        self::assertInstanceOf(WalkingStrategyInterface::class, $strategy);
    }

    public function testStrategyAppendsCorrectly(): void
    {
        $strategy = new DepthFirst();

        $nodesToWalk = [10, 11, 12, 13];
        $newNodes = [20, 21, 22];

        $result = $strategy->append($nodesToWalk, $newNodes);

        self::assertNull($result, 'append returns nothing');

        self::assertEquals([20, 21, 22], $newNodes, 'the list of new nodes has not been modified');
        self::assertEquals([20, 21, 22, 10, 11, 12, 13], $nodesToWalk, 'the list of nodes to walk has been modified');
    }
}