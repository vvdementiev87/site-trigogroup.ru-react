import { createJSDocAuthorTag } from "typescript";
import { AppDispatch } from "../..";
import AuthService from "../../../api/AuthService";
import UserService from "../../../api/UserService";
import { IUser } from "../../../models/IUser";
import {
  AuthActionEnum,
  SetAuthAction,
  SetErrorAction,
  SetIsLoadingAction,
  SetUserAction,
} from "./types";

export const AuthActionCreator = {
  setUser: (user: IUser): SetUserAction => ({
    type: AuthActionEnum.SET_USER,
    payload: user,
  }),
  setIsAuth: (auth: boolean): SetAuthAction => ({
    type: AuthActionEnum.SET_AUTH,
    payload: auth,
  }),
  setIsLoading: (payload: boolean): SetIsLoadingAction => ({
    type: AuthActionEnum.SET_IS_LOADING,
    payload: payload,
  }),
  setError: (error: string): SetErrorAction => ({
    type: AuthActionEnum.SET_ERROR,
    payload: error,
  }),

  logout: () => async (dispatch: AppDispatch) => {
    try {
      sessionStorage.removeItem("auth");
      sessionStorage.removeItem("username");
      dispatch(AuthActionCreator.setUser({} as IUser));
      dispatch(AuthActionCreator.setIsAuth(false));
    } catch (error) {
      dispatch(AuthActionCreator.setError("Ошибка: " + error));
    }
  },
  login:
    (username: string, password: string) => async (dispatch: AppDispatch) => {
      try {
        dispatch(AuthActionCreator.setIsLoading(true));
        setTimeout(async () => {
          const response = await AuthService.login(username, password);
          /* const mockUser = response.data.find(
            (user) => user.username === username && user.password === password
          );
          if (mockUser) {
            sessionStorage.setItem("auth", "true");
            sessionStorage.setItem("username", mockUser.username);
            dispatch(AuthActionCreator.setUser(mockUser));
            dispatch(AuthActionCreator.setIsAuth(true));
          } else {
            dispatch(AuthActionCreator.setError("Wromg username or password"));
          } */
          if (response) {
            dispatch(AuthActionCreator.setUser(response.data));
            dispatch(AuthActionCreator.setIsAuth(true));
          }
          dispatch(AuthActionCreator.setIsLoading(false));
        }, 1000);
      } catch (error) {
        dispatch(AuthActionCreator.setError("Ошибка: " + error));
      }
    },
};
