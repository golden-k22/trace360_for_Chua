import React from 'react';
import ReactApexChart from 'react-apexcharts';
import {faBorderStyle} from '@fortawesome/free-solid-svg-icons';
import ReactDOM from "react-dom";

// core styles
import "../../scss/volt.scss";
import "@fortawesome/fontawesome-free/css/all.css";
import {RestDataSource} from "../../../service/RestDataSource";
import Preloader from "../../components/Preloader";


class RecipeTrend extends React.Component {

    constructor(props) {
        super(props);
        this.dataSource = new RestDataSource(process.env.MIX_APP_URL ,
            (err) => console.log(err));
        this.state = {
            loaded: false,
            series: [],

            options: {
                chart: {
                    type: 'bar',
                    height: 430,
                    dropShadow: {
                        enabled: true,
                        top: 0,
                        left: 0,
                        blur: 3,
                        opacity: 0.5
                    },
                },
                title: {
                    text: 'Recipe Trend over the Months',
                    align: 'left',
                    margin: 0,
                    offsetX: 10,
                    offsetY: 0,
                    floating: false,
                    style: {
                        fontSize: '18px',
                        fontWeight: 400,
                        fontFamily:'sans-serif',
                        color: '#5c5c5c'
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
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
                    followCursor: true,
                    y: {}
                },
                legend: {
                    position: 'right',
                    offsetY: this.props.height / 3
                },
                xaxis: {
                    categories: [],
                },
                yaxis: {
                    axisTicks: {
                        show: true
                    },

                    labels: {
                        show: true,
                        maxWidth: "auto",
                    },

                },

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
                {this.state.loaded == false ?
                    <div className='preloader-container'><Preloader show={this.state.loaded ? false : true}/></div> :
                    <>
                        {/* <div className='header line-chart-header'>
                            <h4 className='title'>Recipe Trend over the Months</h4>
                        </div> */}
                        <div id="chart">
                            <ReactApexChart options={this.state.options} series={this.state.series} type="bar"
                                            height={this.props.height}/>
                        </div>
                    </>
                }
            </div>

        );
    }


    componentDidMount() {
        this.refreshComponent(this.props);
        // this.interval = setInterval(() => this.refreshComponent(this.props), 90000);
    }

    componentWillUnmount() {
        clearInterval(this.interval);
    }

    // componentWillReceiveProps(nextProps){
    //     this.refreshComponent(nextProps);
    // }
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

        this.dataSource.PostRequest("/api/dashboard/v1/recipe-trend",data => {
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


export default RecipeTrend;
