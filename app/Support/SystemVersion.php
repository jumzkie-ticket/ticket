<?php

namespace App\Support;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use SplFileInfo;

class SystemVersion
{
    public static function current(): string
    {
        $version = self::versionParts();
        $patchNumber = self::patchNumber($version['prefix'], $version['patch']);

        return "{$version['prefix']}.".self::formatPatch($patchNumber);
    }

    public static function markChanged(): string
    {
        $version = self::versionParts();

        if (self::hasConfiguredBuild() || ! self::autoIncrementEnabled()) {
            return self::current();
        }

        $state = self::readState();
        $stateBuild = (($state['version'] ?? null) === $version['prefix'])
            ? self::normalizePatch((int) ($state['build'] ?? $version['patch']))
            : max(1, $version['patch']);
        $nextBuild = self::normalizePatch($stateBuild + 1);

        self::writeState([
            'version' => $version['prefix'],
            'signature' => self::sourceSignature(),
            'build' => $nextBuild,
        ]);

        return "{$version['prefix']}.".self::formatPatch($nextBuild);
    }

    /**
     * @return array{prefix: string, patch: int}
     */
    private static function versionParts(): array
    {
        $version = trim((string) config('app.version', 'v1.01'));

        if (! preg_match('/^v?(\d+)(?:\.(\d+))?(?:\.(\d+))?/i', $version, $matches)) {
            return ['prefix' => 'v1.01', 'patch' => 0];
        }

        $major = (string) ((int) $matches[1]);
        $minor = self::formatPatch((int) ($matches[2] ?? 0));
        $patch = self::normalizePatch((int) ($matches[3] ?? 0));

        return [
            'prefix' => "v{$major}.{$minor}",
            'patch' => $patch,
        ];
    }

    private static function patchNumber(string $versionPrefix, int $configuredPatch): int
    {
        if (self::hasConfiguredBuild()) {
            return self::normalizePatchFromString((string) config('app.version_build', ''));
        }

        if (! self::autoIncrementEnabled()) {
            return $configuredPatch;
        }

        $signature = self::sourceSignature();
        $state = self::readState();
        $stateBuild = self::normalizePatch((int) ($state['build'] ?? $configuredPatch));
        $nextBuild = max(1, $configuredPatch);

        if (($state['version'] ?? null) === $versionPrefix) {
            $nextBuild = $stateBuild;

            if (($state['signature'] ?? null) !== $signature) {
                $nextBuild = self::normalizePatch($stateBuild + 1);
            }
        }

        self::writeState([
            'version' => $versionPrefix,
            'signature' => $signature,
            'build' => $nextBuild,
        ]);

        return $nextBuild;
    }

    private static function hasConfiguredBuild(): bool
    {
        return trim((string) config('app.version_build', '')) !== '';
    }

    private static function autoIncrementEnabled(): bool
    {
        return filter_var(config('app.version_auto_increment', true), FILTER_VALIDATE_BOOL);
    }

    private static function normalizePatchFromString(string $value): int
    {
        if (! preg_match('/\d+/', $value, $matches)) {
            return 0;
        }

        return self::normalizePatch((int) $matches[0]);
    }

    private static function normalizePatch(int $value): int
    {
        return max(0, min(99, $value));
    }

    private static function formatPatch(int $value): string
    {
        return str_pad((string) self::normalizePatch($value), 2, '0', STR_PAD_LEFT);
    }

    private static function sourceSignature(): string
    {
        $entries = [];

        foreach (self::sourceDirectories() as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                );

                /** @var SplFileInfo $file */
                foreach ($iterator as $file) {
                    if (! $file->isFile()) {
                        continue;
                    }

                    $entries[] = self::sourceEntry($file->getPathname(), $file->getMTime(), $file->getSize());
                }
            } catch (Throwable) {
                continue;
            }
        }

        foreach (self::sourceFiles() as $file) {
            if (is_file($file)) {
                $entries[] = self::sourceEntry($file, filemtime($file) ?: 0, filesize($file) ?: 0);
            }
        }

        sort($entries);

        return sha1(implode("\n", $entries));
    }

    private static function sourceEntry(string $path, int $modifiedAt, int $size): string
    {
        return str_replace(base_path(), '', $path).'|'.$modifiedAt.'|'.$size;
    }

    /**
     * @return array{version?: string, signature?: string, build?: int}
     */
    private static function readState(): array
    {
        $path = self::statePath();

        if (! is_file($path)) {
            return [];
        }

        $state = json_decode((string) file_get_contents($path), true);

        return is_array($state) ? $state : [];
    }

    /**
     * @param array{version: string, signature: string, build: int} $state
     */
    private static function writeState(array $state): void
    {
        $path = self::statePath();
        $directory = dirname($path);

        try {
            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            file_put_contents($path, json_encode($state, JSON_PRETTY_PRINT));
        } catch (Throwable) {
            //
        }
    }

    private static function statePath(): string
    {
        return (string) config('app.version_state_path', storage_path('app/system-version.json'));
    }

    /**
     * @return list<string>
     */
    private static function sourceDirectories(): array
    {
        return [
            app_path(),
            base_path('config'),
            base_path('database/migrations'),
            base_path('database/seeders'),
            resource_path('css'),
            resource_path('js'),
            resource_path('views'),
            base_path('routes'),
        ];
    }

    /**
     * @return list<string>
     */
    private static function sourceFiles(): array
    {
        return [
            base_path('composer.json'),
            base_path('composer.lock'),
            base_path('package.json'),
            base_path('package-lock.json'),
            base_path('vite.config.js'),
        ];
    }
}
