<?php

declare(strict_types=1);

namespace Test\Converter;

use Bonn\Maker\Converter\PropTypeConverter;
use Bonn\Maker\ModelPropType\BooleanType;
use Bonn\Maker\ModelPropType\CollectionType;
use Bonn\Maker\ModelPropType\IntegerType;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\ModelPropType\StringType;
use Bonn\Maker\Tests\PrivateAccessorTrait;
use PHPUnit\Framework\TestCase;

final class PropTypeConverterTest extends TestCase
{
    use PrivateAccessorTrait;

    public function testCreateClassByNotPassingTypes()
    {
        $converter = new PropTypeConverter();
        $this->assertEquals(PropTypeConverter::TYPES, $converter->getSupportedType());
    }

    public function testCreateClassByPassingTypes()
    {
        $converter = new PropTypeConverter([
            PropTypeInterface::class
        ]);
        $this->assertTrue(in_array(PropTypeInterface::class, $converter->getSupportedType(), true));
        $this->assertCount(count(PropTypeConverter::TYPES)+ 1, $converter->getSupportedType());
    }

    public function testConvert()
    {
        $converter = new PropTypeConverter();
        $propType = $converter->convert('name:' . StringType::getTypeName() . ':ok');
        $this->assertEquals(StringType::class, get_class($propType));
        $this->assertEquals('name', $this->getPrivateProperty(StringType::class, 'name')->getValue($propType));

        $propType = $converter->convert('dd:' . StringType::getTypeName());
        $this->assertEquals(StringType::class, get_class($propType));
        $this->assertEquals('dd', $this->getPrivateProperty(StringType::class, 'name')->getValue($propType));

        $propType = $converter->convert('comments:' . CollectionType::getTypeName() . ':' . PropTypeInterface::class);
        $this->assertEquals(CollectionType::class, get_class($propType));
        $this->assertEquals('comments', $this->getPrivateProperty(CollectionType::class, 'name')->getValue($propType));
        $this->assertEquals(PropTypeInterface::class, $this->getPrivateProperty(CollectionType::class, 'fullInterfaceName')->getValue($propType));
        $this->assertEquals('PropTypeInterface', $this->getPrivateProperty(CollectionType::class, 'interfaceName')->getValue($propType));
    }

    public function testConvertInvalidString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $converter = new PropTypeConverter();
        $converter->convert(":'");
    }

    public function testConvertNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $converter = new PropTypeConverter();
        $converter->convert("s:b:c'");
    }

    public function testConvertMultiple()
    {
        $converter = new PropTypeConverter();
        $propTypes = $converter->convertMultiple("name:string:bon|age:int:50|e:boolean");

        $this->assertCount(3, $propTypes);
        $this->assertEquals(StringType::class, get_class($propTypes[0]));
        $this->assertEquals(IntegerType::class, get_class($propTypes[1]));
        $this->assertEquals(BooleanType::class, get_class($propTypes[2]));
    }

    public function testBuildInfoString()
    {
        $converter = new PropTypeConverter();
        $this->assertEquals('name:integer:0', $converter->buildInfoString("name", "integer", "0"));
        $this->assertEquals('name:string:bon', $converter->buildInfoString("name", "string", "bon"));
        $this->assertEquals('name:string', $converter->buildInfoString("name", "string", ""));
        $this->assertEquals('name:string', $converter->buildInfoString("name", "string"));
    }

    public function testCombineInfos()
    {
        $converter = new PropTypeConverter();
        $this->assertEquals('a|b', $converter->combineInfos(['a', 'b']));
        $this->assertEquals('a|b|c', $converter->combineInfos(['a', 'b', 'c']));
    }
}
