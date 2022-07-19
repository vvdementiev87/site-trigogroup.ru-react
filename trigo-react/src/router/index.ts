import React from "react";
import { Login } from "../pages/Login";

export interface IRoute {
  path: string;
  component: React.ComponentType;
  exact?: boolean;
}
export enum RouteNames {
  LOGIN = "login/",
  EVENT = "/",
}
export const publicRoutes: IRoute[] = [
  { path: RouteNames.LOGIN, exact: true, component: Login },
];
