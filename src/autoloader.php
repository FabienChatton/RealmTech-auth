<?php
spl_autoload_register('autoloader');

/**
 * An example of a project-specific implementation.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
function autoloader($class)
{
    // replace namespace separators with directory separators in the relative 
    // class name, append with .php
    $class_path = str_replace('\\', '/', $class);

    $file =  __DIR__ . '/' . $class_path . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
}
