<?php

namespace Dawen\Bundle\ApiDocPropertyBundle\Tests\Component\Annonation;

use Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty;

class ApiDocPropertyTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorWithSimpleTypeAndNoOtherProperties()
    {
        $data = ['type' => 'int'];

        $annotation = new ApiDocProperty($data);

        $this->assertSame($data['type'], $annotation->type);
        $this->assertEquals([], $annotation->children);
        $this->assertFalse($annotation->isClass);
        $this->assertFalse($annotation->isArrayOfClasses);
        $this->assertNull($annotation->className);
        $this->assertNull($annotation->description);
        $this->assertNull($annotation->name);
    }

    public function testConstructorWithSimpleTypeAndMoreProperties()
    {
        $data = [
            'type' => 'int',
            'name' => 'my-name',
            'description' => 'my-description'
        ];

        $annotation = new ApiDocProperty($data);

        $this->assertSame($data['type'], $annotation->type);
        $this->assertEquals([], $annotation->children);
        $this->assertFalse($annotation->isClass);
        $this->assertFalse($annotation->isArrayOfClasses);
        $this->assertNull($annotation->className);
        $this->assertSame($data['description'], $annotation->description);
        $this->assertSame($data['name'], $annotation->name);
    }

    public function testConstructorWithNotExistingClass()
    {
        $data = ['type' => 'Dawen\\Bundle\\ApiDocPropertyBundle\\NotExisting'];

        $annotation = new ApiDocProperty($data);

        $this->assertSame($data['type'], $annotation->type);
        $this->assertEquals([], $annotation->children);
        $this->assertFalse($annotation->isClass);
        $this->assertFalse($annotation->isArrayOfClasses);
        $this->assertNull($annotation->className);
        $this->assertNull($annotation->description);
        $this->assertNull($annotation->name);
    }

    public function testConstructorWithExistingClass()
    {
        $data = ['type' => 'Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty'];

        $annotation = new ApiDocProperty($data);

        $this->assertSame('ApiDocProperty', $annotation->type);
        $this->assertEquals([], $annotation->children);
        $this->assertTrue($annotation->isClass);
        $this->assertFalse($annotation->isArrayOfClasses);
        $this->assertSame($data['type'], $annotation->className);
        $this->assertNull($annotation->description);
        $this->assertNull($annotation->name);
    }

    public function testConstructorWithExistingArray()
    {
        $class = 'Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty';
        $data = ['type' => 'array<' . $class . '>'];

        $annotation = new ApiDocProperty($data);

        $this->assertSame('array<ApiDocProperty>', $annotation->type);
        $this->assertEquals([], $annotation->children);
        $this->assertFalse($annotation->isClass);
        $this->assertTrue($annotation->isArrayOfClasses);
        $this->assertSame($class, $annotation->className);
        $this->assertNull($annotation->description);
        $this->assertNull($annotation->name);
    }

    public function testConstructorWithExistingArrayException()
    {
        $class = 'Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperties';
        $data = ['type' => 'array<' . $class . '>'];



        try {
            new ApiDocProperty($data);
        } catch (\InvalidArgumentException $exception) {

            $this->assertSame('Class "' . $class . '" not found that is used within array<' . $class . '>',
                $exception->getMessage());
            return;
        }

        $this->fail('expected exception not thrown');
    }
}