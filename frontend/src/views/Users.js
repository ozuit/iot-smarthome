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
          <PageTitle sm="4" title="List Items" subtitle="Users" className="text-sm-left" />
        </Row>

        <Row>
          <Col>
            <Card small className="mb-4">
              <CardHeader className="border-bottom">
                <Row>
                  <Col>
                    <h6 className="m-0">Users Table</h6>
                  </Col>
                  <Col>
                    <Button theme="success" style={{ float: 'right' }} tag={Link} to="new-user">
                      New User
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
                        Email
                      </th>
                      <th scope="col" className="border-0">
                        Phone
                      </th>
                      <th scope="col" className="border-0">
                        Address
                      </th>
                      <th scope="col" className="border-0">
                        Action
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {
                      usersData.map((user, index) => (
                        <tr>
                          <td>{ index + 1 }</td>
                          <td>{ user.name }</td>
                          <td>{ user.email }</td>
                          <td>{ user.phone }</td>
                          <td>{ user.address }</td>
                          <td>
                            <Link to={"edit-user/" + user.id}>
                              <i class="material-icons mr-2" style={styles.edit}>edit</i>
                            </Link>
                            {
                              user.id === 1 ? null : <i class="material-icons mr-2" style={styles.delete} onClick={() => this.handleDelete(user.id)}>delete</i>
                            }
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
          <ModalHeader>Delete User</ModalHeader>
          <ModalBody>
            <h6>Are you sure you want to delete this user ?</h6>
            <Button outline theme="warning" className="mr-2" onClick={() => {
              api.delete('/user/' + deleteUserId).then((res) => {
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

export default Users;
