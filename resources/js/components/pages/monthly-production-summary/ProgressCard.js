import React, {useState, useEffect, useRef} from 'react';
import {Col} from "@themesberg/react-bootstrap";
import ProgressBarCompleted from "./ProgressBarCompleted";
import ProgressBarIncompleted from "./ProgressBarIncompleted";
import ProgressBarInProgress from "./ProgressBarInProgress";

const ProgressCard = (props) => {

    return (
        <div className=" investments card-with-border card shadow shadow-showcase">
            <div className="card-no-border card-header typography" style={{height: "300px"}}>
                <p className="f-16 txt-primary">Start:
                    <small>{
                        new Date(props.products.start_time).toLocaleString('en-GB', {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                        }) + " , " +
                        new Date(props.products.start_time).toLocaleString('en-GB', {
                            hour: "numeric",
                            minute: "numeric",
                            second: "numeric"
                        })
                    }</small>
                </p>
                <h4>{props.products.recipe_name}</h4>
                <div className="progress-showcase">
                    <div className="col">
                        <div className="progress" style={{}}>
                            <ProgressBarCompleted products={props.products}/>
                            <ProgressBarInProgress products={props.products}/>

                            {/*<div className="progress-bar-animated progress-bar-striped bg-warning" role="progressbar"*/}
                                 {/*style={{width: 100 / props.products.totalSteps + "%", border: "2px solid darkorange"}}></div>*/}
                            <ProgressBarIncompleted products={props.products}/>
                        </div>
                    </div>
                </div>
                <div style={{height: "90px"}}>
                    {/*<p className="f-18 txt-warning text-center m-15">{props.products.process_name + " : " + props.products.proc_cat_name}</p>*/}
                    <p className="f-18 txt-warning text-center m-15">{props.products.process_name}</p>
                    <p className="f-18 txt-warning text-center m-15">{props.products.last_process_name}</p>
                </div>

                <p className="f-16 txt-primary text-center m-15">Step {props.products.step_order[0]}-{props.products.step_order[props.products.step_order.length - 1]}
                    <small>Total Step: {props.products.totalSteps}</small>
                </p>
            </div>
            <div className="card-footer" style={{padding: "0"}}>
                <ul className="flex-row simple-list list-group">
                    <li className="text-center list-group-item"><span className="f-18">Step Duration</span><h6
                        className="f-w-600 mb-0">{props.products.duration}</h6></li>
                    <li className="text-center list-group-item"><span className="f-18">Quantity</span><h6
                        className="f-w-600 mb-0">{props.products.quantity}{props.products.tray_cnt}</h6></li>
                </ul>
            </div>
        </div>

    );

};


export default ProgressCard;
