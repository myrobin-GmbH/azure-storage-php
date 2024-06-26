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

namespace Tests\Unit\AzureOSS\Storage\Table\Models\internal;

use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Table\Internal\JsonODataReaderWriter;
use AzureOSS\Storage\Table\Models\EdmType;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class JsonODataReaderWriter
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class JsonODataReaderWriterTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTable()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $tablename = 'mytable';
        $expected = TestResources::getTableJSONFormat($tablename);

        // Test
        $actual = $serializer->getTable($tablename);

        // Assert
        self::assertEquals($expected, $actual);

        return $actual;
    }

    public function testGetEntity()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $entity = TestResources::getTestEntity('123', '456');
        $entity->addProperty('Cost', EdmType::DOUBLE, 12.45);
        $expected = TestResources::ENTITY_JSON_STRING;

        // Test
        $actual = $serializer->getEntity($entity);

        // Assert
        self::assertEquals($expected, $actual);

        return $actual;
    }

    public function testParseTable()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = 'mytable';
        $tableJSON = TestResources::getTableEntryMinimalMetaResult();

        // Test
        $actual = $serializer->parseTable($tableJSON);

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testParseTables()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = ['mytable1', 'mytable2', 'mytable3', 'mytable4', 'mytable5'];
        $tableJSON0 = TestResources::getTableEntriesMinimalMetaResult();
        $tableJSON1 = TestResources::getTableEntriesNoMetaResult();
        $tableJSON2 = TestResources::getTableEntriesFullMetaResult();

        // Test
        $actual0 = $serializer->parseTableEntries($tableJSON0);
        $actual1 = $serializer->parseTableEntries($tableJSON1);
        $actual2 = $serializer->parseTableEntries($tableJSON2);

        // Assert
        self::assertEquals($expected, $actual0);
        self::assertEquals($expected, $actual1);
        self::assertEquals($expected, $actual2);
    }

    public function testParseEntity()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = TestResources::getExpectedTestEntity('123', '456');
        $json = TestResources::getEntityMinimalMetaResult('123', '456');

        // Test
        $actual = $serializer->parseEntity($json);

        // Assert
        $expectedProperties = $expected->getProperties();
        $actualProperties = $actual->getProperties();
        foreach ($expectedProperties as $key => $property) {
            self::assertEquals(
                $property->getEdmType(),
                $actualProperties[$key]->getEdmType()
            );
            self::assertEquals(
                $property->getValue(),
                $actualProperties[$key]->getValue()
            );
        }
    }

    public function testParseEntityStringKeys()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = TestResources::getExpectedTestEntity('0e123', '123e456');
        $json = TestResources::getEntityMinimalMetaResult('0e123', '123e456');

        // Test
        $actual = $serializer->parseEntity($json);

        // Assert
        self::assertSame(
            $expected->getPartitionKey(),
            $actual->getPartitionKey()
        );
        self::assertSame(
            $expected->getRowKey(),
            $actual->getRowKey()
        );
    }

    public function testParseEntities()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $pk1 = '123';
        $pk2 = '124';
        $pk3 = '125';
        $e1 = TestResources::getExpectedTestEntity($pk1, '1');
        $e2 = TestResources::getExpectedTestEntity($pk2, '2');
        $e3 = TestResources::getExpectedTestEntity($pk3, '3');
        $e1->setETag('W/"datetime\'2012-05-17T00%3A59%3A32.1131734Z\'"');
        $e2->setETag('W/"datetime\'2012-05-17T00%3A59%3A32.4252358Z\'"');
        $e3->setETag('W/"datetime\'2012-05-17T00%3A59%3A32.7533014Z\'"');
        $e1->setTimestamp(Utilities::convertToDateTime('2012-05-17T00:59:32.1131734Z'));
        $e2->setTimestamp(Utilities::convertToDateTime('2012-05-17T00:59:32.4252358Z'));
        $e3->setTimestamp(Utilities::convertToDateTime('2012-05-17T00:59:32.7533014Z'));
        $expected = [$e1, $e2, $e3];
        $entitiesJSON = TestResources::getEntitiesMinimalMetaResult();

        // Test
        $actual = $serializer->parseEntities($entitiesJSON);

        // Assert
        for ($i = 0; $i < 3; ++$i) {
            $expectedProperties = $expected[$i]->getProperties();
            $actualProperties = $actual[$i]->getProperties();
            foreach ($expectedProperties as $key => $property) {
                self::assertEquals(
                    $property->getEdmType(),
                    $actualProperties[$key]->getEdmType()
                );
                self::assertEquals(
                    $property->getValue(),
                    $actualProperties[$key]->getValue()
                );
            }
        }
    }

    public function testVariousTypes()
    {
        $serializer = new JsonODataReaderWriter();
        $e = TestResources::getVariousTypesEntity();

        $jsonString = $serializer->getEntity($e);

        $a = $serializer->parseEntity($jsonString);

        self::assertEquals($e, $a);
    }
}
