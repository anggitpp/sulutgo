<?php
require "vendor/autoload.php";

use Dotenv\Dotenv;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class Application extends Container
{

	protected static $aliasesRegistered = false;

	protected $basePath;

	protected $loadedConfigurations = [];

	protected $booted = false;

	protected $loadedProviders = [];

	protected $ranServiceBinders = [];

	public function __construct($basePath = null)
	{
		$this->basePath = $basePath;

		$this->bootstrapContainer();
	}

	protected function bootstrapContainer()
	{
		static::setInstance($this);

		$this->instance('app', $this);
		$this->instance(self::class, $this);

		$this->instance('path', $this->path());

		$this->instance('env', $this->environment());

		$this->registerContainerAliases();
	}

	public function environment()
	{
		$env = env('APP_ENV', 'production');

		if (func_num_args() > 0) {
			$patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

			foreach ($patterns as $pattern) {
				if (Str::is($pattern, $env)) {
					return true;
				}
			}

			return false;
		}

		return $env;
	}

	public function register($provider)
	{
		if (! $provider instanceof ServiceProvider) {
			$provider = new $provider($this);
		}

		if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
			return;
		}

		$this->loadedProviders[$providerName] = $provider;

		if (method_exists($provider, 'register')) {
			$provider->register();
		}

		if ($this->booted) {
			$this->bootProvider($provider);
		}
	}

	public function boot()
	{
		if ($this->booted) {
			return;
		}

		foreach ($this->loadedProviders as $provider) {
			$this->bootProvider($provider);
		}

		$this->booted = true;
	}

	protected function bootProvider(ServiceProvider $provider)
	{
		if (method_exists($provider, 'boot')) {
			return $this->call([$provider, 'boot']);
		}
	}

	public function make($abstract, array $parameters = [])
	{
		$abstract = $this->getAlias($abstract);

		if (! $this->bound($abstract) &&
			array_key_exists($abstract, $this->availableBindings) &&
			! array_key_exists($this->availableBindings[$abstract], $this->ranServiceBinders)) {
			$this->{$method = $this->availableBindings[$abstract]}();

			$this->ranServiceBinders[$method] = true;
		}

		return parent::make($abstract, $parameters);
	}

	protected function registerConfigBindings()
	{
		$this->singleton('config', function () {
			return new ConfigRepository;
		});
	}

	protected function registerDatabaseBindings()
	{
		$this->singleton('db', function () {

			$capsule = new Illuminate\Database\Capsule\Manager;

			$capsule->addConnection([
				"driver" => env('DB_CONNECTION'),
				"host" => env('DB_HOST') . ":" . env('DB_PORT'),
				"database" => env('DB_DATABASE'),
				"username" => env('DB_USERNAME'),
				"password" => env('DB_PASSWORD'),
				'charset' => 'utf8mb4',
				'collation' => 'utf8mb4_unicode_ci',
				'prefix' => ''
			]);

			$capsule->setAsGlobal();
			$capsule->bootEloquent();

			return $capsule->getConnection();
		});
	}

	protected function registerEventBindings()
	{
		$this->singleton('events', function () {
			$this->register(EventServiceProvider::class);

			return $this->make('events');
		});
	}

    protected function registerCacheBindings()
    {
        $this->singleton('cache', function () {
            return $this->loadComponent('cache', CacheServiceProvider::class);
        });
        $this->singleton('cache.store', function () {
            return $this->loadComponent('cache', CacheServiceProvider::class, 'cache.store');
        });
    }

	public function loadComponent($config, $providers, $return = null)
	{
		$this->configure($config);

		foreach ((array) $providers as $provider) {
			$this->register($provider);
		}

		return $this->make($return ?: $config);
	}

	public function configure($name)
	{
		if (isset($this->loadedConfigurations[$name])) {
			return;
		}

		$this->loadedConfigurations[$name] = true;

		$path = $this->getConfigurationPath($name);

		if ($path) {
			$this->make('config')->set($name, require $path);
		}
	}

	public function getConfigurationPath($name = null)
	{

		$appConfigPath = $this->basePath('config').'/'.$name.'.php';

		if (file_exists($appConfigPath)) {
			return $appConfigPath;
		} elseif (file_exists($path = __DIR__ . '/../config/' .$name.'.php')) {
			return $path;
		}

	}

	public function withFacades($aliases = true, $userAliases = [])
	{
		Facade::setFacadeApplication($this);

		if ($aliases) {
			$this->withAliases($userAliases);
		}
	}

	public function withAliases($userAliases = [])
	{
		$defaults = [
			\Illuminate\Support\Facades\DB::class => 'DB',
			\Illuminate\Support\Facades\Schema::class => 'Schema',
		];

		if (! static::$aliasesRegistered) {
			static::$aliasesRegistered = true;

			$merged = array_merge($defaults, $userAliases);

			foreach ($merged as $original => $alias) {
				class_alias($original, $alias);
			}
		}
	}

	public function withEloquent()
	{
		$this->make('db');
	}

	public function path()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'app';
	}

	public function basePath($path = null)
	{
		if (isset($this->basePath)) {
			return $this->basePath.($path ? '/'.$path : $path);
		}

		if ($this->runningInConsole()) {
			$this->basePath = getcwd();
		} else {
			$this->basePath = realpath(getcwd().'/../');
		}

		return $this->basePath($path);
	}

	public function databasePath($path = '')
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'database'.($path ? DIRECTORY_SEPARATOR.$path : $path);
	}

	public function eventsAreCached()
	{
		return false;
	}

	public function runningInConsole()
	{
		return \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';
	}

	public function prepareForConsoleCommand($aliases = true)
	{
		$this->withFacades($aliases);

		$this->make('cache');
		$this->make('queue');

		$this->configure('database');
	}

	public function flush()
	{
		parent::flush();

		$this->loadedProviders = [];
		$this->reboundCallbacks = [];
		$this->resolvingCallbacks = [];
		$this->ranServiceBinders = [];
		$this->loadedConfigurations = [];
		$this->afterResolvingCallbacks = [];

		$this->dispatcher = null;
		static::$instance = null;
	}

	protected function registerContainerAliases()
	{
		$this->aliases = [
			\Illuminate\Contracts\Foundation\Application::class => 'app',
			\Illuminate\Contracts\Config\Repository::class => 'config',
			\Illuminate\Container\Container::class => 'app',
			\Illuminate\Contracts\Container\Container::class => 'app',
			\Illuminate\Database\ConnectionResolverInterface::class => 'db',
			\Illuminate\Database\DatabaseManager::class => 'db',
			\Illuminate\Contracts\Events\Dispatcher::class => 'events',
            \Illuminate\Contracts\Cache\Factory::class => 'cache',
            \Illuminate\Contracts\Cache\Repository::class => 'cache.store',
		];
	}

	public $availableBindings = [
		'config' => 'registerConfigBindings',
		'db' => 'registerDatabaseBindings',
		\Illuminate\Database\Eloquent\Factories\Factory::class => 'registerDatabaseBindings',
		'events' => 'registerEventBindings',
        'cache' => 'registerCacheBindings',
        'cache.store' => 'registerCacheBindings',
        \Illuminate\Contracts\Cache\Factory::class => 'registerCacheBindings',
        \Illuminate\Contracts\Cache\Repository::class => 'registerCacheBindings',
	];

}


Dotenv::create(Env::getRepository(), './', '.env')->safeLoad();

$app = new Application(dirname(__FILE__));

$app->withFacades();
$app->withEloquent();

$app->singleton(
	Illuminate\Contracts\Console\Kernel::class,
	App\Console\Kernel::class
);

$app->configure('app');
$app->configure('export');

$app->register(AppServiceProvider::class);

return $app;