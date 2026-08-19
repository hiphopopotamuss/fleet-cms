<?php

declare(strict_types=1);

namespace App\Validation;

final class Validator
{
    private array $errors = [];

    public function requireString(string $field, mixed $value, int $max = 255): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            $this->errors[$field] = 'This field is required.';
            return null;
        }
        $value = trim($value);
        if (strlen($value) > $max) {
            $this->errors[$field] = 'This field is too long.';
            return null;
        }
        return $value;
    }

    public function intRange(string $field, mixed $value, int $min, int $max): ?int
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = 'Enter a valid number.';
            return null;
        }
        $int = (int) $value;
        if ($int < $min || $int > $max) {
            $this->errors[$field] = "Enter a number between {$min} and {$max}.";
            return null;
        }
        return $int;
    }

    public function date(string $field, mixed $value, bool $required = false): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            if ($required) {
                $this->errors[$field] = 'Enter a date.';
            }
            return null;
        }
        $value = trim($value);
        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$dt || $dt->format('Y-m-d') !== $value) {
            $this->errors[$field] = 'Enter a valid date.';
            return null;
        }
        return $dt->format('Y-m-d');
    }

    public function inList(string $field, mixed $value, array $allowed): ?string
    {
        if (!is_string($value) || !in_array($value, $allowed, true)) {
            $this->errors[$field] = 'Choose a valid option.';
            return null;
        }
        return $value;
    }

    public function bool(string $field, mixed $value): int
    {
        return (!empty($value) && $value !== '0') ? 1 : 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function failed(): bool
    {
        return $this->errors !== [];
    }
}
