<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
    public $rows;
    public $columns;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($columns, $rows = null, $sort = null)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->sort = $sort;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.data-table');
    }
}
