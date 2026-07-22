<?php

namespace Tests\Unit;

use App\Support\SystemVersion;
use Tests\TestCase;

class SystemVersionTest extends TestCase
{
    private string $versionStatePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->versionStatePath = storage_path('framework/testing/system-version-'.uniqid().'.json');

        config(['app.version_state_path' => $this->versionStatePath]);
    }

    protected function tearDown(): void
    {
        if (is_file($this->versionStatePath)) {
            unlink($this->versionStatePath);
        }

        parent::tearDown();
    }

    public function test_version_uses_configured_build_when_present(): void
    {
        config([
            'app.version' => 'v2.10',
            'app.version_auto_increment' => true,
            'app.version_build' => 'build-45',
        ]);

        $this->assertSame('v2.10.45', SystemVersion::current());
    }

    public function test_version_uses_base_version_when_auto_increment_is_disabled(): void
    {
        config([
            'app.version' => 'v2.10',
            'app.version_auto_increment' => false,
            'app.version_build' => null,
        ]);

        $this->assertSame('v2.10.00', SystemVersion::current());
    }

    public function test_version_auto_increments_from_latest_source_change(): void
    {
        config([
            'app.version' => 'v2.10',
            'app.version_auto_increment' => true,
            'app.version_build' => null,
        ]);

        $this->assertMatchesRegularExpression('/^v2\.10\.\d{2}$/', SystemVersion::current());
    }

    public function test_version_keeps_configured_patch_when_auto_increment_is_disabled(): void
    {
        config([
            'app.version' => 'v2.10.07',
            'app.version_auto_increment' => false,
            'app.version_build' => null,
        ]);

        $this->assertSame('v2.10.07', SystemVersion::current());
    }
}
