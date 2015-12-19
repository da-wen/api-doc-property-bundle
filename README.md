# api-doc-property-bundle
[![Build Status](https://travis-ci.org/da-wen/api-doc-property-bundle.svg?branch=master)](https://travis-ci.org/da-wen/api-doc-property-bundle)

This bundle allows you to expose properties for api documentation that are not handled by json, jms or validator parsers

---

## Installation

Require the dawen/api-doc-property-bundle package in your composer.json and update your dependencies.

```shell
    $ composer require dawen/api-doc-property-bundle
```

```php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Dawen\Bundle\ApiDocPropertyBundle\ApiDocPropertyBundle(),
        );
    }
```

Register the bundle in app/AppKernel.php:

---

## Usage And Examples

For defining a property you can add a new annotation to your property

```php
    @ApiDocProperty(type="string")
```


### Available properties for setting your informations:

#### name
    Type: string
    Description: Name of the property. If this is not set, then the name of the class property will be taken
    
#### description
    Type: string
    Description: This will be set as description for the property
    
#### type
    Types: all php types, full qualified class name, array<full qualified class name>
    Description: The type property has no strict limitation. you can write whatever you want. But keep in mind, 
    that it appears in the api documentation. There is only some magic for classes and array of objects. This will be parsed recursively
   

### Example for a simple class:

```php

    namespace Dawen\Bundle\ApiDocPropertyBundle;
    
    use Dawen\Bundle\ApiDocPropertyBundle\Component\Annotation\ApiDocProperty;
    
    class Dummy
    {
        /**
         * @ApiDocProperty(type="string")
         * @var string
         */
        private $firstName;
    
        /**
         * @ApiDocProperty(type="string")
         * @var string 
         */
        private $lastName;
    
        /**
         * @ApiDocProperty(type="int")
         * @var int
         */
        private $age = 0;
    
        /**
         * @ApiDocProperty(type="Dawen\Bundle\ApiDocPropertyBundle\Address")
         * @var Dawen\Bundle\ApiDocPropertyBundle\Address
         */
        private $address;
    
        /**
         * @ApiDocProperty(type="array<Dawen\Bundle\ApiDocPropertyBundle\Key>")
         * @var array
         */
        private $keys = [];
    
        /**
         * @return string
         */
        public function getFirstName()
        {
            return $this->firstName;
        }
    
        /**
         * @param string $firstName
         */
        public function setFirstName($firstName)
        {
            $this->firstName = $firstName;
        }
    
        /**
         * @return string
         */
        public function getLastName()
        {
            return $this->lastName;
        }
    
        /**
         * @param string $lastName
         */
        public function setLastName($lastName)
        {
            $this->lastName = $lastName;
        }
    
        /**
         * @return int
         */
        public function getAge()
        {
            return $this->age;
        }
    
        /**
         * @param int $age
         */
        public function setAge($age)
        {
            $this->age = $age;
        }
    
        /**
         * @return Dawen\Bundle\ApiDocPropertyBundle\Address
         */
        public function getAddress()
        {
            return $this->address;
        }
    
        /**
         * @param Dawen\Bundle\ApiDocPropertyBundle\Address $address
         */
        public function setAddress($address)
        {
            $this->address = $address;
        }
    
        /**
         * @return array
         */
        public function getKeys()
        {
            return $this->keys;
        }
    
        /**
         * @param array $keys
         */
        public function setKeys($keys)
        {
            $this->keys = $keys;
        }
        
    }
```





