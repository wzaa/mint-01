<?php
namespace App\Tests\TreeWalker\WalkingStrategy;

use App\TreeWalker\TreeWalker;
use App\TreeWalker\WalkingStrategy\BreadthFirst;
use App\TreeWalker\WalkingStrategy\DepthFirst;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class TreeWalkerTest extends TestCase
{
    private function getTestingTree(): array
    {
        return [
            [
                'node' => 1,
                'subnodes' => [
                    [
                        'node' => 11,
                        'subnodes' => [
                            [
                                'node' => 111
                            ]
                        ]
                    ],
                    [
                        'node' => 12,
                        'subnodes' => []
                    ],
                    [
                        'node' => 13
                    ]
                ]
            ],
            [
                'node' => 2,
                'subnodes' => [
                    [
                        'node' => 21
                    ],
                    [
                        'node' => 22,
                        'subnodes' => [
                            [
                                'node' => 221
                            ]
                        ]
                    ],
                    [
                        'node' => 23
                    ]
                ]
            ],
            [
                'node' => 3,
                'subnodes' => [
                    [
                        'node' => 31,
                        'subnodes' => []
                    ],
                    [
                        'node' => 32,
                        'subnodes' => []
                    ],
                    [
                        'node' => 33,
                        'subnodes' => [
                            [
                                'node' => 331
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function testWalkingBreadthFirst(): void
    {
        $walker = new TreeWalker(new BreadthFirst(), 'subnodes');

        $tree = $this->getTestingTree();

        $generator = $walker->walk($tree);

        $walkedNodes = [];

        foreach ($generator as $node) {
            $walkedNodes[] = $node['node'];
        }

        self::assertEquals([1, 2, 3, 11, 12, 13, 21, 22, 23, 31, 32, 33, 111, 221, 331], $walkedNodes);
    }

    public function testWalkingDepthFirst(): void
    {
        $walker = new TreeWalker(new DepthFirst(), 'subnodes');

        $tree = $this->getTestingTree();

        $generator = $walker->walk($tree);

        $walkedNodes = [];

        foreach ($generator as $node) {
            $walkedNodes[] = $node['node'];
        }

        self::assertEquals([1, 11, 111, 12, 13, 2, 21, 22, 221, 23, 3, 31, 32, 33, 331], $walkedNodes);
    }

    public function testWalkingEmptyTree(): void
    {
        $walker = new TreeWalker(new DepthFirst(), 'subnodes');

        $tree = [];

        $generator = $walker->walk($tree);

        $walked = 0;
        foreach ($generator as $node) {
            $walked++;
        }

        self::assertEquals(0, $walked);
    }

    public function testThatNodesAreReturnedByReference(): void
    {
        $walker = new TreeWalker(new DepthFirst(), 'subnodes');

        $tree = [
            ['node' => 123]
        ];

        $generator = $walker->walk($tree);

        foreach ($generator as &$node) {
            $node['walked'] = true;
        }
        unset($node);

        self::assertEquals([
            'node' => 123,
            'walked' => true
        ], $tree[0]);
    }
}