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

use AzureOSS\Storage\Tests\Framework\TableServiceRestProxyTestBase;

class IntegrationTestBase extends TableServiceRestProxyTestBase
{
    private static $isOneTimeSetup = false;

    public function setUp(): void
    {
        parent::setUp();
        if (!self::$isOneTimeSetup) {
            self::$isOneTimeSetup = true;
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$isOneTimeSetup) {
            $integrationTestBase = new IntegrationTestBase();
            $integrationTestBase->setUp();
            if (!$integrationTestBase->isEmulated()) {
                $serviceProperties = TableServiceFunctionalTestData::getDefaultServiceProperties();
                $integrationTestBase->restProxy->setServiceProperties($serviceProperties);
            }
            self::$isOneTimeSetup = false;
        }
        parent::tearDownAfterClass();
    }
}
