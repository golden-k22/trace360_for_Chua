import React from 'react';
import ReactApexChart from 'react-apexcharts';
import ReactDOM from "react-dom";

// core styles
import "../../scss/volt.scss";
import "@fortawesome/fontawesome-free/css/all.css";
import {RestDataSource} from "../../../service/RestDataSource";
import Preloader from "../../components/Preloader";

class OperatorPerformance extends React.Component {
    constructor(props) {
        super(props);
        this.dataSource = new RestDataSource(process.env.MIX_APP_URL ,
            (err) => console.log(err));
        this.state = {
            loaded: false,
            series: [],
            options: {
                chart: {
                    height: this.props.height,
                    type: 'line',
                    zoom: {
                        enabled: true
                    },
                    animations: {
                        enabled: true
                    },
                    dropShadow: {
                        enabled: true,
                        top: 0,
                        left: 0,
                        blur: 3,
                        opacity: 0.5
                    },
                },
                title: {
                    text: 'Operator performance over time',
                    align: 'left',
                    margin: 0,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                        fontSize: '16px',
                        fontWeight: 400,
                        fontFamily:'sans-serif',
                        color: '#5c5c5c'
                    },
                },
                stroke: {
                    // width: [5, 5, 4, 1],
                    width: 3,
                    curve: 'straight'
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: (v) =>formatTime(v)
                    }
                },
                yaxis: {
                    // min: 0.000,
                    // max: 0.010,
                    // tickAmount: 7,
                    labels: {
                        show: true,
                        formatter: (v) =>formatTime(v)
                            // if (v != null)
                            //     return v.toFixed(3);
                    },
                    // axisTicks: {
                    //   show: true
                    // },
                    title: {
                        // text: undefined
                        text: "Time (hh : mm)",
                        style: {
                            fontSize: '14px',
                            fontWeight: 7,
                        }
                    },
                },
            },


        };
    }


    render() {
        return (
            <div className='react-component-container'>
                {this.state.loaded == false ?
                    <div className='preloader-container'><Preloader show={this.state.loaded ? false : true}/></div> :
                    <>
                        {/* <div className='header line-chart-header'>
                            <h4 className='title'>Operator performance over time</h4>
                        </div> */}
                        <div id="chart" className='operator-performance-dashboard'>
                            <ReactApexChart options={this.state.options} series={this.state.series} type="line"
                                            height={this.props.height}/>
                        </div>
                    </>
                }
            </div>
        );
    }

    componentDidMount() {
        this.refreshComponent(this.props);
        // this.interval = setInterval(() => this.refreshComponent(this.props), 70000);
    }

    componentWillUnmount() {
        clearInterval(this.interval);
    }

   
    componentDidUpdate(prevProps) {
        if (this.props.dateRange !== prevProps.dateRange) {
            this.refreshComponent(this.props);
        }
    }


    refreshComponent(requestData) {
        let request_data = {
            from: { year: requestData.dateRange[0].getFullYear(), month: requestData.dateRange[0].getMonth(), day: requestData.dateRange[0].getDate() },
            to: { year: requestData.dateRange[1].getFullYear(), month: requestData.dateRange[1].getMonth(), day: requestData.dateRange[1].getDate() }
        };
        this.dataSource.PostRequest("/api/dashboard/v1/operator-performance",data=>{
            this.setState({series: data.dataset} ,
                () => {this.setState({loaded: true})}
            );
        }, request_data);
    }


}


function formatTime(initialSecs) {
    var mins = Math.floor(initialSecs / (60));
    var divisor_for_secs = initialSecs % (60);
    var secs = Math.ceil(divisor_for_secs);

    let formattedMinutes = mins.toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });

    let formattedSecs = secs.toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });
    return formattedMinutes + ":" + formattedSecs;
}

export default OperatorPerformance;
