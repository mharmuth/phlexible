<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Tree extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleTreeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');
        $loader->load('mediators.yml');
        $loader->load('content.yml');
        $loader->load('configurators.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_tree.patterns', $config['patterns']);

        $container->setAlias('phlexible_tree.tree_factory', 'phlexible_tree.doctrine.tree_factory');
        $container->setAlias('phlexible_tree.state_manager', 'phlexible_tree.doctrine.state_manager');

        $container->setAlias('phlexible_tree.content_tree_manager', 'phlexible_tree.content_tree_manager.delegating');
        $container->setAlias('phlexible_tree.router.url_generator', $config['router']['url_generator_service']);
        $container->setAlias('phlexible_tree.router.request_matcher', $config['router']['request_matcher_service']);
        $container->setAlias('phlexible_tree.element_mediator.viewable_voter', $config['mediator']['element_viewable_voter']);
        $container->setAlias('phlexible_tree.element_mediator.sluggable_voter', $config['mediator']['element_sluggable_voter']);
        $container->setAlias('phlexible_tree.node_url_generator', 'phlexible_tree.node_url_generator.language');
    }
}
