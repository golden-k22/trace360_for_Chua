import React, {useState, useEffect, useRef} from 'react';
import { Fragment } from "react";

const Ribbon = (props) => {
    return (
        <Fragment>
            <div {...props}>{props.children}</div>
        </Fragment>
    );
};

export default Ribbon;
