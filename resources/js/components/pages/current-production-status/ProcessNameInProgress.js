import React, {useState, useEffect, useRef} from 'react';

const ProcessNameInProgress = (props) => {
    let inProgressSteps = [];
	let stepState = props.products.step_order[0];
	let stepLength = props.products.step_order.length;
    /*for (let i = 0; i < props.products.step_order.length; i++) {*/
        if (props.products.status[0]==4) {
            inProgressSteps.push(
				<p className="f-18 txt-light text-center m-5">{props.products.process_name}</p>
            );
            inProgressSteps.push(
                <p className="f-18 txt-light text-center m-5">{props.products.last_process_name}</p>
            );
        } else {
            inProgressSteps.push(
                <p className="f-18 txt-warning text-center m-5">{props.products.process_name}</p>
            );
            inProgressSteps.push(
                <p className="f-18 txt-warning text-center m-5">{props.products.last_process_name}</p>
            );
        }
    /*}*/
    return (inProgressSteps);
};


export default ProcessNameInProgress;
