<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Access control extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleAccessControlExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter(
            'phlexible_access_control.permissive_on_empty_acl',
            $config['permissive_on_empty_acl']
        );

        $loader->load('doctrine.yml');
        $container->setAlias(
            'phlexible_access_control.access_manager',
            'phlexible_access_control.doctrine.access_manager'
        );

        $container->setParameter('phlexible_access_control.backend_type_orm', true);
    }
}
