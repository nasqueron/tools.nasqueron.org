<?php

/**
 * Nasqueron Tools
 *
 * JSON composer
 *
 * @package     NasqueronTools
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Composes a JSON file
 */
class JsonComposer {
    /**
     * Gets a JSON representation from two arrays, one with the properties, the other with the values
     *
     * @param array $properties An array of the properties, each item a string
     * @param array $data An array of the values, each item an array with the same amount of columns than the properties array, each item a value
     * @return string the JSON representation
     */
    public static function fromKeysAndValuesArrays($properties, $data) {
        $dataArray = [];
        $countProperties = count($properties);

        foreach ($data as $row) {
            $rowProperties = $properties;
            $delta = count($row) - $countProperties;
            if ($delta < 0) {
                //When we've more properties than values, we fill with empty strings.
                $row += array_fill($countProperties + $delta, $delta * - 1, '');
            } elseif ($delta > 0) {
                //When we miss properties, adds property4, property5, property6, ...
                for ($i = $countProperties ; $i < $delta + $countProperties ; $i ++) {
                    $rowProperties[] = "property$i";
                }

            }
            $dataArray[] = array_combine($rowProperties, $row);
        }
        return json_encode($dataArray, JSON_PRETTY_PRINT);
    }
 }
