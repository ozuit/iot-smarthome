import React from "react";
import PropTypes from "prop-types";
import { Container, Row, Col, Button } from "shards-react";

import PageTitle from "../components/common/PageTitle";
import SmallStats from "../components/common/SmallStats";
import TemperatureOverview from "../components/room/TemperatureOverview";

const Dashboard = ({ smallStats }) => (
  <Container fluid className="main-content-container px-4">
    {/* Page Header */}
    <Row noGutters className="page-header py-4">
      <PageTitle title="Home Overview" subtitle="Dashboard" className="text-sm-left mb-3" />
    </Row>

    {/* Small Stats Blocks */}
    <Row>
      {smallStats.map((stats, idx) => (
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
      <Col lg="12" md="12" sm="12" className="mb-4">
        <TemperatureOverview />
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
        <Button outline block theme="dark">
          Chế Độ Thư Giãn
        </Button>
      </Col>
      <Col lg="6" md="12" sm="12" className="mb-4">
        <Button outline block theme="dark">
          Chế Độ Đi Ngủ
        </Button>
      </Col>
      <Col lg="6" md="12" sm="12" className="mb-4">
        <Button outline block theme="dark">
          Chế Độ Đi LàmLàm
        </Button>
      </Col>
    </Row>
  </Container>
);

Dashboard.propTypes = {
  /**
   * The small stats dataset.
   */
  smallStats: PropTypes.array
};

Dashboard.defaultProps = {
  smallStats: [
    {
      label: "Livingroom Devices",
      value: "6",
      attrs: { md: "6", sm: "6" },
    },
    {
      label: "Bedroom Devices",
      value: "2",
      attrs: { md: "6", sm: "6" },
    },
    {
      label: "Kitchen Devices",
      value: "2",
      attrs: { md: "4", sm: "6" },
    },
    {
      label: "Bathroom Devices",
      value: "1",
      attrs: { md: "4", sm: "6" },
    },
    {
      label: "Members",
      value: "4",
      attrs: { md: "4", sm: "6" },
    }
  ]
};

export default Dashboard;
