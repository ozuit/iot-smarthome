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
    FormTextarea,
    Button
} from "shards-react";
import api from "../../api";
import { Link } from "react-router-dom";

class EditUser extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            user: {},
        }
    }

    componentWillMount() {
        const { match: { params } } = this.props;

        api.get("/user/" + params.user_id).then((res) => {
            this.setState({
                user: res.data
            })
        })
    }

    handleCreate() {
        const { match: { params } } = this.props;
        const { user } = this.state;
        
        api.put("/user/" + params.user_id, user).then((res) => {
            if (res.status) {
                alert('Cập nhật thành công!!')
            }
        })
    }

    render() {
        const { user } = this.state;

        return (
            <Container fluid className="main-content-container py-4 px-4">
                <Card small>
                    <CardHeader className="border-bottom">
                        <h6 className="m-0">Thông tin người dùng</h6>
                    </CardHeader>
                    <ListGroup flush>
                        <ListGroupItem className="p-3">
                            <Row>
                                <Col>
                                    <Form>
                                        <Row form>
                                            {/* Họ & tên */}
                                            <Col md="6" className="form-group">
                                                <label htmlFor="feFullname">Họ & tên</label>
                                                <FormInput
                                                    id="feFullname"
                                                    placeholder="Họ & tên"
                                                    value={user.name || ''}
                                                    onChange={(e) => this.setState({ user: { ...user, name: e.target.value } })}
                                                />
                                            </Col>
                                            {/* Số điện thoại */}
                                            <Col md="6" className="form-group">
                                                <label htmlFor="fePhoneNumber">Số điện thoại</label>
                                                <FormInput
                                                    id="fePhoneNumber"
                                                    placeholder="Số điện thoại"
                                                    value={user.phone || ''}
                                                    onChange={(e) => this.setState({ user: { ...user, phone: e.target.value } })}
                                                />
                                            </Col>
                                        </Row>
                                        <Row form>
                                            {/* Email */}
                                            <Col md="6" className="form-group">
                                                <label htmlFor="feEmail">Email</label>
                                                <FormInput
                                                    type="email"
                                                    id="feEmail"
                                                    placeholder="Địa chỉ Email"
                                                    value={user.email || ''}
                                                    onChange={(e) => this.setState({ user: { ...user, email: e.target.value } })}
                                                    autoComplete="email"
                                                />
                                            </Col>
                                            {/* Password */}
                                            <Col md="6" className="form-group">
                                                <label htmlFor="fePassword">Password</label>
                                                <FormInput
                                                    type="password"
                                                    id="fePassword"
                                                    placeholder="Password"
                                                    value={user.password || ''}
                                                    onChange={(e) => this.setState({ user: { ...user, password: e.target.value } })}
                                                    autoComplete="current-password"
                                                />
                                            </Col>
                                        </Row>
                                        <FormGroup>
                                            <label htmlFor="feAddress">Địa chỉ</label>
                                            <FormInput
                                                id="feAddress"
                                                placeholder="Địa chỉ"
                                                value={user.address || ''}
                                                onChange={(e) => this.setState({ user: { ...user, address: e.target.value } })}
                                            />
                                        </FormGroup>
                                        <Row form>
                                            {/* Description */}
                                            <Col md="12" className="form-group">
                                                <label htmlFor="feDescription">Mô tả</label>
                                                <FormTextarea id="feDescription" rows="5"
                                                    value={user.note || ''}
                                                    onChange={(e) => this.setState({ user: { ...user, note: e.target.value } })}
                                                />
                                            </Col>
                                        </Row>

                                        <Button theme="info" outline className="mr-2" tag={Link} to="/users">Trở về</Button>

                                        <Button theme="accent" onClick={() => this.handleCreate()}>Cập nhật</Button>
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

EditUser.propTypes = {
    /**
     * The component's title.
     */
    title: PropTypes.string
};

EditUser.defaultProps = {
    title: "Account Details"
};

export default EditUser;
