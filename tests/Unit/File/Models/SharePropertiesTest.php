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

namespace AzureOSS\Storage\Tests\Unit\File\Models;

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\File\Models\ShareProperties;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class ShareProperties
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class SharePropertiesTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $responseArray = TestResources::getInterestingSharePropertiesArray();
        $shareProperties = ShareProperties::create($responseArray);
        $expectedLastModified = Utilities::rfc1123ToDateTime($responseArray[Resources::QP_LAST_MODIFIED]);
        $expectedEtag = $responseArray[Resources::QP_ETAG];
        $expectedQuota = $responseArray[Resources::QP_QUOTA];

        self::assertEquals($expectedLastModified, $shareProperties->getLastModified());
        self::assertEquals($expectedEtag, $shareProperties->getETag());
        self::assertEquals($expectedQuota, $shareProperties->getQuota());
    }
}
