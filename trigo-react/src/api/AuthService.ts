import axios, { AxiosResponse } from "axios";
import { IUser } from "../models/IUser";



const API_URL = "https://devavi.ru/";

export default class AuthService {
  static async login(
    username: string,
    password: string
  ): Promise<AxiosResponse<IUser>> {
    let params = new URLSearchParams();
  params.append('username', username);
  params.append('password', password);
  console.log(params);
    axios.defaults.headers.post["Content-Type"] =
    "application/x-www-form-urlencoded";    
    /* axios.defaults.withCredentials = true; */
    return axios.post(API_URL, params)
      .then((response: AxiosResponse) => {
        console.log(response);
        if (response) {
          sessionStorage.setItem("username", JSON.stringify(response));
        }
        console.log("response.data", response.data);
        return response;
      })
      .catch((response) => {
        console.log(response);
        return response;
      });
  }
}
