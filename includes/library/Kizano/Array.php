/**
 * Kizano/Misc.php
 *
 * PHP version 5.*
 *
 *   Namespace placeholder for functions that would normally be free-floating.
 *   Copyright (C) 2010 Markizano Draconus <markizano@markizano.net>
 *
 *   This class is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Kizano
 * @package   Kizano
 * @author    Markizano Draconus <markizano@markizano.net>
 * @copyright 2010 Markizano Draconus
 * @license   http://www.gnu.org/licenses/gpl.html GNU Public License
 * @link      https://github.com/markizano/markizano/blob/master/includes/library/Kizano/Array.php
 */

class Kizano_Array
{

    /**
     *  Surprisingly this function does not exist in PHP natively.
     *  Behaves similar to array_merge in that it will merge two or more arrays, except it checks
     *  for existing keys and attempts to overwrite them instead of appending them.
     *  @param-list     arrays to merge
     *  @return array
     */
    public static function array_merge()
    {
        $result = array();
        # For each of the arguments passed into this function
        foreach (func_get_args() as $arrays) {
            if (is_array($arrays) && count($arrays)) {
                # Process each argument as if it were an array
                foreach ($arrays as $a => $array) {
                    if (is_array($array)) {
                        $result[$a] = self::array_merge($result[$a], $array);
                    } else {
                        $result[$a] = $array;
                    }
                }
            }
        }
        return $result;
    }
}

