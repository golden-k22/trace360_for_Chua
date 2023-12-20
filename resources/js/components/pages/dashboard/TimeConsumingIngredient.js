
import React from 'react';
// core styles
import "../../scss/volt.scss";
// vendor styles
import "@fortawesome/fontawesome-free/css/all.css";
import "react-datetime/css/react-datetime.css";
import {RestDataSource} from "../../../service/RestDataSource";
import Preloader from "../../components/Preloader";
import {Col, Row} from "@themesberg/react-bootstrap";

class TimeConsumingIngredient extends React.Component {

    constructor(props) {
        super(props);

        this.dataSource = new RestDataSource(process.env.MIX_APP_URL,
            (err) => console.log(err));

        this.state = {
            loaded: false,
            series: [],
        };
    }


    render() {
        return (

            <div className='react-component-container'>
                {this.state.loaded == false ? <div className='preloader-container'><Preloader show={this.state.loaded ? false : true}/></div> :
                    (
                        <div className='list-container'>
                            <div className='header line-chart-header'>
                                <h4 className='title'>Most Time Consuming Ingredient</h4>
                            </div>
                            {this.state.series.map((data)=>
                                <Row className="list_row" key={data.name}>
                                    <Col xs={6} xl={6} sm={6} md={6} lg={7} >
                                        {data.name}
                                    </Col>
                                    <Col xs={6} xl={6} sm={6} md={6} lg={5} >
                                        {formatTime(data.time_space)}
                                    </Col>
                                </Row>
                            )}
                        </div>

                    )
                }
            </div>

        );
    }

    componentDidMount() {
        this.refreshComponent(this.props);
        // this.interval = setInterval(() => this.refreshComponent(this.props), 50000);
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
        this.dataSource.PostRequest("/api/dashboard/v1/timeconsuming-ingredients",data=>{
            this.setState({series: data.dataset} ,
                () => {this.setState({loaded: true})}
            );
        }, request_data);
    }


}
export default TimeConsumingIngredient;

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
    return formattedMinutes + " min  " + formattedSecs+" sec";
}



