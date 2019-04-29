<?php
namespace App;

class CategoryNameExtractor
{
    public function extractNames(array $entries, $language = 'pl_PL'): array
    {
        $names = [];

        foreach ($entries as $entry) {
            if (!isset($entry['category_id'], $entry['translations'])) {
                continue;
            }

            if (!isset($entry['translations'][$language])) {
                continue;
            }

            $translationObject = $entry['translations'][$language];

            $names[$entry['category_id']] = $translationObject['name'];
        }

        return $names;
    }
}