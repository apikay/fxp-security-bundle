<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\SecurityBundle\DependencyInjection\Extension;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RoleHierarchyBuilder implements ExtensionBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        if ($config['role_hierarchy']['enabled']) {
            BuilderUtils::validate($container, 'role_hierarchy', 'doctrine', 'doctrine/doctrine-bundle');
            $loader->load('role_hierarchy.xml');

            // role hierarchy cache
            if (null !== ($cacheId = $config['role_hierarchy']['cache'])) {
                $cacheAlias = new Alias($cacheId, false);
                $container->setAlias('fxp_security.role_hierarchy.cache', $cacheAlias);
            }

            // doctrine orm role hierarchy listener
            if ($config['doctrine']['orm']['listeners']['role_hierarchy']) {
                BuilderUtils::validate($container, 'doctrine.orm.listeners.role_hierarchy', 'doctrine.orm.entity_manager', 'doctrine/orm');
                $loader->load('orm_listener_role_hierarchy.xml');
            }
        }
    }
}
