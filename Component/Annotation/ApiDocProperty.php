<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 17/12/15
 * Time: 14:11
 */

namespace Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class ApiDocProperty
{
    /**
     * @Required()
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $isClass = false;

    /**
     * @var string
     */
    public $className;

    /**
     * @var bool
     */
    public $isArrayOfClasses = false;

    /**
     * @var array
     */
    public $children = [];

    /**
     * ApiDocProperty constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        if(isset($data['name'])) {
            $this->name = trim($data['name']);
        }

        if(isset($data['description'])) {
            $this->description = trim($data['description']);
        }

        $type = trim($data['type']);

        if (class_exists($type)) {

            $this->isClass = true;
            $this->className = $type;
            $this->type = $this->getClassName($type);

        } elseif (preg_match('/array<(.*)>/', $type, $matches)) {

            $className = trim($matches[1]);
            if (!class_exists($className)) {
                throw new \InvalidArgumentException(
                    'Class "' . $className . '" not found that is used within array<' . $className . '>');
            }

            $this->type = 'array<' . $this->getClassName($className) . '>';
            $this->isArrayOfClasses = true;
            $this->className = $className;
        } else {
            $this->type = $type;
        }
    }

    /**
     * Returns only the className without full qualified path
     *
     * @param string $className
     *
     * @return string
     */
    private function getClassName($className)
    {
        $classPath = explode('\\', $className);

        return array_pop($classPath);
    }



}