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
    const INTERFACE = 4;
    const TRAIT     = 8;
    const ENUM      = 16;
    const FINAL     = 32;
    const ABSTRACT  = 64;
    const READONLY  = 128;
    const CLASSES   = self::FINAL | self::ABSTRACT | self::READONLY;
    const ALL       = self::INTERFACE | self::TRAIT | self::ENUM | self::CLASSES;
}
