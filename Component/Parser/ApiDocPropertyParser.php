<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 17/12/15
 * Time: 11:36
 */

namespace Dawen\Bundle\ApiDocPropertyBundle\Component\Parser;

use Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty;
use Doctrine\Common\Annotations\Reader;
use Nelmio\ApiDocBundle\Parser\ParserInterface;

/**
 * Class ApiDocPropertyParser
 *
 * @package Dawen\Bundle\ApiDocPropertyBundle\Component\Parser
 */
class ApiDocPropertyParser implements ParserInterface
{

    const ANNOTATION_CLASS = 'Dawen\\Bundle\\ApiDocPropertyBundle\\Component\\Annotation\\ApiDocProperty';

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var array
     */
    private $reflectionClasses = [];

    /**
     * ApiDocPropertyParser constructor.
     *
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Return true/false whether this class supports parsing the given class.
     *
     * @param  array $item containing the following fields: class, groups. Of which groups is optional
     *
     * @return boolean
     */
    public function supports(array $item)
    {
        $className = $item['class'];
        $reflectionClass = $this->getReflectionClass($className);

        return $this->hasMetadata($reflectionClass);
    }

    /**
     * Returns an array of class property metadata where each item is a key (the property name) and
     * an array of data with the following keys:
     *  - dataType          string
     *  - required          boolean
     *  - description       string
     *  - readonly          boolean
     *  - children          (optional) array of nested property names mapped to arrays
     *                      in the format described here
     *  - class             (optional) the fully-qualified class name of the item, if
     *                      it is represented by an object
     *
     * @param  array $item The string type of input to parse.
     *
     * @return array
     */
    public function parse(array $item)
    {
        $className = $item['class'];
        $reflectionClass = $this->getReflectionClass($className);

        $annotations = $this->getMetadata($reflectionClass);

        return $this->mapAnnotations($annotations);
    }

    /**
     * Gets a reflections and occupies the instace cache
     *
     * @param string $className
     *
     * @return \ReflectionClass
     */
    private function getReflectionClass($className)
    {
        if(!isset($this->reflectionClasses[$className])) {
            $this->reflectionClasses[$className] = new \ReflectionClass($className);
        }

        return $this->reflectionClasses[$className];
    }

    /**
     * Checks id a class has annotations
     *
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    private function hasMetadata(\ReflectionClass $class)
    {
        foreach($class->getProperties() as $property) {
            $annotation = $this->annotationReader->getPropertyAnnotation($property, self::ANNOTATION_CLASS);

            if(null !== $annotation) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all the annotations
     *
     * @param \ReflectionClass $class
     *
     * @return array
     */
    private function getMetadata(\ReflectionClass $class)
    {
        $annotations = [];

        foreach($class->getProperties() as $property) {
            /** @var ApiDocProperty $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, self::ANNOTATION_CLASS);

            if(null !== $annotation) {
                if(null === $annotation->name) {
                    $annotation->name = $property->name;
                }

                if($annotation->isClass || $annotation->isArrayOfClasses) {
                    $annotation->children = $this->getMetadata($this->getReflectionClass($annotation->className));
                }

                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }

    /**
     * Maps all annotaions for the expected result array
     *
     * @param array $annotations
     *
     * @return array
     */
    private function mapAnnotations(array $annotations)
    {
        $mappedData = [];

        /** @var ApiDocProperty $annotation */
        foreach($annotations as $annotation) {
            $class = null;
            $description = null;
            $children = [];

            if ($annotation->isClass || $annotation->isArrayOfClasses) {
                $class = $annotation->className;
                $description = $annotation->description;

                if (!empty($annotation->children)) {
                    $children = $this->mapAnnotations($annotation->children);
                }
            }

            $mappedData[$annotation->name] = $this->createDataset($annotation->type, $class, $children, $description);
        }

        return $mappedData;
    }

    /**
     * Creates one array with all expected data
     *
     * @param $dataType
     * @param null|string $class
     * @param array $children
     * @param null|string $description
     *
     * @return array
     */
    private function createDataset($dataType, $class = null, array $children = [], $description = null)
    {
        $data = [
            'dataType' => $dataType,
            'required' => null,
            'readonly' => null
        ];

        if(null !== $class) {
            $data['class'] = $class;
        }

        if(!empty($children)) {
            $data['children'] = $children;
        }

        if(null !== $description) {
            $data['description'] = $description;
        }

        return $data;
    }
}