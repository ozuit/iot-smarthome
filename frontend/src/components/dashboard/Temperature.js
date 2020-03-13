import React from "react";
import { Card, CardHeader, CardBody } from "shards-react";
import api from "../../api";
import Chart from "../../utils/chart";

let timeout = null

class Temperature extends React.Component {
  constructor(props) {
    super(props);

    this.canvasRef = React.createRef();
  }

  componentDidMount() {
    this.fetchData(this.renderChart([]))
  }

  componentWillUnmount() {
    clearTimeout(timeout)
  }

  fetchData(chart) {
    api.get('/data/temp').then((res) => {
      if (res.data.length > 0) { 
        chart.destroy()
        chart = this.renderChart(res.data)
      }
    })

    const that = this
    timeout = setTimeout(() => {
      that.fetchData(chart)
    }, 3000);
  }

  renderChart(temp_data) {
    const chartOptions = {
      responsive: true,
      legend: {
        position: "top",
        display: false
      },
      elements: {
        line: {
          // A higher value makes the line look skewed at this ratio.
          tension: 0.3
        },
        point: {
          radius: 0
        }
      },
      scales: {
        xAxes: [
          {
            display: false,
            gridLines: false,
            ticks: {
              callback(tick, index) {
                // Jump every 7 values on the X axis labels to avoid clutter.
                return index % 7 !== 0 ? "" : tick;
              }
            }
          }
        ],
        yAxes: [
          {
            ticks: {
              suggestedMax: 45,
              callback(tick) {
                if (tick === 0) {
                  return tick;
                }
                // Format the amounts using Ks for thousands.
                return tick > 999 ? `${(tick / 1000).toFixed(1)}K` : tick;
              }
            }
          }
        ]
      },
      hover: {
        mode: "nearest",
        intersect: false
      },
      tooltips: {
        custom: false,
        mode: "nearest",
        intersect: false
      },
    };

    const ChartOverview = new Chart(this.canvasRef.current, {
      type: "LineWithLine",
      options: chartOptions,
      data: {
        labels: Array.from(new Array(temp_data.length), (_, i) => (i === 0 ? 1 : i)),
        datasets: [
          {
            label: "Temperature",
            fill: "start",
            data: temp_data,
            backgroundColor: "rgba(0,123,255,0.1)",
            borderColor: "rgba(0,123,255,1)",
            pointBackgroundColor: "#ffffff",
            pointHoverBackgroundColor: "rgb(0,123,255)",
            borderWidth: 1.5,
            pointRadius: 0,
            pointHoverRadius: 3
          }
        ]
      }
    });

    // Render the chart.
    ChartOverview.render();
    
    return ChartOverview
  }

  render() {
    return (
      <Card small className="h-80">
        <CardHeader className="border-bottom">
          <h6 className="m-0">Nhiệt độ phòng khách</h6>
        </CardHeader>
        <CardBody className="pt-20">
          <canvas
            height="120"
            ref={this.canvasRef}
            style={{ maxWidth: "100% !important" }}
          />
        </CardBody>
      </Card>
    );
  }
}

export default Temperature;
