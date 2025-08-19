<?php

declare(strict_types=1);

namespace Spiral\McpServer;

use PhpMcp\Server\Context;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * TODO: Remove this class when RegisteredTool is used Tool handler.
 * @see https://github.com/php-mcp/server/issues/62
 */
final class RegisteredTool extends \PhpMcp\Server\Elements\RegisteredTool
{
    public function handle(ContainerInterface $container, array $arguments, Context $context): mixed
    {
        return \call_user_func($this->handler, $arguments);
    }
}
