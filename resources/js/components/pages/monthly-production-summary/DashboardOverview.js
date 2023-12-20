import React from "react";
import {createRoot} from 'react-dom/client';
import {RestDataSource} from '../../../service/RestDataSource'
import {Col, Row, Button} from '@themesberg/react-bootstrap';
import '../../scss/dashboard.css';
import "../../scss/monthPickerStyle.css";
import ProdOrdersCard from "./ProdOrdersCard";
import MonthlyGroupedChart from "./MonthlyGroupedChart";
import ButtonGroup from "../ButtonGroup";
import "../../scss/xolo-scss/app.scss";
import Select from 'react-select';


class DashboardOverview extends React.Component {

    constructor(props) {
        super(props);
        this.dataSource = new RestDataSource(process.env.MIX_APP_URL, (err) => console.log(err));
        this.state = {
            outstandingPOs: [],
            completedPOs: [],
            progressProds: [],
            monthOptions:[
                { value: 'chocolate', label: 'Chocolate' },
                { value: 'strawberry', label: 'Strawberry' },
                { value: 'vanilla', label: 'Vanilla' },
            ],
            selectedMonth:{},
            recipeOptions:[],
            selectedRecipe:{},
        }

    };

    componentDidMount() {
        this.refreshCurrentProducts();
        this.interval = setInterval(() => this.refreshCurrentProducts(), 5000);
    }

    convertSecondstoTime(duration, unit){
        var givenMiliSec=duration*1000;

        // if(unit===1){
        //     givenMiliSec=parseInt(duration);
        // }else if (unit === 2) {
        //     givenMiliSec=parseInt(duration)*1000;
        // }else if (unit === 3) {
        //     givenMiliSec=parseInt(duration)*60*1000;
        // }else if (unit === 4) {
        //     givenMiliSec=parseInt(duration)*60*60*1000;
        // }
        var dateObj = new Date(givenMiliSec);
        var hours = dateObj.getUTCDate()*24+dateObj.getUTCHours();
        var minutes = dateObj.getUTCMinutes();
        var seconds = dateObj.getSeconds();

        // var timeString = hours.toString().padStart(2, '0')
        //     + 'hr, ' + minutes.toString().padStart(2, '0')
        //     + 'min, ' + seconds.toString().padStart(2, '0')+'sec';
        return hours.toString()
            + 'hr, ' + minutes.toString()
            + 'min, ' + seconds.toString()+'sec';

    }

    convertQuantityFormat(qty, measure){
        if(qty===-1)
            return "In Progress";
        else if (qty === null) {
            return "0"+measure;
        }else {
            return qty+measure;
        }
    }
    convertTrayCnt(tray_cnt){
        if (tray_cnt === null) {
            return "";
        }else {
            return " / "+tray_cnt+"trays";
        }
    }
    refreshCurrentProducts() {
        this.dataSource.GetRequest("/api/dashboard/v1/current-outstanding-orders?status=3",
            data => {
                this.setState({outstandingPOs: data.dataset});
            });
        this.dataSource.GetRequest("/api/dashboard/v1/completed-orders?status=2",
            data => {
                this.setState({completedPOs: data.dataset});
            });
        this.dataSource.GetRequest("/api/dashboard/v1/progress-orders",
            data => {
                data.dataset.map((orderData, index)=>{
                    orderData.duration=this.convertSecondstoTime(orderData.duration, 2);
                    orderData.quantity=this.convertQuantityFormat(orderData.quantity, orderData.symbol);
                    orderData.tray_cnt=this.convertTrayCnt(orderData.tray_cnt);
                });
                this.setState({progressProds: data.dataset});

            });
    }

    render() {
        return (
            <div className="dashboard-container">

                <Row className="section-container">
                    <Row>
                        <ButtonGroup></ButtonGroup>
                    </Row>
                    <Row className='top-section'>
                        <span className="section-title align-center">Monthly Production Summary</span>
                    </Row>
                </Row>


                <Row className="mb-3  section-content align-right">
                    <Col md={3} className={"d-flex align-items-center"}>
                    <span className={"h6 me-2 mb-0"}>
                            Month
                        </span>
                        <Select
                            className="facility-type-value w-50"
                            defaultValue={this.state.selectedMonth}
                            onChange={(value)=>{console.log(value)}}
                            options={this.state.monthOptions}
                        />
                    </Col>
                    <Col md={3} className={"d-flex align-items-center"}>
                    <span className={"h6 me-2 mb-0"}>
                            Product
                        </span>
                        <Select
                            className="facility-type-value w-50"
                            defaultValue={this.state.selectedMonth}
                            onChange={(value)=>{console.log(value)}}
                            options={this.state.monthOptions}
                        />
                    </Col>
                </Row>

                <div className="container-fluid">
                    <Row>
                        <MonthlyGroupedChart></MonthlyGroupedChart>
                    </Row>
                    <Row>
                        <Col xs={12} sm={12} lg={6} xl={6} className="mb-4">
                            <ProdOrdersCard title="Outstanding PO" products={this.state.outstandingPOs}></ProdOrdersCard>
                        </Col>

                        <Col xs={12} sm={12} lg={6} xl={6} className="mb-4">
                            <ProdOrdersCard title="Completed PO" products={this.state.completedPOs}></ProdOrdersCard>
                        </Col>
                    </Row>
                </div>


            </div>
        );
    }

}

export default DashboardOverview;


const root = createRoot(document.getElementById('monthly-production-summary'));
root.render(<DashboardOverview/>);