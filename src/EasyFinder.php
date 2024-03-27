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

use ReflectionClass;

trait EasyFinder
{

    /**
     * Whether a class is readonly.
     *
     * @param string|null $namespace Namespace
     * @param int         $options   Whether which classes should return
     */
    public function isReadonly(?string $namespace = null, int $options = FindType::ALL): array
    {
        return $this->getClasses($namespace, $options, fn (ReflectionClass $v) => $v->isReadOnly());
    }

    /**
     * Whether class use this trait.
     *
     * @param class-string $class     Class name
     * @param string|null  $namespace Namespace
     * @param int          $options   Whether which classes should return
     */
    public function hasTraitClass(string $class, ?string $namespace = null, int $options = FindType::ALL): array
    {
        return $this->getClasses($namespace, $options, fn (ReflectionClass $v) => \in_array($class, $v->getTraitNames()));
    }

    /**
     * Whether class implements an interface.
     *
     * @param class-string $class     Class name
     * @param string|null  $namespace Namespace
     * @param int          $options   Whether which classes should return
     */
    public function implementsClass(string $class, ?string $namespace = null, int $options = FindType::ALL): array
    {
        return $this->getClasses($namespace, $options, fn (ReflectionClass $v) => $v->implementsInterface($class));
    }

    /**
     * Whether class is subclass of this class.
     *
     * @param class-string $class     Class name
     * @param string|null  $namespace Namespace
     * @param int          $options   Whether which classes should return
     */
    public function isSubClassOf(string $class, ?string $namespace = null, int $options = FindType::ALL): array
    {
        return $this->getClasses($namespace, $options, fn (ReflectionClass $v) => $v->isSubclassOf($class));
    }

    /**
     * Whether class is of this class or has this class as one of its parents.
     *
     * @param class-string $class     Class name
     * @param string|null  $namespace Namespace
     * @param int          $options   Whether which classes should return
     */
    public function isAOf(string $class, ?string $namespace = null, int $options = FindType::ALL): array
    {
        return $this->getClasses($namespace, $options, fn (ReflectionClass $v) => \is_a($v->getName(), $class, true));
    }

    /**
     * Returns just interface classes.
     *
     * @param string $namespace Namespace
     */
    public function isInterface(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::INTERFACE);
    }

    /**
     * Returns just enum classes.
     *
     * @param string $namespace Namespace
     */
    public function isEnum(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::ENUM);
    }

    /**
     * Returns just trait classes.
     *
     * @param string $namespace Namespace
     */
    public function isTrait(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::TRAIT);
    }

    /**
     * Returns just abstract classes.
     *
     * @param string $namespace Namespace
     */
    public function isAbstract(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::ABSTRACT);
    }

    /**
     * Returns just final classes.
     *
     * @param string $namespace Namespace
     */
    public function isFinalClasses(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::FINAL);
    }
}
