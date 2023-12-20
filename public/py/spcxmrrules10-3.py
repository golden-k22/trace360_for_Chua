import pandas as pd
import plotly.graph_objects as go
from plotly.subplots import make_subplots
import psycopg2 as pdb
import sys

USER = "postgres"
PASSWORD = "postgres"
DB_NAME = "trace360"
HOST_NAME = "localhost"
PORT = 5432


def plotSPC(devices, products, sensors, j_from, j_to):
    try:

        psql_con = pdb.connect(database=DB_NAME, user=USER, password=PASSWORD, host=HOST_NAME, port=PORT)
        cur = psql_con.cursor()
        cur.execute(
            "INSERT INTO utilizations  (type, tenant_id, to_address, content, created_at) VALUES(%s, %s, %s, %s, %s)",
            ('email', alarm_record['user_id'], to_mail, alarm_record['message'], cur_time))
        cur.execute("SELECT ppd.id, to_char(ppd.timestamp, \'HH24:MI:SS\') as timestamp, ppd.value1 FROM public.product_process_data as ppd "
                    "inner join public.physical_devices pd on pd.id = ppd.fr_deviceid "
                    "inner join public.production_orders po on po.id = ppd.fr_poid "
                    "inner join public.batch_conts bc on bc.id = ppd.fr_bcid "
                    "inner join public.recipes r on r.id = bc.fr_recipe_id "
                    "inner join public.sensors s on s.id = ppd.f_sensor_id "
                    "where 'timestamp' between %s::timestamp and %s::timestamp"
                    , (gateway_imei,))

        # $available_pdds = DB::table('public.product_process_data as ppd')->selectRaw(
        #     'ppd.id, to_char(ppd.timestamp, \'HH24:MI:SS\') as timestamp, ppd.value1')
        # ->leftJoin('public.physical_devices as pd', 'pd.id', '=', 'ppd.fr_deviceid')
        # ->leftJoin('production_orders as po', 'po.id', '=', 'ppd.fr_poid')
        # ->leftJoin('public.batch_conts as bc', 'bc.id', '=', 'ppd.fr_bcid')
        # ->leftJoin('public.recipes as r', 'r.id', '=', 'bc.fr_recipe_id')
        # ->leftJoin('public.sensors as s', 's.id', '=', 'ppd.f_sensor_id')
        # ->whereBetween('ppd.timestamp', [$date_start, $date_end]);


        gateway = cur.fetchone()
        psql_con.commit()
        psql_con.close()
    except pdb.DatabaseError as e:
        with psql_con:
            psql_con.close()


    # Create a DataFrame
    data = pd.DataFrame({"Timestamp": timestamps, "Value": values})
    data["Timestamp"] = pd.to_datetime(data["Timestamp"])

    # Sort data by timestamp
    data = data.sort_values("Timestamp")

    # Calculate Moving Average, Upper Control Limit (UCL), Lower Control Limit (LCL), Sigma values
    target = 380.00
    vmaxl = target * 1.1
    vminl = target * 0.9
    data["Moving Average"] = data["Value"].expanding().mean()
    data["Sigma"] = data["Value"].expanding().std()
    data["Sigma2"] = data["Sigma"] * 2
    data["Sigma3"] = data["Sigma"] * 3
    data["UCL"] = data["Moving Average"] + data["Sigma3"]
    data["+2 Sigma"] = data["Moving Average"] + data["Sigma2"]
    data["+1 Sigma"] = data["Moving Average"] + data["Sigma"]
    data["-1 Sigma"] = data["Moving Average"] - data["Sigma"]
    data["-2 Sigma"] = data["Moving Average"] - data["Sigma2"]
    data["LCL"] = data["Moving Average"] - data["Sigma3"]

    # Determine out-of-control points for X-Chart
    out_of_control_points_x = data[(data['Value'] > data['UCL']) | (data['Value'] < data['LCL'])]

    # Determine out-of-control points for X-Chart for Rule 2
    rule2_points = []

    for i in range(2, len(data)):
        if (
            (data.iloc[i - 2]['Value'] > data.iloc[i - 2]['UCL'] and data.iloc[i - 1]['Value'] > data.iloc[i - 1][
                'UCL'] and data.iloc[i]['Value'] > data.iloc[i]['UCL']) or
            (data.iloc[i - 2]['Value'] < data.iloc[i - 2]['LCL'] and data.iloc[i - 1]['Value'] < data.iloc[i - 1][
                'LCL'] and data.iloc[i]['Value'] < data.iloc[i]['LCL'])
        ):
            rule2_points.append(data.index[i])

    # Determine out-of-control points for X-Chart for Rule 3
    rule3_points = []

    for i in range(4, len(data)):
        if (
            (data.iloc[i - 4]['Value'] > data.iloc[i - 4]['UCL'] and data.iloc[i - 3]['Value'] > data.iloc[i - 3][
                'UCL'] and
             data.iloc[i - 2]['Value'] > data.iloc[i - 2]['UCL'] and data.iloc[i - 1]['Value'] > data.iloc[i - 1][
                 'UCL'] and
             data.iloc[i]['Value'] > data.iloc[i]['UCL']) or
            (data.iloc[i - 4]['Value'] < data.iloc[i - 4]['LCL'] and data.iloc[i - 3]['Value'] < data.iloc[i - 3][
                'LCL'] and
             data.iloc[i - 2]['Value'] < data.iloc[i - 2]['LCL'] and data.iloc[i - 1]['Value'] < data.iloc[i - 1][
                 'LCL'] and
             data.iloc[i]['Value'] < data.iloc[i]['LCL'])
        ):
            rule3_points.append(data.index[i])

    # Determine out-of-control points for X-Chart for Rule 4
    rule4_points = []

    for i in range(8, len(data)):
        if all(
            (data.iloc[i - j]['Value'] > data.iloc[i - j]['UCL'] for j in range(9)) or
            (data.iloc[i - j]['Value'] < data.iloc[i - j]['LCL'] for j in range(9))
        ):
            rule4_points.append(data.index[i])

    # Determine out-of-control points for X-Chart for Rule 5
    rule5_points = []

    for i in range(5, len(data)):
        if all(
            (data.iloc[i - j]['Value'] > data.iloc[i - j - 1]['Value'] for j in range(5)) or
            (data.iloc[i - j]['Value'] < data.iloc[i - j - 1]['Value'] for j in range(5))
        ):
            rule5_points.append(data.index[i])

    # Determine out-of-control points for X-Chart for Rule 6
    rule6_points = []

    for i in range(13, len(data)):
        if all(
            (data.iloc[i - 2 * j]['Value'] > data.iloc[i - 2 * j - 1]['Value'] for j in range(7)) and
            (data.iloc[i - 2 * j - 1]['Value'] < data.iloc[i - 2 * j - 2]['Value'] for j in range(7))
        ) or all(
            (data.iloc[i - 2 * j]['Value'] < data.iloc[i - 2 * j - 1]['Value'] for j in range(7)) and
            (data.iloc[i - 2 * j - 1]['Value'] > data.iloc[i - 2 * j - 2]['Value'] for j in range(7))
        ):
            rule6_points.append(data.index[i])

    # Determine out-of-control points for X-Chart for Rule 7
    rule7_points = []

    for i in range(13, len(data)):
        if all(
            (data.iloc[i - 2 * j]['Value'] > data.iloc[i - 2 * j - 1]['Value'] for j in range(7)) and
            (data.iloc[i - 2 * j - 1]['Value'] < data.iloc[i - 2 * j - 2]['Value'] for j in range(7)) or
            (data.iloc[i - 2 * j]['Value'] < data.iloc[i - 2 * j - 1]['Value'] for j in range(7)) and
            (data.iloc[i - 2 * j - 1]['Value'] > data.iloc[i - 2 * j - 2]['Value'] for j in range(7))
        ):
            rule7_points.append(data.index[i])

    # Determine out-of-control points for X-Chart for Rule 8
    rule8_points = []

    for i in range(8, len(data)):
        if all(
            (data.iloc[i - j]['Value'] > data.iloc[i - j - 1]['Value'] for j in range(7)) and
            (data.iloc[i - j - 1]['Value'] < data.iloc[i - j - 2]['Value'] for j in range(7)) or
            (data.iloc[i - j]['Value'] < data.iloc[i - j - 1]['Value'] for j in range(7)) and
            (data.iloc[i - j - 1]['Value'] > data.iloc[i - j - 2]['Value'] for j in range(7))
        ):
            rule8_points.append(data.index[i])

    # Create X-Chart and MR-Chart subplots
    fig = make_subplots(rows=2, cols=1, shared_xaxes=True, vertical_spacing=0.1,
                        subplot_titles=("X-Chart", "MR-Chart", "Summary"))

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["Value"], mode='lines+markers', name='Values'), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["Value"],
        mode='lines+markers',
        name='Values',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["Moving Average"], mode='lines', name='Moving Average', line=dict(dash='dash')), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["Moving Average"],
        mode='lines',
        name='Moving Average',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
        line=dict(dash='dash'),
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["UCL"], mode='lines', name='UCL', line=dict(color='red')), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["UCL"],
        mode='lines',
        name='UCL',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["+2 Sigma"], mode='lines', name='+2 Sigma', line=dict(dash='dot')), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["+2 Sigma"],
        mode='lines',
        name='+2 Sigma',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
        line=dict(dash='dot'),
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["+1 Sigma"], mode='lines', name='+1 Sigma', line=dict(dash='dashdot')), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["+1 Sigma"],
        mode='lines',
        name='+1 Sigma',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
        line=dict(dash='dashdot'),
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["-1 Sigma"], mode='lines', name='-1 Sigma', line=dict(dash='dashdot')), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["-1 Sigma"],
        mode='lines',
        name='-1 Sigma',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
        line=dict(dash='dashdot'),
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["-2 Sigma"], mode='lines', name='-2 Sigma', line=dict(dash='dot')), row=1, col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["-2 Sigma"],
        mode='lines',
        name='-2 Sigma',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
        line=dict(dash='dot'),
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    fig.add_trace(
        go.Scatter(x=data["Timestamp"], y=data["LCL"], mode='lines', name='LCL', line=dict(color='darkorange')), row=1,
        col=1)
    x_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["LCL"],
        mode='lines',
        name='LCL',
        hovertemplate='Time: %{x|%H:%M:%S}, value: %{y:.2f}',
    )
    fig.add_trace(x_chart_trace, row=1, col=1)

    # Mark out-of-control points on X-Chart
    fig.add_trace(go.Scatter(x=out_of_control_points_x["Timestamp"], y=out_of_control_points_x["Value"], mode='markers',
                             name='R1', marker=dict(color='red', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 2
    fig.add_trace(
        go.Scatter(x=data.loc[rule2_points]["Timestamp"], y=data.loc[rule2_points]["Value"] - 20, mode='markers',
                   name='R2', marker=dict(symbol='triangle-up', color='orange', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 3
    fig.add_trace(
        go.Scatter(x=data.loc[rule3_points]["Timestamp"], y=data.loc[rule3_points]["Value"] - 35, mode='markers',
                   name='R3', marker=dict(symbol='triangle-down', color='purple', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 4
    fig.add_trace(
        go.Scatter(x=data.loc[rule4_points]["Timestamp"], y=data.loc[rule4_points]["Value"] - 35, mode='markers',
                   name='R4', marker=dict(symbol='hexagon', color='brown', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 5
    fig.add_trace(
        go.Scatter(x=data.loc[rule5_points]["Timestamp"], y=data.loc[rule5_points]["Value"] - 50, mode='markers',
                   name='R5', marker=dict(symbol='star', color='darkgreen', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 6
    fig.add_trace(
        go.Scatter(x=data.loc[rule6_points]["Timestamp"], y=data.loc[rule6_points]["Value"] - 50, mode='markers',
                   name='R6', marker=dict(symbol='cross', color='indigo', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 7
    fig.add_trace(
        go.Scatter(x=data.loc[rule7_points]["Timestamp"], y=data.loc[rule7_points]["Value"] - 65, mode='markers',
                   name='R7', marker=dict(symbol='diamond', color='orange', size=8)), row=1, col=1)

    # Mark out-of-control points on X-Chart for Rule 8
    fig.add_trace(
        go.Scatter(x=data.loc[rule8_points]["Timestamp"], y=data.loc[rule8_points]["Value"] - 65, mode='markers',
                   name='R8', marker=dict(symbol='square', color='darkcyan', size=8)), row=1, col=1)

    # Plot MR-Chart

    data["mrMoving Average"] = data["Value"].diff().abs().rolling(window=2).mean().dropna()
    data["mrSigma"] = data["Value"].expanding().std()
    data["mrSigma2"] = data["mrSigma"] * 2
    data["mrSigma3"] = data["mrSigma"] * 3
    data["mrUCL"] = data["mrMoving Average"] + data["mrSigma3"]
    data["mr+2 Sigma"] = data["mrMoving Average"] + data["mrSigma2"]
    data["mr+1 Sigma"] = data["mrMoving Average"] + data["mrSigma"]
    data["mr-1 Sigma"] = data["mrMoving Average"] - data["mrSigma"]
    data["mr-2 Sigma"] = data["mrMoving Average"] - data["mrSigma2"]
    data["mrLCL"] = data["mrMoving Average"] - data["mrSigma3"]

    # Create MR-Chart
    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mrMoving Average"], mode='lines', name='mR Moving Average', line=dict(color='red', dash='dash')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mrMoving Average"],
        mode='lines',
        name='mR Moving Average',
        line=dict(dash='dash'),
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mrUCL"], mode='lines', name='UCL', line=dict(color='red')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mrUCL"],
        mode='lines',
        name='UCL',
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mr+2 Sigma"], mode='lines', name='+2 Sigma', line=dict(dash='dot')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mr+2 Sigma"],
        mode='lines',
        name='+2 Sigma',
        line=dict(dash='dot'),
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mr+1 Sigma"], mode='lines', name='+1 Sigma', line=dict(dash='dashdot')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mr+1 Sigma"],
        mode='lines',
        name='+1 Sigma',
        line=dict(dash='dashdot'),
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mr-1 Sigma"], mode='lines', name='-1 Sigma', line=dict(dash='dashdot')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mr-1 Sigma"],
        mode='lines',
        name='-1 Sigma',
        line=dict(dash='dashdot'),
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mr-2 Sigma"], mode='lines', name='-2 Sigma', line=dict(dash='dot')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mr-2 Sigma"],
        mode='lines',
        name='-2 Sigma',
        line=dict(dash='dot'),
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # fig.add_trace(go.Scatter(x=data["Timestamp"], y=data["mrLCL"], mode='lines', name='LCL', line=dict(color='darkorange')), row=2, col=1)
    mr_chart_trace = go.Scatter(
        x=data["Timestamp"],
        y=data["mrLCL"],
        mode='lines',
        name='LCL',
        hovertemplate='Time: %{x|%H:%M:%S} : %{y:.2f}',
    )
    fig.add_trace(mr_chart_trace, row=2, col=1)

    # Calculate Mean and CPK
    mean_value = data["Value"].mean()
    sigma_value = data["Sigma"].iloc[-1]  # Use the last calculated sigma value
    cpk_value = min((mean_value - data["LCL"].iloc[-1]) / (3 * sigma_value),
                    (data["UCL"].iloc[-1] - mean_value) / (3 * sigma_value))

    # Add annotations for Mean and CPK
    fig.add_annotation(
        xref='paper', yref='paper',
        x=0.5, y=-0.07,
        text=f'Mean: {mean_value:.2f}--CPK: {cpk_value:.2f}',
        showarrow=False,
        font=dict(size=14),
    )

    # Customize layouts
    fig.update_layout(
        # title=dict(text="Combined Chart with Out-of-Control Points for Rules 1-8", x=0.5),
        xaxis=dict(title="Timestamps"),
        yaxis=dict(title="Value"),
        legend=dict(font=dict(size=10), orientation="h", yanchor="top", y=-0.1, xanchor="center", x=0.5)
    )

    fig.write_html('./py/plot.html', full_html=False, include_plotlyjs=True, div_id='plotly-spc-chart')


if __name__ == '__main__':
    if len(sys.argv) < 6:
        print("please pass the arguments...")
    else:
        devices = eval(sys.argv[1])
        print(devices[0])
        # str1 = str(timestamps.replace(' ]', '').replace('[ ', ''))
        # timestamps = str1.split(" , ")
        products = eval(sys.argv[2])
        sensors = eval(sys.argv[3])
        j_from = sys.argv[4]
        j_to = sys.argv[5]
        # plotSPC(devices, products, sensors, j_from, j_to)
