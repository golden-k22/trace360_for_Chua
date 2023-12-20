import React from 'react'
import ReactTable from 'react-table-v6'
import 'react-table-v6/react-table.css'


class PoSortableTable extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const columns = [
            {
                Header: 'Recipe',
                accessor: 'recipe', // String-based value accessors!
                // Cell: props => <input type="checkbox" onClick={me.onCheck} />
                // width: 50, // A hardcoded width for the column. This overrides both min and max width options
                // minWidth: 100, // A minimum width for this column. If there is extra room, column will flex to fill available space (up to the max-width, if set)
                maxWidth: undefined, // A maximum width for this column.
                resizable: false,
                style: {
                    color: "#373737",
                }, // Set the style of the `td` element of the column
                // Header & HeaderGroup Options
                headerClassName: 'font-14-bold', // Set the classname of the `th` element of the column
                headerStyle: {
                    color: "#373737"
                },
            },
            {
                Header: 'PO No',
                accessor:'po_no',
                resizable: false,
                style: {
                    color: "#373737",
                }, // Set the style of the `td` element of the column
                // Header & HeaderGroup Options
                headerClassName: 'font-14-bold', // Set the classname of the `th` element of the column
                headerStyle: {
                    color: "#373737"
                },
            }
        ];

        return <ReactTable
            data={this.props.products}
            columns={columns}
            showPagination={false}
            pageSize={this.props.products.length > 10 ? this.props.products.length : 10}
            style={{
                height: "400px" // This will force the table body to overflow and scroll, since there is not enough room
            }}
        />
    }
}

export default PoSortableTable;
