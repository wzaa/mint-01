<?php
namespace App\Command;

use App\CategoryNameExtractor;
use App\Helper\JsonFileHandler;
use App\TreeWalker\TreeWalker;
use App\TreeWalker\WalkingStrategy\BreadthFirst;
use App\TreeWalker\WalkingStrategy\DepthFirst;
use App\TreeWalker\WalkingStrategy\WalkingStrategyInterface;
use function count;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

class MatchNames extends Command
{
    /**
     * @var JsonFileHandler
     */
    private $jsonFileHandler;

    /**
     * @var CategoryNameExtractor
     */
    private $categoryNameExtractor;

    public function __construct(
        CategoryNameExtractor $categoryNameExtractor,
        JsonFileHandler $jsonFileHandler
    )
    {
        $this->categoryNameExtractor = $categoryNameExtractor;
        $this->jsonFileHandler = $jsonFileHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('match-names')
            ->addArgument('list', InputArgument::REQUIRED, 'List file')
            ->addArgument('tree', InputArgument::REQUIRED, 'Tree file')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file, - for stdout', '-')
            ->addOption('language', 'l', InputOption::VALUE_REQUIRED, 'Language to match', 'pl_PL')
            ->addOption('skip-on-error', null, InputOption::VALUE_NONE, 'Whether to skip when the corresponding name is not found')
            ->addOption('strategy', 's', InputOption::VALUE_REQUIRED, 'breadth for breadth-first or depth for depth-first', 'breadth')
            ->addOption('pretty', 'p', InputOption::VALUE_NONE, 'Whether to pretty print output JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var string $listFilename */
        $listFilename = $input->getArgument('list');

        /** @var string $treeFilename  */
        $treeFilename = $input->getArgument('tree');

        /** @var string $outputFilename */
        $outputFilename = $input->getOption('output');

        /** @var string $language */
        $language = $input->getOption('language');

        /** @var bool $skipOnError */
        $skipOnError = $input->getOption('skip-on-error');

        /** @var string $strategyString */
        $strategyString = $input->getOption('strategy');

        /** @var bool $prettyPrint */
        $prettyPrint = $input->getOption('pretty');

        $list = $this->jsonFileHandler->readJsonFile($listFilename);
        $tree = $this->jsonFileHandler->readJsonFile($treeFilename);

        $names = $this->extractNames($list, $language, $output);

        $strategy = $this->getStrategyByString($strategyString);
        $walker = new TreeWalker($strategy, 'children');

        $this->walkTree($walker, $tree, $names, $skipOnError, $output);

        $this->jsonFileHandler->writeJsonFile($outputFilename === '-' ? 'php://stdout' : $outputFilename, $tree, $prettyPrint);
    }

    /**
     * Convert a user-supplied string representing the walking strategy to an object capable of that strategy
     */
    private function getStrategyByString(string $strategyString): WalkingStrategyInterface
    {
        switch (strtolower($strategyString)) {
            case 'breadth':
                return new BreadthFirst();

            case 'depth':
                return new DepthFirst();
        }

        throw new InvalidOptionException('Invalid walking strategy: ' . $strategyString);
    }

    /**
     * Traverse the supplied list to extract category names in a provided language.
     * Optionally print a table with these names, if the command is in verbose mode.
     */
    private function extractNames(array $list, string $language, OutputInterface $output)
    {
        $names = $this->categoryNameExtractor->extractNames($list, $language);

        if (count($names) === 0) {
            throw new InvalidOptionException('There are NO names in language ' . $language);
        }

        $output->writeln('List file contains <fg=green>' . count($names) . '</> entries in language <fg=yellow>' . $language . '</>', OutputInterface::VERBOSITY_VERBOSE);

        if ($output->isDebug()) {
            $t = new Table($output);

            $t->setHeaders(['ID', 'Name in ' . $language]);

            foreach ($names as $id => $name) {
                $t->addRow([$id, $name]);
            }

            $t->render();
        }


        $output->writeln('');

        return $names;
    }

    /**
     * Assign the category names to nodes on the tree, modifying the tree in place.
     */
    protected function walkTree(TreeWalker $walker, array &$tree, array $names, bool $skipOnError, OutputInterface $output): void
    {
        $generator = $walker->walk($tree);

        foreach ($generator as &$node) {
            $id = $node['id'];

            $output->writeln('', OutputInterface::VERBOSITY_VERY_VERBOSE);
            $output->writeln('Visiting node with ID ' . $id, OutputInterface::VERBOSITY_VERY_VERBOSE);

            if (!isset($names[$id])) {
                $error = 'There is no name entry for category ID ' . $id;

                if (!$skipOnError) {
                    throw new UnexpectedValueException($error);
                }

                $output->writeln('<error>' . $error . '</error>');
                continue;
            }

            $node['name'] = $names[$id];

            $output->writeln('Node ' . $id . ' now has name ' . $names[$id], OutputInterface::VERBOSITY_VERY_VERBOSE);
        }

        unset($node);
    }
}