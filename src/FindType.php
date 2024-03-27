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

enum FindType
{
    const INTERFACE = 1;
    const TRAIT     = 2;
    const ENUM      = 4;
    const FINAL     = 8;
    const ABSTRACT  = 16;
    const SIMPLE_CLASS  = 32;
    const CLASSES   = self::FINAL | self::ABSTRACT | self::SIMPLE_CLASS;
    const ALL       = self::INTERFACE | self::TRAIT | self::ENUM | self::CLASSES;
}
