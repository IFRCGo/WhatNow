
## Component Documentation

### 1. Routes
The route files serve as the entry point for HTTP requests, mapping each request to a specific action in a controller. These route files are organized as follows:

- **API Routes:** Defined in files using `Route::group` and methods such as `Route::post`, `Route::put`, `Route::delete`, `Route::get`, `Route::patch`. These routes handle requests for the REST API and are located in `routes/api.php`.
- **Broadcast Channels:** Defined in `routes/channels.php`, allowing for the configuration of broadcasting channels for real-time events.
- **Console Commands:** Defined in `routes/console.php`, these routes are executed in the console and allow for custom Artisan commands.
- **Web Routes:** Defined in `routes/web.php` for the web interface routes accessed by users through a browser.

### 2. Controllers
Controllers contain the logic for each route. They are organized into different namespaces based on their functionality to enhance modularity and maintainability.

- **WhatNow Namespace:** Contains controllers that manage the main functionality of the application, such as `ApplicationController`, `InstructionController`, `EventTypeController`, `OrganisationController`, `RegionController`, `UsageController`, and `WhatNowImportController`.
- **Auth Namespace:** Manages authentication and contains controllers like `LoginController`, `OAuthController`, `RegisterController`, `ForgotPasswordController`, `ResetPasswordController`, `UserConfirmationController`, and `UserController`.
- **Audit Namespace:** Includes `HistoryController` for managing audit logs.
- **Terms Namespace:** `TermsController` handles terms and conditions.
- **Settings Namespace:** `PasswordController` manages password updates.
- **Alert Namespace:** `AlertController` deals with alert management.

### 3. Controller Methods
Each controller implements methods that correspond to the defined routes and handle various actions:

- **CRUD Operations:** Common methods such as `list`, `create`, `update`, and `delete` allow for creating, reading, updating, and deleting resources.
- **Custom Operations:** Specific methods like `publishTranslations`, `import`, `export`, and `renderImage` implement custom business logic.

### 4. Validation and Authorization
Controllers utilize Laravel's validation and authorization features to ensure data integrity and security.

- **Validation:** Incoming request data is validated using `$this->validate($request, [...])`.
- **Authorization:** User permissions are checked using `$this->authorize('action', Model::class)` to ensure the user is allowed to perform certain actions.

### 5. Error Handling
Controllers manage errors using custom methods such as `respondWithError`, `respondWithNotFound`, `respondWithConflict`, `respondWithForbidden`, and `respondWithSuccess`. This ensures that errors are handled consistently across operations.

### 6. Events
Some controllers trigger events using Laravel's event system. Examples of events include `InstructionCreated`, `InstructionUpdated`, and `InstructionDeleted`, which allow notifying other parts of the system about significant changes.

### 7. Resources
Controllers return data using Laravel's resource classes. These resources format the data consistently before sending it in JSON responses. Examples include `ApplicationResource`, `InstructionResource`, `EventTypeResource`, `OrganisationResource`, `RegionResource`, `HistoryResource`, and `AlertResource`.

### 8. Middleware
Routes are grouped with middleware to handle authentication and other cross-cutting concerns, such as authorized access and protection against CSRF attacks.

---

### Example: Route and Controller Breakdown

**Route:**
```php
Route::post('instructions', 'WhatNow\InstructionController@create');
```

**Controller Method (`create`):**
This method is responsible for validating the request, creating a new instruction, and returning a JSON response. Below is a breakdown of the key steps in the `create` method of the `InstructionController`:

```php
public function create(Request $request)
{
    // Validate incoming request data
    $this->validate($request, [
        'countryCode' => 'required|alpha|size:3',
        'eventType' => 'required|string|max:50',
        'translations' => 'required|array',
        'translations.*.lang' => 'required|alpha|size:2',
        'translations.*.title' => 'required|string',
        'translations.*.description' => 'required|string',
        'translations.*.webUrl' => 'nullable|url',
    ]);

    // Process the request and create the instruction
    $data = $request->all();
    $instruction = Instruction::createFromRequest($data);

    // Handle exceptions and send response
    try {
        $instruction = $this->client->createInstruction($instruction);
        event(new InstructionCreated($instruction));
        return new JsonResponse(InstructionResource::make($instruction), JsonResponse::HTTP_CREATED);
    } catch (RcnApiResourceNotFoundException $e) {
        return $this->respondWithNotFound($e);
    } catch (RcnApiResourceConflictException $e) {
        return $this->respondWithConflict($e, trans('rcnapi.conflict.instruction'));
    } catch (RcnApiException $e) {
        return $this->respondWithError($e);
    }
}
```

- **Validation:** The input is validated to ensure that `countryCode`, `eventType`, and `translations` meet the specified requirements.
- **Data Processing:** The data is transformed into an instruction and created in the system.
- **Exception Handling:** Any API errors are caught, and an appropriate response is returned based on the type of exception.

