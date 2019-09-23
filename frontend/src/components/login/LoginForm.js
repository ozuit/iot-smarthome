import React from "react";
import {
  ListGroup,
  ListGroupItem,
  Row,
  Col,
  Form,
  FormInput,
  FormGroup,
  Button,
  Alert
} from "shards-react";
import "../../assets/login.css";
import api from "../../api";
import { storeToken, getRole } from "../../utils/auth";
import md5 from "md5";

class LoginForm extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      username: '',
      password: '',
      showAlert: false,
    }

    document.getElementById('voice-control').style.display = 'none'
  }

  handleSubmit(e) {
    e.preventDefault();

    api.post('/user/login', 
      {
        email: this.state.username,
        password: this.state.password,
        device_id: md5(this.state.username + Math.floor(Math.random() * 100000000))
      }
    ).then(res => {
      if (!res.status) {
        this.setState({
          showAlert: true
        })
      } else {
        storeToken(res.token)
        if (getRole().indexOf('ADMIN') !== -1) {
          window.location.href = '/dashboard';
        }
      }
    }).catch(() => {
      
    })
  }

  render() {
    return (
      <ListGroup flush>
        <Alert className="mb-3" open={this.state.showAlert} theme="warning">
          Login Fail!
        </Alert>
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
                    value={this.state.username}
                    onChange={(e) => this.setState({username: e.target.value})}
                  />
                </FormGroup>
                <FormGroup>
                  <label htmlFor="fePassword">Password</label>
                  <FormInput
                    id="fePassword"
                    type="password"
                    placeholder="Password"
                    value={this.state.password}
                    onChange={(e) => this.setState({password: e.target.value})}
                  />
                </FormGroup>

                <Button className="btn-submit" type="submit" onClick={(e) => this.handleSubmit(e)}>Submit</Button>
              </Form>
            </Col>
          </Row>
        </ListGroupItem>
      </ListGroup>
    )
  }
};

export default LoginForm;
