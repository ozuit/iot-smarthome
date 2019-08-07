import React from "react";
import PropTypes from "prop-types";
import {
  Container,
  Card,
  CardHeader,
  CardBody,
} from "shards-react";
import api from "../../api";
import mqtt from "../../utils/mqtt";

class ShowDevice extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      devices: []
    }

  }

  componentWillMount() {
    this.fetchData()
  }

  fetchData() {
    const { match: { params } } = this.props;

    api.get("/node", {
        params: {
            _filter: `room_id:${params.room_id};is_sensor:0`
        }
    }).then((res) => {
        this.setState({
            devices: res.data
        })
    })
  }

  handleControl(device) {
    const payload = device.active ? '0' : '1'
    api.put('/node/update/' + device.id, {
        topic: device.topic,
        payload: mqtt.signature(payload),
        status: payload,
    }).then((res) => {
        if (res.status) {
            this.fetchData()
        } else {
            alert(res.message)
        }
    })
  }

  render() {
    const { devices } = this.state

    return (
      <Container fluid className="main-content-container py-4 px-4">
        <Card small>
            <CardHeader className="border-bottom">
                <h6 className="m-0">{this.props.title}</h6>
            </CardHeader>
            <CardBody style={styles.container}>
                {
                    devices.map((device, id) => (
                        <div key={id} style={styles.deviceItem} onClick={this.handleControl.bind(this, device)}>
                            <span>{ device.name }</span>
                            <p style={styles.status}>{ device.active ? 'ON' : 'OFF' }</p>
                        </div>
                    ))
                }
            </CardBody>
        </Card>
      </Container>
    )
  }
};

const styles = {
    container: {
        display: 'flex',
    },
    deviceItem: {
        flex: 1,
        border: '1px solid #5a6169',
        margin: '10px 20px',
        padding: 20,
        borderRadius: 10,
        textAlign: 'center',
        cursor: 'pointer',
    },
    status: {
        margin: 0,
        padding: 0,
        fontWeight: 'bold',
        marginTop: 5,
    },
}

ShowDevice.propTypes = {
  /**
   * The component's title.
   */
  title: PropTypes.string
};

ShowDevice.defaultProps = {
  title: "List Devices"
};

export default ShowDevice;
