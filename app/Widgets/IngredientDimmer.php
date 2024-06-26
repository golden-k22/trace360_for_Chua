<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\Ingredient;

class IngredientDimmer extends AbstractWidget
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
        $count = Ingredient::all()->count();
        $string = trans_choice('voyager::dimmer.ingredient', $count);
		
        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-lab',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.ingredient_text', ['count' => $count, 'string' => Str::lower($string)]),			
            'button' => [
                'text' =>  __('voyager::dimmer.ingredient_link_text'),
                'link' => route('voyager.ingredients.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/ingredients.jpg'),
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
