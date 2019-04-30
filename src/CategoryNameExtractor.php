<?php
namespace App;

use function is_array;

class CategoryNameExtractor
{
    public function extractNames(array $entries, $language = 'pl_PL'): array
    {
        $names = [];

        foreach ($entries as $entry) {
            if (!is_array($entry) || !isset($entry['category_id'], $entry['translations'])) {
                continue;
            }

            if (!isset($entry['translations'][$language])) {
                continue;
            }

            $translationObject = $entry['translations'][$language];

            if (!isset($translationObject['name'])) {
                continue;
            }

            $names[$entry['category_id']] = $translationObject['name'];
        }

        return $names;
    }
}