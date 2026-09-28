<?php

namespace Tests\Unit;

use App\Services\UploadService;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Tests\TestCase;

class UploadServiceTest extends TestCase
{
    public function test_ini_sizes_convert_to_bytes(): void
    {
        $this->assertSame(2 * 1024 * 1024, UploadService::iniToBytes('2M'));
        $this->assertSame(512 * 1024, UploadService::iniToBytes('512K'));
        $this->assertSame(0, UploadService::iniToBytes('-1'));
    }

    public function test_max_label_follows_the_smaller_server_limit(): void
    {
        $expected = min(
            UploadService::iniToBytes((string) ini_get('upload_max_filesize')) ?: PHP_INT_MAX,
            UploadService::iniToBytes((string) ini_get('post_max_size')) ?: PHP_INT_MAX,
        );

        $this->assertSame($expected, UploadService::maxBytes());
        $this->assertNotSame('', UploadService::maxLabel());
    }

    public function test_php_rejected_upload_is_not_saved_as_an_empty_file(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'up');
        file_put_contents($path, 'x');
        $file = new UploadedFile($path, 'photo.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail melebihi had pelayan ('.UploadService::maxLabel().')');

        app(UploadService::class)->save($file, 'applications');
    }

    public function test_file_larger_than_server_limit_is_rejected(): void
    {
        $overKb = (int) floor(UploadService::maxBytes() / 1024) + 1;
        $file = UploadedFile::fake()->create('big.jpg', $overKb, 'image/jpeg');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail melebihi had pelayan');

        app(UploadService::class)->save($file, 'applications');
    }
}
