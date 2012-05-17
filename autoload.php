<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once 'vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

set_include_path(realpath('vendor/') . PATH_SEPARATOR . get_include_path());

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'EasyCSV'        => 'lib/',
    'EasyCSV\Tests'  => 'Test',
));
$loader->register();

