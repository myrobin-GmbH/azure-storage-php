<?php

namespace AzureOSS\Storage\Table\Models;

use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\Table\Internal\TableResources as Resources;

class EdmType
{
    public const DATETIME = 'Edm.DateTime';
    public const BINARY = 'Edm.Binary';
    public const BOOLEAN = 'Edm.Boolean';
    public const DOUBLE = 'Edm.Double';
    public const GUID = 'Edm.Guid';
    public const INT32 = 'Edm.Int32';
    public const INT64 = 'Edm.Int64';
    public const STRING = 'Edm.String';

    public static function propertyType($value)
    {
        if (is_int($value)) {
            if ($value <= Resources::INT32_MAX && $value >= Resources::INT32_MIN) {
                return EdmType::INT32;
            }
            return EdmType::INT64;
        }
        if (Utilities::isDouble($value)) {
            return EdmType::DOUBLE;
        }
        if (is_bool($value)) {
            return EdmType::BOOLEAN;
        }
        if ($value instanceof \DateTime) {
            return EdmType::DATETIME;
        }
        return EdmType::STRING;
    }

    public static function typeRequired($type)
    {
        switch ($type) {
            case EdmType::BINARY:
            case EdmType::INT64:
            case EdmType::DATETIME:
            case EdmType::GUID:
            case EdmType::DOUBLE:
            case EdmType::BOOLEAN:
                return true;

            default:
                return false;
        }
    }

    /**
     * Converts the type to string if it's empty and validates the type.
     *
     * @param string $type The Edm type
     *
     * @internal
     *
     * @return string
     */
    public static function processType($type)
    {
        $type = empty($type) ? self::STRING : $type;
        Validate::isTrue(self::isValid($type), Resources::INVALID_EDM_MSG);

        return $type;
    }

    /**
     * Validates that the value associated with the EDM type is valid.
     *
     * @param string $type       The EDM type.
     * @param mixed  $value      The EDM value.
     * @param string &$condition The error message.
     *
     * @internal
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public static function validateEdmValue($type, $value, &$condition = null)
    {
        // Having null value means that the user wants to remove the property name
        // associated with this value. Leave the value as null so this hold.
        if (null === $value) {
            return true;
        }
        switch ($type) {
            case EdmType::GUID:
            case EdmType::BINARY:
            case EdmType::STRING:
            case EdmType::INT64:
            case null:
                // NULL also is treated as EdmType::STRING
                $condition = 'is_string';
                return is_string($value);

            case EdmType::DOUBLE:
                $condition = 'is_double or is_string';
                return is_float($value) || is_int($value) || is_string($value);

            case EdmType::INT32:
                $condition = 'is_int or is_string';
                return is_int($value) || is_string($value);

            case EdmType::DATETIME:
                $condition = 'instanceof \DateTimeInterface';
                return $value instanceof \DateTimeInterface;

            case EdmType::BOOLEAN:
                $condition = 'is_bool';
                return is_bool($value);

            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * Serializes EDM value into proper value for sending it to Windows Azure.
     *
     * @param string $type  The EDM type.
     * @param mixed  $value The EDM value.
     *
     * @internal
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function serializeValue($type, $value)
    {
        switch ($type) {
            case null:
                return $value;

            case EdmType::INT32:
                return (int) $value;

            case EdmType::INT64:
            case EdmType::GUID:
            case EdmType::STRING:
                return (string) $value;

            case EdmType::DOUBLE:
                return (string) $value;

            case EdmType::BINARY:
                return base64_encode($value);

            case EdmType::DATETIME:
                return Utilities::convertToEdmDateTime($value);

            case EdmType::BOOLEAN:
                return null === $value ? '' : ($value == true ? true : false);

            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * Serializes EDM value into proper value to be used in query.
     *
     * @param string $type  The EDM type.
     * @param mixed  $value The EDM value.
     *
     * @internal
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function serializeQueryValue($type, $value)
    {
        switch ($type) {
            case EdmType::DATETIME:
                $edmDate = Utilities::convertToEdmDateTime($value);
                return 'datetime\'' . $edmDate . '\'';

            case EdmType::BINARY:
                return 'X\'' . implode('', unpack('H*', $value)) . '\'';

            case EdmType::BOOLEAN:
                return $value ? 'true' : 'false';

            case EdmType::DOUBLE:
            case EdmType::INT32:
                return $value;

            case EdmType::INT64:
                return $value . 'L';

            case EdmType::GUID:
                return 'guid\'' . $value . '\'';

            case null:
            case EdmType::STRING:
                // NULL also is treated as EdmType::STRING
                return '\'' . str_replace('\'', '\'\'', $value) . '\'';

            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * Converts the value into its proper type.
     *
     * @param string $type  The edm type.
     * @param string $value The edm value.
     *
     * @internal
     *
     * @throws \InvalidArgumentException
     */
    public static function unserializeQueryValue($type, $value)
    {
        // Having null value means that the user wants to remove the property name
        // associated with this value. Leave the value as null so this hold.
        if (null === $value) {
            return null;
        }
        switch ($type) {
            case self::GUID:
            case self::STRING:
            case self::INT64:
            case null:
                return (string) $value;

            case self::BINARY:
                return base64_decode($value, true);

            case self::DATETIME:
                return Utilities::convertToDateTime($value);

            case self::BOOLEAN:
                return Utilities::toBoolean($value);

            case self::DOUBLE:
                return (float) $value;

            case self::INT32:
                return (int) $value;

            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * Check if the $type belongs to valid header types.
     *
     * @param string $type The type string to check.
     *
     * @internal
     *
     * @return bool
     */
    public static function isValid($type)
    {
        switch ($type) {
            case $type == self::DATETIME:
            case $type == self::BINARY:
            case $type == self::BOOLEAN:
            case $type == self::DOUBLE:
            case $type == self::GUID:
            case $type == self::INT32:
            case $type == self::INT64:
            case $type == self::STRING:
            case $type == null:
                // NULL also is treated as EdmType::STRING
                return true;

            default:
                return false;
        }
    }
}
