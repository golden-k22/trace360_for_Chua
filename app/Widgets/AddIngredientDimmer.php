<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class AddIngredientDimmer extends AbstractWidget
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
            'title'  => __('voyager::dimmer.add_ingredient'),
            'text'   => __('voyager::dimmer.add_ingredient_text'),			
            'button' => [
                'text' =>  __('voyager::dimmer.add_ingredient_link_text'),
                'link' => route('voyager.ingredients.create'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/add_ing.jpg'),
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
