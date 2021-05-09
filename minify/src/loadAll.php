<?php

namespace MatthiasMullie\Minify;

function loadAll() {
    $base_classes = ['ConverterInterface', 'Minify', 'CSS', 'JS', 'Exception'];
    $exceptions = ['BasicException', 'FileImportException', 'IOException'];
    $converterClasses=['Converter', 'NoConverter'];
    foreach ($base_classes as $class) {
        if (!class_exists("MatthiasMullie\\Minify\\$class")) {
            require __DIR__ . "/class.$class.php";
        }
    }
    foreach ($exceptions as $class) {
        if (!class_exists("MatthiasMullie\\Minify\\Exceptions\\$class")) {
            require __DIR__ . "/Exceptions/class.$class.php";
        }
    }
    foreach ($converterClasses as $class) {
        if (!class_exists("MatthiasMullie\\PathConverter\\$class")) {
            require __DIR__ . "/class.$class.php";
        }
    }
}

loadAll();
