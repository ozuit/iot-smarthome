import React from "react";
import { Container, Row, Col, Card, CardHeader, CardBody, Button, Modal, ModalBody, ModalHeader } from "shards-react";
import api from "../api";
import { Link } from "react-router-dom";

import PageTitle from "../components/common/PageTitle";

class Rooms extends React.Component 
{
  constructor(props) {
    super(props)

    this.state = {
      roomsData: [],
      openModal: false,
      deleteRoomId: null,
    }
  }

  componentWillMount() {
    this.fetch()
  }

  fetch() {
    api.get('/room').then((res) => {
      this.setState({
        roomsData: res.data
      })
    })
  }
  
  handleDelete(room_id) {
    this.setState({
      openModal: true,
      deleteRoomId: room_id
    })
  }

  render() {
    let { roomsData, openModal, deleteRoomId } = this.state;

    return (
      <Container fluid className="main-content-container px-4">
        {/* Page Header */}
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="Danh sách phòng" subtitle="Phòng" className="text-sm-left" />
        </Row>

        <Row>
          <Col>
            <Card small className="mb-4">
              <CardHeader className="border-bottom">
                <Row>
                  <Col>

                  </Col>
                  <Col>
                    <Button theme="primary" style={{ float: 'right' }} tag={Link} to="new-room">
                      Thêm mới phòng
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
                        Tên phòng
                      </th>
                      <th scope="col" className="border-0">
                        Topic
                      </th>
                      <th scope="col" className="border-0">
                        Số lượng thiết bị
                      </th>
                      <th scope="col" className="border-0">
                        Hành động
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {
                      roomsData.map((room, index) => (
                        <tr key={index}>
                          <td>{ index + 1 }</td>
                          <td>{ room.name }</td>
                          <td>{ room.topic }</td>
                          <td>{ room.number }</td>
                          <td>
                            <Link to={"show-device/" + room.id}>
                              <i className="material-icons mr-2" style={styles.button}>devices_other</i>
                            </Link>
                            <Link to={"edit-room/" + room.id}>
                              <i className="material-icons mr-2" style={styles.button}>edit</i>
                            </Link>
                            <i className="material-icons mr-2" style={styles.button} onClick={() => this.handleDelete(room.id)}>delete</i>
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
          <ModalHeader>Xoá phòng</ModalHeader>
          <ModalBody>
            <h6>Bạn có thật sự muốn xoá phòng này không ?</h6>
            <Button outline theme="warning" className="mr-2" onClick={() => {
              api.delete('/room/' + deleteRoomId).then((res) => {
                this.setState({ openModal: false })
                this.fetch()
              })
            }}>Có</Button>
            <Button outline theme="primary" onClick={() => { this.setState({ openModal: false }) }}>Không</Button>
          </ModalBody>
        </Modal>
      </Container>
    )
  }
}

const styles = {
  button: {
    cursor: 'pointer',
    fontSize: 14,
    color: '#5a6169',
  },
}

export default Rooms;
