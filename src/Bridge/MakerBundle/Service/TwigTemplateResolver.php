<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Service;

class TwigTemplateResolver implements TwigTemplateResolverInterface
{
    public function resolve(array $data): array
    {
        $results = [];

        $defaults = $data;
        array_walk_recursive($defaults, function ($v, $k) use (&$results) {
            $results = array_merge($results, $this->resolveRecursive($v));
        });

        $onlyTwigs = array_filter($results, function ($template) {
            return str_contains((string) $template, '.html.twig');
        });

        return array_map(function ($template) {
            // old template
            $explodeOldTemplate = explode(':', $template);
            if (count($explodeOldTemplate) > 1) {
                $explodeOldTemplate[0] = '@' . str_replace('Bundle', '', $explodeOldTemplate[0]);
                $template = implode('/', $explodeOldTemplate);
            }

            return $template;
        }, array_unique($onlyTwigs));
    }

    protected function resolveRecursive($data): array
    {
        $results = [];
        if (is_array($data)) {
            $results = array_merge($results, $this->resolveRecursive($data));
        } else {
            $results = (array) $data;
        }

        return $results;
    }
}
