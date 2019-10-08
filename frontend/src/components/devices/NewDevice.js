import React from "react";
import PropTypes from "prop-types";
import {
  Container,
  Card,
  CardHeader,
  ListGroup,
  ListGroupItem,
  Row,
  Col,
  Form,
  FormGroup,
  FormInput,
  FormRadio,
  Button,
  FormSelect
} from "shards-react";
import api from "../../api";
import { Link } from "react-router-dom";

class NewDevice extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      device: {},
      roomData: [],
    }
  }

  componentWillMount() {
    api.get('/room').then((res) => {
      this.setState({
        roomData: res.data
      })
    })
  }

  handleCreate() {
    const { device } = this.state;
    device.is_sensor = false;
    
    api.post("/node", device).then((res) => {
      if(res.status) {
        alert('Thêm mới thành công!')
      }
    })
  }

  render() {
    const { device, roomData } = this.state;

    return (
      <Container fluid className="main-content-container py-4 px-4">
        <Card small>
          <CardHeader className="border-bottom">
            <h6 className="m-0">Thêm thiết bị</h6>
          </CardHeader>
          <ListGroup flush>
            <ListGroupItem className="p-3">
              <Row>
                <Col>
                  <Form>
                    <FormGroup>
                      <label htmlFor="feName">Tên thiết bị</label>
                      <FormInput
                        id="feName"
                        placeholder="Nhập vào tên thiết bị"
                        value={device.name || ''}
                        onChange={(e) => this.setState({ device: { ...device, name: e.target.value } })}
                      />
                    </FormGroup>

                    <FormGroup>
                      <label htmlFor="feTopic">Topic</label>
                      <FormInput
                        id="feTopic"
                        placeholder="Nhập vào một topic cho thiết bị"
                        value={device.topic || ''}
                        onChange={(e) => this.setState({ device: { ...device, topic: e.target.value } })}
                      />
                    </FormGroup>

                    <FormGroup>
                      <label htmlFor="feRoom">Thuộc phòng</label>
                      <FormSelect id="feRoom" onChange={(e) => this.setState({ device: { ...device, room_id: e.target.value } })}>
                        {
                          roomData.map((room, index) => (
                            <option key={index} value={room.id}>{ room.name }</option>
                          ))
                        }
                      </FormSelect>
                    </FormGroup>

                    <FormGroup>
                      <FormRadio
                        inline
                        name="active"
                        checked={device.active === 1}
                        onChange={() => this.setState({ device: { ...device, active: 1 } })}
                      >
                        Mở
                      </FormRadio>
                      <FormRadio
                        inline
                        name="active"
                        checked={device.active === 0}
                        onChange={() => this.setState({ device: { ...device, active: 0 } })}
                      >
                        Tắt
                      </FormRadio>
                    </FormGroup>
                   
                    <Button theme="info" outline className="mr-2" tag={Link} to="/devices">Trở về</Button>
                    <Button theme="accent" onClick={() => this.handleCreate()}>Thêm mới</Button>
                  </Form>
                </Col>
              </Row>
            </ListGroupItem>
          </ListGroup>
        </Card>
      </Container>
    )
  }
};

NewDevice.propTypes = {
  /**
   * The component's title.
   */
  title: PropTypes.string
};

NewDevice.defaultProps = {
  title: "Device Details"
};

export default NewDevice;
