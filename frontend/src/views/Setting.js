import React from "react";
import { Container, Row, Col } from "shards-react";

import PageTitle from "../components/common/PageTitle";
import UserSetting from "../components/setting/UserSetting";

const UserProfile = () => (
  <Container fluid className="main-content-container px-4">
    <Row noGutters className="page-header py-4">
      <PageTitle title="Thiết lập người dùng" subtitle="Thiết lập" md="12" className="ml-sm-auto mr-sm-auto" />
    </Row>
    <Row>
      <UserSetting />
    </Row>
  </Container>
);

export default UserProfile;
