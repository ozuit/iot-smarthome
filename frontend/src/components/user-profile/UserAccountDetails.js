import React from "react";
import PropTypes from "prop-types";
import {
  Card,
  CardHeader,
  ListGroup,
  ListGroupItem,
  Row,
  Col,
  Form,
  FormGroup,
  FormInput,
  FormTextarea,
  Button
} from "shards-react";
import api from "../../api";
import { getUserID } from "../../utils/auth";

class UserAccountDetails extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      user: {},
    }
  }

  componentWillMount() {
    api.get("/user/" + getUserID()).then((res) => {
      this.setState({
        user: res.data
      })
    })
  }

  handleUpdate() {
    let { user } = this.state;
    api.put("/user/" + getUserID(), user).then((res) => {
      if(res.status) {
        alert('Cập nhật thành công!!')
      }
    })
  }

  render() {
    let {user} = this.state;

    return (
    <Card small className="mb-4">
      <CardHeader className="border-bottom">
        <h6 className="m-0">{this.props.title}</h6>
      </CardHeader>
      <ListGroup flush>
        <ListGroupItem className="p-3">
          <Row>
            <Col>
              <Form>
                <Row form>
                  {/* Fullname */}
                  <Col md="6" className="form-group">
                    <label htmlFor="feFullname">Fullname</label>
                    <FormInput
                      id="feFullname"
                      placeholder="Fullname"
                      value={user.name || ''}
                      onChange={(e) => this.setState({ user: { ...user, name: e.target.value } })}
                    />
                  </Col>
                  {/* Phone Number */}
                  <Col md="6" className="form-group">
                    <label htmlFor="fePhoneNumber">Phone Number</label>
                    <FormInput
                      id="fePhoneNumber"
                      placeholder="Phone Number"
                      value={user.phone || ''}
                      onChange={(e) => this.setState({ user: { ...user, phone: e.target.value } })}
                    />
                  </Col>
                </Row>
                <Row form>
                  {/* Email */}
                  <Col md="6" className="form-group">
                    <label htmlFor="feEmail">Email</label>
                    <FormInput
                      type="email"
                      id="feEmail"
                      placeholder="Email Address"
                      value={user.email || ''}
                      onChange={(e) => this.setState({ user: { ...user, email: e.target.value } })}
                      autoComplete="email"
                    />
                  </Col>
                  {/* Password */}
                  <Col md="6" className="form-group">
                    <label htmlFor="fePassword">Password</label>
                    <FormInput
                      type="password"
                      id="fePassword"
                      placeholder="Password"
                      value={user.password || ''}
                      onChange={(e) => this.setState({ user: { ...user, password: e.target.value } })}
                      autoComplete="current-password"
                    />
                  </Col>
                </Row>
                <FormGroup>
                  <label htmlFor="feAddress">Address</label>
                  <FormInput
                    id="feAddress"
                    placeholder="Address"
                    value={user.address || ''}
                    onChange={(e) => this.setState({ user: { ...user, address: e.target.value } })}
                  />
                </FormGroup>
                <Row form>
                  {/* Description */}
                  <Col md="12" className="form-group">
                    <label htmlFor="feDescription">Description</label>
                    <FormTextarea id="feDescription" rows="5" 
                      value={user.note || ''}
                      onChange={(e) => this.setState({ user: { ...user, note: e.target.value } })} 
                    />
                  </Col>
                </Row>
                <Button theme="accent" onClick={() => this.handleUpdate()}>Update Account</Button>
              </Form>
            </Col>
          </Row>
        </ListGroupItem>
      </ListGroup>
    </Card>
    )
  }
};

UserAccountDetails.propTypes = {
  /**
   * The component's title.
   */
  title: PropTypes.string
};

UserAccountDetails.defaultProps = {
  title: "Account Details"
};

export default UserAccountDetails;
