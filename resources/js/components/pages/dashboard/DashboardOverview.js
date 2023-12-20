import React from "react";
import {createRoot} from 'react-dom/client';
import { Col, Row, Button } from '@themesberg/react-bootstrap';
import WeighingDuration from "./WeighingDuration";
import RecipeTrend from "./RecipeTrend";
import OperatorPerformance from "./OperatorPerformance";
import EventSummary from "./EventSummary";
import OutstandingOrders from "./OutstandingOrders";
import TimeConsumingIngredient from "./TimeConsumingIngredient";
import DateRangePicker from '@wojtekmaj/react-daterange-picker';

import "../../scss/monthPickerStyle.css";
import CurrentProgress from "./CurrentProgress";

class DashboardOverview extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            dateRange1: [this.getDateFrom(2), new Date()],
            dateRange2: [this.getDateFrom(2), new Date()],
            dateRange3: [this.getDateFrom(2), new Date()],
            height: 400
        };
    }

    /**Get the date of {agoMonth} ago from now. */
    getDateFrom(agoMonth) {
        let curDate = new Date();
        curDate.setMonth(curDate.getMonth() - agoMonth);
        return curDate;
    }

    render() {
        return (
            <div className="dashboard-container">

                <Row className="section-container">
                    <Row className='top-section'>
                        <span className="section-title">Current Status</span>
                    </Row>
                    <Row className="mb-4 current-progress-wrapper">
                        <CurrentProgress></CurrentProgress>
                    </Row>
                    <Row className='top-section'>
                        <span className='date-picker-group'>
                            <span>
                                <Button variant="primary" className="mb-2 me-2 date-picker-button"
                                    onClick={() => this.setState({ dateRange1: [this.getDateFrom(1), new Date()] })}>
                                    Last 1 Month
                                        </Button>
                            </span>
                            <span>
                                <Button variant="primary" className="mb-2 me-2 date-picker-button"
                                    onClick={() => this.setState({ dateRange1: [this.getDateFrom(3), new Date()] })}>
                                    Last 3 Months
                                        </Button>
                            </span>
                            <span>
                                <DateRangePicker
                                    calendarAriaLabel="Toggle calendar"
                                    clearAriaLabel="Clear value"
                                    rangeDivider="~"
                                    dayAriaLabel="Day"
                                    monthAriaLabel="Month"
                                    nativeInputAriaLabel="Date"
                                    clearIcon={null}
                                    onChange={(value) => this.setState({ dateRange1: value })}
                                    value={this.state.dateRange1}
                                    yearAriaLabel="Year"
                                />
                            </span>
                        </span>
                    </Row>
                    <Col xs={12} xl={4} lg={5} className="mb-4">
                        <OutstandingOrders dateRange={this.state.dateRange1} />
                        <TimeConsumingIngredient dateRange={this.state.dateRange1} />
                    </Col>
                    <Col xs={12} xl={8} lg={7} className="mb-4">
                        <Row>
                            <EventSummary height="600" dateRange={this.state.dateRange1} />
                        </Row>
                    </Col>
                </Row>
                <Row className="section-container">
                    <Row className='top-section'>
                        <span className="section-title">Overall Production Trend</span>
                        <span className='date-picker-group'>
                            <span>
                                <Button variant="primary" className="mb-2 me-2 date-picker-button"
                                    onClick={() => this.setState({ dateRange2: [this.getDateFrom(1), new Date()] })}>
                                    Last 1 Month
                                        </Button>
                            </span>
                            <span>
                                <Button variant="primary" className="mb-2 me-2 date-picker-button"
                                    onClick={() => this.setState({ dateRange2: [this.getDateFrom(3), new Date()] })}>
                                    Last 3 Months
                                        </Button>
                            </span>
                            <span>
                                <DateRangePicker
                                    calendarAriaLabel="Toggle calendar"
                                    clearAriaLabel="Clear value"
                                    rangeDivider="~"
                                    dayAriaLabel="Day"
                                    monthAriaLabel="Month"
                                    nativeInputAriaLabel="Date"
                                    clearIcon={null}
                                    onChange={(value) => this.setState({ dateRange2: value })}
                                    value={this.state.dateRange2}
                                    yearAriaLabel="Year"
                                />
                            </span>
                        </span>
                    </Row>
                    <Col xs={12} xl={8} className="mb-4">
                        <RecipeTrend height={this.state.height} dateRange={this.state.dateRange2} />
                    </Col>
                </Row>
                <Row className="section-container">
                    <Row className='top-section'>
                        <span className="section-title">Operator performance</span>
                        <span className='date-picker-group'>
                            <span>
                                <Button variant="primary" className="mb-2 me-2 date-picker-button"
                                    onClick={() => this.setState({ dateRange3: [this.getDateFrom(1), new Date()] })}>
                                    Last 1 Month
                                        </Button>
                            </span>
                            <span>
                                <Button variant="primary" className="mb-2 me-2 date-picker-button"
                                    onClick={() => this.setState({ dateRange3: [this.getDateFrom(3), new Date()] })}>
                                    Last 3 Months
                                        </Button>
                            </span>
                            <span>
                                <DateRangePicker
                                    calendarAriaLabel="Toggle calendar"
                                    clearAriaLabel="Clear value"
                                    rangeDivider="~"
                                    dayAriaLabel="Day"
                                    monthAriaLabel="Month"
                                    nativeInputAriaLabel="Date"
                                    clearIcon={null}
                                    onChange={(value) => this.setState({ dateRange3: value })}
                                    value={this.state.dateRange3}
                                    yearAriaLabel="Year"
                                />
                            </span>
                        </span>
                    </Row>
                    <Col xs={12} lg={6} className="mb-4">
                        <WeighingDuration height={this.state.height} dateRange={this.state.dateRange3} />
                    </Col>
                    <Col xs={12} lg={6} className="mb-4">
                        <OperatorPerformance height={this.state.height} dateRange={this.state.dateRange3} />
                    </Col>
                </Row>
            </div>
        );
    }

}

export default DashboardOverview;

if (document.getElementById('dashboard-overview')) {
    const root = createRoot(document.getElementById('dashboard-overview'));
    root.render(<DashboardOverview/>);
}


