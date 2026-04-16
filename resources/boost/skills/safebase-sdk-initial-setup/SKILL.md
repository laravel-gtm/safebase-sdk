---
name: safebase-sdk-initial-setup
description: One-time skill to generate API-specific development skill and guidelines after running the init script.
---

# Initial setup — generate development skill and guidelines

## When to use this skill

Run this skill **once** after `./init-saloon-sdk.sh` has completed and you have started building out the SDK (added a connector, some request classes, and response DTOs). This skill generates the `safebase-sdk-development` skill and `guidelines/core.blade.php` with content specific to your API, replacing the placeholder stubs that ship with the template.

Do not run this skill again after the development skill and guidelines have been populated.

## What to do

### Step 1 — Examine the codebase

Read and understand the current SDK structure:

1. **`composer.json`** — package name, namespace, dependencies
2. **Connector class** in `src/` — base URL, auth method (Bearer, header key, OAuth), rate limits, timeouts
3. **SDK class** in `src/` — all public methods, resource accessors, return types
4. **`src/Requests/`** — every request class, its HTTP method, endpoint path, constructor parameters
5. **`src/Responses/`** — every response DTO, its properties and `fromArray()` shape
6. **`src/Resources/`** (if it exists) — resource groupings and their methods
7. **`src/ValueObjects/`** and **`src/Enums/`** (if they exist)
8. **`config/safebase-sdk.php`** — configuration keys, env var names
9. **API documentation** — look for `openapi.json`, `openapi.yaml`, `swagger.json`, a `docs/` directory, or documentation URLs in `README.md`

### Step 2 — Generate the development skill

Write API-specific content to `resources/boost/skills/safebase-sdk-development/SKILL.md`, replacing the stub. Follow this structure:

**Frontmatter:**

```yaml
---
name: safebase-sdk-development
description: <Rewrite to describe this specific API SDK, e.g. "Build features using the HubSpot CRM SDK, including contacts, companies, deals, and properties.">
---
```

**Sections to include:**

1. **`# {Package name} development`** — heading using the real package name (not "Saloon API SDK template")

2. **`## When to use this skill`** — name the specific package (`laravel-gtm/safebase-sdk`) and API. Describe what kinds of tasks this skill covers (adding endpoints, building integrations, writing tests).

3. **`## SDK entry point`** — show both injection patterns:

   ```php
   // Via Laravel container (recommended)
   $sdk = app(SafebaseSdk::class);

   // Standalone
   $sdk = SafebaseSdk::make(
       baseUrl: '...',
       token: '...',
   );
   ```

   Use the real constructor parameters from the connector/config.

4. **`## Resources and methods`** — the largest section. For each resource class or group of SDK methods:
   - A `###` heading (e.g., `### Contacts`, `### Events`)
   - PHP code examples for each public method showing:
     - Request class instantiation with real constructor parameter names
     - Response property access
     - Return types
   - Include both simple calls and more complex patterns (create/update with many params)

5. **`## Value objects`** (if `src/ValueObjects/` exists) — document each with construction, serialization, and usage examples.

6. **`## Enums`** (if `src/Enums/` exists) — list each enum with its backed cases.

7. **`## Pagination pattern`** (if the connector implements `HasPagination`) — show the cursor/page iteration loop with a real request class.

8. **`## Rate limits`** — document limits from the connector (e.g., "100 requests per 10 seconds") and any API-specific throttling behavior.

9. **`## Testing with the SDK`** — show a complete test example:

   ```php
   use Saloon\Http\Faking\MockClient;
   use Saloon\Http\Faking\MockResponse;

   $mockClient = new MockClient([
       RealRequestClass::class => MockResponse::make([...realistic mock data...]),
   ]);

   $connector->withMockClient($mockClient);
   ```

   Use a real request class and realistic mock data matching actual API response shapes.

10. **`## Conventions`** — PHP version, strict types, PHPStan level, Pint formatting, rule file locations.

### Step 3 — Generate the guidelines

Write API-specific content to `resources/boost/guidelines/core.blade.php`, replacing the stub. Follow this structure:

1. **Header** — `## {Package name} (\`laravel-gtm/safebase-sdk\`)`

2. **Setup** — env vars with the real prefix and key names from config, plus the config publish command:

   ```
   php artisan vendor:publish --tag=safebase-sdk-config
   ```

3. **Usage** — brief overview of SDK instantiation and resource method access.

4. **Request pattern** — a real code example wrapped in `@verbatim <code-snippet>`:

   ```blade
   @verbatim
   <code-snippet name="Descriptive name" lang="php">
   // Real SDK usage example
   </code-snippet>
   @endverbatim
   ```

5. **Testing** — mock example with `@verbatim <code-snippet>` using a real request class.

6. **Important notes** — rate limits, value objects, auth patterns, API-specific gotchas.

### Step 4 — Verify

After generating both files:

1. Read back `resources/boost/skills/safebase-sdk-development/SKILL.md` and confirm:
   - No references to "template", "ExampleGetRequest", "ping()", or "replace the example"
   - All class names match the actual codebase
   - Code examples use real request/response classes
2. Read back `resources/boost/guidelines/core.blade.php` and confirm likewise
3. Run `composer test`, `composer analyse`, and `composer lint` to ensure nothing is broken

## Reference — what a good development skill looks like

The `luma-sdk` development skill is the quality bar. Key characteristics:

- **Thorough** — every public SDK method documented with constructor parameters, return types, and property access patterns
- **Real code examples** — uses actual class names from the codebase, not placeholders
- **Organized by resource** — events, guests, hosts, ticket types, calendars, webhooks, etc., each with their own heading
- **Value objects documented** — parse/serialize examples for custom types (dates, durations, location IDs)
- **All enums listed** — backed enum cases spelled out so the agent knows valid values
- **Pagination pattern** — shown with a real request class and cursor/loop example
- **Rate limits stated** — specific numbers (e.g., "500 GETs per 5 min")
- **Testing section** — complete mock example with realistic response data matching the actual API shape
- **No template language** — zero references to "example", "replace", "template", or "boilerplate"
- **~400+ lines** — thorough enough to be genuinely useful as an agent reference
