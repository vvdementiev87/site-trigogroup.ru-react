import React from "react";
import { Login } from "../pages/Login";
import { MainPage } from "../pages/MainPage";

export interface IRoute {
  path: string;
  component: React.ComponentType;
  exact?: boolean;
}
export enum RouteNames {
  LOGIN = "login/",
  MAINPAGE = "/",
}
export const publicRoutes: IRoute[] = [
  { path: RouteNames.LOGIN, exact: true, component: Login },
];
export const privateRoutes: IRoute[] = [
  { path: RouteNames.MAINPAGE, exact: true, component: MainPage },
];
