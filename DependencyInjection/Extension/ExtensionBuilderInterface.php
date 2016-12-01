<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\SecurityBundle\DependencyInjection\Extension;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
interface ExtensionBuilderInterface
{
    /**
     * Build the config.
     *
     * @param ContainerBuilder $container The container
     * @param LoaderInterface  $loader    The config loader
     * @param array            $config    The config
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config);
}
