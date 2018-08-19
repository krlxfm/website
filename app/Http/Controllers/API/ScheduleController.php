<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    /**
     * Save changes to the working show times.
     *
     * @param  KRLX\Show  $show
     * @return KRLX\Show
     */
    public function update(Request $request, Show $show)
    {
        $request->validate([
            'day' => ['sometimes', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start' => ['sometimes', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
            'end' => ['sometimes', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
        ]);
    }
}
