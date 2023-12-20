import React, {useState, useEffect, useRef} from 'react';
import ProgressBarCompleted from "./ProgressBarCompleted";
import ProgressBarIncompleted from "./ProgressBarIncompleted";
import ProgressBarInProgress from "./ProgressBarInProgress";
import ProcessNameInProgress from "./ProcessNameInProgress";
import ProcessStepsInProgress from "./ProcessStepsInProgress";

const ProgressCard = (props) => {

    return (
        <div className=" investments card-with-border card shadow shadow-showcase cps-card-margin-btm">
            <div className="card-no-border card-header typography cps-card-padding">
                <p className="cps-card-date txt-primary">Start:
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
                <p className="cps-card-resipe-title">{props.products.recipe_name} &#40;{props.products.po_id}&#41;</p>
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
                <div className="cps-card-pname-progress">
                    {/*<p className="f-18 txt-warning text-center m-15">{props.products.process_name + " : " + props.products.proc_cat_name}</p>*/}
                    <ProcessNameInProgress products={props.products}/>
                </div>

                <p className="txt-primary text-center cps-card-pname-stepnow">Step now <ProcessStepsInProgress products={props.products}/>
                    <small>Total Step: {props.products.totalSteps}</small>
                </p>
            </div>
            <div className="card-footer" style={{padding: "0"}}>
                <ul className="flex-row simple-list list-group">
                    <li className="text-center list-group-item" style={{padding: "10px"}}>
						<span className="cps-card-stepdur-title">Step Duration</span>
						<h6 className="cps-card-stepdur-text">{props.products.duration}</h6>
					</li>
                    <li className="text-center list-group-item" style={{padding: "10px"}}>
						<span className="cps-card-qlty-title">Quantity</span>
						<h6 className="cps-card-qlty-text">{props.products.quantity}{props.products.tray_cnt}</h6>
					</li>
                </ul>
            </div>
        </div>

    );

};


export default ProgressCard;
