<?php

namespace Freegli\Component\APNs;

use Freegli\Component\APNs\Exception\ConnectionException;

class ConnectionFactory
{
    private $certificatPath;
    private $certificatPassPhrase;

    public function __construct($certificatPath = null, $certificatPassPhrase = null)
    {
        $this->certificatPath       = $certificatPath;
        $this->certificatPassPhrase = $certificatPassPhrase;
    }

    /**
     * Open stream connection to APNs.
     *
     * @param string $url Service URL to connect
     *
     * @return resource
     *
     * @throws ConnectionException
     */
    public function getConnection($url)
    {
        $streamContext = stream_context_create();

        if ($this->certificatPath) {
            stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->certificatPath);
        }
        if ($this->certificatPassPhrase) {
            stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->certificatPassPhrase);
        }

        try {
            $connection = stream_socket_client($url, $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $streamContext);
        } catch (\Exception $e) {
            throw new ConnectionException(sprintf('Unable to connect to "%s"', $url), null, $e);
        }
        if ($connection === false) {
            throw new ConnectionException($errstr, $errno);
        }

        return $connection;
    }
}
