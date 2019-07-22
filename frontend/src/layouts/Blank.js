import React from "react";
import { Container } from "shards-react";

const DefaultLayout = ({ children }) => (
  <Container fluid>
    {children}
  </Container>
);

export default DefaultLayout;
