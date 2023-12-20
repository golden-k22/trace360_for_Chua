<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\WorkOrder;

class WorkOrderDimmer extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = WorkOrder::all()->count();
        $string = trans_choice('voyager::dimmer.work_order', $count);
		
        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-download',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.work_order_text', ['count' => $count, 'string' => Str::lower($string)]),			
            'button' => [
                'text' =>  __('voyager::dimmer.work_order_link_text'),
                'link' => route('voyager.work-orders.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/work_order.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Voyager::model('Post'));
    }
}
