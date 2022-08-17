import { Breadcrumb, Layout} from "antd";
import React from "react";
import { GithubOutlined } from '@ant-design/icons';

const FooterComp: React.FC = () => {
  return (
    <Layout.Footer style={{display:"flex", justifyContent:"flex-end"}}>
    <Breadcrumb>
        <Breadcrumb.Item href="https://github.com/vvdementiev87/">
            <GithubOutlined />
            <span> GIT: vvdementiev87 </span>
        </Breadcrumb.Item>
    </Breadcrumb>
    </Layout.Footer>
  );
};
export default FooterComp;