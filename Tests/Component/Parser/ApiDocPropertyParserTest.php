<?php

namespace Dawen\Bundle\ApiDocPropertyBundle\Tests\Component\Parser;

use Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty;
use Dawen\Bundle\ApiDocPropertyBundle\Component\Parser\ApiDocPropertyParser;

class ApiDocPropertyParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var ApiDocPropertyParser
     */
    private $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->reader = $this->getMockBuilder('Doctrine\Common\Annotations\Reader')->getMock();

        $this->parser = new ApiDocPropertyParser($this->reader);
    }

    protected  function tearDown()
    {
        $this->reader = null;
        $this->parser = null;

        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\ApiDocPropertyBundle\Component\Parser\ApiDocPropertyParser',
            $this->parser);
        $this->assertInstanceOf('Nelmio\ApiDocBundle\Parser\ParserInterface', $this->parser);
    }

    public function testSupportsFalse()
    {
        $this->reader->expects($this->exactly(7))
            ->method('getPropertyAnnotation')
            ->with($this->isInstanceOf('ReflectionProperty'), ApiDocPropertyParser::ANNOTATION_CLASS)
            ->willReturn(null);

        $result = $this->parser->supports($this->createData());

        $this->assertFalse($result);
    }

    public function testSupportsTrue()
    {
        $this->reader->expects($this->exactly(1))
            ->method('getPropertyAnnotation')
            ->with($this->isInstanceOf('ReflectionProperty'), ApiDocPropertyParser::ANNOTATION_CLASS)
            ->willReturn(new ApiDocProperty(['type' => 'string']));

        $result = $this->parser->supports($this->createData());

        $this->assertTrue($result);

    }

    public function testParseEmpty()
    {
        $this->reader->expects($this->exactly(7))
            ->method('getPropertyAnnotation')
            ->with($this->isInstanceOf('ReflectionProperty'), ApiDocPropertyParser::ANNOTATION_CLASS)
            ->willReturn(null);

        $result = $this->parser->parse($this->createData());

        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testParseWithoutChildren()
    {
        $description = 'my-description';
        $name = 'my-name';

        $this->reader->expects($this->exactly(7))
            ->method('getPropertyAnnotation')
            ->with($this->isInstanceOf('ReflectionProperty'), ApiDocPropertyParser::ANNOTATION_CLASS)
            ->willReturnOnConsecutiveCalls(
                new ApiDocProperty(['type' => 'string']),
                new ApiDocProperty(['type' => 'int', 'description' => $description]),
                new ApiDocProperty(['type' => 'array', 'name' => $name]),
                new ApiDocProperty(['type' => 'bool'])
            );

        $result = $this->parser->parse($this->createData());

        $this->assertTrue(is_array($result));
        $this->assertCount(4, $result);

        $this->assertArrayHasKey('type', $result);
        $this->assertSame('string', $result['type']['dataType']);
        $this->assertNull($result['type']['required']);
        $this->assertNull($result['type']['readonly']);
        $this->assertFalse(isset($result['type']['class']));
        $this->assertFalse(isset($result['type']['description']));
        $this->assertFalse(isset($result['type']['children']));

        $this->assertArrayHasKey('name', $result);
        $this->assertSame('int', $result['name']['dataType']);
        $this->assertNull($result['name']['required']);
        $this->assertNull($result['name']['readonly']);
        $this->assertFalse(isset($result['name']['class']));
        $this->assertSame($description, $result['name']['description']);
        $this->assertFalse(isset($result['name']['children']));

        $this->assertArrayHasKey($name, $result);
        $this->assertSame('array', $result[$name]['dataType']);
        $this->assertNull($result[$name]['required']);
        $this->assertNull($result[$name]['readonly']);
        $this->assertFalse(isset($result[$name]['class']));
        $this->assertFalse(isset($result[$name]['description']));
        $this->assertFalse(isset($result[$name]['children']));

        $this->assertArrayHasKey('isClass', $result);
        $this->assertSame('bool', $result['isClass']['dataType']);
        $this->assertNull($result['isClass']['required']);
        $this->assertNull($result['isClass']['readonly']);
        $this->assertFalse(isset($result['isClass']['class']));
        $this->assertFalse(isset($result['isClass']['description']));
        $this->assertFalse(isset($result['isClass']['children']));
    }

    public function testParseWithChildren()
    {
        $this->reader->expects($this->exactly(14))
            ->method('getPropertyAnnotation')
            ->with($this->isInstanceOf('ReflectionProperty'), ApiDocPropertyParser::ANNOTATION_CLASS)
            ->willReturnOnConsecutiveCalls(
                new ApiDocProperty(['type' => 'array<' . ApiDocPropertyParser::ANNOTATION_CLASS . '>']),
                new ApiDocProperty(['type' => 'string']
                )
            );

        $result = $this->parser->parse($this->createData());

        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);

        $this->assertArrayHasKey('type', $result);
        $this->assertSame('array<ApiDocProperty>', $result['type']['dataType']);
        $this->assertNull($result['type']['required']);
        $this->assertNull($result['type']['readonly']);
        $this->assertSame(ApiDocPropertyParser::ANNOTATION_CLASS, $result['type']['class']);
        $this->assertFalse(isset($result['type']['description']));
        $this->assertTrue(isset($result['type']['children']));
        $this->assertSame('string', $result['type']['children']['type']['dataType']);
    }

    private function createData()
    {
        return ['class' => 'Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty'];
    }
}