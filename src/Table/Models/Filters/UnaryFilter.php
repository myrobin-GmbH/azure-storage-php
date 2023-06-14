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

/**
 * Unary filter
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class UnaryFilter extends Filter
{
    private $_operator;
    private $_operand;

    /**
     * Constructor.
     *
     * @param string $operator The operator.
     * @param Filter $operand  The operand filter.
     */
    public function __construct($operator, Filter $operand = null)
    {
        $this->_operand = $operand;
        $this->_operator = $operator;
    }

    /**
     * Gets operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->_operator;
    }

    /**
     * Gets operand
     *
     * @return Filter
     */
    public function getOperand()
    {
        return $this->_operand;
    }
}