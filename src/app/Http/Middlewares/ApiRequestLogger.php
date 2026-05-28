<?php declare(strict_types=1);

namespace App\Http\Middlewares;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class ApiRequestLogger
{
    /**
     * @phpstan-param \Closure(Request):Response $next
     * @phpstan-return mixed
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        Context::add(key: 'request_id', value: Str::uuid7()->toString());
        Context::add(key: 'timestamp', value: now()->toIso8601String());
        Context::add(key: 'path', value: $request->path());
        Context::add(key: 'method', value: $request->method());

        $user = $request->user();

        if (is_object(value: $user) && property_exists(
            object_or_class: $user,
            property: 'id'
        )) {
            Context::add(key: 'user_id', value: $user->id);
        }

        $startTime = microtime(as_float: true);
        $response = $next($request);

        $responseTime = round(
            num: (microtime(as_float: true) - $startTime) * 1000,
            precision: 2
        );

        Context::add(key: 'response_time', value: $responseTime);
        Context::add(key: 'status_code', value: $response->getStatusCode());

        Log::info(message: 'API Request Processed');

        return $response;
    }
}
