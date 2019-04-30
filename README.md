# What is this?

This command-line tool fills in category names into a tree structure and outputs the modified tree.

# Installation

## Requirements ##

* PHP 7.2
* PHP's ext-json
* Composer

## Setup ##

Simply run `composer install`.

## Configuration ##

There is no further configuration.

# Running the matcher

Run `bin/console match-names`.

## Usage ##

Required arguments:

1. list file name (in json format)
2. tree file name (in json format)

Additional options:

* `-o`/`--output` file name to write the output to (**will be overwritten without asking**), `-` for stdout (default)
* `-l`/`--language` the language to extract from list file, default is `pl_PL`
* `--skip-on-error` whether to skip when encountering a missing name, default is to fail
* `-s`/`--strategy` tree traversal strategy, either `breadth` or `depth` for breadth-first search or depth-first search, respectively
* `-p`/`--pretty` whether to pretty print the json output, defaults to not prettifying
   
This can also be accessed with `--help` flag.

# Running tests

Simply run `composer test`.