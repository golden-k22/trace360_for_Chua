
import React from 'react';
// core styles
import "../../scss/volt.scss";
// vendor styles
import "@fortawesome/fontawesome-free/css/all.css";
import "react-datetime/css/react-datetime.css";
import { RestDataSource } from "../../../service/RestDataSource";
import Preloader from "../../components/Preloader";
import ProgressBar from "@ramonak/react-progress-bar";
import { now } from 'lodash';
class CurrentProgress extends React.Component {

    constructor(props) {
        super(props);

        this.dataSource = new RestDataSource(process.env.MIX_APP_URL ,
            (err) => console.log(err));

        this.state = {
            loaded: true,
            completedValue: 0,
            currentProduction: "XXX",
            estimagedTime:"XXX",

        };
    }


    render() {
        return (

            <div className='react-component-container' >
                {this.state.loaded == false ? <div className='preloader-container'><Preloader show={this.state.loaded ? false : true} /></div> :
                    (
                        <div className='list-container'>
                            <div className='col-sm-4 col-md-3 col-lg-3 col-xl-3'>
                                <div className='header line-chart-header'>
                                    <h4 className='title'>Today's Order Completion</h4>
                                </div>
                                <ProgressBar completed={this.state.completedValue} bgColor='#3bc773' height='30px' width='100%'  labelSize='20px' borderRadius='20px' />
                            </div>
                            <div className='col-md-1 col-lg-1 col-xl-1'></div>
                            <div className='col-sm-4 col-md-3 col-lg-3 col-xl-3'>
                                <div className='header line-chart-header'>
                                    <h4 className='title'>Current Production Recipe</h4>
                                </div>
                                <h5>{this.state.currentProduction}</h5>
                            </div>
                            <div className='col-md-1 col-lg-1 col-xl-1'></div>
                            <div className='col-sm-4 col-md-4 col-lg-4 col-xl-4'>
                                <div className='header line-chart-header'>
                                    <h4 className='title'>Estimated Time to Completion</h4>
                                </div>
                                <h5>{this.state.estimagedTime}</h5>
                            </div>
                        </div>
                    )
                }
            </div>

        );
    }


    componentDidMount() {
        this.refreshComponent();
        this.interval = setInterval(() => this.refreshComponent(), 120000);
    }

    componentWillUnmount() {
        clearInterval(this.interval);
    }


    refreshComponent() {
        let request_data = {year: new Date().getFullYear(), month: new Date().getMonth(), day:new Date().getDate()};
        this.dataSource.PostRequest("/api/dashboard/v1/current-progress",data=>{
            this.setState({completedValue: data.progress});
            this.setState({currentProduction: data.production});
            this.setState({estimagedTime: data.estimated_time},
                () => {this.setState({loaded: true})}
            );
        }, request_data);
    }



}
export default CurrentProgress;

