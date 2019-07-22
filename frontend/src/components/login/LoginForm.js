import React from "react";
import {
  ListGroup,
  ListGroupItem,
  Row,
  Col,
  Form,
  FormInput,
  FormGroup,
  Button
} from "shards-react";
import "../../assets/login.css";

const LoginForm = () => (
  <ListGroup flush>
    <ListGroupItem className="p-3">
      <Row>
        <Col>
          <Form>
            <FormGroup>
              <label htmlFor="feEmailAddress">Email</label>
              <FormInput
                id="feEmailAddress"
                type="email"
                placeholder="Email"
              />
            </FormGroup>
            <FormGroup>
              <label htmlFor="fePassword">Password</label>
              <FormInput
                id="fePassword"
                type="password"
                placeholder="Password"
              />
            </FormGroup>

            <Button className="btn-submit" type="submit">Submit</Button>
          </Form>
        </Col>
      </Row>
    </ListGroupItem>
  </ListGroup>
);

export default LoginForm;
