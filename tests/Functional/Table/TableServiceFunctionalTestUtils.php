<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @see      https://github.com/azure/azure-storage-php
 */

namespace AzureOSS\Storage\Tests\Functional\Table;

use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Table\Models\EdmType;
use AzureOSS\Storage\Table\Models\Entity;
use AzureOSS\Storage\Table\Models\Filters\BinaryFilter;
use AzureOSS\Storage\Table\Models\Filters\ConstantFilter;
use AzureOSS\Storage\Table\Models\Filters\Filter;
use AzureOSS\Storage\Table\Models\Filters\PropertyNameFilter;
use AzureOSS\Storage\Table\Models\Filters\QueryStringFilter;
use AzureOSS\Storage\Table\Models\Filters\UnaryFilter;
use AzureOSS\Storage\Table\Models\Property;
use AzureOSS\Storage\Tests\Functional\Table\Enums\MutatePivot;

class TableServiceFunctionalTestUtils
{
    public static function isEqNotInTopLevel($filter)
    {
        return self::isEqNotInTopLevelWorker($filter, 0);
    }

    private static function isEqNotInTopLevelWorker($filter, $depth)
    {
        if (null === $filter) {
            return false;
        }
        if ($filter instanceof UnaryFilter) {
            return self::isEqNotInTopLevelWorker($filter->getOperand(), $depth + 1);
        }
        if ($filter instanceof BinaryFilter) {
            $binaryFilter = $filter;
            if ($binaryFilter->getOperator() == ('eq') && $depth != 0) {
                return true;
            }

            $left = self::isEqNotInTopLevelWorker($binaryFilter->getLeft(), $depth + 1);
            $right = self::isEqNotInTopLevelWorker($binaryFilter->getRight(), $depth + 1);
            return $left || $right;
        }
        return false;

    }

    public static function cloneRemoveEqNotInTopLevel($filter)
    {
        return self::cloneRemoveEqNotInTopLevelWorker($filter, 0);
    }

    private static function cloneRemoveEqNotInTopLevelWorker($filter, $depth)
    {
        if ($filter instanceof PropertyNameFilter) {
            return new PropertyNameFilter($filter->getPropertyName());
        }
        if ($filter instanceof ConstantFilter) {
            return new ConstantFilter($filter->getEdmType(), $filter->getValue());
        }
        if ($filter instanceof UnaryFilter) {
            $operand = self::cloneRemoveEqNotInTopLevelWorker($filter->getOperand(), $depth + 1);
            return new UnaryFilter($filter->getOperator(), $operand);
        }
        if ($filter instanceof BinaryFilter) {
            if ($filter->getOperator() == ('eq') && $depth != 0) {
                return Filter::applyConstant(false);
            }
            $left = self::cloneRemoveEqNotInTopLevelWorker($filter->getLeft(), $depth + 1);
            $right = self::cloneRemoveEqNotInTopLevelWorker($filter->getRight(), $depth + 1);
            return new BinaryFilter($left, $filter->getOperator(), $right);
        }
        if ($filter instanceof QueryStringFilter) {
            return new QueryStringFilter($filter->getQueryString());
        }
        throw new \Exception();

    }

    public static function filterList($filter, $input)
    {
        $output = [];
        foreach ($input as $i) {
            if (self::filterInterperter($filter, $i)) {
                array_push($output, $i);
            }
        }
        return $output;
    }

    public static function filterEntityList($filter, $input)
    {
        $output = [];
        foreach ($input as $i) {
            $result = self::filterInterperter($filter, $i);
            if (null !== $result && $result) {
                array_push($output, $i);
            }
        }
        return $output;
    }

    public static function cloneEntity($initialEnt)
    {
        $ret = new Entity();
        $initialProps = $initialEnt->getProperties();
        $retProps = [];
        foreach ($initialProps as $propName => $initialProp) {
            // Don't mess with the timestamp.
            if ($propName == ('Timestamp')) {
                continue;
            }

            $retProp = new Property();
            $retProp->setEdmType($initialProp->getEdmType());
            $retProp->setValue($initialProp->getValue());
            $retProps[$propName] = $retProp;
        }
        $ret->setProperties($retProps);
        $ret->setETag($initialEnt->getETag());
        return $ret;
    }

