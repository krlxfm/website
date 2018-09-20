<?php

namespace KRLX;

class Priority
{
    public $terms = 0;
    public $year = 0;
    public $relative_year = 0;

    public function __construct(int $terms = null, int $year = null, int $rel_year = null)
    {
        $this->terms = $terms;
        $this->year = $year;
        $this->relative_year = $rel_year;
    }

    /**
     * Get the string representation of this priority.
     *
     * @return string
     */
    public function display()
    {
        $terms = '';
        if (($this->terms ?? 0) >= count(config('defaults.priority.terms'))) {
            $terms = config('defaults.priority.default');
        } else {
            $terms = config('defaults.priority.terms')[($this->terms ?? 0)];
        }

        $year = '';
        if (($this->year ?? 0) >= count(config('defaults.status_codes'))) {
            $year = $this->year;
        } else {
            $year = config('defaults.status_codes')[($this->year ?? 0)];
        }

        return $terms.' | '.$year;
    }

    /**
     * Get the short code representation of this priority.
     *
     * @return string
     */
    public function code()
    {
        $zone = $this->zone();
        $group = '';
        if (strlen($zone) == 1) {
            if ($this->year < 1000) {
                return config('defaults.priority.default');
            } elseif ($zone == 'A') {
                $group = 12 - $this->terms;
            } else {
                $group = $this->year - $this->relative_year;
            }
            if ($group < 0) {
                $group = 0;
            }
        }

        return $zone.$group;
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

    /**
     * Get the zone letter corresponding to this priority's terms.
     *
     * @return string
     */
    public function zone()
    {
        if ($this->year < 1000 and $this->year > 0) {
            return 'A';
        }

        $letters = range('J', 'A');

        return $this->terms < count($letters) ? $letters[$this->terms] : 'A';
    }
}
