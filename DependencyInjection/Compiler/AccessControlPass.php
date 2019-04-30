<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AccessControlPass implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private static $availableExpressionNames = [
        'token', 'user', 'object', 'roles', 'request', 'trust_resolver',
    ];

    /**
     * @var Reference[]
     */
    private $requestMatchers = [];

    /**
     * @var Reference[]
     */
    private $expressions = [];

    /**
     * @var null|ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('fxp_security.access_control')) {
            return;
        }

        $accesses = $container->getParameter('fxp_security.access_control');
        $this->createAuthorization($container, $accesses);
        $container->getParameterBag()->remove('fxp_security.access_control');
    }

    /**
     * Create the authorization.
     *
     * @param ContainerBuilder $container The container
     * @param array            $accesses  The control accesses
     */
    private function createAuthorization(ContainerBuilder $container, array $accesses): void
    {
        foreach ($accesses as $access) {
            $matcher = $this->createRequestMatcher(
                $container,
                $access['path'],
                $access['host'],
                $access['methods'],
                $access['ips']
            );

            $attributes = $access['roles'];

            if ($access['allow_if']) {
                $attributes[] = $this->createExpression($container, $access['allow_if']);
            }

            $container->getDefinition('security.access_map')
                ->addMethodCall('add', [$matcher, $attributes, $access['requires_channel']])
            ;
        }
    }

    /**
     * Create the request matcher.
     *
     * @param ContainerBuilder $container  The container
     * @param null|string      $path       Tha path
     * @param null|string      $host       The host
     * @param array            $methods    The request methods
     * @param null|string      $ip         The client ip
     * @param array            $attributes The attributes
     *
     * @return Reference
     */
    private function createRequestMatcher(
        ContainerBuilder $container,
        $path = null,
        $host = null,
        $methods = [],
        $ip = null,
        array $attributes = []
    ) {
        if (!empty($methods)) {
            $methods = array_map('strtoupper', (array) $methods);
        }

        $serialized = serialize([$path, $host, $methods, $ip, $attributes]);
        $id = 'security.request_matcher.'.md5($serialized).sha1($serialized);

        if (isset($this->requestMatchers[$id])) {
            return $this->requestMatchers[$id];
        }

        // only add arguments that are necessary
        $arguments = [$path, $host, $methods, $ip, $attributes];
        while (\count($arguments) > 0 && !end($arguments)) {
            array_pop($arguments);
        }

        $container
            ->register($id, 'Symfony\Component\HttpFoundation\RequestMatcher')
            ->setPublic(false)
            ->setArguments($arguments)
        ;

        return $this->requestMatchers[$id] = new Reference($id);
    }

    /**
     * Create the expression.
     *
     * @param ContainerBuilder $container  The container
     * @param string           $expression The expression
     *
     * @return Reference
     */
    private function createExpression(ContainerBuilder $container, $expression)
    {
        if (isset($this->expressions[$id = 'security.expression.'.sha1($expression)])) {
            return $this->expressions[$id];
        }

        $container
            ->register($id, SerializedParsedExpression::class)
            ->setPublic(false)
            ->addArgument($expression)
            ->addArgument(serialize(
                $this->getExpressionLanguage($container)->parse(
                $expression,
                self::$availableExpressionNames
            )->getNodes()
            ))
        ;

        return $this->expressions[$id] = new Reference($id);
    }

    /**
     * Get the expression language.
     *
     * @param ContainerBuilder $container The container
     *
     * @return ExpressionLanguage
     */
    private function getExpressionLanguage(ContainerBuilder $container)
    {
        if (null === $this->expressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage(
                null,
                $this->getExpressionFunctions($container)
            );
        }

        return $this->expressionLanguage;
    }

    /**
     * Get the expression function providers of expression language.
     *
     * @param ContainerBuilder $container The container
     *
     * @return ExpressionFunctionProviderInterface[]
     */
    private function getExpressionFunctions(ContainerBuilder $container)
    {
        $providers = [];
        $services = $container->findTaggedServiceIds('security.expression_language_provider');

        foreach ($services as $id => $attributes) {
            $def = $container->getDefinition($id);
            $ref = new \ReflectionClass($def->getClass());
            $providers[] = $ref->newInstance();
        }

        return $providers;
    }
}
