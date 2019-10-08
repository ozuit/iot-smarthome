import React from "react";
import { Container, Row, Col, Card, CardHeader, CardBody, Button, Modal, ModalBody, ModalHeader } from "shards-react";
import api from "../api";
import { Link } from "react-router-dom";
import mqtt from "../utils/mqtt";

import PageTitle from "../components/common/PageTitle";

class Devices extends React.Component 
{
  constructor(props) {
    super(props)

    this.state = {
      devicesData: [],
      openModal: false,
      deleteDeviceId: null,
    }
  }

  componentWillMount() {
    this.fetch()
  }

  fetch() {
    api.get('/node', {
      params: {
        _filter: 'is_sensor:0',
        _relations: 'room'
      }
    }).then((res) => {
      this.setState({
        devicesData: res.data
      })
    })
  }
  
  handleDelete(device_id) {
    this.setState({
      openModal: true,
      deleteDeviceId: device_id
    })
  }

  toggleDevice(device) {
    api.put('/node/update/' + device.id, {
      topic: device.topic,
      payload: mqtt.signature(device.active === 1 ? '0' : '1'),
      status: device.active === 1 ? 0: 1
    }).then((res) => {
      if (res.status) {
        this.fetch()
      }
    })
  }

  render() {
    let { devicesData, openModal, deleteDeviceId } = this.state;

    return (
      <Container fluid className="main-content-container px-4">
        {/* Page Header */}
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="List Items" subtitle="Devices" className="text-sm-left" />
        </Row>

        <Row>
          <Col>
            <Card small className="mb-4">
              <CardHeader className="border-bottom">
                <Row>
                  <Col>
                    <h6 className="m-0">Devices Table</h6>
                  </Col>
                  <Col>
                    <Button theme="primary" style={{ float: 'right' }} tag={Link} to="new-device">
                      New Device
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
                        Active
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
                      devicesData.map((device, index) => (
                        <tr key={index}>
                          <td>{ index + 1 }</td>
                          <td>{ device.name }</td>
                          <td>{ device.topic }</td>
                          <td>{ device.active === 1 ? (
                            <Button theme="success" onClick={this.toggleDevice.bind(this, device)}>Mở</Button>
                          ) : (
                            <Button theme="light" onClick={this.toggleDevice.bind(this, device)}>Tắt</Button>
                          )}</td>
                          <td>{ device.room_id ? device.room.data.name : '' }</td>
                          <td>
                            <Link to={"edit-device/" + device.id}>
                              <i className="material-icons mr-2" style={styles.edit}>edit</i>
                            </Link>
                            <i className="material-icons mr-2" style={styles.delete} onClick={() => this.handleDelete(device.id)}>delete</i>
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
          <ModalHeader>Delete Device</ModalHeader>
          <ModalBody>
            <h6>Are you sure you want to delete this device ?</h6>
            <Button outline theme="warning" className="mr-2" onClick={() => {
              api.delete('/node/' + deleteDeviceId).then((res) => {
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

export default Devices;
