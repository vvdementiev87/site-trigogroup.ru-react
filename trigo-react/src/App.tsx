import { Layout } from "antd";
import React, { useEffect } from "react";
import { AppRouter } from "./components/AppRouter";
import FooterComp from "./components/FooterComp";
import { Navbar } from "./components/Navbar";
import { useAction } from "./hooks/useAction";
import { IUser } from "./models/IUser";

const App: React.FC = () => {
  const { setUser, setIsAuth } = useAction();
  useEffect(() => {
    if (sessionStorage.getItem("auth")) {
      setUser({ username: sessionStorage.getItem("username" || "") } as IUser);
      setIsAuth(true);
    }
  }, []);
  return (
    <Layout style={{ height: "100vh" }}>
      <Navbar />
      <Layout.Content>
        <AppRouter />
      </Layout.Content>
      <FooterComp />
    </Layout>
  );
};
export default App;
