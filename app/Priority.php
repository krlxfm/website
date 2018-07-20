<?php

namespace KRLX;

class Priority
{
    protected $terms;
    protected $year;

    function __construct()
    {
        $this->terms = 0;
        $this->year = 0;
    }

    /**
     * Get the number of terms this priority corresponds to.
     *
     * @return int
     */
    public function terms()
    {
        return $this->terms;
    }

    /**
     * Validates that a new term number is a non-negative integer, and sets the
     * priority's number of terms if validation passes.
     *
     * @param  int  $terms
     * @return $this
     */
    public function setTerms(int $terms)
    {
        if($terms >= 0) {
            $this->terms = $terms;
        }
        return $this;
    }

    /**
     * Get the year value this priority corresponds to.
     *
     * @return int
     */
    public function year()
    {
        return $this->year;
    }

    /**
     * Validates that a new year is a non-negative integer, and is at most 5
     * larger than the current year. If validation passes, set the year.
     *
     * @param  int  $year
     * @return $this
     */
    public function setYear(int $year)
    {
        $maxYear = date('Y') + 5;
        if($year >= 0 and $year <= $maxYear) {
            $this->year = $year;
        }
        return $this;
    }

    /**
     * Get the string representation of this priority.
     *
     * @return string
     */
    public function display()
    {
        $terms = '';
        if ($this->terms >= count(config('defaults.priority.terms'))) {
            $terms = config('defaults.priority.default');
        } else {
            $terms = config('defaults.priority.terms')[$this->terms];
        }

        $year = '';
        if ($this->year >= count(config('defaults.status_codes'))) {
            $year = $this->year;
        } else {
            $year = config('defaults.status_codes')[$this->year];
        }

        return $terms.' | '.$year;
    }

    /**
     * Get the HTML representation of this priority.
     *
     * @return string
     */
    public function html()
    {
        return str_replace(' | ', ' &bull; ', $this->display());
    }
}
