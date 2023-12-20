import React, {useState, useEffect, useRef} from 'react';
import {Card, CardBody, Col} from "reactstrap";
import {ArrowUp, ArrowDown} from "react-feather";
import ReactApexChart from "react-apexcharts";

const OEEChart = (props) => {
    const options = {
        chart: {
            type: 'bar',
            // height: 192,
            sparkline: {
                enabled: true
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '50%'
            }
        },
        // labels: ["1", "2", "3", "4", "5"],
        colors: ['#476afd'],
        xaxis: {
            crosshairs: {
                width: 1
            },
        },
        tooltip: {
            fixed: {
                enabled: false
            },
            x: {
                show: false
            },
            y: {
                title: {
                    formatter: function (seriesName) {
                        return ''
                    }
                }
            },
            marker: {
                show: false
            }
        }
    };
    const arrowDirection=props.series[0].data[4]>props.series[0].data[3]?'up':
        props.series[0].data[4]<props.series[0].data[3]?'down':'--';
    return (
        <Col className=" text-center" sm="4" lg="4" xl="4">
            <div className="likes-page ps-0 text-center">
                <h4 className="mb-0">{Math.round(props.series[0].data[4])}%
                    <span className={arrowDirection==='up'?'font-success f-12':arrowDirection==='down'?'font-danger f-12':'font-primary f-12'}>
                        {arrowDirection==='up'?<ArrowUp/>:arrowDirection==='down'?<ArrowDown/>:'--'}
                    </span>
                </h4>
            </div>

            <Card className={"card-border m-b-10 m-t-10"}>
                <CardBody className={'p-10'}>
                    <ReactApexChart id="spark3 monthly" options={options} series={props.series} type="bar"
                                    height={120}/>
                </CardBody>
            </Card>
            <div className=" card-no-border">
                <div className="text-center txt-primary"><span className="f-w-700 f-14">{props.label}</span>
                </div>
            </div>
        </Col>
    );
};

export default OEEChart;