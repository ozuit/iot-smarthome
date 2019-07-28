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
  Button,
} from "shards-react";
import api from "../../api";
import { Link } from "react-router-dom";

class NewRoom extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      room: {},
    }
  }

  componentWillMount() {
    const { match: { params } } = this.props;

    api.get('/room/' + params.room_id).then((res) => {
      this.setState({
        room: res.data
      })
    })
  }

  handleUpdate() {
    const { match: { params } } = this.props;
    const { room } = this.state;
    
    api.put("/room/" + params.room_id, room).then((res) => {
      if(res.status) {
        alert('Update Successful!')
      }
    })
  }

  render() {
    const { room } = this.state;

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
                      <label htmlFor="feName">Name Room</label>
                      <FormInput
                        id="feName"
                        placeholder="Enter a name for room or sensor"
                        value={room.name || ''}
                        onChange={(e) => this.setState({ room: { ...room, name: e.target.value } })}
                      />
                    </FormGroup>
                    
                    <FormGroup>
                      <label htmlFor="feTopic">Topic</label>
                      <FormInput
                        id="feTopic"
                        placeholder="Enter a topic for room"
                        value={room.topic || ''}
                        onChange={(e) => this.setState({ room: { ...room, topic: e.target.value } })}
                      />
                    </FormGroup>
                   
                    <Button theme="info" outline className="mr-2" tag={Link} to="/rooms">Go Back</Button>
                    <Button theme="accent" onClick={() => this.handleUpdate()}>Update Room</Button>
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

NewRoom.propTypes = {
  /**
   * The component's title.
   */
  title: PropTypes.string
};

NewRoom.defaultProps = {
  title: "Room Details"
};

export default NewRoom;
