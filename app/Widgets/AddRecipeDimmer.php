<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class AddRecipeDimmer extends AbstractWidget
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
            'icon'   => 'voyager-plus',
            'title'  => __('voyager::dimmer.add_recipe'),
            'text'   => __('voyager::dimmer.add_recipe_text'),			
            'button' => [
                'text' =>  __('voyager::dimmer.add_recipe_link_text'),
                'link' => route('voyager.recipes.create'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/add_recipe.jpg'),
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
