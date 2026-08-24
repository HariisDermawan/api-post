import api from "./axsios";


export const loginApi = (payload: {
    email: string,
    password: string,
}) => api.post("/login", payload)

export const meApi = () => api.get("/me")
export const logoutApi = () => api.post("/logout")
