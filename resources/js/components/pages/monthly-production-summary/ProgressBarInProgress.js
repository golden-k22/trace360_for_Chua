import React, {useState, useEffect, useRef} from 'react';

const ProgressBarInProgress = (props) => {
    let inProgressSteps = [];
    for (let i = 0; i < props.products.step_order.length; i++) {
        inProgressSteps.push(<div key={"inprogress_bar" + props.products.bc_id+i}
                                 className="progress-bar-animated progress-bar-striped bg-warning"
                                 role="progressbar"
                                 style={{
                                     width: 100 / props.products.totalSteps + "%",
                                     border: "2px solid darkorange"
                                 }}></div>);
}
    return (inProgressSteps);

};


export default ProgressBarInProgress;
