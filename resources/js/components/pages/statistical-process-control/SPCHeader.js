import React, {useState, useEffect, useRef} from 'react';
import {FormGroup, Input, Label, Row} from "reactstrap";
import DateRangePicker from '@wojtekmaj/react-daterange-picker';
import Select from 'react-select';


const SPCHeader = (props) => {
    const [machineOptions, setMachineOptions] = useState(props.machineOptions);
    const [productOptions, setProductOptions] = useState(props.productOptions);
    const [sensorOptions, setSensorOptions] = useState(props.sensorOptions);
    const [noOptionsMessage, setNoOptionsMessage] = useState("No options");
    const [enable, setEnable] = useState(false);

    // useEffect(() => {
    //     if (props.selectedProducts.length === 3) {
    //         setProductOptions([]);
    //         setNoOptionsMessage("Available up to 3");
    //     } else {
    //         setProductOptions(props.productOptions);
    //         setNoOptionsMessage("No options");
    //     }
    // }, []);

    // useEffect(()=>{
    //     let request_data = {
    //         po_nos:props.selectedProducts,
    //         devices:props.selectedMachines,
    //         sensors:props.selectedSensors,
    //         from: { year: props.dateRange[0].getFullYear(), month: props.dateRange[0].getMonth(), day: props.dateRange[0].getDate() },
    //         to: { year: props.dateRange[1].getFullYear(), month: props.dateRange[1].getMonth(), day: props.dateRange[1].getDate() }
    //     };
    //     props.dataSource.PostRequest("/api/dashboard/v1/spc-header-datas", data => {
    //         props.setProductOptions(data.dataset);
    //         setProductOptions(data.dataset);
    //     }, request_data);
    // }, [props.selectedProducts]);

    useEffect(()=>{
        let request_data = {
            po_nos:props.selectedProducts,
            devices:props.selectedMachines,
            sensors:props.selectedSensors,
            from: { year: props.dateRange[0].getFullYear(), month: props.dateRange[0].getMonth(), day: props.dateRange[0].getDate() },
            to: { year: props.dateRange[1].getFullYear(), month: props.dateRange[1].getMonth(), day: props.dateRange[1].getDate() }
        };
        props.dataSource.PostRequest("/api/dashboard/v1/spc-header-datas", data => {
            console.log(data.dataset.sensors);
            props.setSelectedProducts([]);
            props.setProductOptions(data.dataset.products);
            setProductOptions(data.dataset.products);
            props.setSelectedMachines([]);
            props.setMachineOptions(data.dataset.devices);
            setMachineOptions(data.dataset.devices);
            props.setSelectedSensors([]);
            props.setSensorOptions(data.dataset.sensors);
            setSensorOptions(data.dataset.sensors);``
        }, request_data);

    }, [props.dateRange]);

    function filterSPC() {
        props.refreshSPCChart();
        console.log("filter button clicked.")
    }

    function resetFilterOptions() {
        props.setSelectedMachines([]);
        props.setSelectedSensors([]);
        props.setSelectedProducts([]);
        props.setDateRange([new Date(), new Date()]);
    }

    return (

        <div className="container-fluid">
            <div className="page-header">
                <div className="row">
                    <div className="col-md-3 section-title">Statistical Process Control</div>
                    <div className=" text-center col-md-9">
                        <div className="row">

                            <div className="col-md-3 mb-3">
                                <div className="text-end"><Label className="form-label">Date</Label></div>
                                <div className="theme-form">
                                    <DateRangePicker
                                        calendarAriaLabel="Toggle calendar"
                                        clearAriaLabel="Clear value"
                                        rangeDivider="~"
                                        dayAriaLabel="Day"
                                        monthAriaLabel="Month"
                                        nativeInputAriaLabel="Date"
                                        clearIcon={null}
                                        onChange={(value) => props.setDateRange(value)}
                                        value={props.dateRange}
                                        yearAriaLabel="Year"
                                    />
                                </div>
                            </div>
                            <div className="col-md-2 mb-3">
                                <div className="text-end"><Label className="mb-2 form-label">Machine</Label></div>
                                <Select
                                    className={props.className ? props.className : ""}
                                    value={props.selectedMachines}
                                    onChange={(machines) => props.setSelectedMachines(machines)}
                                    options={machineOptions}
                                    noOptionsMessage={() => noOptionsMessage}
                                    isMulti
                                />
                            </div>
                            <div className="col-md-2 mb-3">
                                <div className="text-end"><Label className="form-label">Production Order</Label>
                                </div>
                                <Select
                                    options={productOptions}
                                    className={props.className ? props.className : ""}
                                    value={props.selectedProducts}
                                    onChange={(products) => props.setSelectedProducts(products)}
                                    noOptionsMessage={() => noOptionsMessage}
                                    // isDisabled={enable}
                                    isMulti
                                />
                            </div>
                            <div className="col-md-2 mb-3">
                                <div className="text-end"><Label className="form-label">Sensor</Label>
                                </div>
                                <Select
                                    options={sensorOptions}
                                    className={props.className ? props.className : ""}
                                    value={props.selectedSensors}
                                    onChange={(sensors) => props.setSelectedSensors(sensors)}
                                    noOptionsMessage={() => noOptionsMessage}
                                    // isDisabled={enable}
                                    isMulti
                                />
                            </div>
                            <div className="col-md-1 mb-3 text-center">
                                <div><Label className="form-label">Filter</Label></div>
                                <button className="btn btn-secondary btn-sm filter-btn" onClick={filterSPC}>Filter
                                </button>
                            </div>
                            <div className="col-md-2 mb-3 text-center">
                                <div><Label className="form-label">Reset Filters</Label></div>
                                <button
                                    ref={element => {
                                        if (element) element.style.setProperty('border-color', '#dddddd', 'important');
                                    }}
                                    onClick={resetFilterOptions}
                                    className="btn btn-light btn-sm text-black-color reset-btn">Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SPCHeader;
