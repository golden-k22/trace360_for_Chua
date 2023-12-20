import React, {useState, useEffect, useRef} from 'react';
import "../../scss/cardStyle.css";

const ProdOrdersCard = (props) => {

    return (
        <div>
            <div class="card-with-border total-users-lists card">
                <div class="card-no-border card-header">
                    <h5>{props.title}</h5>
                </div>
                <div class="p-0 card-body" style={{padding: "1px"}}>
                    <div class="users-total table-responsive theme-scrollbar">
                        <table class="table table-bordernone table">
                            <thead>
                            <tr>
                                <th>Recipes</th>
                                <th>PO NO</th>
                            </tr>
                            </thead>
                            <tbody>

                            {props.products.map((prod, index) => {
                                    return <tr key={"product_order" + index} xs={6} sm={6} lg={4} xl={4} className="mb-4">
                                        <td>
                                            <div className="d-flex align-items-center align-middle">
                                                <div className="d-inline-block">
                                                    <span className="cps-card-proorder-text">{prod.recipe}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span className="cps-card-proorder-text">{prod.po_no}</span>
                                        </td>
                                    </tr>
                                }
                            )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    );

};


export default ProdOrdersCard;
