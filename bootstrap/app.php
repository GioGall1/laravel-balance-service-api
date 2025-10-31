<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Всегда отдаём JSON для API
        $exceptions->shouldRenderJsonWhen(
            fn (Request $r) => $r->expectsJson() || $r->is('api/*')
        );

        // Хелпер JSON-ответа
        $json = function (string $code, string $message, int $status, ?array $errors = null) {
            $payload = ['status' => 'error', 'code' => $code, 'message' => $message];
            if ($errors) $payload['errors'] = $errors;
            return response()->json($payload, $status);
        };

        // 422 — ошибки валидации
        $exceptions->render(function (ValidationException $e) use ($json) {
            return $json('validation_error', 'Данные не прошли валидацию.', 422, $e->errors());
        });

        // 404 — общий (роут/модель)
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e) use ($json) {
            return $json('not_found', 'Ресурс не найден.', 404);
        });

        // 405 — метод не поддерживается
        $exceptions->render(function (MethodNotAllowedHttpException $e) use ($json) {
            return $json('method_not_allowed', 'Метод не поддерживается для данного маршрута.', 405);
        });

        // 401 / 403
        $exceptions->render(function (AuthenticationException $e) use ($json) {
            return $json('unauthorized', 'Требуется аутентификация.', 401);
        });
        $exceptions->render(function (AuthorizationException $e) use ($json) {
            return $json('forbidden', 'Доступ запрещён.', 403);
        });

        // 400 — ошибки SQL / некорректные запросы
        $exceptions->render(function (QueryException $e) use ($json) {
            return $json('bad_request', 'Некорректный запрос к базе данных.', 400);
        });

        // 404 / 409
        $exceptions->render(function (\App\Exceptions\UserNotFoundException $e) use ($json) {
            return $json('not_found', $e->getMessage(), 404);
        });
        $exceptions->render(function (\App\Exceptions\InsufficientFundsException $e) use ($json) {
            return $json('conflict', $e->getMessage(), 409);
        });

        // Любые другие Http-исключения
        $exceptions->render(function (HttpExceptionInterface $e) use ($json) {
            $status = $e->getStatusCode();
            $code = $status === 409 ? 'conflict'
                  : ($status === 404 ? 'not_found'
                  : ($status >= 500 ? 'server_error' : 'bad_request'));
            $msg = $e->getMessage() ?: ($status >= 500 ? 'Внутренняя ошибка сервера.' : 'Ошибка HTTP.');
            return $json($code, $msg, $status);
        });

        // 500 — фоллбек
        $exceptions->render(function (Throwable $e) use ($json) {
            return $json('server_error', 'Внутренняя ошибка сервера.', 500);
        });
    })
    ->create();
