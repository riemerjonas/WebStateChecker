<?php

namespace Webchecker\Handlers;

use Webchecker\Classes\TestResult;
use Webchecker\Utilities\ConfigLoader;

class OutputHandler
{

    public static function print(array $results)
    {
        $configLoader = new ConfigLoader("config.json");
        $outputType = $configLoader->get("output_type", "json");

        switch (strtolower($outputType)) {
            case 'html':
                foreach ($results as $result) {
                    self::printHTML($result);
                }
                break;
            case 'json':
            default:
                self::printJSON($results);
                break;
        }
    }

    private static function printHTML(TestResult $result): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $statusText  = $result->hasError() ? '❌ Fehlgeschlagen' : '✅ Erfolgreich';
        $statusColor = $result->hasError() ? '#c62828' : '#2e7d32';
        $message     = $result->getErrorMessage() ?: '-';

        echo <<<HTML
            <div style="
                font-family: system-ui, sans-serif;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                padding: 1rem;
                margin: 1rem 0;
                max-width: 800px;
            ">
                <div style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: .5rem;">
                    <span style="color:#333; word-break: break-all;">{$result->getWebsiteUrl()}</span>
                    <span style="color: {$statusColor};">{$statusText}</span>
                </div>
                <div style="font-size: 0.9rem; color: #555; margin-bottom: .5rem;">
                    <span style="margin-right: 1rem;">HTTP-Code: {$result->getStatusCode()}</span>
                    <span>{$result->getTimestamp()}</span>
                </div>
                <div style="font-size: 0.9rem; color: #444;">
                    {$message}
                </div>
            </div>
        HTML;
    }

    private static function printJSON(array $results): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $output = [];
        foreach ($results as $result) {
            $output[] = [
                'website'     => $result->getWebsiteUrl(),
                'status_code' => $result->getStatusCode(),
                'timestamp'   => $result->getTimestamp(),
                'has_error'   => $result->hasError(),
                'error_msg'   => $result->getErrorMessage() ?: null,
            ];
        }
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
