<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Cache;

use CodeIgniter\Cache\FactoriesCache\FileVarExportHandler;
use PHPUnit\Framework\Attributes\Group;
use ReflectionProperty;

/**
 * @internal
 */
#[Group('Others')]
final class FactoriesCacheFileVarExportHandlerTest extends AbstractFactoriesCacheHandlerTestCase
{
    protected function createFactoriesCache(): void
    {
        $this->handler = new FileVarExportHandler();
        $this->cache   = new FactoriesCache($this->handler);
    }

    public function testSaveCreatesDirectoryWithCorrectPermissions(): void
    {
        $dir = WRITEPATH . 'cache_test_dir_' . uniqid('', true);

        try {
            $handler = new FileVarExportHandler();
            $ref     = new ReflectionProperty(FileVarExportHandler::class, 'path');
            $ref->setValue($handler, $dir);

            $handler->save('test_key', ['data']);

            $this->assertDirectoryExists($dir);

            if (! is_windows()) {
                $perms    = fileperms($dir) & 0777;
                $expected = 0755 & ~umask();
                $this->assertSame($expected, $perms);
            }
        } finally {
            if (is_dir($dir)) {
                $files = glob("{$dir}/*");

                if ($files !== false) {
                    array_map(unlink(...), $files);
                }

                rmdir($dir);
            }
        }
    }
}
