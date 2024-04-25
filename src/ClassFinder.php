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

use Closure;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use ReflectionClass;
use ReflectionEnum;

/**
 * @mixin ClassFinder
 */
final class ClassFinder
{
    use EasyFinder;

    public const FileSystemFlags = FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS;

    private array $namespaces = [];
    private array $files = [];

    /**
     * @param ?string $path Dirname of the vendor folder
     */
    public function __construct(?string $path = null)
    {
        if (!\interface_exists('Reflector')) {
            throw new Exception('Could not find Reflector interface');
        }
        $path           = \rtrim($path ?? __DIR__ . '/..', '/');
        $vendor         = $path   . '/vendor';
        $autoload       = $vendor . '/autoload.php';
        $autoload_psr4  = $vendor . '/composer/autoload_psr4.php';
        $autoload_files = $vendor . '/composer/autoload_files.php';
        require_once $autoload;
        if (\file_exists($autoload_files)) {
            $this->files  = \array_map('realpath', \array_values(require_once $autoload_files));
        }
        $this->namespaces = require_once($autoload_psr4);
    }

    /**
     * Get classes from namespace.
     *
     * @param string|null           $namespace Namespace
     * @param int                   $options   Whether which classes should return
     * @param callable|Closure|null $cb        Extra Callback to filter
     */
    public function getClasses(?string $namespace = null, int $options = FindType::ALL, callable|Closure $cb = null): array
    {
        $cb ??= fn (ReflectionClass $v) => true;
        $uncheck   = empty($namespace);
        $classes = [];
        $namespace .= \str_ends_with($namespace ?? '', '\\') ? '' : '\\';
        foreach ($this->namespaces as $k => $v) {
            if ($uncheck || \str_starts_with($namespace, $k) || \str_starts_with($k, $namespace)) {
                $main = \implode('', $v);
                $path = $main . '/' . \str_replace('\\', DIRECTORY_SEPARATOR, \substr($namespace, \strlen($k)));
                $name = \rtrim($k, '\\');
                $classes += $this->getClassesInternal($path, $main, $name, $options, $cb);
            }
        }
        return \array_values($classes);
    }

    /**
     * Filter the class.
     *
     * @param class-string     $class
     */
    private function filterClass(string $class, int $options, callable|Closure $cb): ReflectionClass|false
    {
        if (\enum_exists($class) && ($options & FindType::ENUM)) {
            $refClass = new ReflectionEnum($class);
            return $cb($refClass) ? $refClass : false;
        } elseif (\trait_exists($class) && ($options & FindType::TRAIT) || \interface_exists($class) && ($options & FindType::INTERFACE)) {
            $refClass = new ReflectionClass($class);
            return $cb($refClass) ? $refClass : false;
        } elseif (\class_exists($class) && ($options & FindType::CLASSES)) {
            $refClass = new ReflectionClass($class);
            return match (true) {
                $refClass->isEnum()     => ($options & FindType::ENUM)         && $cb($refClass) ? $refClass : false,
                $refClass->isFinal()    => ($options & FindType::FINAL)        && $cb($refClass) ? $refClass : false,
                $refClass->isAbstract() => ($options & FindType::ABSTRACT)     && $cb($refClass) ? $refClass : false,
                default                 => ($options & FindType::SIMPLE_CLASS) && $cb($refClass) ? $refClass : false
            };
        }
        return false;
    }

    private function getClassesInternal(RecursiveDirectoryIterator|string $path, string $mainpath, string $namespace, int $options, callable|Closure $cb): array
    {
        $classes = [];
        if (\is_string($path)) {
            $path = \realpath($path);
            $path = $path ? new RecursiveDirectoryIterator($path, self::FileSystemFlags) : [];
        }

        foreach ($path as $k => $v) {
            if ($v->isDir()) {
                $classes += $this->getClassesInternal($v->getChildren(), $mainpath, $namespace, $options, $cb);
            } elseif ($v->isFile() && \str_ends_with($v->getRealPath(), '.php')) {
                $class = $this->createClassName($v, $mainpath, $namespace);
                if ($this->filterClass($class, $options, $cb) !== false) {
                    $classes[$k] = $class;
                }
            }
        }
        return $classes;
    }

    private function createClassName(RecursiveDirectoryIterator $file, string $main, string $namespace): string
    {
        if (\in_array($file->getRealPath(), $this->files)) {
            return '';
        }
        $namespace .= \substr($file->getPath(), \strlen($main));
        $namespace .= '\\' . $file->getBasename('.php');
        return \str_replace('/', '\\', $namespace);
    }
}
