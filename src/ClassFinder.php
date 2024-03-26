<?php declare(strict_types=1);

/**
 * This file is part of ClassFinder.
 * ClassFinder is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * ClassFinder is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    AhJ <AmirHosseinJafari8228@gmail.com>
 * @copyright 2023-2024 AhJ <AmirHosseinJafari8228@gmail.com>
 * @license   https://choosealicense.com/licenses/gpl-3.0/ GPLv3
 */

namespace AhjDev\ClassFinder;

use ReflectionEnum;
use ReflectionClass;
use FilesystemIterator;
use RecursiveDirectoryIterator;

class ClassFinder
{
    public const FileSystemFlags = FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS;

    public array $namespaces = [];

    /**
     * @param ?string $path Dirname of the vendor folder
     */
    public function __construct(?string $path = null)
    {
        if (!interface_exists('Reflector'))
            throw new Exception('Could not find Reflector interface');

        $path          = rtrim($path ?? __DIR__, '/');
        $vendor        = $path   . '/vendor';
        $autoload      = $vendor . '/autoload.php';
        $autoload_psr4 = $vendor . '/composer/autoload_psr4.php';
        require_once $autoload;
        $this->namespaces = include $autoload_psr4;
    }

    
    private function filterClass($class, $options = FindType::ALL, ?string $subclass = null): ReflectionClass|false
    {
        if (!empty($subclas) && !is_subclass_of($class, $subclass))
            return false;

        if (enum_exists($class) && ($options & FindType::ENUM))
            return new ReflectionEnum($class);

        elseif (trait_exists($class) && ($options & FindType::TRAIT) || interface_exists($class) && ($options & FindType::INTERFACE))
            return new ReflectionClass($class);

        elseif (class_exists($class) && ($options & FindType::CLASSES)) {
            $refClass = new ReflectionClass($class);
            return match (true)
            {
                $refClass->isEnum()           => ($options & FindType::ENUM) ? $refClass : false,
                $options & FindType::FINAL    => $refClass->isFinal()    ? $refClass : false,
                $options & FindType::ABSTRACT => $refClass->isAbstract() ? $refClass : false,
                $options & FindType::READONLY => $refClass->isReadOnly() ? $refClass : false,
                default => $refClass,
            };
            // isInstance isInstantiable isSubclassOf getInterfaces getParentClass getTraitNames implementsInterface
        }
        return false;
    }

    public function getClasses(string $namespace, $options = FindType::ALL, ?string $subclass = null): array
    {
        $namespace .= str_ends_with($namespace, '\\') ? '' : '\\';
        foreach ($this->namespaces as $k => $v) {
            if (str_starts_with($namespace, $k)) {
                $main    = implode('', $v);
                $path    = $main . '/' . substr($namespace, strlen($k));
                $name    = rtrim($k, '\\');
                $classes = $this->getClassesInternal($path, $main, $name, $options, $subclass);
                return array_values($classes);
            }
        }
        return [];
    }

    private function getClassesInternal(RecursiveDirectoryIterator|string $path, string $mainpath, string $namespace, $options = FindType::ALL, ?string $subclass = null): array
    {
        $classes = [];
        if (is_string($path))
            $path = new RecursiveDirectoryIterator($path, self::FileSystemFlags);
    
        foreach ($path as $k => $v) {
            if ($v->isDir())
                $classes += $this->getClassesInternal($v->getChildren(), $mainpath, $namespace, $options);
    
            elseif ($v->isFile()) {
                $class = $this->createClassName($v, $mainpath, $namespace);
                if ($this->filterClass($class, $options, $subclass) !== false)
                    $classes[$k] = $class;
            }
        }
        return $classes;
    }

    private function createClassName(RecursiveDirectoryIterator $file, string $main, string $namespace): string
    {
        $namespace .= substr($file->getPathname(), strlen($main));
        $class = rtrim($namespace, '.php');
        return str_replace('/', '\\', $class);
    }

    public function getSubClasses(string $namespace, string $subclass, $options = FindType::ALL): array
    {
        return $this->getClasses($namespace, $options, $subclass);
    }

    public function getInterfaces(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::INTERFACE);
    }

    public function getEnums(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::ENUM);
    }

    public function getTraits(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::TRAIT);
    }

    public function getAbstractClasses(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::ABSTRACT);
    }

    public function getFinalClasses(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::FINAL);
    }
}
