import React from "react";
import {createRoot} from 'react-dom/client';
import {RestDataSource} from '../../../service/RestDataSource'
import {Col, Row, Button} from '@themesberg/react-bootstrap';
import '../../scss/dashboard.css';
import "../../scss/monthPickerStyle.css";
import ProdOrdersCard from "./ProdOrdersCard";
import ProgressCard from "./ProgressCard";
import ButtonGroup from "../ButtonGroup";
import "../../scss/xolo-scss/app.scss";

class DashboardOverview extends React.Component {

    constructor(props) {
        super(props);
        this.dataSource = new RestDataSource(process.env.MIX_APP_URL, (err) => console.log(err));
        this.state = {
            outstandingPOs: [],
            completedPOs: [],
            progressProds: [],
        }

    };

    componentDidMount() {
        this.refreshCurrentProducts();
        this.interval = setInterval(() => this.refreshCurrentProducts(), 5000);
    }

    convertSecondstoTime(duration, unit){
        // var givenMiliSec=duration*1000;
        // var dateObj = new Date(givenMiliSec);
        // // var hours = dateObj.getUTCDate()*24+dateObj.getUTCHours();
        // var hours = dateObj.getUTCDate()*24+dateObj.getUTCHours();
        // var minutes = dateObj.getUTCMinutes();
        // var seconds = dateObj.getSeconds();
        //
        // return hours.toString()
        //     + 'hr, ' + minutes.toString()
        //     + 'min, ' + seconds.toString()+'sec';


        var d = Number(duration);
        var h = Math.floor(d / 3600);
        var m = Math.floor(d % 3600 / 60);
        var s = Math.floor(d % 3600 % 60);

        var hDisplay = h > 0 ? h + (h == 1 ? " hr, " : " hrs, ") : "";
        var mDisplay = m > 0 ? m + (m == 1 ? " min, " : " mins, ") : "";
        var sDisplay = s > 0 ? s + (s == 1 ? " sec" : " secs") : "";
        return hDisplay + mDisplay + sDisplay;

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
        if (tray_cnt === -1) {
            return "";
        }else {
            return " / "+tray_cnt+" trays";
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
                // console.log(data);
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
                        <span className="section-title align-center">Current Production Status</span>
                        <span className="section-content align-right padding-right-50 font-14-bold">{
                            new Date().toLocaleString('en-GB', {
                                day: "numeric",
                                month: "long",
                                year: "numeric",
                            }) + " , " +
                            new Date().toLocaleString('en-GB', {
                                hour: "numeric",
                                minute: "numeric",
                                second: "numeric"
                            })
                        }</span>
                    </Row>
                </Row>


                <div className="container-fluid">
                    <Row>
						<Col xs={9} sm={9} lg={9} xl={9}>
							{this.state.progressProds.map((prod, index) => {
									return <Col key={"progress_col" + index} xs={6} sm={6} lg={4} xl={4} className="mb-4">
										<ProgressCard bgColor="#cccccc" products={prod}/>
									</Col>
                            }
                        )}
						</Col>
						<Col xs={3} sm={3} lg={3} xl={3}>
							<Col xs={12} sm={12} lg={12} xl={12} className="mb-4">
								<ProdOrdersCard title="Outstanding PO" products={this.state.outstandingPOs}></ProdOrdersCard>
							</Col>

							<Col xs={12} sm={12} lg={12} xl={12} className="mb-4">
								<ProdOrdersCard title="Completed PO" products={this.state.completedPOs}></ProdOrdersCard>
							</Col>
						</Col>
                    </Row>
                </div>


            </div>
        );
    }

}

export default DashboardOverview;


const root = createRoot(document.getElementById('current-production-status'));
root.render(<DashboardOverview/>);