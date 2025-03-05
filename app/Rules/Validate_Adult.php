<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
// carbon is a library that helps with dates
// https://laravel.com/docs/12.x/helpers#dates

class Validate_Adult implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */


    // makes it possible to "reuse" the rule for different models, not only employees
    protected $Called_From;

    public function __construct($Called_From)
    {
        $this->Called_From = $Called_From;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $birthdate = Carbon::parse($value);
        $age = $birthdate->diffInYears(Carbon::now());

        if ($age < 18) {
            $fail('The ' . $this->Called_From . ' must be an adult');
        }
    }
}
