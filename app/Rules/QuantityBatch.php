<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class QuantityBatch implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $rQuantity;
    private $rBatch;

    public function __construct($quantity,$batch)
    {
        //
        $this->rQuantity = $quantity;
        $this->rBatch = $batch;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
	if(rQuantity == 1 && rBatch != 1)
	{
		$this->error = 'Quantity is 1, Batch setting must set to 1-Normal.';
	}
	else if (rQuantity > 1 && rBatch == 1)
	{
		$this->error = 'Quantity is more than 1, Batch setting must not set to 1-Normal.';
	}
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Error: {$this->error}";
    }
}
