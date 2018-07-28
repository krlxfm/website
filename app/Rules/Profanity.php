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
        $target = preg_replace('/[@#\$%\^]/', '*', strtolower($value));
        $words = explode(' ', $target);
        foreach (array_merge(config('defaults.banned_words.full'), config('defaults.banned_words.partial')) as $bad_word) {
            if (in_array($bad_word, $words) or in_array(str_plural($bad_word), $words)) {
                $this->word = $bad_word;

                return false;
            }
        }

        return $this->partialWordsPass($target);
    }

    /**
     * Check the partial words - these are words which could appear as they
     * normally are, or in any number of creative derivatives.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function partialWordsPass($value)
    {
        foreach ($this->assembleDerivatives() as $word => $derivatives) {
            foreach ($derivatives as $derivative) {
                if (strpos($target, $derivative) !== false) {
                    $this->word = $word;

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Assemble the derivatives list used in partial word validation.
     *
     * @return array
     */
    protected function assembleDerivatives()
    {
        $bad_words = [];
        foreach (config('defaults.banned_words.partial') as $bad_word) {
            $bad_words[$bad_word] = [
                $bad_word,
                str_plural($bad_word),
                $bad_word[0].str_repeat('*', strlen($bad_word) - 1),
                $bad_word[0].str_repeat('*', strlen($bad_word) - 2).$bad_word[-1],
            ];
        }
        return $bad_words;
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
