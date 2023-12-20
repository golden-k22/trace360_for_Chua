
import React from 'react';
import ReactDOM from "react-dom";
// core styles
import "../../scss/volt.scss";
// vendor styles
import "@fortawesome/fontawesome-free/css/all.css";
import "react-datetime/css/react-datetime.css";
import {RestDataSource} from "../../../service/RestDataSource";
import Preloader from "../../components/Preloader";
import {Row} from "@themesberg/react-bootstrap";

class OutstandingOrders extends React.Component {

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
                                <h4 className='title'>Outstanding Orders</h4>
                            </div>
                            {this.state.series.map((recipe)=><Row key={recipe.id} className="list_row_outstanding">{recipe.name}</Row>)}
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
        this.dataSource.PostRequest("/api/dashboard/v1/outstanding-orders",data=>{
            this.setState({series: data.dataset} ,
                () => {this.setState({loaded: true})},
            );
        }, request_data);
    }



}
export default OutstandingOrders;


