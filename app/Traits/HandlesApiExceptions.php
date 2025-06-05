<?php

namespace App\Traits;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\JsonResponse;
use Throwable;

trait HandlesApiExceptions
{
    protected function handleApi(callable $callback): JsonResponse
    {
        try {
            $response = $callback();

            return $response instanceof JsonResponse
                ? $response
                : response()->json($response, 200);
        } catch (HttpExceptionInterface $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}
