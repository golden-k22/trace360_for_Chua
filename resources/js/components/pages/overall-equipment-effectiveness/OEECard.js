import React, {useState, useEffect, useRef} from 'react';
import {Col, Card, CardBody, CardFooter, Label} from "reactstrap";
import Select from 'react-select';
import Ribbon from './Ribbon';
import OEEChart from "./OEEChart";

const OEECard = (props) => {
    const colourStyles = {
        control: (styles, state) => ({
            ...styles,
            // backgroundColor: '#51bb25',
            border: state.isFocused ? 0 : 0,
            // This line disable the blue border
            boxShadow: state.isFocused ? '0 0 0 0.25rem rgb(13 110 253 / 25%)' : 0,
            '&:hover': {
                border: '0 !important',
                boxShadow: '0 0 0 0.25rem rgb(13 110 253 / 25%)',
            },

        }),
        option: (base, state) => ({
            ...base,
            color: '#FFF !important',
            // backgroundColor: state.isSelected ? '#22a7f0' : '#51bb25',
            // ':active': {
            //     backgroundColor: state.isSelected ? '#22a7f0' : '#21d7f0'
            // },
            // ':hover': {
            //     backgroundColor: state.isSelected ? '#22a7f0' : '#21d7f0'
            // },
        }),
        // singleValue: (provided, state) => ({
        //     ...provided,
        //     color: '#fff !important',
        //     ':active': {
        //         border: '0 !important',
        //         boxShadow: '0 0 0 0.25rem rgb(13 110 253 / 25%)'
        //     }
        // })
    };

    const cycle_percent=props.oeeitem.oee_datas.cycle_pct?parseFloat(props.oeeitem.oee_datas.cycle_pct[0]):0;
    const ribbonClass =props.oeeitem.status===2?'ribbon ribbon-clip-right ribbon-right right-important z-index-0 ribbon-secondary':
        cycle_percent <1 ? 'ribbon ribbon-clip-right ribbon-right right-important z-index-0 ribbon-light txt-dark':
        cycle_percent < 110 ? 'ribbon ribbon-clip-right ribbon-right right-important z-index-0 ribbon-success' :
        cycle_percent <= 300 ? 'ribbon ribbon-clip-right ribbon-right right-important z-index-0 ribbon-warning' :
            'ribbon ribbon-clip-right ribbon-right right-important z-index-0 ribbon-danger';
    // ribbon-secondary
    const ribbonTitle =
        props.oeeitem.status===2?'Completed':
        cycle_percent <1 ? 'Waiting':
        cycle_percent < 110 ? 'Running' :
        cycle_percent <= 300 ? 'Slow' :
            'Stop';

    const productClass =props.oeeitem.status===2?'media-right txt-secondary':
        cycle_percent <1 ? 'media-right txt-dark':
        cycle_percent < 110 ? 'media-right txt-success' :
        cycle_percent <= 300 ? 'media-right txt-warning'  :
            'media-right txt-danger';
    return (
        <Col key={props.oeeitem.po_id} sm="14" md="6" lg="6">
            <Card className={'card-border'}>
                <CardBody>
                    <div>
                        <label className="col-form-label form-label">Machines</label>
                        <Select
                            options={props.oeeitem.devices}
                            onChange={(device) => props.onChangeDevice(props.oeeitem.recipe_id, props.oeeitem.po_id, device)}
                            styles={colourStyles}
                            // maxMenuHeight={200}
                            theme={(theme) => ({
                                ...theme,
                                borderRadius: 5,
                                colors: {
                                    ...theme.colors,
                                    /*
                                     * multiValue(remove)/backgroundColor(focused)
                                     * multiValue(remove)/backgroundColor:hover
                                     */
                                    // dangerLight: 'var(--danger-light)',

                                    /*
                                     * control/backgroundColor
                                     * menu/backgroundColor
                                     * option/color(selected)
                                     */
                                    neutral0: '#51bb25',

                                    /*
                                      * control/backgroundColor(disabled)
                                     */
                                    neutral5: 'red',

                                    /*
                                     * control/borderColor(disabled)
                                     * multiValue/backgroundColor
                                     * indicators(separator)/backgroundColor(disabled)
                                     */
                                    neutral10: 'white',

                                    /*
                                     * control/borderColor
                                     * option/color(disabled)
                                     * indicators/color
                                     * indicators(separator)/backgroundColor
                                     * indicators(loading)/color
                                     */
                                    neutral20: 'white',

                                    /*
                                     * control/borderColor(focused)
                                     * control/borderColor:hover
                                     */
                                    neutral30: 'rgb(13 110 253 / 25%)',

                                    /*
                                     * menu(notice)/color
                                     * singleValue/color(disabled)
                                     * indicators/color:hover
                                     */
                                    neutral40: 'white',

                                    /*
                                     * placeholder/color
                                     */
                                    neutral50: 'white',

                                    /*
                                     * indicators/color(focused)
                                     * indicators(loading)/color(focused)
                                     */
                                    neutral60: 'white',

                                    neutral70: 'white',

                                    /*
                                     * input/color
                                     * multiValue(label)/color
                                     * singleValue/color
                                     * indicators/color(focused)
                                     * indicators/color:hover(focused)
                                     */
                                    neutral80: 'white',

                                    neutral90: 'white',

                                    /*
                                     * control/boxShadow(focused)
                                     * control/borderColor(focused)
                                     * control/borderColor:hover(focused)
                                     * option/backgroundColor(selected)
                                     * option/backgroundColor:active(selected)
                                     */
                                    primary: 'rgb(13 110 253)',

                                    /*
                                     * option/backgroundColor(focused)
                                     */
                                    primary25: '#22a7f0',

                                    /*
                                     * option/backgroundColor:active
                                     */
                                    primary50: '#22a7f0',
                                    // primary75: 'var(--primary-75)',
                                },
                            })}
                        />
                    </div>
                    <Card className={'ribbon-wrapper-right m-t-5 m-b-10 no-border'}>
                        <Ribbon
                            className={ribbonClass}>{ribbonTitle}</Ribbon>
                        <CardBody
                            className="shadow shadow-showcase social-widget-card card m-t--40 m-b-10 m-r-15 m-l-15">
                            <div className="d-flex b-b-light">
                                <div className={productClass}>
                                    <h5>{props.oeeitem.recipe_name} ({props.oeeitem.po_id})</h5></div>
                            </div>
                            <Col sm="4" lg="4" xl="4">
                                <div className=" text-center b-r-light">
                                    <span>From {props.oeeitem.start_time}</span>
                                    <h5 className="counter mb-0 mt-0">{props.oeeitem.lapse_time}</h5>
                                </div>
                            </Col>
                            <Col sm="4" lg="4" xl="4">
                                <div className=" text-center b-r-light col">
                                    <span>Current / Target</span>
                                    <h5 className="counter mb-0 mt-0">{props.oeeitem.current} / {props.oeeitem.target}</h5>
                                </div>
                            </Col>
                            <Col sm="4" lg="4" xl="4">
                                <div className=" text-center col">
                                    <span>Secondary Output</span>
                                    <h5 className="counter mb-0 mt-0">{props.oeeitem.sec_output} trays</h5>
                                </div>
                            </Col>
                        </CardBody>
                    </Card>

                    <div className="text-center">
                        <span className="f-16 f-w-600 txt-primary">OEE - </span><span
                        className="f-24 f-w-700 txt-info">{props.oeeitem.oee_overall}%</span>
                    </div>

                    <div className="row m-10">
                        {props.oeeitem.oee_datas.availability?
                            <div>
                                <OEEChart
                                    series={[{
                                        data: props.oeeitem.oee_datas.availability.reverse()
                                    }]}
                                    label={"AVAILABILITY"}
                                />
                                <OEEChart
                                    series={[{
                                        data: props.oeeitem.oee_datas.yield.reverse()
                                    }]}
                                    label={"YIELD"}
                                />
                                <OEEChart
                                    series={[{
                                        data: props.oeeitem.oee_datas.quantity.reverse()
                                    }]}
                                    label={"QUALITY"}
                                />
                            </div>:
                            <div></div>
                        }

                    </div>

                </CardBody>
            </Card>
        </Col>
    );
};

export default OEECard;