import React, {useState, useEffect, useRef} from 'react';

const ProgressBarIncompleted = (props) => {
    let completedSteps = [];
    let length=props.products.totalSteps-props.products.step_order[props.products.step_order.length-1];
    for (let i = 0; i < length; i++) {
        completedSteps.push(<div key={"step_bar" + props.products.bc_id+i}
                                 className="progress-bar progress-bar-striped bg-light"
                                 role="progressbar"
                                 style={{
                                     width: 100 / props.products.totalSteps + "%",
                                     border: "2px solid lightgray"
                                 }}></div>);
    }
    return (completedSteps);

};


export default ProgressBarIncompleted;
