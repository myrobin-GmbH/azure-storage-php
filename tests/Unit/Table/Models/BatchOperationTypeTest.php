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

namespace AzureOSS\Storage\Tests\Unit\Table\Models;

use AzureOSS\Storage\Table\Models\BatchOperationType;

/**
 * Unit tests for class BatchOperationType
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class BatchOperationTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        // Setup
        $name = BatchOperationType::DELETE_ENTITY_OPERATION;

        // Test
        $actual = BatchOperationType::isValid($name);

        // Assert
        self::assertTrue($actual);
    }

    public function testIsValidWithInvalid()
    {
        // Setup
        $name = 'zeta el senen';

        // Test
        $actual = BatchOperationType::isValid($name);

        // Assert
        self::assertFalse($actual);
    }
}
