import React, {useState, useEffect, useRef} from 'react';

const ProcessStepsInProgress = (props) => {
    let inProgressSteps = [];

	for (let i = 0; i < props.products.step_order.length; i++) 
	{
		if (props.products.status[i]!== 4 && i!== (props.products.step_order.length - 1 ) )
		{
            inProgressSteps.push(props.products.step_order[i]);
			inProgressSteps.push(", ");
        }else if (props.products.status[i]!== 4 && i== (props.products.step_order.length - 1 ) )
		{
            inProgressSteps.push(props.products.step_order[i]);
        }	
    }
    return (inProgressSteps);
};

export default ProcessStepsInProgress;
