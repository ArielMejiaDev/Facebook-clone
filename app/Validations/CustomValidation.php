<?php namespace App\Validations;

use App\Exceptions\ValidationErrorException;
use Illuminate\Validation\ValidationException;

class CustomValidation
{
    public function validate(array $rules = [])
    {
        try {
            $data = request()->validate($rules);
        } catch (ValidationException $e) {
            throw new ValidationErrorException(json_encode($e->errors()));
        }
        return $data;
    }
}
