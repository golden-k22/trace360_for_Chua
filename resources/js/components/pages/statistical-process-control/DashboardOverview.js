import React from "react";
import {createRoot} from 'react-dom/client';
import {RestDataSource} from '../../../service/RestDataSource'
import {Col, Row, Button} from '@themesberg/react-bootstrap';
import '../../scss/dashboard.css';
import './spc.css';
import "../../scss/monthPickerStyle.css";
import ButtonGroup from "../ButtonGroup";
import "../../scss/xolo-scss/app.scss";
import SPCHeader from "./SPCHeader";
import SPCPlotModule from "./SPCPlotModule";
import Preloader from "../../components/Preloader";
import "../../scss/volt.scss";


class DashboardOverview extends React.Component {

    constructor(props) {
        super(props);
        this.dataSource = new RestDataSource(process.env.MIX_APP_URL, (err) => console.log(err));
        this.state = {
            machineOptions: [],
            selectedMachines: [],
            productOptions: [],
            selectedProducts: [],
            sensorOptions: [],
            selectedSensors: [],
            dateRange: [new Date(), new Date()],
            oeeCards: [],
            loaded:false,
        }
    };

    componentDidMount() {
        this.refreshSPCChart();
    }

    refreshSPCChart() {
        let request_data = {
            po_nos:this.state.selectedProducts,
            devices:this.state.selectedMachines,
            sensors:this.state.selectedSensors,
            from: { year: this.state.dateRange[0].getFullYear(), month: this.state.dateRange[0].getMonth(), day: this.state.dateRange[0].getDate() },
            to: { year: this.state.dateRange[1].getFullYear(), month: this.state.dateRange[1].getMonth(), day: this.state.dateRange[1].getDate() }
        };
        console.log(request_data);
        this.dataSource.PostRequest("/api/dashboard/v1/refresh-spc-plot", data => {
            console.log(data);
            this.setState({loaded: false});
            if (data.generated === "True") {
                console.log("generated");
                this.setState({loaded: true})
            }
            this.setState({oeeCards: data.dataset});
        }, request_data);
    }

    // onChangeDevice(recipe_id, po_id, device){
    //     let request_data = {
    //         recipe: {value:recipe_id},
    //         po_nos: [{value:po_id}],
    //         device:device,
    //         from: {
    //             year: this.state.dateRange[0].getFullYear(),
    //             month: this.state.dateRange[0].getMonth(),
    //             day: this.state.dateRange[0].getDate()
    //         },
    //         to: {
    //             year: this.state.dateRange[1].getFullYear(),
    //             month: this.state.dateRange[1].getMonth(),
    //             day: this.state.dateRange[1].getDate()
    //         }
    //     };
    //     this.dataSource.PostRequest("/api/dashboard/v1/overall-equipment-effectiveness", data => {
    //         // this.setState({oeeCards: data.dataset});
    //
    //         // let json_obj = JSON.parse(data.toString());
    //         let copyOeeCards = [...this.state.oeeCards];
    //         for (let index = 0; index < this.state.oeeCards.length; index++) {
    //             let oeeitem = this.state.oeeCards[index];
    //             if (oeeitem.po_id === data.dataset[0].po_id) {
    //                 copyOeeCards[index].start_time = data.dataset[0].start_time;
    //                 copyOeeCards[index].lapse_time = data.dataset[0].lapse_time;
    //                 copyOeeCards[index].current = data.dataset[0].current;
    //                 copyOeeCards[index].cycle_time = data.dataset[0].cycle_time;
    //                 copyOeeCards[index].target = data.dataset[0].target;
    //                 copyOeeCards[index].sec_output = data.dataset[0].sec_output;
    //                 break;
    //             }
    //         }
    //         console.log(data);
    //         this.setState({oeeCards: copyOeeCards});
    //     }, request_data);
    // }


    render() {
        return (
            <div className="dashboard-container">

                <Row className="section-container">
                    <Row>
                        <ButtonGroup></ButtonGroup>
                    </Row>
                </Row>


                <div className="container-fluid">
                    <SPCHeader
                        dateRange={this.state.dateRange}
                        setDateRange={(dateRange) => this.setState({dateRange: dateRange})}
                        machineOptions={this.state.machineOptions} selectedMachines={this.state.selectedMachines}
                        setMachineOptions={(mOptions) => this.setState({machineOptions: mOptions})}
                        setSelectedMachines={(machines) => this.setState({selectedMachines: machines})}
                        productOptions={this.state.productOptions} selectedProducts={this.state.selectedProducts}
                        setProductOptions={(pOptions) => this.setState({productOptions: pOptions})}
                        setSelectedProducts={(products) => this.setState({selectedProducts: products})}
                        sensorOptions={this.state.sensorOptions} selectedSensors={this.state.selectedSensors}
                        setSensorOptions={(sOptions) => this.setState({sensorOptions: sOptions})}
                        setSelectedSensors={(sensors) => this.setState({selectedSensors: sensors})}
                        refreshSPCChart={() => this.refreshSPCChart()}
                        dataSource={this.dataSource}
                    />
                </div>
                <div className="container-fluid">
                    {this.state.loaded == false ?
                        <div className='preloader-container'><Preloader show={this.state.loaded ? false : true}/>
                        </div> :
                        (
                            <SPCPlotModule productOptions={"Test product"}/>
                        )
                    }
                </div>
            </div>
        );
    }

}

export default DashboardOverview;
const root = createRoot(document.getElementById('statistical-process-control'));
root.render(<DashboardOverview/>);