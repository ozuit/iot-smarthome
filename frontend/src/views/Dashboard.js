import React from "react";
import PropTypes from "prop-types";
import { Container, Row, Col, Button } from "shards-react";
import api from "../api";

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
    api.put('/node/turn-off-all')
  }

  handleSleepMode() {
    api.put('/node/sleep-mode')
  }

  handleMovieMode() {
    api.put('/node/movie-mode')
  }

  handleBookMode() {
    api.put('/node/book-mode')
  }

  render() {
    return (
      <Container fluid className="main-content-container px-4">
        {/* Page Header */}
        <Row noGutters className="page-header py-4">
          <PageTitle title="Tổng quan" subtitle="Giao diện trang chủ" className="text-sm-left mb-3" />
        </Row>

        {/* Small Stats Blocks */}
        <Row>
          {this.state.smallStats.map((stats, idx) => (
            <Col lg="3" md="12" sm="12" className="col-lg mb-4" key={idx} {...stats.attrs}>
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
          <Col lg="6" md="12" sm="12" className="mb-4">
            <Button outline block theme="dark" onClick={this.handleSleepMode.bind(this)}>
              Chế Độ Đi Ngủ
            </Button>
          </Col>
          <Col lg="6" md="12" sm="12" className="mb-4">
            <Button outline block theme="dark" onClick={this.turnOffAll.bind(this)}>
              Chế Độ Đi Làm
            </Button>
          </Col>
        </Row>
        <Row>
          <Col lg="6" md="12" sm="12" className="mb-4">
            <Button outline block theme="dark" onClick={this.handleMovieMode.bind(this)}>
              Chế Độ Xem Phim
            </Button>
          </Col>
          <Col lg="6" md="12" sm="12" className="mb-4">
            <Button outline block theme="dark" onClick={this.handleBookMode.bind(this)}>
              Chế Độ Đọc Sách
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
