import { AppDispatch } from "../..";
import AuthService from "../../../api/AuthService";
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
      dispatch(AuthActionCreator.setError(""));
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
        dispatch(AuthActionCreator.setError(""));
        dispatch(AuthActionCreator.setIsLoading(true));
          const response = await AuthService.login(username, password);
          if (response) {
            let text=response.data;
            if (response instanceof(Error)){
              throw response;
            }
            dispatch(AuthActionCreator.setUser(response.data));
            dispatch(AuthActionCreator.setIsAuth(true));
          }
          dispatch(AuthActionCreator.setIsLoading(false));
        
      } catch (error) {
        dispatch(AuthActionCreator.setError(`${error}`));
      }
    },
};
