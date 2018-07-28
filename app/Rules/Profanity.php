<?php

namespace KRLX\Rules;

use Illuminate\Contracts\Validation\Rule;

class Profanity implements Rule
{
    /**
     * The word that has been found.
     *
     * @var string|null
     */
    public $word;

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
        $words = explode(' ', strtolower($value));
        foreach(array_merge(config('defaults.banned_words.full'), config('defaults.banned_words.partial')) as $bad_word) {
            if(in_array($bad_word, $words) or in_array(str_plural($bad_word), $words)) {
                $this->word = $bad_word;
                return false;
            }
        }

        foreach(config('defaults.banned_words.partial') as $bad_word) {
            if(strpos(strtolower($value), $bad_word) !== false or strpos(strtolower($value), str_plural($bad_word)) !== false) {
                $this->word = $bad_word;
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The word {$this->word} can't appear in the :attribute.";
    }
}
