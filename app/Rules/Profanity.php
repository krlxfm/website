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
        $target = preg_replace('/[@#\$%\^0-9]/', '*', strtolower($value));
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
        $bad_words = $this->assembleDerivatives();
        foreach ($bad_words as $word => $derivatives) {
            if (! $this->singleWordDerivativesPass($word, $derivatives, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a single word's derivatives show up.
     *
     * @param  string  $word
     * @param  array  $derivatives
     * @param  string  $value
     * @return bool
     */
    private function singleWordDerivativesPass($word, $derivatives, $value)
    {
        foreach ($derivatives as $derivative) {
            if (stripos($value, $derivative) !== false) {
                $this->word = $word;

                return false;
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

            for ($i = 0; $i < strlen($bad_word); $i++) {
                $test_word = substr($bad_word, 0);
                $test_word[$i] = '*';
                $bad_words[$bad_word][] = $test_word;
            }
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
