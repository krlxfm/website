<?php

namespace KRLX;

class Priority
{
    public $terms = 0;
    public $year = 0;
    public $override = null;
    public $relative_year = 0;

    public function __construct(int $terms = null, int $year = null, int $relative_year = null, string $override = null)
    {
        $this->terms = $terms;
        $this->year = $year;
        $this->override = $override;
        $this->$relative_year = $relative_year ?? date('Y');
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
        if ($this->override) {
            return $this->override;
        }

        $zone = explode(' | ', $this->display())[0];
        $group = '';
        if (strlen($zone) == 1) {
            $group = $this->year - $this->relative_year;
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
        $letters = range('J', 'A');
        if ($this->override) {
            return $this->override[0];
        }

        return $this->terms < count($letters) ? $letters[$this->terms] : 'A';
    }
}
