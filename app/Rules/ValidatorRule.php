<?php

namespace KRLX\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidatorRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $rules = ['required', 'integer', 'numeric', 'string', 'array', 'min:', 'max:', 'english', 'profanity'];

        $needle = $value;
        $colon = strpos($value, ":");
        if($colon === false) return in_array($needle, $rules);

        $needle = substr($value, 0, ($colon + 1));
        $param = substr($value, ($colon + 1));
        if(strlen($param) == 0) return false;
        if(!is_numeric($param)) return false;
        if($param < 0) return false;

        return in_array($needle, $rules);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a validation rule.';
    }
}
