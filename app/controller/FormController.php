<?php

namespace controller;

class FormController
{
    public static function showFieldError(string $field): void {
        if (isset($_SESSION['formError'][$field])) {
            $error = $_SESSION['formError'][$field];
            echo '<p class="fieldError">' . $error . '</p>';
            self::removeError($field);
        }

    }

    public static function addError(string $fieldName, string $error): void {
        $_SESSION['formError'][$fieldName] = $error;
    }

    public static function removeError(string $fieldName): void {
        $_SESSION['formError'][$fieldName] = null;
    }

    public static function validateLoginForm($form): bool {
        $valid = true;

        if (self::emptyField($form['email'])) {
            $valid = false;
            self::addError('email', 'Az E-mail kötelező mező!');
        } else if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $valid = false;
            self::addError('email', 'Érvénytelen E-mail!');
        }

        if (self::emptyField($form['password'])) {
            $valid = false;
            self::addError('password', 'Az Jelszó kötelező mező!');
        }

        return $valid;
    }

    public static function validateSignupForm($form): bool {
        $valid = true;

        if (self::emptyField($form['email'])) {
            $valid = false;
            self::addError('email', 'Az E-mail kötelező mező!');
        } else if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $valid = false;
            self::addError('email', 'Érvénytelen E-mail!');
        }

        if (self::emptyField($form['name'])) {
            $valid = false;
            self::addError('name', 'A teljes név kötelező mező!');
        }

        if (self::emptyField($form['password'])) {
            $valid = false;
            self::addError('password', 'Az Jelszó kötelező mező!');
        }

        if (self::emptyField($form['passwordAgain'])) {
            $valid = false;
            self::addError('passwordAgain', 'Az Jelszó megerősítés kötelező mező!');
        }

        if ($form['passwordAgain'] !== $form['password']) {
            $valid = false;
            self::addError('passwordAgain', 'Nem egyeznek meg a jelszavak!');
            self::addError('passwordAgain', 'Nem egyeznek meg a jelszavak!');
        }

        return $valid;
    }

    private static function emptyField($field): bool {
        return !isset($field) || $field == null || $field == '';
    }
}