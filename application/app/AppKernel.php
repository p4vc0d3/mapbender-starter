<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Mapbender3 kernel
 */
class AppKernel extends Mapbender\BaseKernel
{
    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     */
    public function registerBundles()
    {
        $bundles = array(
            // Standard Symfony2 bundles
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),

            // FoM bundles
            new FOM\CoreBundle\FOMCoreBundle(),
            new FOM\ManagerBundle\FOMManagerBundle(),
            new FOM\UserBundle\FOMUserBundle(),

            // Optional Mapbender bundles
            new Mapbender\WmcBundle\MapbenderWmcBundle(),
            new Mapbender\WmsBundle\MapbenderWmsBundle(),
            new Mapbender\WmtsBundle\MapbenderWmtsBundle(),
            new Mapbender\ManagerBundle\MapbenderManagerBundle(),
            new Mapbender\PrintBundle\MapbenderPrintBundle(),
            new Mapbender\MobileBundle\MapbenderMobileBundle(),

            // Extra bundles installed by default starter
            new \Mapbender\CoordinatesUtilityBundle\MapbenderCoordinatesUtilityBundle(),
            new \Mapbender\DataSourceBundle\MapbenderDataSourceBundle(),
            new \Mapbender\DataManagerBundle\MapbenderDataManagerBundle(),
            new \Mapbender\DigitizerBundle\MapbenderDigitizerBundle(),

            // OWSProxy3 bundles
            new OwsProxy3\CoreBundle\OwsProxy3CoreBundle(),
        );

        // Symfony 3 only. Only required by starter.
        if (class_exists('\Symfony\Bundle\WebServerBundle\WebServerBundle')) {
            $bundles[] = new \Symfony\Bundle\WebServerBundle\WebServerBundle();
        }

        // prepend bundles required by Mapbender (including MapbenderCoreBundle)
        $bundles = array_merge(parent::registerBundles(), $bundles);

        // If you get "Uncaught LogicException: Trying to register two bundles with the same name"
        // uncomment the next line for a quick fix
        // $bundles = BaseKernel::filterUniqueBundles($bundles)

        return $bundles;
    }

    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $streamEnv = \getenv('MB_LOG_STREAM');
        if ($streamEnv && $streamEnv !== 'off' && $streamEnv !== 'false') {
            if ($streamEnv !== 'stdout' && $streamEnv !== 'stderr') {
                $streamEnv = 'stdout';
            }
            $container->setParameter('log_path', "php://{$streamEnv}");
        }
        return $container;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getProjectDir()
    {
        return dirname(dirname(__FILE__));
    }

    public function getCacheDir()
    {
        $cacheRoot = \getenv('MB_CACHE_DIR') ?: $this->getProjectDir() . '/app/cache';
        return \rtrim($cacheRoot, '/\\') . '/' .  $this->getEnvironment();
    }

    public function getLogDir()
    {
        return \rtrim(\getenv('MB_LOG_DIR') ?: $this->getProjectDir() . '/app/logs', '/\\');
    }
}
