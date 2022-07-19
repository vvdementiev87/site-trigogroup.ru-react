import { Card, Col, Layout, Row } from "antd";
import React from "react";
import { LoginForm } from "../components/LoginForm";

export const Login = () => {
  return (
    <Layout style={{ height: "100%" }}>
      <Row justify="center" align="middle" style={{ height: "100%" }}>
        <Col flex="500px">
          <Card>
            <LoginForm />
          </Card>
        </Col>
      </Row>
    </Layout>
  );
};
