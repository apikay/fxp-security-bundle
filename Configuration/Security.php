<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\SecurityBundle\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * The Security class handles the Security annotation.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @Annotation
 */
class Security extends ConfigurationAnnotation
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * If set, will throw Symfony\Component\HttpKernel\Exception\HttpException
     * with the given $statusCode.
     * If null, Symfony\Component\Security\Core\Exception\AccessDeniedException.
     * will be used.
     *
     * @var null|int
     */
    protected $statusCode;

    /**
     * The message of the exception.
     *
     * @var null|string
     */
    protected $message;

    /**
     * @var bool
     */
    protected $override = false;

    /**
     * Get the expression.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * Set the expression.
     *
     * @param string $expression The expression
     */
    public function setExpression(string $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * Get the status code.
     *
     * @return null|int
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Set the status code.
     *
     * @param null|int $statusCode The status code
     */
    public function setStatusCode(?int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Get the message.
     *
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set the message.
     *
     * @param null|string $message The message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * Set the expression.
     *
     * @param string $expression The expression
     */
    public function setValue(string $expression): void
    {
        $this->setExpression($expression);
    }

    /**
     * Define if the annotation override all previous security annotation.
     *
     * @param bool $override
     */
    public function setOverride(bool $override): void
    {
        $this->override = $override;
    }

    /**
     * Check if the annotation override all previous security annotation.
     *
     * @return bool
     */
    public function isOverriding(): bool
    {
        return $this->override;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasName(): string
    {
        return 'fxp_security';
    }

    /**
     * {@inheritdoc}
     */
    public function allowArray(): bool
    {
        return true;
    }
}
