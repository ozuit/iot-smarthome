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

class NewSensor extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      sensor: {},
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
    const { sensor } = this.state;
    sensor.is_sensor = true;
    
    api.post("/node", sensor).then((res) => {
      if(res.status) {
        alert('Thêm mới thành công!')
      }
    })
  }

  render() {
    const { sensor, roomData } = this.state;

    return (
      <Container fluid className="main-content-container py-4 px-4">
        <Card small>
          <CardHeader className="border-bottom">
            <h6 className="m-0">Thêm cảm biến</h6>
          </CardHeader>
          <ListGroup flush>
            <ListGroupItem className="p-3">
              <Row>
                <Col>
                  <Form>
                    <FormGroup>
                      <label htmlFor="feName">Tên cảm biến</label>
                      <FormInput
                        id="feName"
                        placeholder="Nhập vào tên của cảm biến"
                        value={sensor.name || ''}
                        onChange={(e) => this.setState({ sensor: { ...sensor, name: e.target.value } })}
                      />
                    </FormGroup>

                    <FormGroup>
                      <label htmlFor="feTopic">Topic</label>
                      <FormInput
                        id="feTopic"
                        placeholder="Nhập vào một topic cho cảm biến"
                        value={sensor.topic || ''}
                        onChange={(e) => this.setState({ sensor: { ...sensor, topic: e.target.value } })}
                      />
                    </FormGroup>

                    <FormGroup>
                      <label htmlFor="feRoom">Thuộc phòng</label>
                      <FormSelect id="feRoom" onChange={(e) => this.setState({ sensor: { ...sensor, room_id: e.target.value } })}>
                        {
                          roomData.map((room, index) => (
                            <option key={index} value={room.id}>{ room.name }</option>
                          ))
                        }
                      </FormSelect>
                    </FormGroup>

                    <Button theme="info" outline className="mr-2" tag={Link} to="/sensors">Trở về</Button>
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

NewSensor.propTypes = {
  /**
   * The component's title.
   */
  title: PropTypes.string
};

NewSensor.defaultProps = {
  title: "Sensor Details"
};

export default NewSensor;
