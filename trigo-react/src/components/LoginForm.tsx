import { Button, Form, Input } from "antd";
import React, { useState } from "react";
import { useAction } from "../hooks/useAction";
import { useTypedSelector } from "../hooks/useTypedSelector";
import { rules } from "../utils/rules";

export const LoginForm: React.FC = () => {
  const { error, isLoading } = useTypedSelector((state) => state.auth);
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const { login } = useAction();
  const onFinishFalied = (errorInfo: any) => {
    console.log("Failed:", errorInfo);
  };
  const submit = () => {
    login(username, password);
  };
  return (
    <Form
      name="basic"
      onFinish={submit}
      onFinishFailed={onFinishFalied}
      autoComplete="off"
    >
      {error && <div style={{ color: "red" }}>Ощибка: {error}</div>}
      <Form.Item
        label="Username"
        name="usernmae"
        rules={[rules.required("Please enter your username")]}
      >
        <Input value={username} onChange={(e) => setUsername(e.target.value)} />
      </Form.Item>
      <Form.Item
        label="Password"
        name="password"
        rules={[rules.required("Please input your password!")]}
      >
        <Input.Password
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
      </Form.Item>
      <Form.Item>
        <Button type="primary" htmlType="submit" loading={isLoading}>
          Submit
        </Button>
      </Form.Item>
    </Form>
  );
};
