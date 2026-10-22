<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

use PHPUnit\Framework\TestCase;
use Secretary\Helpers\Uploads;

/**
 * Uploads::protectRoot() keeps the deny .htaccess in the uploads root, which
 * lives inside the web root. Without it invoices and e-mailed PDFs are served
 * to anyone who knows a file name.
 */
final class UploadsProtectRootTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/secretary-uploads-' . bin2hex(random_bytes(6));
        mkdir($this->root);
    }

    protected function tearDown(): void
    {
        foreach (array_diff(scandir($this->root), ['.', '..']) as $entry)
        {
            unlink($this->root . '/' . $entry);
        }
        rmdir($this->root);
    }

    public function testCreatesTheDenyFileWhenMissing()
    {
        $this->assertTrue(Uploads::protectRoot($this->root));
        $this->assertSame(Uploads::DENY_HTACCESS, file_get_contents($this->root . '/.htaccess'));
    }

    public function testDenyFileClosesTheFolderOnApache22And24()
    {
        $this->assertStringContainsString('Require all denied', Uploads::DENY_HTACCESS);
        $this->assertStringContainsString('Deny from all', Uploads::DENY_HTACCESS);
        $this->assertStringContainsString('Options -Indexes', Uploads::DENY_HTACCESS);
    }

    public function testShipsTheSameRulesTheInstallerCopies()
    {
        $this->assertSame(Uploads::DENY_HTACCESS, file_get_contents(__DIR__ . '/../../com_secretary/admin/htaccess.txt'));
    }

    public function testLeavesAnExistingFileUntouched()
    {
        file_put_contents($this->root . '/.htaccess', "deny from all\n# local\n");

        $this->assertTrue(Uploads::protectRoot($this->root));
        $this->assertSame("deny from all\n# local\n", file_get_contents($this->root . '/.htaccess'));
    }

    public function testNeverWritesThroughASymlink()
    {
        $target = $this->root . '/elsewhere';
        symlink($target, $this->root . '/.htaccess');

        $this->assertFalse(Uploads::protectRoot($this->root));
        $this->assertFileDoesNotExist($target);
        unlink($this->root . '/.htaccess');
    }
}
