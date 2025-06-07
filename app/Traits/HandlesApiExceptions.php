<?php

namespace App\Traits;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Response;

trait HandlesApiExceptions
{
    protected function handleApi(callable $callback): JsonResponse|Response
    {
        try {
            $response = $callback();

            return $response instanceof \Symfony\Component\HttpFoundation\Response
                ? $response
                : response()->json($response, 200);
        } catch (HttpExceptionInterface $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (Throwable $e) {
            if (app()->environment('local')) {
                return response()->json([
                    'message' => 'Internal Server Error',
                    'error'   => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ], 500);
            }

            return response()->json([
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}
