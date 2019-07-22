import React from "react";
import {
    Row,
    Col,
    Card,
    CardHeader,
} from "shards-react";
import LoginForm from "../components/login/LoginForm";

const Login = () => (
    <Row className="screen-center">
        <Col md="4">
            <Card small>
                <CardHeader className="border-bottom">
                    <h6 className="m-0 text-center">LOGIN</h6>
                </CardHeader>
                <LoginForm />
            </Card>
        </Col>
    </Row>
);

export default Login;