<?php

/**
 * Credits: https://gist.github.com/xeebeast/4882f86da91272e7c5fc65dd1c2f3a5f / https://medium.com/@xeebeast/adding-google-cloud-storage-in-lumen-2acc519e6b4b
 * Author: xeebeast / Zeeshan Tariq
 * 
 * Note from Conor: Added this service provider for use with google cloud storage for images. This code does not belong to me.
 * 
 */


namespace App\Providers;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

class GoogleCloudStorageServiceProvider extends ServiceProvider
{
	/**
	 * Perform post-registration booting of services.
	 */
	public function boot()
	{
		$factory = $this->app->make( 'filesystem' );
		/* @var FilesystemManager $factory */
		$factory->extend( 'gcs', function ( $app, $config ) {
			$storageClient = new StorageClient( [
				'projectId'   => $config['project_id'],
				'keyFilePath' => array_get( $config, 'key_file' ),
			] );
			$bucket        = $storageClient->bucket( $config['bucket'] );
			$pathPrefix    = array_get( $config, 'path_prefix' );
			$storageApiUri = array_get( $config, 'storage_api_uri' );
			$adapter = new GoogleStorageAdapter( $storageClient, $bucket, $pathPrefix, $storageApiUri );
			return new Filesystem( $adapter );
		} );
	}
	/**
	 * Register bindings in the container.
	 */
	public function register()
	{
		//
	}
}