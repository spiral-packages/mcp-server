# Spiral MCP Server

A powerful and flexible **Model Context Protocol (MCP) Server** implementation for the Spiral Framework. This package
provides a complete MCP server solution with automatic tool discovery, attribute-based configuration, and seamless
integration with Spiral's dependency injection container.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Creating Tools](#creating-tools)
- [Tool Attributes](#tool-attributes)
- [Middleware](#middleware)
- [Contributing](#contributing)
- [License](#license)

## Features

✨ **Automatic Tool Discovery** - Automatically discovers and registers MCP tools using PHP attributes  
🔧 **Multiple Transport Options** - Supports HTTP, streaming HTTP, and STDIO transports  
🎯 **Attribute-Based Configuration** - Define tool behavior with simple PHP attributes  
🛡️ **Schema Validation** - Automatic JSON schema generation using spiral/json-schema-generator  
⚡ **Middleware Support** - Extensible middleware system for request/response processing  
🔌 **Spiral Integration** - First-class integration with Spiral Framework's IoC container  
📊 **Session Management** - Built-in session handling with configurable TTL  
🚀 **Production Ready** - Designed for high-performance production environments

## Requirements

- PHP 8.3 or higher
- Spiral Framework 3.15+
- Composer

## Installation

Install the package via Composer:

```bash
composer require spiral/mcp-server
```

Add the bootloader to your Spiral application:

```php
// app/src/Application/Kernel.php
use Spiral\McpServer\Bootloader\McpServerBootloader;

protected const LOAD = [
    // ... other bootloaders
    McpServerBootloader::class,
];
```

## Quick Start

### 1. Create Your First Tool

Create a simple calculator tool:

```php
<?php

namespace App\Tool;

use Spiral\McpServer\Attribute\Tool;
use Spiral\McpServer\Attribute\IsReadonly;

#[Tool(
    name: 'calculator_add',
    description: 'Adds two numbers together'
)]
#[IsReadonly]
class CalculatorTool
{
    public function __invoke(CalculatorRequest $request): array
    {
        return [
            'result' => $request->a + $request->b,
            'operation' => 'addition'
        ];
    }
}
```

### 2. Define the Request Schema

```php
<?php

namespace App\Tool;

use Spiral\JsonSchemaGenerator\Attribute\Field;

final readonly class CalculatorRequest
{
    public function __construct(
        #[Field(
            title: 'First Number',
            description: 'The first number to add'
        )]
        public float $a,
        
        #[Field(
            title: 'Second Number', 
            description: 'The second number to add'
        )]
        public float $b,
    ) {}
}
```

### 3. Set Environment Variables

```bash
# .env
MCP_TRANSPORT=http
MCP_HOST=127.0.0.1
MCP_PORT=8090
MCP_SERVER_NAME="My MCP Server"
MCP_SERVER_VERSION="1.0.0"
```

### 4. Start the Server

```bash
SAPI=mcp php app.php
```

Your MCP server is now running and ready to accept requests!

## Configuration

### Environment Variables

| Variable             | Default      | Description                                  |
|----------------------|--------------|----------------------------------------------|
| `MCP_TRANSPORT`      | `http`       | Transport type: `http`, `stream`, or `stdio` |
| `MCP_HOST`           | `127.0.0.1`  | Server host address                          |
| `MCP_PORT`           | `8090`       | Server port number                           |
| `MCP_SERVER_NAME`    | `MCP Server` | Server identification name                   |
| `MCP_SERVER_VERSION` | `1.0.0`      | Server version                               |
| `MCP_SESSION_TTL`    | `3600`       | Session TTL in seconds                       |
| `SAPI`               | `cli`        | Set to `mcp` to enable MCP dispatcher        |

### Custom Configuration

You can override the default configuration by binding your own `Configuration` instance:

```php
use PhpMcp\Server\Configuration;
use PhpMcp\Schema\Implementation;
use PhpMcp\Schema\ServerCapabilities;

// In your bootloader
$this->container->bindSingleton(Configuration::class, function() {
    return new Configuration(
        serverInfo: Implementation::make('Custom Server', '2.0.0'),
        capabilities: ServerCapabilities::make(),
        // ... other options
    );
});
```

## Creating Tools

### Basic Tool Structure

Tools are simple PHP classes that implement the `__invoke()` method:

```php
<?php

namespace App\Tool;

use Spiral\McpServer\Attribute\Tool;
use Spiral\McpServer\Attribute\IsReadonly;
use Spiral\JsonSchemaGenerator\Attribute\Field;

#[Tool(
    name: 'file_reader',
    description: 'Reads content from a file with validation'
)]
#[IsReadonly]
class FileReaderTool
{
    public function __invoke(FileRequest $request): array
    {
        if (!file_exists($request->path)) {
            throw new \InvalidArgumentException('File not found');
        }

        return [
            'content' => file_get_contents($request->path),
            'size' => filesize($request->path),
            'mime_type' => mime_content_type($request->path),
        ];
    }
}

final readonly class FileRequest
{
    public function __construct(
        #[Field(
            title: 'File Path',
            description: 'Absolute path to the file to read'
        )]
        public string $path,
        
        #[Field(
            title: 'Encoding',
            description: 'Expected file encoding',
            default: 'utf-8'
        )]
        public string $encoding = 'utf-8',
        
        #[Field(
            title: 'Maximum Size',
            description: 'Maximum file size in bytes',
            default: 1048576
        )]
        public int $maxSize = 1048576, // 1MB
    ) {}
}
```

### Tools Without Parameters

For tools that don't require input parameters:

```php
#[Tool(
    name: 'system_status',
    description: 'Gets current system status'
)]
class SystemStatusTool
{
    public function __invoke(): array
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'uptime' => $this->getUptime(),
        ];
    }
    
    private function getUptime(): int
    {
        // Implementation details...
        return 0;
    }
}
```

### Complex Request Objects

Use complex objects with Field attributes for rich schema generation:

```php
use Spiral\JsonSchemaGenerator\Attribute\Field;
use Spiral\JsonSchemaGenerator\Schema\Format;

final readonly class DatabaseQueryRequest
{
    public function __construct(
        #[Field(
            title: 'Table Name',
            description: 'Name of the database table to query'
        )]
        public string $table,
        
        #[Field(
            title: 'Conditions',
            description: 'WHERE conditions as key-value pairs',
            default: []
        )]
        public array $conditions = [],
        
        #[Field(
            title: 'Limit',
            description: 'Maximum number of records to return'
        )]
        public ?int $limit = null,
        
        #[Field(
            title: 'Order By',
            description: 'Order by fields with direction (ASC/DESC)',
            default: []
        )]
        public array $orderBy = [],
    ) {}
}

#[Tool(
    name: 'db_query',
    description: 'Executes a database query with conditions'
)]
class DatabaseQueryTool
{
    public function __construct(
        private readonly DatabaseInterface $db,
    ) {}

    public function __invoke(DatabaseQueryRequest $request): array
    {
        return $this->db->query($request->table)
            ->where($request->conditions)
            ->limit($request->limit)
            ->orderBy($request->orderBy)
            ->fetchAll();
    }
}
```

### Using Format Validation

Leverage built-in format validation for common data types:

```php
final readonly class UserRegistrationRequest
{
    public function __construct(
        #[Field(
            title: 'Username',
            description: 'Unique username for the account'
        )]
        public string $username,
        
        #[Field(
            title: 'Email Address',
            description: 'Valid email address for account verification',
            format: Format::Email
        )]
        public string $email,
        
        #[Field(
            title: 'Date of Birth',
            description: 'User birth date in ISO format',
            format: Format::Date
        )]
        public string $birthDate,
        
        #[Field(
            title: 'Website',
            description: 'Optional personal website URL',
            format: Format::Uri
        )]
        public ?string $website = null,
        
        #[Field(
            title: 'User ID',
            description: 'Auto-generated UUID for the user',
            format: Format::Uuid
        )]
        public string $userId,
    ) {}
}
```

### Enum Support with Schema Generation

The schema generator automatically handles PHP enums:

```php
enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';
    case Moderator = 'moderator';
}

enum Priority: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;
}

final readonly class TaskRequest
{
    public function __construct(
        #[Field(
            title: 'Task Title',
            description: 'Descriptive title for the task'
        )]
        public string $title,
        
        #[Field(
            title: 'Assigned Role',
            description: 'User role that can handle this task'
        )]
        public UserRole $assignedRole,
        
        #[Field(
            title: 'Priority Level',
            description: 'Task priority from 1 (low) to 4 (critical)'
        )]
        public Priority $priority,
        
        #[Field(
            title: 'Tags',
            description: 'List of tags for task categorization',
            default: []
        )]
        public array $tags = [],
    ) {}
}
```

### Complex Nested Objects

Handle nested objects and arrays with proper schema references:

```php
final readonly class Address
{
    public function __construct(
        #[Field(title: 'Street', description: 'Street address')]
        public string $street,
        
        #[Field(title: 'City', description: 'City name')]
        public string $city,
        
        #[Field(title: 'ZIP Code', description: 'Postal code')]
        public string $zipCode,
        
        #[Field(title: 'Country', description: 'Country name')]
        public string $country,
    ) {}
}

final readonly class ContactInfo
{
    public function __construct(
        #[Field(
            title: 'Email',
            description: 'Primary email address',
            format: Format::Email
        )]
        public string $email,
        
        #[Field(title: 'Phone', description: 'Phone number')]
        public ?string $phone = null,
    ) {}
}

final readonly class CreateUserRequest
{
    public function __construct(
        #[Field(
            title: 'Full Name',
            description: 'User full name'
        )]
        public string $name,
        
        #[Field(
            title: 'Home Address',
            description: 'User residential address'
        )]
        public Address $address,
        
        #[Field(
            title: 'Contact Information',
            description: 'Contact details for the user'
        )]
        public ContactInfo $contact,
        
        /**
        * @var array<Address>
         */
        #[Field(
            title: 'Additional Addresses',
            description: 'Optional additional addresses (work, etc.)',
            default: []
        )]
        public array $additionalAddresses = [], // array of Address objects
        
        #[Field(
            title: 'User Preferences',
            description: 'Key-value pairs of user preferences',
            default: []
        )]
        public array $preferences = [],
    ) {}
}
```

## Tool Attributes

### Core Tool Attribute

The `#[Tool]` attribute is required for all MCP tools:

```php
#[Tool(
    name: 'unique_tool_name',
    description: 'Clear description of what this tool does'
)]
```

### Behavioral Attributes

#### Read-Only Tools

```php
#[IsReadonly(readOnlyHint: true)]  // Tool doesn't modify environment
```

#### Destructive Operations

```php
#[IsDestructive(destructive: true)]  // Tool may perform destructive updates
```

#### Idempotent Operations

```php
#[IsIdempotent(idempotent: true)]  // Repeated calls have no additional effect
```

#### Open World Tools

```php
#[IsOpenWorld(openWorld: true)]  // Tool interacts with external entities
```

### Complete Example with All Attributes

```php
#[Tool(
    name: 'file_manager',
    description: 'Manages file operations with external storage'
)]
#[IsDestructive(destructive: true)]
#[IsIdempotent(idempotent: false)]
#[IsOpenWorld(openWorld: true)]
class FileManagerTool
{
    public function __invoke(FileOperation $operation): array
    {
        // Implementation...
        return ['status' => 'success'];
    }
}
```

## Middleware

### Creating Custom Middleware

```php
<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoggingMiddleware
{
    public function __invoke(
        ServerRequestInterface $request, 
        callable $next
    ): ResponseInterface {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        error_log("MCP Request processed in {$duration}s");
        
        return $response;
    }
}
```

### Registering Middleware

```php
// In your bootloader or service
use Spiral\McpServer\MiddlewareRegistryInterface;

public function boot(MiddlewareRegistryInterface $registry): void
{
    $registry->register(new LoggingMiddleware());
    $registry->register(new AuthenticationMiddleware());
    $registry->register(new RateLimitingMiddleware());
}
```

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Run static analysis: `composer psalm`

### Code Style

This project follows PSR-12 coding standards. Run the code fixer:

```bash
composer cs-fix
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

**Built with ❤️ by the Spiral Team**

For more information about the Spiral Framework, visit [spiral.dev](https://spiral.dev)