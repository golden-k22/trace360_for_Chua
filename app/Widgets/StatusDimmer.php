<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class StatusDimmer extends AbstractWidget
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
        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-dashboard',
            'title'  => __('voyager::dimmer.status_panels'),
            'text'   => __('voyager::dimmer.status_panels_text'),			
            'button' => [
                'text' =>  __('voyager::dimmer.status_panels_link_text'),
                'link' => route('dashboards.overview'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/status.jpg'),
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
