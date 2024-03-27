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
    public function isReadonly(?string $namespace = null, $options = FindType::ALL)
    {
        return $this->getClasses($namespace, $options, fn(ReflectionClass $v) => $v->isReadOnly());
    }

    public function hasTraitClass(string $class, ?string $namespace = null, $options = FindType::ALL)
    {
        return $this->getClasses($namespace, $options, fn(ReflectionClass $v) => in_array($class, $v->getTraitNames()));
    }

    public function implementsClass(string $class, ?string $namespace = null, $options = FindType::ALL)
    {
        return $this->getClasses($namespace, $options, fn(ReflectionClass $v) => $v->implementsInterface($class));
    }

    public function isSubClassOf(string $class, ?string $namespace = null, $options = FindType::ALL)
    {
        return $this->getClasses($namespace, $options, fn(ReflectionClass $v) => $v->isSubclassOf($class));
    }

    public function isAOf(string $class, ?string $namespace = null, $options = FindType::ALL)
    {
        return $this->getClasses($namespace, $options, fn(ReflectionClass $v) => is_a($v->getName(), $class, true));
    }

    public function isInterface(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::INTERFACE);
    }

    public function isEnum(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::ENUM);
    }

    public function isTrait(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::TRAIT);
    }

    public function isAbstract(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::ABSTRACT);
    }

    public function isFinalClasses(string $namespace): array
    {
        return $this->getClasses($namespace, FindType::FINAL);
    }
}
