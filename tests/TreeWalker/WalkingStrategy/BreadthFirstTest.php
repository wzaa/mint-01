<?php
namespace App\Tests\TreeWalker\WalkingStrategy;

use App\TreeWalker\WalkingStrategy\BreadthFirst;
use App\TreeWalker\WalkingStrategy\WalkingStrategyInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class BreadthFirstTest extends TestCase
{
    public function testObjectCanBeCreated(): void
    {
        $strategy = new BreadthFirst();

        self::assertInstanceOf(WalkingStrategyInterface::class, $strategy);
    }

    public function testStrategyAppendsCorrectly(): void
    {
        $strategy = new BreadthFirst();

        $nodesToWalk = [10, 11, 12, 13];
        $newNodes = [20, 21, 22];

        $result = $strategy->append($nodesToWalk, $newNodes);

        self::assertNull($result, 'append returns nothing');

        self::assertEquals([20, 21, 22], $newNodes, 'the list of new nodes has not been modified');
        self::assertEquals([10, 11, 12, 13, 20, 21, 22], $nodesToWalk, 'the list of nodes to walk has been modified');
    }
}