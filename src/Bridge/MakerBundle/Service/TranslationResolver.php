<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Service;

class TranslationResolver implements TranslationResolverInterface
{
    public function resolve(array $data): array
    {
        $results = [];

        $defaults = $data;
        array_walk_recursive($defaults, function ($v, $k) use (&$results) {
            if ($k !== 'label') {
                return;
            }

            $results = array_merge($results, $this->resolveRecursive($v));
        });

        return array_filter($results, function ($template) {
            return 1 < count(explode('.', $template));
        });
    }

    protected function resolveRecursive($data): array
    {
        $results = [];
        if (is_array($data)) {
            $results = array_merge($results, $this->resolveRecursive($data));
        } else {
            $results = (array)$data;
        }

        return $results;
    }
}
