<?php

declare(strict_types=1);

namespace Pollen\Asset\Loaders;

use JsonException;
use Pollen\Support\Filesystem as fs;
use RuntimeException;
use SplFileInfo;

class ViteManifestLoader extends ManifestLoader
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
                $baseDir = $this->getBaseDir();

                foreach ($entries as $entry) {
                    if ($file = $entry['file'] ?? null) {
                        $type = (new LocalFileTypeDispatcher(
                            new SplFileInfo(fs::normalizePath($baseDir . fs::DS . $file)),
                            $this->basePath,
                            $baseDir,
                            $this->baseUrl
                        ))->dispatch();

                        if ($type) {
                            $this->preloaded[] = $type;
                        }
                    }

                    if (($files = $entry['css'] ?? []) && is_array($files)) {
                        foreach ($files as $file) {
                            $type = (new LocalFileTypeDispatcher(
                                new SplFileInfo(fs::normalizePath($baseDir . fs::DS . $file)),
                                $this->basePath,
                                $baseDir,
                                $this->baseUrl
                            ))->dispatch();

                            if ($type) {
                                $this->preloaded[] = $type;
                            }
                        }
                    }
                }
            } catch (JsonException $e) {
                throw new RuntimeException(sprintf('Unable to decode manifest file : [%s].', $e->getMessage()));
            }
        }
    }
}
