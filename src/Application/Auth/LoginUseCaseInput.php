<?php

declare(strict_types=1);

namespace Rucaro\Application\Auth;

use Rucaro\Domain\Exception\ValidationException;

final readonly class LoginUseCaseInput
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
        $errors = [];
        if (trim($email) === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = ['email must be a valid address'];
        }
        if (strlen($password) < 8) {
            $errors['password'] = ['password must be at least 8 characters'];
        }
        if ($errors !== []) {
            throw ValidationException::withErrors($errors);
        }
    }
}
