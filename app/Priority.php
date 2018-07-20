<?php

namespace KRLX;

class Priority
{
    public $terms = 0;
    public $year = 0;

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