    public static function mutateEntity(&$ent, $pivot)
    {
        if ($pivot == MutatePivot::CHANGE_VALUES) {
            self::mutateEntityChangeValues($ent);
        } elseif ($pivot == MutatePivot::ADD_PROPERTY) {
            $ent->addProperty('BOOLEAN' . TableServiceFunctionalTestData::getNewKey(), EdmType::BOOLEAN, true);
            $ent->addProperty('DATETIME' . TableServiceFunctionalTestData::getNewKey(), EdmType::DATETIME, Utilities::convertToDateTime('2012-01-26T18:26:19.0000470Z'));
            $ent->addProperty('DOUBLE' . TableServiceFunctionalTestData::getNewKey(), EdmType::DOUBLE, 12345678901);
            $ent->addProperty('GUID' . TableServiceFunctionalTestData::getNewKey(), EdmType::GUID, '90ab64d6-d3f8-49ec-b837-b8b5b6367b74');
            $ent->addProperty('INT32' . TableServiceFunctionalTestData::getNewKey(), EdmType::INT32, 23);
            $ent->addProperty('INT64' . TableServiceFunctionalTestData::getNewKey(), EdmType::INT64, '-1');
            $ent->addProperty('STRING' . TableServiceFunctionalTestData::getNewKey(), EdmType::STRING, 'this is a test!');
        } elseif ($pivot == MutatePivot::REMOVE_PROPERTY) {
            $propToRemove = null;
            foreach ($ent->getProperties() as $propName => $propValue) {
                // Don't mess with the keys.
                if ($propName == ('PartitionKey') || $propName == ('RowKey') || $propName == ('Timestamp')) {
                    continue;
                }
                $propToRemove = $propName;
                break;
            }

            $props = $ent->getProperties();
            unset($props[$propToRemove]);
        } elseif ($pivot == MutatePivot::NULL_PROPERTY) {
            foreach ($ent->getProperties() as $propName => $propValue) {
                // Don't mess with the keys.
                if ($propName == ('PartitionKey') || $propName == ('RowKey') || $propName == ('Timestamp')) {
                    continue;
                }
                $propValue->setValue(null);
            }
        }
    }

    private static function mutateEntityChangeValues($ent)
    {
        foreach ($ent->getProperties() as $propName => $initialProp) {
            // Don't mess with the keys.
            if ($propName == ('PartitionKey') || $propName == ('RowKey') || $propName == ('Timestamp')) {
                continue;
            }

            $ptype = $initialProp->getEdmType();
            if (null === $ptype) {
                $eff = $initialProp->getValue();
                $initialProp->setValue($eff . 'AndMore');
            } elseif ($ptype == (EdmType::DATETIME)) {
                $value = $initialProp->getValue();
                if (null === $value) {
                    $value = new \DateTime('1/26/1692');
                }
                $value->modify('+1 day');
                $initialProp->setValue($value);
            } elseif ($ptype == (EdmType::BINARY)) {
                $eff = $initialProp->getValue();
                $initialProp->setValue($eff . 'x');
            } elseif ($ptype == (EdmType::BOOLEAN)) {
                $eff = $initialProp->getValue();
                $initialProp->setValue(!$eff);
            } elseif ($ptype == (EdmType::DOUBLE)) {
                $eff = $initialProp->getValue();
                $initialProp->setValue($eff + 1);
            } elseif ($ptype == (EdmType::GUID)) {
                $initialProp->setValue(Utilities::getGuid());
            } elseif ($ptype == (EdmType::INT32)) {
                $eff = $initialProp->getValue();
                $eff = ($eff > 10 ? 0 : $eff + 1);
                $initialProp->setValue($eff);
            } elseif ($ptype == (EdmType::INT64)) {
                $eff = $initialProp->getValue();
                $eff = ($eff > 10 ? 0 : $eff + 1);
                $initialProp->setValue((string) $eff);
            } elseif ($ptype == (EdmType::STRING)) {
                $eff = $initialProp->getValue();
                $initialProp->setValue($eff . 'AndMore');
            }
        }
    }

    public static function filterToString($filter, $pad = '  ')
    {
        if (null === $filter) {
            return $pad . 'filter <null>' . "\n";
        }
        if ($filter instanceof PropertyNameFilter) {
            return $pad . 'entity.' . $filter->getPropertyName() . "\n";
        }
        if ($filter instanceof ConstantFilter) {
            $ret = $pad;
            if (null === $filter->getValue()) {
                $ret .= 'constant <null>';
            } elseif (is_bool($filter->getValue())) {
                $ret .= ($filter->getValue() ? 'true' : 'false');
            } else {
                $ret .= '\'' . FunctionalTestBase::tmptostring($filter->getValue()) . '\'';
            }
            return $ret . "\n";
        }
        if ($filter instanceof UnaryFilter) {
            $ret = $pad . $filter->getOperator() . "\n";
            $ret .= self::filterToString($filter->getOperand(), $pad . '  ');
            return $ret;
        }
        if ($filter instanceof BinaryFilter) {
            $ret = self::filterToString($filter->getLeft(), $pad . '  ');
            $ret .= $pad . $filter->getOperator() . "\n";
            $ret .= self::filterToString($filter->getRight(), $pad . '  ');
            return $ret;
        }
    }

