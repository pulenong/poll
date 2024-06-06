<?php

declare(strict_types=1);

namespace Application\Model;

trait SlugTrait
{
    protected const TRANSLITERATOR = 'Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove; Lower()';

    public function createSlug(?string $title = null, ?int $maxLength = null): string
    {
        if (empty($title)) {
            $title = $this->getTitle();
        }

        $slug = mb_strtolower($title); // convert to lowercase

        // replace special characters with descriptive words
        $slug = str_replace(['%','@','&'], ['percent', 'at', 'and'], $slug);

        // replace non-alphanumeric characters with dashes
        $slug = preg_replace('/\W+/', '-', $slug);

        if (null === $maxLength) { $maxLength = 100; }

        // truncate to maximum length (if provided)
        if (isset($maxLength) && mb_strlen($slug) > $maxLength) {
            $slug = mb_substr($slug, 0, $maxLength, 'UTF-8');
        }

        // remove leading and trailing dashes
        $slug = trim($slug, '-');

        // transliterate and convert to alphanumeric characters/spaces
        $slug = transliterator_transliterate(self::TRANSLITERATOR, $slug);
        $slug = preg_replace('/[^a-zA-Z0-9\s]+/u', '-', $slug);

        return $slug;
    }

    // abstract method to be implemented in the class using this trait
    abstract public function getTitle(): string;
}
