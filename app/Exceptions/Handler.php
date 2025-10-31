<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    private function jsonError(string $code, string $message, int $status, ?array $errors = null)
    {
        $payload = [
            'status'  => 'error',
            'code'    => $code,
            'message' => $message,
        ];
        if ($errors) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $status, ['Content-Type' => 'application/json']);
    }

    public function register(): void
    {
        // 422 — ошибки валидации
        $this->renderable(function (ValidationException $e) {
            return $this->jsonError('validation_error', 'Данные не прошли валидацию.', 422, $e->errors());
        });

        // 404 — роут/модель
        $this->renderable(function (ModelNotFoundException|NotFoundHttpException $e) {
            return $this->jsonError('not_found', 'Ресурс не найден.', 404);
        });

        // 405 — метод не поддерживается
        $this->renderable(function (MethodNotAllowedHttpException $e) {
            return $this->jsonError('method_not_allowed', 'Метод не поддерживается для данного маршрута.', 405);
        });

        // 401 / 403
        $this->renderable(function (AuthenticationException $e) {
            return $this->jsonError('unauthorized', 'Требуется аутентификация.', 401);
        });
        $this->renderable(function (AuthorizationException $e) {
            return $this->jsonError('forbidden', 'Доступ запрещён.', 403);
        });

        // 400 — ошибки уровня SQL/некорректный запрос
        $this->renderable(function (QueryException $e) {
            return $this->jsonError('bad_request', 'Некорректный запрос к базе данных.', 400);
        });

        $this->renderable(function (HttpExceptionInterface $e) {
            $status = $e->getStatusCode();
            $code   = $status === 409 ? 'conflict'
                   : ($status === 404 ? 'not_found'
                   : ($status >= 500 ? 'server_error' : 'bad_request'));
            $msg    = $e->getMessage() ?: ($code === 'server_error' ? 'Внутренняя ошибка сервера.' : 'Ошибка HTTP.');
            return $this->jsonError($code, $msg, $status);
        });

        // 500 — фоллбек
        $this->renderable(function (Throwable $e) {
            return $this->jsonError('server_error', 'Внутренняя ошибка сервера.', 500);
        });
    }
}