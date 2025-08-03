# Subscription Management System SDK

This SDK allows external applications to integrate with the Subscription Management System.

## Features

- User subscription management
- Access control verification
- Plan information retrieval
- Application statistics
- Feature-based access control

## Available SDKs

### PHP SDK

Located in `php/SubscriptionClient.php`

#### Installation

1. Copy the `SubscriptionClient.php` file to your project
2. Include the file in your application

#### Usage

```php
<?php
require_once 'path/to/SubscriptionClient.php';

use SubscriptionManager\SDK\SubscriptionClient;

// Initialize the client
$client = new SubscriptionClient(
    'https://your-subscription-system.com',
    'your-api-key',
    'your-api-secret'
);

// Check if user has access to a specific plan
$hasAccess = $client->checkUserAccess(123, 456);

// Get user's subscriptions
$subscriptions = $client->getUserSubscriptions(123);

// Check if user has any active subscription
$hasActiveSubscription = $client->hasActiveSubscription(123);

// Check feature access
$hasFeature = $client->hasFeatureAccess(123, 'Advanced Analytics');

// Get available plans
$plans = $client->getAvailablePlans();

// Create a subscription
$result = $client->createSubscription(123, 456);
```

## API Endpoints

The SDK communicates with the following API endpoints:

### Authentication
- `POST /api/auth` - Authenticate and get access token
- `POST /api/auth/verify` - Verify token validity

### Subscriptions
- `GET /api/users/{user}/subscriptions` - Get user subscriptions
- `GET /api/users/{user}/access/{plan}` - Check user access to plan
- `POST /api/subscriptions` - Create new subscription

### Application Data
- `GET /api/plans` - Get available plans
- `GET /api/stats` - Get application statistics

## Authentication

The system uses API key and secret authentication:

1. Each application receives a unique API key and secret
2. Authenticate to receive a temporary access token
3. Use the access token for subsequent API calls
4. Tokens expire after 24 hours

## Error Handling

The SDK throws exceptions for:
- Authentication failures
- Network errors
- API errors (4xx, 5xx responses)

```php
try {
    $hasAccess = $client->checkUserAccess(123, 456);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Integration Examples

### Basic Access Control

```php
// In your application's access control logic
if ($client->hasActiveSubscription($userId)) {
    // Allow access to premium features
    return true;
} else {
    // Redirect to subscription page
    return false;
}
```

### Feature-Based Access

```php
// Check specific feature access
if ($client->hasFeatureAccess($userId, 'Advanced Reports')) {
    // Show advanced reporting interface
    showAdvancedReports();
} else {
    // Show upgrade message
    showUpgradeMessage();
}
```

### Subscription Management

```php
// Create subscription when user signs up for a plan
try {
    $result = $client->createSubscription($userId, $planId);
    if ($result['success']) {
        // Subscription created successfully
        updateUserAccess($userId);
    }
} catch (Exception $e) {
    // Handle subscription creation error
    logError($e->getMessage());
}
```

## Configuration

Store your API credentials securely:

```php
// config.php
return [
    'subscription_system' => [
        'base_url' => env('SUBSCRIPTION_SYSTEM_URL'),
        'api_key' => env('SUBSCRIPTION_API_KEY'),
        'api_secret' => env('SUBSCRIPTION_API_SECRET'),
    ]
];
```

## Support

For support and integration questions, please contact the system administrator or refer to the API documentation.