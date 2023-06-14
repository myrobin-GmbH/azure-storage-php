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

namespace AzureOSS\Storage\Table\Models;

use AzureOSS\Storage\Common\Internal\ACLBase;
use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\Table\Internal\TableResources as Resources;

/**
 * Holds table ACL members.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class TableACL extends ACLBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        //setting the resource type to a default value.
        $this->setResourceType(Resources::RESOURCE_TYPE_TABLE);
    }

    /**
     * Parses the given array into signed identifiers and create an instance of
     * TableACL
     *
     * @param array $parsed The parsed response into array representation.
     *
     * @internal
     *
     * @return TableACL
     */
    public static function create(array $parsed = null)
    {
        $result = new TableACL();
        $result->fromXmlArray($parsed);

        return $result;
    }

    /**
     * Validate if the resource type is for the class.
     *
     * @param string $resourceType the resource type to be validated.
     *
     * @throws \InvalidArgumentException
     *
     * @internal
     */
    protected static function validateResourceType($resourceType)
    {
        Validate::isTrue(
            $resourceType == Resources::RESOURCE_TYPE_TABLE,
            Resources::INVALID_RESOURCE_TYPE
        );
    }

    /**
     * Create a TableAccessPolicy object.
     *
     * @return TableAccessPolicy
     */
    protected static function createAccessPolicy()
    {
        return new TableAccessPolicy();
    }
}