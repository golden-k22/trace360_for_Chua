import React, {useState, useEffect, useRef } from 'react';
import {Button, Col} from "@themesberg/react-bootstrap";

const BottonGroup = (props) => {
    useEffect(()=>{
    },[]);

    const routeChange = (subPath) =>{
        let path = "/admin"+subPath;
        location.href=path;
    };

    return (
        <span className='date-picker-group' style={{margin: "10px", float: "left"}}>
                            <span>
                                <Button className="mb-2 me-2 date-picker-button top-tab-button cps-topbtn-txtsize"
                                        onClick={() => routeChange("/current-production-status")}>
                                    Current Production Status
                                        </Button>
                            </span>
                            <span>
                                <Button className="mb-2 me-2 date-picker-button top-tab-button cps-topbtn-txtsize"
                                        onClick={() => routeChange("/overall-equipment-effectiveness")}>
                                    Overall Equipment Effectiveness (OEE)
                                        </Button>
                            </span>
                            <span>
                                <Button className="mb-2 me-2 date-picker-button top-tab-button cps-topbtn-txtsize"
                                        onClick={() => routeChange("/statistical-process-control")}>
                                    Statistical Process Control (SPC)
                                        </Button>
                            </span>
                            <span>
                                <Button className="mb-2 me-2 date-picker-button top-tab-button cps-topbtn-txtsize"
                                        onClick={() => routeChange("/lsc-utilisation")}>
                                    Batch Process Monitoring
                                        </Button>
                            </span>
                            <span>
                                <Button className="mb-2 me-2 date-picker-button top-tab-button cps-topbtn-txtsize"
                                        onClick={() => routeChange("/monthly-production-summary")}>
                                    Monthly Production Summary
                                        </Button>
                            </span>
                            <span>
                                <Button className="mb-2 me-2 date-picker-button top-tab-button cps-topbtn-txtsize"
                                        onClick={() => routeChange("/single-product-analysis")}>
                                    Single Product Detail
                                        </Button>
                            </span>
                        </span>
    );

};


export default BottonGroup;
