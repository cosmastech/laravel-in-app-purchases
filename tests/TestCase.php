<?php

declare(strict_types=1);

namespace Tests;

use Faker\Factory;
use Imdhemy\Purchases\ServiceProviders\LiapServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\Doubles\LiapTestProvider;

/**
 * Test Case
 * All test cases that requires a laravel app instance should extend this class.
 */
class TestCase extends Orchestra
{
    protected Faker $faker;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = new Faker(Factory::create());
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            LiapServiceProvider::class,
            LiapTestProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Get the path to assets dir.
     */
    protected function assetPath(?string $path = null): string
    {
        $assetsPath = __DIR__.'/assets';

        if ($path) {
            return $assetsPath.'/'.$path;
        }

        return $assetsPath;
    }

    /**
     * Generates a fake p8 key.
     */
    protected function generateP8Key(): string
    {
        $key = 'MHQCAQEEIPKsJBiuilVdbtkxtPpSp0LLlUeqCmwx6Ss2OBvIhTbioAcGBSuBBAAK
oUQDQgAEacH/sdtom9kl/0AvHFNNuoxnUWzLwWXf70qH2O1FDrvjDXY2aC7NFg9t
WtcP+PnScROkjnSv6H6A6ekLVAzQYg==';

        $filename = 'privateKey-'.time().'.p8';
        $path = $this->assetPath($filename);

        if (! file_exists($path)) {
            $contents = "-----BEGIN EC PRIVATE KEY-----\n".$key."\n-----END EC PRIVATE KEY-----";
            file_put_contents($path, $contents);
        }

        return $path;
    }

    /**
     * Deletes the given file is exists.
     */
    protected function deleteFile(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
