<?php

declare(strict_types=1);

namespace Test\Utils;

use Bonn\Maker\Utils\PhpDoctypeCode;
use PHPUnit\Framework\TestCase;

final class PhpDoctypeCodeTest extends TestCase
{
    public function testRenderDeclareType()
    {
        $expectCode = <<<EOD
<?php

declare(strict_types=1);

class Test
{
    const OK = true;

    const OK1 = true;
}
EOD;

        $content = <<<EOD
class Test
{
    const OK = true;


    const OK1 = true;
}
EOD;

        $this->assertEquals($expectCode, PhpDoctypeCode::render($content));
    }
}
