<?php

namespace AzureOSS\Storage\File\Models;

use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\File\Internal\FileResources as Resources;

class CopyFileResult
{
    private $lastModified;
    private $etag;
    private $copyID;
    private $copyStatus;

    /**
     * Creates CopyFileResult object from parsed response header.
     *
     * @param array $headers HTTP response headers
     *
     * @internal
     *
     * @return CopyFileResult
     */
    public static function create(array $headers)
    {
        $result = new CopyFileResult();
        $headers = array_change_key_case($headers);

        $date = $headers[Resources::LAST_MODIFIED];
        $date = Utilities::rfc1123ToDateTime($date);

        $result->setCopyStatus($headers[Resources::X_MS_COPY_STATUS]);
        $result->setCopyID($headers[Resources::X_MS_COPY_ID]);
        $result->setETag($headers[Resources::ETAG]);
        $result->setLastModified($date);

        return $result;
    }

    /**
     * Gets file lastModified.
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Sets file lastModified.
     *
     * @param \DateTime $lastModified value.
     */
    protected function setLastModified(\DateTime $lastModified)
    {
        Validate::isDate($lastModified);
        $this->lastModified = $lastModified;
    }

    /**
     * Gets file etag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * Sets file etag.
     *
     * @param string $etag value.
     */
    protected function setETag($etag)
    {
        Validate::canCastAsString($etag, 'etag');
        $this->etag = $etag;
    }

    /**
     * Gets file copyID.
     *
     * @return string
     */
    public function getCopyID()
    {
        return $this->copyID;
    }

    /**
     * Sets file copyID.
     *
     * @param string $copyID value.
     */
    protected function setCopyID($copyID)
    {
        Validate::canCastAsString($copyID, 'copyID');
        $this->copyID = $copyID;
    }

    /**
     * Gets copyStatus
     *
     * @return string
     */
    public function getCopyStatus()
    {
        return $this->copyStatus;
    }

    /**
     * Sets copyStatus
     *
     * @param string $copyStatus copyStatus to set
     */
    protected function setCopyStatus($copyStatus)
    {
        Validate::canCastAsString($copyStatus, 'copyStatus');
        $this->copyStatus = $copyStatus;
    }
}
