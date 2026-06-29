<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase.factory', function ($app) {
            $credentialsPath = config('firebase.credentials');

            // Resolve path relative to base_path jika bukan absolute path
            if (!str_starts_with($credentialsPath, '/') && !str_contains($credentialsPath, ':\\')) {
                $credentialsPath = base_path($credentialsPath);
            }

            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);

            // In Windows / Local environment, we disable SSL verification for all Guzzle HTTP requests
            // performed by the Firebase factory (such as auth token fetching, REST API calls, etc.).
            $clientOptions = \Kreait\Firebase\Http\HttpClientOptions::default()
                ->withGuzzleConfigOptions($this->httpOptions());
            $factory = $factory->withHttpClientOptions($clientOptions);

            // Kreait Firebase PHP SDK passes 'credentialsFetcher' to googleCloudClientConfig,
            // but Google Cloud FirestoreClient expects 'credentials' instead.
            $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                Factory::API_CLIENT_SCOPES,
                $serviceAccount
            );

            // In Windows / Local environment, we disable SSL verification for Firebase API requests
            // and force the 'rest' transport (since gRPC can block/hang without correct certificates).
            $httpClient = new \GuzzleHttp\Client($this->httpOptions());
            $httpHandler = function (\Psr\Http\Message\RequestInterface $request, array $options = []) use ($httpClient) {
                $options['verify'] = false;
                return $httpClient->sendAsync($request, $options);
            };
            $authHttpHandler = new \Google\Auth\HttpHandler\Guzzle6HttpHandler($httpClient);

            return $factory->withFirestoreClientConfig([
                'credentials' => $credentials,
                'transport' => 'rest',
                'credentialsConfig' => [
                    'authHttpHandler' => $authHttpHandler,
                ],
                'transportConfig' => [
                    'rest' => [
                        'httpHandler' => $httpHandler,
                    ],
                ],
            ]);
        });

        $this->app->singleton('firebase.firestore', function ($app) {
            return $app['firebase.factory']->createFirestore();
        });

        $this->app->singleton('firebase.storage', function ($app) {
            $credentialsPath = config('firebase.credentials');
            if (!str_starts_with($credentialsPath, '/') && !str_contains($credentialsPath, ':\\')) {
                $credentialsPath = base_path($credentialsPath);
            }

            $serviceAccount = json_decode(file_get_contents($credentialsPath), true, 512, JSON_THROW_ON_ERROR);
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                Factory::API_CLIENT_SCOPES,
                $serviceAccount
            );

            // Google Cloud Storage uses its own HTTP client, separate from Kreait's API client.
            // Apply the local Windows SSL workaround to both OAuth and object requests.
            $httpClient = new \GuzzleHttp\Client($this->httpOptions());
            $httpHandler = static function (\Psr\Http\Message\RequestInterface $request, array $options = []) use ($httpClient) {
                $options['verify'] = false;
                return $httpClient->send($request, $options);
            };
            $authHttpHandler = new \Google\Auth\HttpHandler\Guzzle6HttpHandler($httpClient);

            $storageClient = new \Google\Cloud\Storage\StorageClient([
                'projectId' => config('firebase.project_id'),
                'credentialsFetcher' => $credentials,
                'authHttpHandler' => $authHttpHandler,
                'httpHandler' => $httpHandler,
            ]);

            return new \Kreait\Firebase\Storage(
                $storageClient,
                config('firebase.storage.bucket')
            );
        });

        $this->app->singleton('firebase.auth', function ($app) {
            $factory = $app['firebase.factory'];

            // We construct Auth manually to bypass SSL verification issues when fetching
            // Google public certificates inside verifyIdToken (used by middleware).
            $refFactory = new \ReflectionClass($factory);

            // Get Project ID
            $methodGetProjectId = $refFactory->getMethod('getProjectId');
            $methodGetProjectId->setAccessible(true);
            $projectId = $methodGetProjectId->invoke($factory);

            // Get default cache
            $propCache = $refFactory->getProperty('defaultCache');
            $propCache->setAccessible(true);
            $cache = $propCache->getValue($factory);

            // Get tenantId
            $propTenantId = $refFactory->getProperty('tenantId');
            $propTenantId->setAccessible(true);
            $tenantId = $propTenantId->getValue($factory);

            // Get errorResponseParser
            $propParser = $refFactory->getProperty('errorResponseParser');
            $propParser->setAccessible(true);
            $errorResponseParser = $propParser->getValue($factory);

            // Setup Custom Public Keys Handler with SSL verification disabled
            $clock = \Beste\Clock\SystemClock::create();
            $customHttpClient = new \GuzzleHttp\Client($this->httpOptions([
                'http_errors' => false,
            ]));
            $innerKeyHandler = new \Kreait\Firebase\JWT\Action\FetchGooglePublicKeys\WithGuzzle($customHttpClient, $clock);
            $keyHandler = new \Kreait\Firebase\JWT\Action\FetchGooglePublicKeys\WithPsr6Cache($innerKeyHandler, $cache, $clock);
            $keys = new \Kreait\Firebase\JWT\GooglePublicKeys($keyHandler, $clock);

            // Setup custom verifiers
            $idTokenHandler = new \Kreait\Firebase\JWT\Action\VerifyIdToken\WithLcobucciJWT($projectId, $keys, $clock);
            $customIdTokenVerifier = new \Kreait\Firebase\JWT\IdTokenVerifier($idTokenHandler);

            $sessionCookieHandler = new \Kreait\Firebase\JWT\Action\VerifySessionCookie\WithLcobucciJWT($projectId, $keys, $clock);
            $customSessionCookieVerifier = new \Kreait\Firebase\JWT\SessionCookieVerifier($sessionCookieHandler);

            // Setup client and auth components
            $httpClient = $factory->createApiClient();
            $signInHandler = new \Kreait\Firebase\Auth\SignIn\GuzzleHandler($projectId, $httpClient);

            $authApiClient = new \Kreait\Firebase\Auth\ApiClient(
                $projectId,
                $tenantId,
                $httpClient,
                $signInHandler,
                $clock,
                new \Kreait\Firebase\Exception\AuthApiExceptionConverter($errorResponseParser),
            );

            $methodCreateCustomTokenGenerator = $refFactory->getMethod('createCustomTokenGenerator');
            $methodCreateCustomTokenGenerator->setAccessible(true);
            $customTokenGenerator = $methodCreateCustomTokenGenerator->invoke($factory);

            return new \Kreait\Firebase\Auth(
                $authApiClient,
                $customTokenGenerator,
                $customIdTokenVerifier,
                $customSessionCookieVerifier,
                $clock
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    private function httpOptions(array $overrides = []): array
    {
        return array_replace([
            'verify' => $this->app->environment('local') ? false : true,
            'connect_timeout' => config('firebase.connect_timeout', 8),
            'timeout' => max(
                config('firebase.request_timeout', 5),
                config('firebase.write_timeout', 15)
            ),
        ], $overrides);
    }
}
