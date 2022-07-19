import { Col, Layout, Menu, Row } from "antd";
import React from "react";
import type { MenuProps } from "antd";
import { useAction } from "../hooks/useAction";
import { useTypedSelector } from "../hooks/useTypedSelector";
import { LogoutOutlined, LoginOutlined } from "@ant-design/icons";

export const Navbar: React.FC = () => {
  const { isAuth, user } = useTypedSelector((state) => state.auth);
  const { logout } = useAction();

  const onClick: MenuProps["onClick"] = () => {
    logout();
  };
  const itemsLogout: MenuProps["items"] = [
    {
      label: "Выход",
      key: "1",
      icon: <LogoutOutlined />,
    },
  ];
  const itemsLogin: MenuProps["items"] = [
    {
      label: "Логин",
      key: "1",
      icon: <LoginOutlined />,
    },
  ];

  return (
    <Layout.Header>
      <Row justify="end">
        {isAuth ? (
          <>
            <Col span="auto">
              <div style={{ color: "white" }}>{user.username}</div>
            </Col>
            <Col span={2}>
              <Menu
                onClick={onClick}
                theme="dark"
                mode="horizontal"
                selectable={false}
                items={itemsLogout}
              />
            </Col>
          </>
        ) : (
          <Col span={3}>
            <Menu
              onClick={onClick}
              theme="dark"
              mode="horizontal"
              selectable={false}
              items={itemsLogin}
            />
          </Col>
        )}
      </Row>
    </Layout.Header>
  );
};
