import axios, { AxiosResponse } from "axios";
import { IUser } from "../models/IUser";

axios.defaults.headers.post["Content-Type"] =
  "application/x-www-form-urlencoded";
axios.defaults.headers.post["Access-Control-Allow-Origin"] = "*";
const API_URL = "https://trigogroup.ru/";

export default class AuthService {
  static async login(
    username: string,
    password: string
  ): Promise<AxiosResponse<IUser>> {
    return fetch(API_URL + "include/controller.php", {
      method: "POST",
      mode: "no-cors",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username: username, password: password }),
    })
      .then((response) => {
        console.log(JSON.stringify({ Username: username, password: password }));
        if (response) {
          sessionStorage.setItem("username", JSON.stringify(response));
        }
        console.log("response.data", response);
        return response;
      })
      .catch((response) => {
        console.log(response);
        return response;
      });
  }
}
