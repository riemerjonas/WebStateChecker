<?php

namespace Webchecker\Classes;
class Test
{
    private string $url;
    private bool $notifyOnError;
    private TestResult $result;


    public function __construct(string $url, bool $notifyOnError = false)
    {
        $this->url = $url;
        $this->notifyOnError = $notifyOnError;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function shouldNotifyOnError(): bool
    {
        return $this->notifyOnError;
    }

    public function hasResult(): bool
    {
        return isset($this->result);
    }

    public function getResult(): ?TestResult
    {
        return $this->result ?? null;
    }

    public function setResult(TestResult $result): void
    {
        $this->result = $result;
    }

    public function runTest() : void
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS sites
        curl_exec($ch);

        $errorMessage = '';
        $hasError = false;
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            $hasError = true;
            $statusCode = 0; // Indicate that no valid status code was received
        } elseif ($statusCode < 200 || $statusCode >= 400) {
            $hasError = true;
            $errorMessage = "Unexpected status code: $statusCode";
        }

        curl_close($ch);

        $this->result = new TestResult([
            'websiteUrl'   => $this->url,
            'hasError'     => $hasError,
            'statusCode'   => $statusCode,
            'errorMessage' => $errorMessage,
            'timestamp'    => date('Y-m-d H:i:s'),
        ]);
    }
}

?>