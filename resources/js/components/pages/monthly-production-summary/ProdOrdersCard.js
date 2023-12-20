import React, {useState, useEffect, useRef} from 'react';
import {Card, CardBody, CardHeader} from "react-simple-card";
import "../../scss/cardStyle.css";
import PoSortableTable from "./PoSortableTable";
import {Col} from "@themesberg/react-bootstrap";
import ProgressCard from "./ProgressCard";

const ProdOrdersCard = (props) => {

    return (
        <div>
            <div class="card-with-border total-users-lists card">
                <div class="card-no-border card-header">
                    <h5>{props.title}</h5>
                </div>
                <div class="p-0 card-body">
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
                                                <img className="img-50 align-top m-r-15 b-r-10"
                                                     src="https://www.simplyrecipes.com/thmb/B95yQ4LLNAKz135C19FK1IWxTfQ=/750x0/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/__opt__aboutcom__coeus__resources__content_migration__simply_recipes__uploads__2018__10__HT-Make-an-Omelet-LEAD-VERTICAL-812f32afcf76474681217c82b654b6e9.jpg"
                                                     alt=""/>
                                                <div className="d-inline-block">
                                                    <a href={"/admin/recipes/"+prod.fr_rec_id}><span
                                                        className="f-18">{prod.recipe}</span></a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span className="f-18">{prod.po_no}</span>
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
