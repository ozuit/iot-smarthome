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
    const { match: { params } } = this.props;

    api.get("/node/" + params.sensor_id).then((res) => {
        this.setState({
            sensor: res.data
        })
    })

    api.get('/room').then((res) => {
      this.setState({
        roomData: res.data
      })
    })
  }

  handleUpdate() {
    const { match: { params } } = this.props;
    const { sensor } = this.state;
    
    api.put("/node/" + params.sensor_id, sensor).then((res) => {
      if(res.status) {
        alert('Update Successful!')
      }
    })
  }

  render() {
    const { sensor, roomData } = this.state;

    return (
      <Container fluid className="main-content-container py-4 px-4">
        <Card small>
          <CardHeader className="border-bottom">
            <h6 className="m-0">{this.props.title}</h6>
          </CardHeader>
          <ListGroup flush>
            <ListGroupItem className="p-3">
              <Row>
                <Col>
                  <Form>
                    <FormGroup>
                      <label htmlFor="feName">Name Sensor</label>
                      <FormInput
                        id="feName"
                        placeholder="Enter a name for sensor or sensor"
                        value={sensor.name || ''}
                        onChange={(e) => this.setState({ sensor: { ...sensor, name: e.target.value } })}
                      />
                    </FormGroup>

                    <FormGroup>
                      <label htmlFor="feTopic">Topic</label>
                      <FormInput
                        id="feTopic"
                        placeholder="Enter a topic for sensor"
                        value={sensor.topic || ''}
                        onChange={(e) => this.setState({ sensor: { ...sensor, topic: e.target.value } })}
                      />
                    </FormGroup>

                    <FormGroup>
                      <label htmlFor="feRoom">Room</label>
                      <FormSelect id="feRoom" onChange={(e) => this.setState({ sensor: { ...sensor, room_id: e.target.value } })}>
                        {
                          roomData.map((room, index) => (
                            <option key={index} value={room.id} selected={ room.id === sensor.room_id }>{ room.name }</option>
                          ))
                        }
                      </FormSelect>
                    </FormGroup>
                    
                    <FormGroup>
                      <FormRadio
                        inline
                        name="active"
                        checked={sensor.active === 1}
                        onChange={() => this.setState({ sensor: { ...sensor, active: 1 } })}
                      >
                        Active
                      </FormRadio>
                      <FormRadio
                        inline
                        name="active"
                        checked={sensor.active === 0}
                        onChange={() => this.setState({ sensor: { ...sensor, active: 0 } })}
                      >
                        Unactive
                      </FormRadio>
                    </FormGroup>
                   
                    <Button theme="info" outline className="mr-2" tag={Link} to="/sensors">Go Back</Button>
                    <Button theme="accent" onClick={() => this.handleUpdate()}>Update Sensor</Button>
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
