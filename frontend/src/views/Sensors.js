import React from "react";
import { Container, Row, Col, Card, CardHeader, CardBody, Button, Modal, ModalBody, ModalHeader } from "shards-react";
import api from "../api";
import { Link } from "react-router-dom";

import PageTitle from "../components/common/PageTitle";

class Sensors extends React.Component 
{
  constructor(props) {
    super(props)

    this.state = {
      sensorsData: [],
      openModal: false,
      deleteSensorId: null,
    }
  }

  componentWillMount() {
    this.fetch()
  }

  fetch() {
    api.get('/node', {
      params: {
        _filter: 'is_sensor:1',
        _relations: 'room'
      }
    }).then((res) => {
      this.setState({
        sensorsData: res.data
      })
    })
  }
  
  handleDelete(sensor_id) {
    this.setState({
      openModal: true,
      deleteSensorId: sensor_id
    })
  }

  render() {
    let { sensorsData, openModal, deleteSensorId } = this.state;

    return (
      <Container fluid className="main-content-container px-4">
        {/* Page Header */}
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="List Items" subtitle="Sensors" className="text-sm-left" />
        </Row>

        <Row>
          <Col>
            <Card small className="mb-4">
              <CardHeader className="border-bottom">
                <Row>
                  <Col>
                    <h6 className="m-0">Sensors Table</h6>
                  </Col>
                  <Col>
                    <Button theme="success" style={{ float: 'right' }} tag={Link} to="new-sensor">
                      New Sensor
                    </Button>
                  </Col>
                </Row>
              </CardHeader>
              <CardBody className="p-0 pb-3">
                <table className="table mb-0">
                  <thead className="bg-light">
                    <tr>
                      <th scope="col" className="border-0">
                        #
                      </th>
                      <th scope="col" className="border-0">
                        Name
                      </th>
                      <th scope="col" className="border-0">
                        Topic
                      </th>
                      <th scope="col" className="border-0">
                        Room
                      </th>
                      <th scope="col" className="border-0">
                        Action
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {
                      sensorsData.map((sensor, index) => (
                        <tr key={index}>
                          <td>{ index + 1 }</td>
                          <td>{ sensor.name }</td>
                          <td>{ sensor.topic }</td>
                          <td>{ sensor.room_id ? sensor.room.data.name : '' }</td>
                          <td>
                            <Link to={"edit-sensor/" + sensor.id}>
                              <i className="material-icons mr-2" style={styles.edit}>edit</i>
                            </Link>
                            <i className="material-icons mr-2" style={styles.delete} onClick={() => this.handleDelete(sensor.id)}>delete</i>
                          </td>
                        </tr>
                      ))
                    }
                  </tbody>
                </table>
              </CardBody>
            </Card>
          </Col>
        </Row>

        <Modal open={openModal}>
          <ModalHeader>Delete Sensor</ModalHeader>
          <ModalBody>
            <h6>Are you sure you want to delete this sensor ?</h6>
            <Button outline theme="warning" className="mr-2" onClick={() => {
              api.delete('/node/' + deleteSensorId).then((res) => {
                this.setState({ openModal: false })
                this.fetch()
              })
            }}>Yes</Button>
            <Button outline theme="primary" onClick={() => { this.setState({ openModal: false }) }}>No</Button>
          </ModalBody>
        </Modal>
      </Container>
    )
  }
}

const styles = {
  edit: {
    cursor: 'pointer',
    fontSize: 14,
    color: '#5a6169',
  },
  delete: {
    cursor: 'pointer',
    color: '#5a6169',
    fontSize: 14,
  }
}

export default Sensors;