    private static function filterInterperter($filter, $obj)
    {
        if (null === $filter) {
            return true;
        }
        if (null === $obj) {
            return false;
        }
        if ($filter instanceof PropertyNameFilter) {
            $name = $filter->getPropertyName();
            return $obj instanceof Entity ? $obj->getPropertyValue($name) : $obj->{$name};
        }
        if ($filter instanceof ConstantFilter) {
            return $filter->getValue();
        }
        if ($filter instanceof UnaryFilter) {
            $ret = null;
            if ($filter->getOperator() == ('not')) {
                $op = self::filterInterperter($filter->getOperand(), $obj);
                if (null === $op) {
                    //confirmed with FE that not(NULL) = true;
                    $ret = true;
                } else {
                    $ret = !$op;
                }

                return $ret;
            }
        } elseif ($filter instanceof BinaryFilter) {
            $left = self::filterInterperter($filter->getLeft(), $obj);
            $right = self::filterInterperter($filter->getRight(), $obj);

            $ret = null;
            if ($filter->getOperator() == ('and')) {
                $ret = self::nullPropAnd($left, $right);
            } elseif ($filter->getOperator() == ('or')) {
                $ret = self::nullPropOr($left, $right);
            } elseif ($filter->getOperator() == ('eq')) {
                $ret = self::nullPropEq($left, $right);
            } elseif ($filter->getOperator() == ('ne')) {
                $ret = self::nullPropNe($left, $right);
            } elseif ($filter->getOperator() == ('ge')) {
                $ret = self::nullPropGe($left, $right);
            } elseif ($filter->getOperator() == ('gt')) {
                $ret = self::nullPropGt($left, $right);
            } elseif ($filter->getOperator() == ('lt')) {
                $ret = self::nullPropLt($left, $right);
            } elseif ($filter->getOperator() == ('le')) {
                $ret = self::nullPropLe($left, $right);
            }

            return $ret;
        }

        throw new \Exception();
    }

    private static function nullPropAnd($left, $right)
    {
        // http://msdn.microsoft.com/en-us/library/ms191504.aspx
        if (null === $left && null === $right) {
            return null;
        }
        if (null === $left) {
            return $right ? null : false;
        }
        if (null === $right) {
            return $left ? null : false;
        }
        return $left && $right;

    }

    private static function nullPropOr($left, $right)
    {
        // http://msdn.microsoft.com/en-us/library/ms191504.aspx
        if (null === $left && null === $right) {
            return null;
        }
        if (null === $left) {
            return $right ? true : null;
        }
        if (null === $right) {
            return $left ? true : null;
        }
        return $left || $right;

    }

    private static function nullPropEq($left, $right)
    {
        if (null === $left || null === $right) {
            return null;
        }
        if (is_string($left) || is_string($right)) {
            return ('' . $left) == ('' . $right);
        }
        return $left == $right;
    }

    private static function nullPropNe($left, $right)
    {
        if (null === $left || null === $right) {
            return null;
        }
        if (is_string($left) || is_string($right)) {
            return ('' . $left) != ('' . $right);
        }
        return $left != $right;
    }

    private static function nullPropGt($left, $right)
    {
        if (null === $left || null === $right) {
            return null;
        }
        if (is_string($left) || is_string($right)) {
            return ('' . $left) > ('' . $right);
        }
        return $left > $right;
    }

    private static function nullPropGe($left, $right)
    {
        if (null === $left || null === $right) {
            return null;
        }
        if (is_string($left) || is_string($right)) {
            return ('' . $left) >= ('' . $right);
        }
        return $left >= $right;
    }

    private static function nullPropLt($left, $right)
    {
        if (null === $left || null === $right) {
            return null;
        }
        if (is_string($left) || is_string($right)) {
            return ('' . $left) < ('' . $right);
        }
        return $left < $right;
    }

    private static function nullPropLe($left, $right)
    {
        if (null === $left || null === $right) {
            return null;
        }
        if (is_string($left) || is_string($right)) {
            return ('' . $left) <= ('' . $right);
        }
        return $left <= $right;
    }

    public static function showEntityListDiff($actualData, $expectedData)
    {
        $ret = '';
        if (count($expectedData) != count($actualData)) {
            $ret .= 'VVV actual VVV' . "\n";
            for ($i = 0; $i < count($actualData); ++$i) {
                $e = $actualData[$i];
                $ret .= $e->getPartitionKey() . '/' . $e->getRowKey() . "\n";
            }
            $ret .= '-----------------' . "\n";

            for ($i = 0; $i < count($expectedData); ++$i) {
                $e = $expectedData[$i];
                $ret .= $e->getPartitionKey() . '/' . $e->getRowKey() . "\n";
            }
            $ret .= '^^^ expected ^^^' . "\n";

            for ($i = 0; $i < count($actualData); ++$i) {
                $in = false;
                $ei = $actualData[$i];
                for ($j = 0; $j < count($expectedData); ++$j) {
                    $ej = $expectedData[$j];
                    if ($ei->getPartitionKey() == $ej->getPartitionKey() && $ei->getRowKey() == $ej->getRowKey()) {
                        $in = true;
                    }
                }
                if (!$in) {
                    $ret .= 'returned ' . FunctionalTestBase::tmptostring($ei) . "\n";
                }
            }

            for ($j = 0; $j < count($expectedData); ++$j) {
                $in = false;
                $ej = $expectedData[$j];
                for ($i = 0; $i < count($actualData); ++$i) {
                    $ei = $actualData[$i];
                    if ($ei->getPartitionKey() == $ej->getPartitionKey() && $ei->getRowKey() == $ej->getRowKey()) {
                        $in = true;
                    }
                }
                if (!$in) {
                    $ret .= 'expected ' . FunctionalTestBase::tmptostring($ej) . "\n";
                }
            }
        }
        return $ret;
    }
}
