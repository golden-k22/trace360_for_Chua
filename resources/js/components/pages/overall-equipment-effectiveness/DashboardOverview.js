import React from "react";
import {createRoot} from 'react-dom/client';
import {RestDataSource} from '../../../service/RestDataSource'
import {Col, Row, Button} from '@themesberg/react-bootstrap';
import '../../scss/dashboard.css';
import './custom-oee-style.css';
import "../../scss/monthPickerStyle.css";
import ButtonGroup from "../ButtonGroup";
import "../../scss/xolo-scss/app.scss";
import OEEHeader from "./OEEHeader";
import OEECard from "./OEECard";
import ProgressCard from "../current-production-status/ProgressCard";


class DashboardOverview extends React.Component {

    constructor(props) {
        super(props);
        this.dataSource = new RestDataSource(process.env.MIX_APP_URL, (err) => console.log(err));
        this.state = {
            recipeOptions: [],
            selectedRecipe: null,
            productOptions: [],
            selectedProducts: [],
            dateRange: [new Date(), new Date()],
            oeeCards: []
        }
    };

    componentDidMount() {
        this.refreshRecipes();
        this.refreshOEECards();
    }

    refreshRecipes() {
        this.dataSource.GetRequest("/api/dashboard/v1/recipes",
            data => {
                this.setState({recipeOptions: data.dataset});
            });

    }

    refreshOEECards() {
        let request_data = {
            recipe: this.state.selectedRecipe,
            po_nos: this.state.selectedProducts,
            device:null,
            from: {
                year: this.state.dateRange[0].getFullYear(),
                month: this.state.dateRange[0].getMonth(),
                day: this.state.dateRange[0].getDate()
            },
            to: {
                year: this.state.dateRange[1].getFullYear(),
                month: this.state.dateRange[1].getMonth(),
                day: this.state.dateRange[1].getDate()
            }
        };
        this.dataSource.PostRequest("/api/dashboard/v1/overall-equipment-effectiveness", data => {
            this.setState({oeeCards: data.dataset});
            console.log(data);
        }, request_data);
    }
    onChangeDevice(recipe_id, po_id, device){
        let request_data = {
            recipe: {value:recipe_id},
            po_nos: [{value:po_id}],
            device:device,
            from: {
                year: this.state.dateRange[0].getFullYear(),
                month: this.state.dateRange[0].getMonth(),
                day: this.state.dateRange[0].getDate()
            },
            to: {
                year: this.state.dateRange[1].getFullYear(),
                month: this.state.dateRange[1].getMonth(),
                day: this.state.dateRange[1].getDate()
            }
        };
        this.dataSource.PostRequest("/api/dashboard/v1/overall-equipment-effectiveness", data => {
            // this.setState({oeeCards: data.dataset});

            // let json_obj = JSON.parse(data.toString());
            let copyOeeCards = [...this.state.oeeCards];
            for (let index = 0; index < this.state.oeeCards.length; index++) {
                let oeeitem = this.state.oeeCards[index];
                if (oeeitem.po_id === data.dataset[0].po_id) {
                    copyOeeCards[index].start_time = data.dataset[0].start_time;
                    copyOeeCards[index].lapse_time = data.dataset[0].lapse_time;
                    copyOeeCards[index].current = data.dataset[0].current;
                    copyOeeCards[index].cycle_time = data.dataset[0].cycle_time;
                    copyOeeCards[index].target = data.dataset[0].target;
                    copyOeeCards[index].sec_output = data.dataset[0].sec_output;
                    copyOeeCards[index].status = data.dataset[0].status;
                    break;
                }
            }
            console.log(data);
            this.setState({oeeCards: copyOeeCards});
        }, request_data);
    }


    render() {
        return (
            <div className="dashboard-container">

                <Row className="section-container">
                    <Row>
                        <ButtonGroup></ButtonGroup>
                    </Row>
                </Row>


                <div className="container-fluid">
                    <OEEHeader
                        dateRange={this.state.dateRange}
                        setDateRange={(dateRange) => this.setState({dateRange: dateRange})}
                        recipeOptions={this.state.recipeOptions} selectedRecipe={this.state.selectedRecipe}
                        setSelectedRecipe={(recipe) => this.setState({selectedRecipe: recipe})}
                        productOptions={this.state.productOptions} selectedProducts={this.state.selectedProducts}
                        setProductOptions={(pOptions) => this.setState({productOptions: pOptions})}
                        setSelectedProducts={(products) => this.setState({selectedProducts: products})}
                        refreshOEECards={() => this.refreshOEECards()}
                        dataSource={this.dataSource}
                    />
                </div>
                <div className="container-fluid">
                    <div className="row">
                        {this.state.oeeCards.map((oeeitem, index) => {
                                return <OEECard key={"oee_cards" + index} oeeitem={oeeitem} onChangeDevice={(recipe_id, po_id, device)=>this.onChangeDevice(recipe_id, po_id, device)}/>
                            }
                        )}
                    </div>
                </div>
            </div>
        );
    }

}

export default DashboardOverview;
const root = createRoot(document.getElementById('overall-equipment-effectiveness'));
root.render(<DashboardOverview/>);