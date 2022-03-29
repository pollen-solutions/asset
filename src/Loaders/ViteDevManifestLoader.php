<?php

declare(strict_types=1);

namespace Pollen\Asset\Loaders;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Pollen\Asset\Types\TagJsType;
use RuntimeException;

class ViteDevManifestLoader extends ManifestLoader
{
    /**
     * @return void
     */
    public function preload(): void
    {
        if (file_exists($this->filename)) {
            $manifestContent = file_get_contents($this->filename);

            try {
                $entries = json_decode($manifestContent, true, 512, JSON_THROW_ON_ERROR);

                if (!empty($entries['url']['network'])) {
                    $baseUrl = current($entries['url']['network']);
                } else {
                    $baseUrl = $entries['url']['local'];
                }

                try {
                    $response = (new HttpClient())->get("$baseUrl/@vite/client", [
                        'timeout' => 0.1, 'verify' => false
                    ]);

                    if ($response->getStatusCode() === 200) {
                        $this->preloaded[] = new TagJsType("$baseUrl/@vite/client", ['type' => 'module', 'defer']);
                        $this->preloaded[] = new TagJsType("$baseUrl/app.js", ['type' => 'module', 'defer']);
                    }
                } catch (GuzzleException $e) {
                    unset($e);
                }
            } catch (JsonException $e) {
                throw new RuntimeException(sprintf('Unable to decode manifest file : [%s].', $e->getMessage()));
            }
        }
    }
}
