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

namespace AzureOSS\Storage\Table\Models\Filters;

use AzureOSS\Storage\Table\Models\EdmType;

/**
 * Constant filter
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ConstantFilter extends Filter
{
    private $_value;
    private $_edmType;

    /**
     * Constructor.
     *
     * @param string $edmType The EDM type.
     * @param string $value   The EDM value.
     */
    public function __construct($edmType, $value)
    {
        $this->_edmType = EdmType::processType($edmType);
        $this->_value = $value;
    }

    /**
     * Gets value
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Gets the type of the constant.
     *
     * @return string
     */
    public function getEdmType()
    {
        return $this->_edmType;
    }
}