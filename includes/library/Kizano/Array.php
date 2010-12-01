<?php
/**
 * Kizano/Array.php
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

/**
 *  As surprising as it may be, PHP does not support these functions natively. I've searched the
 *  documentation and the source, but to no avail have I found a native solution to these functions
 *  and their needs, therefore they have been added here to provide additional functionality to
 *  PHP's native functionality.
 */
class Kizano_Array
{

    /**
     *  Behaves similarly to array_merge in that it will merge two or more arrays, except it checks
     *  for existing keys and attempts to overwrite them instead of appending them. Also, working with
     *  array_merge and numeric keys provided unexpected and undesired results. The primary reason
     *  this function was created to provide a more stable way of merging two or more arrays and make
     *  their values overwrite one another instead of appending as with array_merge().
     *  http://php.net/manual/en/function.array-merge.php
     *  @Example:
     *      <?php
     *      
     *      $numbers = array(
     *          0 => 'zero',
     *          1 => 'one',
     *          2 => 'two',
     *          3 => 'three',
     *          4 => 'four',
     *          5 => 'five',
     *      );
     *      
     *      $nums => array(
     *          2 => '2',
     *          3 => '3',
     *          4 => '4',
     *      );
     *      
     *      var_dump(array_merge($numbers, $nums));
     *      ?>
     *      
     *      array
     *          0 => string '2' (length=1)
     *          1 => string '3' (length=1)
     *          2 => string '4' (length=1)
     *          3 => string 'three' (length=5)
     *          4 => string 'four' (length=4)
     *          5 => string 'five' (length=4)
     *  
     *  @notes  As you can see in the code example above, array_merge will first re-order the keys
     *      of the provided arrays, and then attempt to merge them if they are numeric, however for
     *      the purposes of some functions in this application, particularly /admin/page-permissions,
     *      we need the keys to be overwritten instead of re-ordered. This function accomplishes this
     *      task without too much overhead.
     *  
     *  @param-list     arrays to merge
     *  @return array
     */
    public static function merge()
    {
        $result = array();
        # For each of the arguments passed into this function
        foreach (func_get_args() as $arrays) {
            if (is_array($arrays) && count($arrays)) {
                # Process each argument as if it were an array
                foreach ($arrays as $a => $array) {
                    if (is_array($array)) {
                        $me = __FUNCTION__;
                        if (isset($result[$a])) {
                            $result[$a] = self::$me($result[$a], $array);
                        } else {
                            $result[$a] = $array;
                        }
                    } else {
                        $result[$a] = $array;
                    }
                }
            }
        }
        return $result;
    }

    /**
     *  Cuts an array in half and returns the halves as an array.
     *  @param array            Array   The array to split.
     *  @param preserve_keys    Boolean Whether to preserve the keys in the original array.
     *  @return array
     */
    public static function half($array, $preserve_keys = false)
    {
        $half = count($array) / 2;
        return array(
            array_slice($array, 0, $half, $preserve_keys),
            array_slice($array, $half, null, $preserve_keys)
        );
    }
}
