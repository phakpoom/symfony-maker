<?php

declare(strict_types=1);

namespace Bonn\Maker\Utils;

use Nette\PhpGenerator\Helpers;

final class PhpDoctypeCode
{
    public static function render(string $content): string
    {
        return str_replace("\n\n\n", "\n\n", Helpers::tabsToSpaces("<?php\n\ndeclare(strict_types=1);\n\n" . $content));
    }
}
