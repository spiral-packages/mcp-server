<?php

declare(strict_types=1);

namespace Spiral\McpServer\Bootloader;

use PhpMcp\Schema\Implementation;
use PhpMcp\Schema\ServerCapabilities;
use PhpMcp\Server\Configuration;
use PhpMcp\Server\Contracts\ServerTransportInterface;
use PhpMcp\Server\Contracts\SessionHandlerInterface;
use PhpMcp\Server\Dispatcher;
use PhpMcp\Server\Protocol;
use PhpMcp\Server\Server;
use PhpMcp\Server\ServerBuilder;
use PhpMcp\Server\Session\ArraySessionHandler;
use PhpMcp\Server\Session\SessionManager;
use PhpMcp\Server\Session\SubscriptionManager;
use PhpMcp\Server\Transports\HttpServerTransport;
use PhpMcp\Server\Transports\StdioServerTransport;
use PhpMcp\Server\Transports\StreamableHttpServerTransport;
use PhpMcp\Server\Utils\DocBlockParser;
use PhpMcp\Server\Utils\SchemaGenerator;
use Psr\Container\ContainerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Console\Bootloader\ConsoleBootloader;
use Spiral\Core\FactoryInterface;
use Spiral\Logger\LogsInterface;
use Spiral\McpServer\Discovery\ToolsLocator;
use Spiral\McpServer\Entrypoint\McpCommand;
use Spiral\McpServer\Entrypoint\McpDispatcher;
use Spiral\McpServer\Internal\Registry;
use Spiral\McpServer\Internal\SchemaValidator;
use Spiral\McpServer\MiddlewareManager;
use Spiral\McpServer\MiddlewareRegistryInterface;
use Spiral\McpServer\MiddlewareRepositoryInterface;
use Spiral\Tokenizer\TokenizerListenerRegistryInterface;

final class McpServerBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            ValinorMapperBootloader::class,
        ];
    }

    public function defineSingletons(): array
    {
        return [
            MiddlewareRepositoryInterface::class => MiddlewareManager::class,
            MiddlewareRegistryInterface::class => MiddlewareManager::class,

            LoopInterface::class => static fn(): LoopInterface => Loop::get(),
            Configuration::class => static fn(
                EnvironmentInterface $env,
                LogsInterface $logs,
                LoopInterface $loop,
                ContainerInterface $container,
            ): Configuration => new Configuration(
                serverInfo: Implementation::make(
                    name: \trim((string) $env->get('MCP_SERVER_NAME', 'MCP Server')),
                    version: \trim((string) $env->get('MCP_SERVER_VERSION', '1.0.0')),
                ),
                capabilities: ServerCapabilities::make(),
                logger: $logs->getLogger('mcp'),
                loop: $loop,
                cache: null,
                container: $container,
                paginationLimit: 50,
                instructions: null,
            ),

            SessionHandlerInterface::class => static fn(
                EnvironmentInterface $env,
            ): SessionHandlerInterface => new ArraySessionHandler(
                ttl: (int) $env->get('MCP_SESSION_TTL', 3600),
            ),

            SessionManager::class => static fn(
                SessionHandlerInterface $sessionHandler,
                LogsInterface $logs,
                LoopInterface $loop,
            ) => new SessionManager(
                handler: $sessionHandler,
                logger: $logs->getLogger('mcp'),
                loop: $loop,
            ),

            ServerTransportInterface::class => static fn(EnvironmentInterface $env, FactoryInterface $factory, MiddlewareRepositoryInterface $middleware) => match ($env->get('MCP_TRANSPORT', 'http')) {
                'http' => new HttpServerTransport(
                    host: $env->get('MCP_HOST', '127.0.0.1'),
                    port: (int) $env->get('MCP_PORT', 8090),
                    middlewares: $middleware->all(),
                ),
                'stream' => new StreamableHttpServerTransport(
                    host: $env->get('MCP_HOST', '127.0.0.1'),
                    port: (int) $env->get('MCP_PORT', 8090),
                    middlewares: $middleware->all(),
                ),
                default => new StdioServerTransport(),
            },

            SchemaGenerator::class => static fn(
                DocBlockParser $parser,
            ) => new SchemaGenerator(docBlockParser: $parser),

            Server::class => static fn(ServerBuilder $builder, Configuration $configuration, SessionManager $sessionManager, Registry $registry, SubscriptionManager $subscriptionManager, SchemaValidator $schemaValidator): Server => new Server(
                configuration: $configuration,
                registry: $registry,
                protocol: new Protocol(
                    configuration: $configuration,
                    registry: $registry,
                    sessionManager: $sessionManager,
                    dispatcher: new Dispatcher(
                        configuration: $configuration,
                        registry: $registry,
                        subscriptionManager: $subscriptionManager,
                        schemaValidator: $schemaValidator,
                    ),
                ),
                sessionManager: $sessionManager,
            ),
        ];
    }

    public function init(
        AbstractKernel $kernel,
        TokenizerListenerRegistryInterface $tokenizerRegistry,
        ToolsLocator $toolsLocator,
        ConsoleBootloader $console,
    ): void {
        $tokenizerRegistry->addListener($toolsLocator);

        $kernel->addDispatcher(McpDispatcher::class);
        $console->addCommand(McpCommand::class);
    }
}
