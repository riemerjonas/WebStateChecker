<?php

namespace Webchecker\Classes;
class TestResult
{
    private string $websiteUrl;
    private bool $hasError;
    private int $statusCode;
    private string $errorMessage;
    private string $timestamp;

    public function __construct(array $parameters = [])
    {
        $this->websiteUrl   = $parameters['websiteUrl']     ?? '';
        $this->hasError     = $parameters['hasError']       ?? false;
        $this->statusCode   = $parameters['statusCode']     ?? 0;
        $this->errorMessage = $parameters['errorMessage']   ?? '';
        $this->timestamp    = $parameters['timestamp']      ?? date('Y-m-d H:i:s');
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function toArray(): array
    {
        return [
            'websiteUrl'   => $this->websiteUrl,
            'hasError'     => $this->hasError,
            'statusCode'   => $this->statusCode,
            'errorMessage' => $this->errorMessage,
            'timestamp'    => $this->timestamp,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}


?>