<?php

namespace App\Forms;

use App\Database;
use App\Forms\Validator;
use App\Session;

class AddUserForm {

    public static $errors = [];
    
    public static $rules = [
        'name' => [
            'required' => true,
            'minLength' => 2,
            'maxLength' => 50,
        ],
        'email' => [
            'required' => true,
            'email' => true,
            'maxLength' => 100,
        ],
        'user_limit' => [
            'tableName' => 'users',
            'objectLimit' => 16,
        ]
    ];

    public static function validate(array $data): bool {
        self::$errors = Validator::validate($data, self::$rules);
        return empty(self::$errors);
    }

    public static function getErrors(): array {
        // Check if errors were stored in session (from redirect after validation)
        $sessionErrors = Session::get('form_errors');
        if ($sessionErrors) {
            self::$errors = $sessionErrors;
            // Clear the session so errors don't persist on next page load
            Session::unset('form_errors');
            return self::$errors;
        }
        return self::$errors;
    }
}