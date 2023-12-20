import React, {useState, useEffect, useRef} from 'react';

const ProgressBarCompleted = (props) => {
    let completedSteps = [];
    for (let i = 1; i < props.products.step_order[0]; i++) {
        completedSteps.push(<div key={"step_bar" + props.products.bc_id+i}
                                 className="progress-bar progress-bar-striped bg-success"
                                 role="progressbar"
                                 style={{
                                     width: 100 / props.products.totalSteps + "%",
                                     border: "2px solid palegreen"
                                 }}></div>);
    }
    return (completedSteps);

};


export default ProgressBarCompleted;
