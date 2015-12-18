<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 12/01/15
 * Time: 12:33
 */
include_once __DIR__ . '/../vendor/autoload.php';
define('FIXTURE_ROOT', realpath(__DIR__ . '/Fixtures'));
use Doctrine\Common\Annotations\AnnotationRegistry;
// Neat trick to autoload annotations without explicitly naming them.
AnnotationRegistry::registerLoader('class_exists');