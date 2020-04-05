import React from "react";
import { Container, Row, Col, Card, CardHeader, CardBody, Button, Modal, ModalBody, ModalHeader } from "shards-react";
import api from "../api";
import { Link } from "react-router-dom";

import PageTitle from "../components/common/PageTitle";

class Users extends React.Component 
{
  constructor(props) {
    super(props)

    this.state = {
      usersData: [],
      openModal: false,
      deleteUserId: null,
    }
  }

  componentWillMount() {
    this.fetch()
  }

  fetch() {
    api.get('/user').then((res) => {
      this.setState({
        usersData: res.data
      })
    })
  }
  
  handleDelete(user_id) {
    this.setState({
      openModal: true,
      deleteUserId: user_id
    })
  }

  render() {
    let { usersData, openModal, deleteUserId } = this.state;

    return (
      <Container fluid className="main-content-container px-4">
        {/* Page Header */}
        <Row noGutters className="page-header py-4">
          <PageTitle sm="4" title="Danh sách người dùng" subtitle="Người dùng" className="text-sm-left" />
        </Row>

        <Row>
          <Col>
            <Card small className="mb-4">
              <CardHeader className="border-bottom">
                <Row>
                  <Col>

                  </Col>
                  <Col>
                    <Button theme="primary" style={{ float: 'right' }} tag={Link} to="new-user">
                      Thêm người dùng
                    </Button>
                  </Col>
                </Row>
              </CardHeader>
              <CardBody className="p-0 pb-3">
                <div style={{overflowX:'auto'}}>
                  <table className="table mb-0">
                    <thead className="bg-light">
                      <tr>
                        <th scope="col" className="border-0">
                          Họ & tên
                        </th>
                        <th scope="col" className="border-0">
                          Email
                        </th>
                        <th scope="col" className="border-0">
                          Số ĐT
                        </th>
                        <th scope="col" className="border-0">
                          Địa chỉ
                        </th>
                        <th scope="col" className="border-0">
                          Hành động
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {
                        usersData.map((user, index) => (
                          <tr key={index}>
                            <td data-label="Họ & tên">{ user.name }</td>
                            <td data-label="Email">{ user.email }</td>
                            <td data-label="Số ĐT">{ user.phone }</td>
                            <td data-label="Địa chỉ">{ user.address }</td>
                            <td data-label="Hành động">
                              <Link to={"edit-user/" + user.id}>
                                <i className="material-icons mr-2" style={styles.edit}>edit</i>
                              </Link>
                              {
                                user.id === 1 ? null : <i className="material-icons mr-2" style={styles.delete} onClick={() => this.handleDelete(user.id)}>delete</i>
                              }
                            </td>
                          </tr>
                        ))
                      }
                    </tbody>
                  </table>
                </div>
              </CardBody>
            </Card>
          </Col>
        </Row>

        <Modal open={openModal}>
          <ModalHeader>Xoá người dùng</ModalHeader>
          <ModalBody>
            <h6>Bạn có thật sự muốn xoá người dùng này không ?</h6>
            <Button outline theme="warning" className="mr-2" onClick={() => {
              api.delete('/user/' + deleteUserId).then((res) => {
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

export default Users;
