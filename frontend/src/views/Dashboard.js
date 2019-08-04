import React from "react";
import PropTypes from "prop-types";
import { Container, Row, Col, Button } from "shards-react";
import api from "../api";
import mqtt from "../utils/mqtt";

import PageTitle from "../components/common/PageTitle";
import SmallStats from "../components/common/SmallStats";
import TemperatureChart from "../components/dashboard/Temperature";
import HumidityChart from "../components/dashboard/Humidity";

class Dashboard extends React.Component
{
  constructor(props) {
    super(props)

    this.state = {
      smallStats: [
        {
          label: "Livingroom Devices",
          value: "0",
        },
        {
          label: "Bedroom Devices",
          value: "0",
        },
        {
          label: "Kitchen Devices",
          value: "0",
        },
        {
          label: "Bathroom Devices",
          value: "0",
        },
        {
          label: "Members",
          value: "0",
        }
      ]
    }
  }

  componentDidMount() {
    api.get('/dashboard/data').then((res) => {
      this.setState({
        smallStats: res.data
      })
    })
  }

  turnOffAll() {
    api.put('/device/turn-off-all', {
      topic: 'smarthome/devices',
      payload: mqtt.signature('turn-off-all')
    })
  }

  render() {
    return (
      <Container fluid className="main-content-container px-4">
        {/* Page Header */}
        <Row noGutters className="page-header py-4">
          <PageTitle title="Home Overview" subtitle="Dashboard" className="text-sm-left mb-3" />
        </Row>

        {/* Small Stats Blocks */}
        <Row>
          {this.state.smallStats.map((stats, idx) => (
            <Col className="col-lg mb-4" key={idx} {...stats.attrs}>
              <SmallStats
                id={`small-stats-${idx}`}
                variation="1"
                label={stats.label}
                value={stats.value}            
              />
            </Col>
          ))}
        </Row>

        <Row>
          {/* Users Overview */}
          <Col lg="6" md="12" sm="12" className="mb-4">
            <TemperatureChart />
          </Col>
          <Col lg="6" md="12" sm="12" className="mb-4">
            <HumidityChart />
          </Col>
        </Row>
        
        <Row>
          {/* Shortcut */}
          <Col lg="6" md="12" sm="12" className="mb-4">
            <Button outline block theme="dark">
              Chế Độ Tự Động
            </Button>
          </Col>
          <Col lg="6" md="12" sm="12" className="mb-4">
            <Button outline block theme="dark" onClick={this.turnOffAll.bind(this)}>
              Chế Độ Đi Làm
            </Button>
          </Col>
        </Row>
      </Container>
    )
  }
}

Dashboard.propTypes = {
  /**
   * The small stats dataset.
   */
  smallStats: PropTypes.array
};

export default Dashboard;
