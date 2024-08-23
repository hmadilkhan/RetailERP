<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',[auth()->user()->role_id]);
        $array = [];

        foreach ($pageid as $value)
        {
            array_push($array,$value->page_id);
        }

        $result = DB::table('pages_details')->whereIN('id',$array)->get();

        return view('components.sidebar',compact('result'));
    }
}
