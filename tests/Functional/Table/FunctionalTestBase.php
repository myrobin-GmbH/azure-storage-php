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
use AzureOSS\Storage\Table\Models\Entity;
use AzureOSS\Storage\Table\Models\Filters\Filter;

class FunctionalTestBase extends IntegrationTestBase
{
    private static $isOneTimeSetup = false;

    public function setUp(): void
    {
        parent::setUp();
        if (!self::$isOneTimeSetup) {
            $this->doOneTimeSetup();
            self::$isOneTimeSetup = true;
        }
    }

    private function doOneTimeSetup()
    {
        TableServiceFunctionalTestData::setupData();

        foreach (TableServiceFunctionalTestData::$testTableNames as $name) {
            // self::println('Creating Table: ' . $name);
            $this->restProxy->createTable($name);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$isOneTimeSetup) {
            $testBase = new FunctionalTestBase();
            $testBase->setUp();
            foreach (TableServiceFunctionalTestData::$testTableNames as $name) {
                $testBase->safeDeleteTable($name);
            }
            self::$isOneTimeSetup = false;
        }
        parent::tearDownAfterClass();
    }

    protected function clearTable($table)
    {
        $index = array_search($table, TableServiceFunctionalTestData::$testTableNames, true);
        if ($index !== false) {
            // This is a well-known table, so need to create a new one to replace it.
            TableServiceFunctionalTestData::$testTableNames[$index] = TableServiceFunctionalTestData::getInterestingTableName();
            $this->restProxy->createTable(TableServiceFunctionalTestData::$testTableNames[$index]);
        }

        $this->restProxy->deleteTable($table);
    }

    protected function getCleanTable()
    {
        $this->clearTable(TableServiceFunctionalTestData::$testTableNames[0]);
        return TableServiceFunctionalTestData::$testTableNames[0];
    }

    public static function println($msg)
    {
        //error_log($msg);
    }

    public static function tmptostring($value)
    {
        if (null === $value) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value == true ? 'true' : 'false';
        }
        if ($value instanceof \DateTime) {
            return Utilities::convertToEdmDateTime($value);
        }
        if ($value instanceof Entity) {
            return self::entityToString($value);
        }
        if (is_array($value)) {
            return self::entityPropsToString($value);
        }
        if ($value instanceof Filter) {
            return TableServiceFunctionalTestUtils::filtertoString($value);
        }
        return $value;

    }

    public static function entityPropsToString($props)
    {
        $ret = '';
        foreach ($props as $k => $value) {
            $ret .= $k . ':';
            if (null === $value) {
                $ret .= 'NULL PROP!';
            } else {
                $ret .= $value->getEdmType() . ':' . self::tmptostring($value->getValue());
            }
            $ret .= "\n";
        }
        return $ret;
    }

    public static function entityToString($ent)
    {
        $ret = 'ETag=' . self::tmptostring($ent->getETag()) . "\n";
        $ret .= 'Props=' . self::entityPropsToString($ent->getProperties());
        return $ret;
    }
}
