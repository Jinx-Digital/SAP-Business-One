# Jinx SAP B1 REST API Client

A standalone PHP library for interacting with the SAP Business One Service Layer. This library is designed for performance, flexibility, and a clean developer experience.

## Key Features

- **PHPStan Compatible**: All classes and methods include full PHPDoc blocks with `@param` and `@return` types for static analysis.
- **PHPUnit Support**: Fully testable via PHPUnit, covering filters, configuration, and response handling.
- **PSR-4 Autoloading**: Standardized project structure via Composer.
- **Query Builder**: Build complex OData queries with `where()`, `andWhere()`, `orWhere()` and nested sub-queries.
- **Advanced Filtering**: Supports a wide range of OData filters including `Any` (collection filtering), `IsNull`, `Not`, and more.
- **Developer Productivity**: Shorthand service access and magic query methods.

---

## Core Classes

### `Client`
The entry point for the library. Manages authentication and service instantiation.
- **`static new(array $config)`**: Authenticates and returns a new Client instance.
- **`getService(string $name)`**: Returns a `Service` instance for a specific OData entity.
- **`items()`, `businessPartners()`, etc.**: Shorthand methods for common services.

### `Service`
Represents an OData service (e.g., Items, Orders).
- **`find($id)`**: Retrieves a single entity by its primary key.
- **`document($id)`**: Semantic alias for `find()`.
- **`create(array $data)`**: Creates a new record (POST).
- **`update($id, array $data)`**: Updates a record (PATCH).
- **`attachment($id)`**: Retrieves the `$value` binary stream (e.g., item images).
- **`query()`**: Returns a new `Query` builder for this service.

### `Query`
A powerful OData query builder.
- **`select('Field1, Field2')`**: Filters returned fields.
- **`limit(10, 5)`**: Pagination ($top, $skip).
- **`where(Filter|Query $filter)`**: Adds an OData `$filter` clause.
- **`subQuery()`**: Creates a nested query object for complex grouping.
- **`where{Field}($value)`**: Magic method for quick equality filters (e.g. `whereItemCode('A001')`).

### `Response`
Wrapper for HTTP responses.
- **`isOk()`**: Returns `true` if the request was successful (2xx).
- **`getJson()`**: Returns the decoded JSON body as an object.
- **`getErrorMessage()`**: Automatically parses SAP's standard error response format.

---

## Filters

The `Jinx\SapB1\Filters` namespace provides several filter types:
- **Equality**: `Equal`, `NotEqual`, `InArray`, `NotInArray`
- **Comparison**: `LessThan`, `LessThanEqual`, `MoreThan`, `MoreThanEqual`
- **String**: `Contains`, `StartsWith`, `EndsWith`
- **Logic**: `Not` (negation)
- **Special**: `IsNull`, `IsNotNull`, `Raw`, `Any` (filter on collection properties)

---

## Usage Example

```php
use Jinx\SapB1\Client;
use Jinx\SapB1\Filters\Equal;
use Jinx\SapB1\Filters\MoreThan;

// 1. Initialize
$client = Client::new([
    'host' => 'sap-server',
    'username' => 'manager',
    'password' => 'secret',
    'company' => 'MyCompany',
    'port' => 50000,
    'https' => true
]);

// 2. Build a complex query
$query = $client->items()->query()
    ->where(new MoreThan('OnHand', 0))
    ->andWhere(
        $query->subQuery()
            ->whereItemCode('ITEM001')
            ->orWhereItemCode('ITEM002')
    );

// 3. Execute
$response = $query->find();

if ($response->isOk()) {
    $data = $response->getJson();
    // Use $data->value ...
} else {
    echo "Error: " . $response->getErrorMessage();
}
```

## Running Tests

The library uses **PHPUnit** for regression testing.

### Setup
1. Install dependencies:
   ```bash
   composer install
   ```

### Execute Tests
Run all tests:
```bash
vendor/bin/phpunit tests
```
