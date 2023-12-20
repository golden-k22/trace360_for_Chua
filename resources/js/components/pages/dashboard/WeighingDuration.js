import {faBorderStyle} from '@fortawesome/free-solid-svg-icons';
import React from 'react';
import ReactApexChart from 'react-apexcharts';
import ReactDOM from "react-dom";
// core styles
import "../../scss/volt.scss";
// vendor styles
import "@fortawesome/fontawesome-free/css/all.css";
import "react-datetime/css/react-datetime.css";
import {RestDataSource} from "../../../service/RestDataSource";
import Preloader from "../../components/Preloader";

class WeighingDuration extends React.Component {

    constructor(props) {
        super(props);

        this.dataSource = new RestDataSource(process.env.MIX_APP_URL,
            (err) => console.log(err));

        this.state = {
            loaded: false,
            series: [],
            options: {
                chart: {
                    type: 'bar',
                    dropShadow: {
                        enabled: true,
                        top: 0,
                        left: 0,
                        blur: 3,
                        opacity: 0.5
                    },
                },
                title: {
                    text: 'Average Weighing Duration for each Recipe',
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
                plotOptions: {
                    bar: {
                        horizontal: false,
                        //   barHeight: '60%',
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: false,
                    offsetX: -5,
                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: (v) => formatTime(v)
                    }
                },
                // legend: {
                //     position: 'right',
                //     offsetY: this.props.height/3
                // },
                xaxis: {
                    categories: []
                },
                yaxis: {
                    // reversed: true,
                    axisTicks: {
                        show: true
                    },
                    title: {
                        // text: undefined
                        text: "Time (hh : mm)",
                        style: {
                            fontSize: '14px',
                            fontWeight: 7,
                        }
                    },

                    labels: {
                        show: true,
                        formatter: (v) => formatTime(v)
                    }
                },
                // title: {
                //   text: 'Custom DataLabels',
                //   align: 'center',
                //   floating: true
                // },
                // subtitle: {
                //   text: 'Category Names as DataLabels inside bars',
                //   align: 'center',
                // },

                grid: {
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                },

                // annotations: {
                //     xaxis: [{
                //         x: 20,
                //         borderColor: '#00E396',
                //         label: {
                //             borderColor: '#00E396',
                //             style: {
                //                 color: '#fff',
                //                 background: '#00E396',
                //             },
                //             text: 'X annotation',
                //         }
                //     }],
                //     yaxis: [{
                //         y: 2004,
                //         y2: 2006,
                //         //   y2: "september",
                //         label: {
                //             borderColor: '#00E396',
                //             style: {
                //                 color: '#fff',
                //                 background: '#00E396',
                //             },
                //             text: 'Y annotation'
                //         }
                //     }]
                // },


                //   fill: {
                //     type: 'pattern',
                //     opacity: 1,
                //     pattern: {
                //       style: ['circles', 'slantedLines', 'verticalLines', 'horizontalLines'], // string or array of strings
                //     }
                //   },

                states: {
                    hover: {
                        filter: faBorderStyle
                    }
                },
            },


        };
    }


    render() {
        return (

            <div className='react-component-container'>
                {this.state.loaded == false ? <div className='preloader-container'><Preloader show={this.state.loaded ? false : true}/></div> :
                    (
                        <>
                            {/* <div className='header line-chart-header'>
                                <h4 className='title'>Average Weighing Duration for each Recipe</h4>
                            </div> */}
                            <div id="chart" className="weighing-duration-dashboard">
                                <ReactApexChart options={this.state.options} series={this.state.series} type="bar"
                                                height={this.props.height}/>
                            </div>
                        </>

                    )
                }
            </div>

        );
    }



    componentDidMount() {
        this.refreshComponent(this.props);
        // this.interval = setInterval(() => this.refreshComponent(this.props), 110000);
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

        this.dataSource.PostRequest("/api/dashboard/v1/weighing-duration", data => {
            this.setState({ series: data.dataset });
            this.setState({
                options: {
                    ...this.state.options, xaxis: { ...this.state.options.xaxis, categories: data.categories }
                }
            }, () => {
                this.setState({ loaded: true })
            });
        }, request_data);
    }


}

function formatTime(mins) {
    var hours = Math.floor(mins / (60));
    var divisor_for_minutes = mins % (60);
    var minutes = Math.ceil(divisor_for_minutes);

    let formattedHours = hours.toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });
    let formattedMinutes = minutes.toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });

    return formattedHours + ":" + formattedMinutes;
}

export default WeighingDuration;
