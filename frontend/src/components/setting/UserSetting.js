import React from "react";
import {
  Card,
  Container,
  ListGroup,
  ListGroupItem,
  Row,
  Col,
  Form,
  FormGroup,
  FormInput,
  Button
} from "shards-react";
import api from "../../api";

class UserSetting extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      setting: {},
    }
  }

  componentWillMount() {
    this.fetch()
  }

  fetch() {
    api.get("/setting").then((res) => {
      this.setState({
        setting: res.data[0]
      })
    })
  }

  handleUpdate() {
    let { setting } = this.state;
    api.put("/setting/1", {
      limit_fan_sensor: setting.limit_fan_sensor
    }).then((res) => {
      if (res.status) {
        alert('Cập nhật thành công!!')
      }
    })
  }

  toggleFanSensor(setting) {
    api.put('/setting/1', {
      active_fan_sensor: setting.active_fan_sensor === 1 ? 0 : 1
    }).then((res) => {
      if (res.status) {
        this.fetch()
      }
    })
  }
  
  toggleLightSensor(setting) {
    api.put('/setting/1', {
      active_motion_detection: setting.active_motion_detection === 1 ? 0 : 1
    }).then((res) => {
      if (res.status) {
        this.fetch()
      }
    })
  }
  
  toggleGasSensor(setting) {
    api.put('/setting/1', {
      active_gas_warning: setting.active_gas_warning === 1 ? 0 : 1
    }).then((res) => {
      if (res.status) {
        this.fetch()
      }
    })
  }

  render() {
    let { setting } = this.state;

    return (
      <Container fluid className="main-content-container px-4">
        <Card className="mb-4">
          <ListGroup flush>
            <ListGroupItem className="p-3">
              <Row>
                <Col>
                  <Form>
                    <Row form>
                      <Col md="6" className="form-group">
                        <label>Mở quạt tự động</label>
                        <Row>
                          <Col>
                            { setting.active_fan_sensor === 1 ? (
                              <Button theme="success" onClick={this.toggleFanSensor.bind(this, setting)}>Đang mở (nhấn để tắt)</Button>
                            ) : (
                              <Button theme="light" onClick={this.toggleFanSensor.bind(this, setting)}>Đang tắt (nhấn để mở)</Button>
                            )}
                          </Col>
                        </Row>
                      </Col>
                      <Col md="6" className="form-group">
                        <label htmlFor="feFanSensorLimit">Mở quạt khi nhiệt độ trên</label>
                        <FormInput
                          id="feFanSensorLimit"
                          placeholder="Nhập giới hạn nhiệt độ"
                          value={setting.limit_fan_sensor}
                          onChange={(e) => this.setState({ setting: { ...setting, limit_fan_sensor: e.target.value } })}
                          onBlur={this.handleUpdate.bind(this)}
                        />
                      </Col>
                    </Row>
                    
                    <Row form>
                      <Col md="6" className="form-group">
                        <label>Mở đèn tự động</label>
                        <Row>
                          <Col>
                            { setting.active_motion_detection === 1 ? (
                              <Button theme="success" onClick={this.toggleLightSensor.bind(this, setting)}>Đang mở (nhấn để tắt)</Button>
                            ) : (
                              <Button theme="light" onClick={this.toggleLightSensor.bind(this, setting)}>Đang tắt (nhấn để mở)</Button>
                            )}
                          </Col>
                        </Row>
                      </Col>
                      <Col md="6" className="form-group">
                        <label>Cảnh báo khí gas</label>
                        <Row>
                          <Col>
                            { setting.active_gas_warning === 1 ? (
                              <Button theme="success" onClick={this.toggleGasSensor.bind(this, setting)}>Đang mở (nhấn để tắt)</Button>
                            ) : (
                              <Button theme="light" onClick={this.toggleGasSensor.bind(this, setting)}>Đang tắt (nhấn để mở)</Button>
                            )}
                          </Col>
                        </Row>
                      </Col>
                    </Row>
                    
                    
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

export default UserSetting;
